<?php

namespace App\Http\Controllers\v4_3_2\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Bulk "open edit page + click Update" for eligible invoices.
 *
 * Eligible = is_deleted = 0 AND no brokerage bill generated
 *            (no broker_purchases row with brokerbill_no for that invoice).
 *
 * MODES (param "mode"):
 *   mismatch (default) -> only invoices where, on any line,
 *                         mng_col.amount  OR  broker_purchases.invoice_grand_total
 *                         is different from  (Rate_per_kg * Net_Weight_Kgs) - order discount %
 *                         Matching invoices are left untouched.
 *   all                -> every eligible invoice (full edit-page recalculation)
 *
 * Extends invoiceController so constructor, permissions, dynamic_connection
 * and all models are reused.
 *
 * Per invoice (inside its own DB transaction):
 *   1. apply invoice formulas on each mng_col row
 *   2. amount = Rate_per_kg * Net_Weight_Kgs - order discount %
 *   3. update order_details + broker_purchases (same fields as edit page)
 *   4. recalc order totals
 *   5. recalc invoice total / GST / roundoff / grand_total
 *   6. adjust payment_details + invoice status (same rules as edit page)
 */
class invoiceBulkController extends invoiceController
{
    // Model name used by getmodel() for the invoice formula table
    const FORMULA_MODEL = 'tbl_invoice_formula';

    const TOL = 0.004; // money tolerance
    const IGNORE_DIFF = 1.00;  // differences smaller than this (₹1) are ignored

    private $formulas      = [];
    private $gardenMap     = []; // garden_name => id
    private $gradeMap      = []; // grade => id
    private $gardenCompany = []; // garden_id => company_id
    private $brokerage     = []; // companymaster id => brokerage
    private $orderCache    = []; // order_detail_id => [order_id, discount]
    private $conn          = null; // SAME connection the models use (transaction + raw queries)

    /**
     * POST invoice/bulk-recalculate
     *
     * Params:
     *  mode        mismatch (default) | all
     *  last_id     (int, default 0)   cursor - send back next_last_id from previous response
     *  batch_size  (int, default 50, max 200)
     *  dry_run     (0/1)              calculate everything, then ROLLBACK (nothing saved)
     *  invoice_id  (optional)         run for one invoice only
     *  from_id     (optional)         start of range (inclusive)
     *  to_id       (optional)         end of range (inclusive)
     */
    public function recalculate(Request $request)
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        if (
            $this->rp['invoicemodule']['invoice']['edit'] != 1 ||
            $this->rp['invoicemodule']['invoice']['alldata'] != 1
        ) {
            return response()->json(['status' => 500, 'message' => 'You are Unauthorized']);
        }

        $dryRun    = filter_var($request->dry_run, FILTER_VALIDATE_BOOLEAN);
        $batchSize = max(1, min(200, (int) ($request->batch_size ?: 50)));
        $lastId    = (int) $request->last_id;
        $singleId  = $request->invoice_id;
        $fromId    = (int) ($request->from_id ?: 0);
        $toId      = (int) ($request->to_id ?: 0);
        $mode      = $request->mode === 'all' ? 'all' : 'mismatch';

        // Log the actual dry_run value received
        Log::info('invoice bulk recalculate started', [
            'dry_run' => $dryRun,
            'dry_run_raw' => $request->dry_run,
            'batch_size' => $batchSize,
            'last_id' => $lastId,
            'single_id' => $singleId,
            'from_id' => $fromId,
            'to_id' => $toId,
            'mode' => $mode,
        ]);

        try {
            $this->loadLookups();
        } catch (\Throwable $e) {
            Log::error('Lookup load failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 500,
                'message' => 'Lookup load failed (check FORMULA_MODEL name): ' . $e->getMessage(),
            ]);
        }

        $query = $this->eligibleInvoices()->orderBy('id');
        if ($singleId) {
            $query->where('id', $singleId);
        } elseif ($fromId && $toId) {
            // Range mode: process from_id to to_id
            $query->where('id', '>=', $fromId)->where('id', '<=', $toId);
        } else {
            // Normal cursor mode
            $query->where('id', '>', $lastId)->limit($batchSize);
        }
        $invoices = $query->get();

        // Use the SAME connection the models use, so commit / rollback covers every write
        $this->conn = $this->invoiceModel::query()->getConnection();
        $conn       = $this->conn;

        $guard = $this->engineGuard($conn);
        if ($guard) {
            return response()->json(['status' => 500, 'message' => $guard]);
        }

        $warnings = [];
        if ($conn->transactionLevel() > 0) {
            $warnings[] = 'A DB transaction was already open (level ' . $conn->transactionLevel()
                . ') before this run. If data does not persist, a middleware/wrapper is holding an outer transaction.';
        }
        if ($conn->getName() !== 'dynamic_connection') {
            $warnings[] = 'Models use connection "' . $conn->getName() . '" (not dynamic_connection). Using the models connection for all writes.';
        }

        $updated   = 0;
        $matched   = 0;  // already correct (mismatch mode)
        $saved     = 0;  // live run: written AND verified by reading back from DB
        $sumMng    = 0;  // mng_col lines whose amount differs
        $sumBroker = 0;  // broker_purchases.invoice_grand_total that differ
        $changed   = [];
        $skipped   = [];
        $failed    = [];

        if ($singleId && $invoices->isEmpty()) {
            $skipped[] = [
                'id'     => (int) $singleId,
                'inv_no' => '-',
                'reason' => 'Invoice not found, deleted, or brokerage bill already generated',
            ];
        }

        foreach ($invoices as $inv) {
            $conn->beginTransaction();
            try {
                $res = $this->processInvoice($inv, $mode === 'mismatch');

                $committed = false;
                if ($res['status'] === 'updated' && !$dryRun) {
                    Log::info('Attempting to commit invoice', ['invoice_id' => $inv->id, 'dry_run' => $dryRun]);
                    $conn->commit();
                    $committed = true;
                    Log::info('Commit successful', ['invoice_id' => $inv->id]);
                } else {
                    Log::info('Rolling back', ['invoice_id' => $inv->id, 'status' => $res['status'], 'dry_run' => $dryRun]);
                    $conn->rollBack();
                }

                // LIVE run: read back from the DB and confirm the values are really saved
                if ($committed) {
                    $problems = $this->verifySaved($inv, $res['expect']);
                    if (!empty($problems)) {
                        Log::error('invoice bulk recalculate: NOT saved', ['invoice_id' => $inv->id, 'problems' => $problems]);
                        $failed[] = [
                            'id'     => $inv->id,
                            'inv_no' => $inv->inv_no,
                            'error'  => 'Committed but DB value is different: ' . implode(' | ', $problems),
                        ];
                        continue;
                    }
                    $saved++;
                    Log::info('Invoice saved and verified', ['invoice_id' => $inv->id]);
                }

                if ($res['status'] === 'matched') {
                    $matched++;
                } elseif ($res['status'] === 'updated') {
                    $updated++;
                    $sumMng    += $res['diff_counts']['mng'];
                    $sumBroker += $res['diff_counts']['broker'];

                    if ($res['changed']) {
                        $changed[] = [
                            'id'              => $inv->id,
                            'inv_no'          => $inv->inv_no,
                            'old_grand'       => $res['old_grand'],
                            'new_grand'       => $res['new_grand'],
                            'reasons'         => $res['reasons'],
                            'invoice_changes' => $res['invoice_changes'],
                            'mng_col_changes' => $res['mng_col_changes'],
                            'broker_changes'  => $res['broker_changes'],
                            'lines_untouched' => $res['lines_untouched'],
                            'lines_total'     => $res['lines_total'],
                        ];
                    }
                } else {
                    $skipped[] = ['id' => $inv->id, 'inv_no' => $inv->inv_no, 'reason' => $res['reason']];
                }
            } catch (\Throwable $e) {
                $conn->rollBack();
                Log::error('invoice bulk recalculate failed', [
                    'invoice_id' => $inv->id,
                    'error'      => $e->getMessage(),
                    'line'       => $e->getLine(),
                ]);
                $failed[] = ['id' => $inv->id, 'inv_no' => $inv->inv_no, 'error' => $e->getMessage()];
            }
        }

        $nextLastId = $invoices->isEmpty() ? $lastId : (int) $invoices->last()->id;
        $done = true;
        if (!$singleId && !($fromId && $toId)) {
            // Only check for more invoices in cursor mode (not single or range)
            $done = !$this->eligibleInvoices()->where('id', '>', $nextLastId)->exists();
        } elseif ($fromId && $toId) {
            // In range mode, we're done when we've processed up to to_id
            $done = $nextLastId >= $toId;
        }

        return response()->json([
            'status'            => 200,
            'dry_run'           => $dryRun,
            'mode'              => $mode,
            'processed'         => $invoices->count(),
            'updated'           => $updated,
            'matched'           => $matched,
            'saved_verified'    => $saved,
            'warnings'          => $warnings,
            'mismatch_mng_col'  => $sumMng,
            'mismatch_broker'   => $sumBroker,
            'changed'           => $changed,
            'skipped'           => $skipped,
            'failed'            => $failed,
            'next_last_id'      => $nextLastId,
            'done'              => $done,
        ]);
    }

    /* ------------------------------------------------------------------ */

    /** is_deleted = 0 and no brokerage bill generated */
    private function eligibleInvoices()
    {
        return $this->invoiceModel::where('is_deleted', 0)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('broker_purchases')
                    ->whereColumn('broker_purchases.invoice_id', 'invoices.id')
                    ->whereNull('broker_purchases.brokerbill_no');
            });
    }

    private function loadLookups(): void
    {
        $this->formulas = $this->getmodel(self::FORMULA_MODEL)::where('is_deleted', 0)
            ->orderBy('id')->get()->all();

        $this->gardenMap = $this->gardenModel::where('is_deleted', 0)
            ->pluck('id', 'garden_name')->toArray();

        $this->gradeMap = $this->gradesModel::where('is_deleted', 0)
            ->pluck('id', 'grade')->toArray();

        $this->gardenCompany = $this->companygardenModel::pluck('company_id', 'garden_id')->toArray();

        $this->brokerage = $this->companymastersModel::pluck('brokerage', 'id')->toArray();

        $this->orderCache = [];
    }

    /** Transactions / Dry Run only work on InnoDB. Stop if a table is MyISAM. */
    private function engineGuard($conn): ?string
    {
        try {
            $rows = $conn->select(
                "SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME IN ('invoices','mng_col','order_details','orders','broker_purchases','payment_details')"
            );
        } catch (\Throwable $e) {
            return null; // cannot check -> do not block
        }

        $bad = [];
        foreach ($rows as $r) {
            $r      = (array) $r;
            $engine = strtoupper((string) ($r['ENGINE'] ?? $r['engine'] ?? ''));
            $table  = $r['TABLE_NAME'] ?? $r['table_name'] ?? '?';
            if ($engine !== 'INNODB') {
                $bad[] = "{$table} ({$engine})";
            }
        }

        return $bad
            ? 'These tables are not InnoDB: ' . implode(', ', $bad)
                . '. Transactions and Dry Run are not safe on them. Convert to InnoDB first.'
            : null;
    }

    /** Re-read from DB after commit. Returns list of problems (empty = really saved). */
    private function verifySaved($inv, array $exp): array
    {
        $problems = [];

        $db = $this->invoiceModel::where('id', $inv->id)->first();
        if (!$db) {
            return ['invoice row not found after save'];
        }

        if (abs((float) $db->grand_total - $exp['grand_total']) > 0.01) {
            $problems[] = "invoices.grand_total is {$db->grand_total}, expected " . round($exp['grand_total'], 2);
        }
        if (abs((float) $db->total - $exp['total']) > 0.01) {
            $problems[] = "invoices.total is {$db->total}, expected " . round($exp['total'], 2);
        }

        foreach ($exp['lines'] as $id => $amt) {
            $v = $this->conn->table('mng_col')->where('id', $id)->value('amount');
            if ($v === null || abs((float) $v - $amt) > 0.01) {
                $problems[] = "mng_col #{$id}.amount is " . ($v ?? 'NULL') . ', expected ' . round($amt, 2);
            }
        }

        foreach ($exp['broker'] as $odId => $amt) {
            $bp = $this->brokerpurchaseModel::where('order_detail_id', $odId)->first();
            // broker column may be whole-number typed, so allow rounding
            if ($bp && abs((float) $bp->invoice_grand_total - $amt) > 0.51) {
                $problems[] = "broker_purchases (order_detail #{$odId}).invoice_grand_total is {$bp->invoice_grand_total}, expected " . round($amt, 2);
            }
        }

        return $problems;
    }

    /** expected line amount = Rate_per_kg * Net_Weight_Kgs - order discount % */
    private function expectedAmount(array $vals, $orderDetailId): array
    {
        $rate       = $vals['Rate_per_kg'] ?? 0;
        $netKg      = $vals['Net_Weight_Kgs'] ?? 0;
        $calculated = $rate * $netKg;

        $orderInfo   = $orderDetailId ? $this->lookupOrder($orderDetailId) : ['order_id' => null, 'discount' => 0];
        $discountAmt = $orderInfo['discount'] > 0 ? ($calculated * $orderInfo['discount']) / 100 : 0;

        return [$calculated - $discountAmt, $calculated, $orderInfo];
    }
    /** expected amount from the SAVED row values (no formulas), used to decide if a line is already correct */
    private function savedExpected(array $row, $orderDetailId): float
    {
        $vals = [
            'Rate_per_kg'    => (float) ($row['Rate_per_kg'] ?? 0),
            'Net_Weight_Kgs' => (float) ($row['Net_Weight_Kgs'] ?? 0),
        ];
        [$expected] = $this->expectedAmount($vals, $orderDetailId);
        return $expected;
    }

    /**
     * True if ANY line has:
     *   mng_col.amount                        != expected
     *   broker_purchases.invoice_grand_total  != expected
     */
    private function hasAmountMismatch($rows): bool
    {
        $odIds = $rows->pluck('order_detail_id')->filter()->unique()->values()->all();
        $bp    = $odIds
            ? $this->brokerpurchaseModel::whereIn('order_detail_id', $odIds)
                ->pluck('invoice_grand_total', 'order_detail_id')->toArray()
            : [];

        foreach ($rows as $r) {
            $row      = (array) $r;
            $odId     = $row['order_detail_id'] ?? null;
            $expected = $this->savedExpected($row, $odId);   // saved Rate x Net Kg - discount

            if (abs((float) ($row['amount'] ?? 0) - $expected) > self::IGNORE_DIFF) {
                return true;
            }
            if ($odId && array_key_exists($odId, $bp) && abs((float) $bp[$odId] - $expected) > self::IGNORE_DIFF) {
                return true;
            }
        }

        return false;
    }

        private function processInvoice($inv, bool $mismatchOnly = false): array
    {
        $conn = $this->conn;

        $rows = $conn->table('mng_col')
            ->where('invoice_id', $inv->id)
            ->where('is_deleted', 0)
            ->where('is_active', 1)
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            return ['status' => 'skipped', 'reason' => 'no line items'];
        }

        // mismatch mode: leave correct invoices untouched
        if ($mismatchOnly && !$this->hasAmountMismatch($rows)) {
            return ['status' => 'matched'];
        }

        // GST safety: never wipe tax if saved settings are missing
        $gs     = json_decode($inv->gstsettings ?? '', true);
        $hasTax = ($inv->cgst || $inv->sgst || $inv->igst || $inv->gst);
        if ($hasTax && empty($gs)) {
            return ['status' => 'skipped', 'reason' => 'GST invoice but gstsettings empty'];
        }

        $sumAmount     = 0;
        $orderIds      = [];
        $mngColChanges = [];
        $brokerChanges = [];
        $expectLines   = []; // mng_col id => expected amount
        $expectBroker  = []; // order_detail_id => expected invoice_grand_total
        $odIds = $rows->pluck('order_detail_id')->filter()->unique()->values()->all();
        $bpMap = $odIds
            ? $this->brokerpurchaseModel::whereIn('order_detail_id', $odIds)
                ->pluck('invoice_grand_total', 'order_detail_id')->toArray()
            : [];
        $untouched = 0;

        foreach ($rows as $r) {
            $row       = (array) $r;
            $oldAmount = (float) ($row['amount'] ?? 0);
            $orderDetailId = $row['order_detail_id'] ?? null;

            // ---- CHECK FIRST on SAVED Rate x Net Kg (before formulas) ----
            $savedExp     = $this->savedExpected($row, $orderDetailId);
            $lineAmountOk = abs($oldAmount - $savedExp) <= self::IGNORE_DIFF;
            $brokerOk     = !$orderDetailId
                            || !array_key_exists($orderDetailId, $bpMap)
                            || abs((float) $bpMap[$orderDetailId] - $savedExp) <= self::IGNORE_DIFF;

            if ($mismatchOnly && $lineAmountOk) {
                // CORRECT LINE: keep saved amount, no formula columns, no order_details, no second discount
                $sumAmount += $oldAmount;
                $expectLines[$row['id']] = $oldAmount;

                // only the broker value is wrong -> fix only that
                if (!$brokerOk && $orderDetailId) {
                    $rawVals = [];
                    foreach ($row as $k => $v) {
                        $rawVals[$k] = is_numeric($v) ? (float) $v : 0.0;
                    }
                    $expectBroker[$orderDetailId] = $oldAmount;
                    $bpDiff = $this->upsertBrokerPurchase($inv, $row, $rawVals, $orderDetailId, $oldAmount);
                    if ($bpDiff) {
                        $brokerChanges[] = $bpDiff;
                    }
                }

                $untouched++;
                continue;
            }

            // ---- WRONG LINE: recalculate ----

            // 1) formulas
            [$vals, $colChanges] = $this->applyFormulas($row);

            // 2) amount
            [$amount, $calculated, $orderInfo] = $this->expectedAmount($vals, $orderDetailId);
            $rate  = $vals['Rate_per_kg'] ?? 0;
            $netKg = $vals['Net_Weight_Kgs'] ?? 0;

            $lineAmount = $amount;

            $sumAmount += $lineAmount;
            $expectLines[$row['id']] = $lineAmount;
            if ($orderDetailId) {
                $expectBroker[$orderDetailId] = $lineAmount;
            }

            // track what changes on this line
            $colDiff = [];
            foreach ($colChanges as $k => $newV) {
                $oldV = (float) ($row[$k] ?? 0);
                if (abs($oldV - (float) $newV) > 0.0005) {
                    $colDiff[] = ['column' => $k, 'old' => $oldV, 'new' => (float) $newV];
                }
            }
            if (abs($oldAmount - $lineAmount) > self::IGNORE_DIFF || !empty($colDiff)) {
                $mngColChanges[] = [
                    'mng_col_id'   => $row['id'],
                    'old_amount'   => $oldAmount,
                    'new_amount'   => $lineAmount,
                    'difference'   => $lineAmount - $oldAmount,
                    'calculated'   => $calculated,
                    'discount_pct' => (float) $orderInfo['discount'],
                    'columns'      => $colDiff,
                ];
            }

            // mng_col
            $updCol = $colChanges;
            $updCol['amount'] = $amount;
            $conn->table('mng_col')->where('id', $row['id'])->update($updCol + ['updated_by' => $this->userId]);

            // 3) order_details + broker_purchases
            if ($orderDetailId) {
                $this->order_detailModel::where('id', $orderDetailId)->update([
                    'bags'   => $vals['No_Of_Pkags'] ?? 0,
                    'kg'     => $vals['Net_Oty_Per_Pkg'] ?? 0,
                    'net_kg' => $netKg,
                    'rate'   => $rate,
                    'amount' => $calculated,
                ]);

                if ($orderInfo['order_id']) {
                    $orderIds[$orderInfo['order_id']] = true;
                }

                $bpDiff = $this->upsertBrokerPurchase($inv, $row, $vals, $orderDetailId, $lineAmount);
                if ($bpDiff) {
                    $brokerChanges[] = $bpDiff;
                }
            }
        }

        // 4) order totals
        foreach (array_keys($orderIds) as $orderId) {
            $sum = $this->order_detailModel::where('order_id', $orderId)
                ->where('is_deleted', 0)
                ->selectRaw('COALESCE(SUM(net_kg),0) as n, COALESCE(SUM(amount),0) as a')
                ->first();

            $order = $this->orderModel::find($orderId);
            if ($order) {
                $discount       = (float) ($order->discount ?? 0);
                $discountAmount = ($sum->a * $discount) / 100;
                $order->update([
                    'totalNetKg'     => $sum->n,
                    'totalAmount'    => $sum->a,
                    'discountAmount' => $discountAmount,
                    'finalAmount'    => $sum->a - $discountAmount,
                ]);
            }
        }

        // 5) invoice totals
        $total  = round($sumAmount, 2);
        $upd    = ['total' => $total];
        $taxSum = 0;

        if ($hasTax) {
            $sgstP = (float) ($gs['sgst'] ?? 0);
            $cgstP = (float) ($gs['cgst'] ?? 0);
            $igstP = (float) ($gs['igst'] ?? 0);
            $gstF  = (float) ($gs['gst'] ?? 0);

            $sv = round(($total * $sgstP) / 100, 2);
            $cv = round(($total * $cgstP) / 100, 2);
            $iv = round(($total * $igstP) / 100, 2);
            $taxSum = $sv + $cv + $iv;

            if ($gstF == 0) {
                $upd['sgst'] = $sv;
                $upd['cgst'] = $cv;
                $upd['igst'] = $iv;
            } else {
                $upd['gst'] = round($taxSum, 2);
            }
        }

        $grand = round(round($total + $taxSum, 2)); // same as applyRoundoff() in JS
        $upd['grand_total'] = $grand;

        // 6) payments (same rules as edit page)
        $payment = $this->payment_detailsModel::where('inv_id', $inv->id)
            ->where('is_deleted', 0)->orderBy('id', 'desc')->first();

        if ($payment) {
            $totalPaidAmount = $payment->amount - $payment->pending_amount;

            if (round($totalPaidAmount, 2) > $grand) {
                return [
                    'status' => 'skipped',
                    'reason' => "already received {$totalPaidAmount} > new grand total {$grand}",
                ];
            }

            $payments  = $this->payment_detailsModel::where('inv_id', $inv->id)
                ->where('is_deleted', 0)->get();
            $totalpaid = 0;
            foreach ($payments as $pay) {
                $totalpaid          += $pay->paid_amount + $pay->tds_amount;
                $pay->amount         = $grand;
                $pay->pending_amount = $grand - $totalpaid;
                $pay->part_payment   = ($grand > $totalPaidAmount) ? 1 : 0;
                $pay->updated_by     = $this->userId;
                $pay->save();
            }

            if ($inv->status != 'cancel') {
                $upd['status'] = (abs($grand - $totalPaidAmount) < 0.005) ? 'paid' : 'part_payment';
            }
        }

        // invoice-level diffs (what actually changes on the invoices table)
        $invoiceChanges = [];
        foreach (['total', 'sgst', 'cgst', 'igst', 'gst', 'grand_total'] as $f) {
            if (array_key_exists($f, $upd)) {
                $old = (float) $inv->{$f};
                if (abs($old - (float) $upd[$f]) > self::IGNORE_DIFF) {
                    $invoiceChanges[] = ['field' => $f, 'old' => $old, 'new' => (float) $upd[$f]];
                }
            }
        }
        if (isset($upd['status']) && $upd['status'] != $inv->status) {
            $invoiceChanges[] = ['field' => 'status', 'old' => $inv->status, 'new' => $upd['status']];
        }

        $upd['updated_by'] = $this->userId;
        $this->invoiceModel::where('id', $inv->id)->update($upd);

        $mngAmountDiffs = 0;
        foreach ($mngColChanges as $m) {
            if (abs($m['difference']) > self::IGNORE_DIFF) {
                $mngAmountDiffs++;
            }
        }

        return [
            'status'          => 'updated',
            'changed'         => !empty($invoiceChanges) || !empty($mngColChanges) || !empty($brokerChanges),
            'old_grand'       => (float) $inv->grand_total,
            'new_grand'       => $grand,
            'invoice_changes' => $invoiceChanges,
            'mng_col_changes' => $mngColChanges,
            'broker_changes'  => $brokerChanges,
            'diff_counts'     => ['mng' => $mngAmountDiffs, 'broker' => count($brokerChanges)],
            'expect'          => ['total' => $total, 'grand_total' => $grand, 'lines' => $expectLines, 'broker' => $expectBroker],
            'reasons'         => $this->buildReasons($inv, $total, $grand, $mngColChanges, $brokerChanges),
            'lines_untouched' => $untouched,
            'lines_total'     => $rows->count(),
        ];
    }

    /** Human readable "why did this invoice change" */
    private function buildReasons($inv, float $total, float $grand, array $mngColChanges, array $brokerChanges): array
    {
        $reasons = [];

        foreach ($mngColChanges as $m) {
            $id = $m['mng_col_id'];

            if (abs($m['difference']) > self::TOL) {
                if ($m['discount_pct'] > 0 && abs($m['old_amount'] - $m['calculated']) <= 0.005) {
                    $reasons[] = "Line #{$id}: saved amount had NO discount (Rate × Net Kg). Order discount {$m['discount_pct']}% is now applied.";
                } else {
                    $reasons[] = "Line #{$id}: amount recalculated from Rate × Net Weight"
                        . ($m['discount_pct'] > 0 ? " with {$m['discount_pct']}% order discount." : '.');
                }
            }

            if (!empty($m['columns'])) {
                $cols = implode(', ', array_column($m['columns'], 'column'));
                $reasons[] = "Line #{$id}: formula updated column(s): {$cols}.";
            }
        }

        foreach ($brokerChanges as $b) {
            if (!empty($b['created'])) {
                $reasons[] = "Broker purchase created for order detail #{$b['order_detail_id']} (invoice_grand_total " . number_format($b['new'], 2) . ').';
            } else {
                $reasons[] = "Broker purchase #{$b['broker_purchase_id']}: invoice_grand_total "
                    . number_format($b['old'], 2) . ' → ' . number_format($b['new'], 2)
                    . ' (now equals Rate × Net Kg − discount).';
            }
        }

        $totalSame = abs($total - (float) $inv->total) <= self::TOL;
        if (empty($mngColChanges) && empty($brokerChanges) && $totalSame && abs($grand - (float) $inv->grand_total) > self::TOL) {
            $reasons[] = 'Only roundoff: saved grand total was not a whole number. Edit page always rounds it.';
        }

        return $reasons;
    }

    /** Same as dynamiccalculaton() formula loop in the edit page JS */
    private function applyFormulas(array $row): array
    {
        $vals = [];
        foreach ($row as $k => $v) {
            $vals[$k] = is_numeric($v) ? (float) $v : 0.0;
        }

        $changes = [];
        foreach ($this->formulas as $f) {
            $k1 = str_replace(' ', '_', $f->first_column);
            $k2 = str_replace(' ', '_', $f->second_column);
            $v1 = $vals[$k1] ?? 0;
            $v2 = $vals[$k2] ?? 0;

            switch ($f->operation) {
                case '+': $res = $v1 + $v2; break;
                case '-': $res = $v1 - $v2; break;
                case '*': $res = $v1 * $v2; break;
                case '/': $res = $v2 != 0 ? $v1 / $v2 : 0; break;
                default:  $res = 0;
            }

            $out = round($res, 3);
            $key = str_replace(' ', '_', $f->output_column);
            $vals[$key] = $out;

            // only persist real mng_col columns; amount is calculated separately
            if (array_key_exists($key, $row) && strtolower($key) !== 'amount' && $key !== 'id') {
                $changes[$key] = $out;
            }
        }

        return [$vals, $changes];
    }

    private function lookupOrder($orderDetailId): array
    {
        if (isset($this->orderCache[$orderDetailId])) {
            return $this->orderCache[$orderDetailId];
        }

        $od       = $this->order_detailModel::find($orderDetailId);
        $orderId  = $od->order_id ?? null;
        $discount = 0;

        if ($orderId) {
            $order    = $this->orderModel::find($orderId);
            $discount = (float) ($order->discount ?? 0);
        }

        return $this->orderCache[$orderDetailId] = ['order_id' => $orderId, 'discount' => $discount];
    }

    /**
     * Update / create broker_purchases row (same fields as edit page).
     * Returns a diff array when invoice_grand_total changed (or row created), else null.
     */
    private function upsertBrokerPurchase($inv, array $row, array $vals, $orderDetailId, float $amount): ?array
    {
        $gardenId  = $this->gardenMap[$row['Garden'] ?? ''] ?? null;
        $gradeId   = $this->gradeMap[$row['Grade'] ?? ''] ?? null;
        $companyId = $gardenId ? ($this->gardenCompany[$gardenId] ?? null) : null;
        $brokerage = $companyId ? ($this->brokerage[$companyId] ?? null) : null;

        $data = [
            'bags'                => $vals['No_Of_Pkags'] ?? 0,
            'net_kg'              => $vals['Net_Weight_Kgs'] ?? 0,
            'shortage'            => $vals['shortage'] ?? 0,
            'final_net_kg'        => ($vals['No_Of_Pkags'] ?? 0) * ($vals['Net_Oty_Per_Pkg'] ?? 0),
            'rate'                => $vals['Rate_per_kg'] ?? 0,
            'invoice_grand_total' => $amount,
            'invoice_id'          => $inv->id,
        ];

        $existing = $this->brokerpurchaseModel::where('order_detail_id', $orderDetailId)->first();

        if ($existing) {
            $old = (float) $existing->invoice_grand_total;

            if ($gardenId)  $data['garden_id'] = $gardenId;
            if ($gradeId)   $data['grade']     = $gradeId;
            if ($brokerage !== null) $data['brokerage'] = $brokerage;
            $data['updated_by'] = $this->userId;
            $existing->update($data);

            return abs($old - $amount) > self::IGNORE_DIFF
                ? [
                    'broker_purchase_id' => $existing->id,
                    'order_detail_id'    => $orderDetailId,
                    'old'                => $old,
                    'new'                => $amount,
                ]
                : null;
        }

        // don't create junk rows if garden/grade can't be resolved
        if (!$gardenId || !$gradeId) {
            return null;
        }

        $new = $this->brokerpurchaseModel::create($data + [
            'garden_id'       => $gardenId,
            'grade'           => $gradeId,
            'invoice_no'      => $row['Invoice_no'] ?? null,
            'order_detail_id' => $orderDetailId,
            'source'          => 'invoice',
            'brokerage'       => $brokerage ?? 0,
            'created_by'      => $this->userId,
        ]);

        return [
            'broker_purchase_id' => $new->id,
            'order_detail_id'    => $orderDetailId,
            'old'                => null,
            'new'                => $amount,
            'created'            => true,
        ];
    }
}