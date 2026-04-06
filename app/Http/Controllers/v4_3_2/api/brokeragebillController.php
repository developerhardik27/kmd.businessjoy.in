<?php

namespace App\Http\Controllers\v4_3_2\api;

use Carbon\Carbon;
use App\Models\company;
use Illuminate\Http\Request;
use App\Models\company_detail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class brokeragebillController extends commonController
{
    public $version, $userId, $companyId, $masterdbname, $rp, $brokerpurchaseModel, $order_detailModel, $gradenModel, $brokerbillinvoiceModel, $companymasterModel, $invoiceModel;

    public function __construct(Request $request)
    {

        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }
        $this->version = 'v4_3_2';

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();
        $this->invoiceModel = $this->getmodel('invoice');
        $this->brokerpurchaseModel = $this->getmodel('broker_purchase');
        $this->order_detailModel = $this->getmodel('order_detail');
        $this->gradenModel = $this->getmodel('graden');
        $this->brokerbillinvoiceModel = $this->getmodel('broker_bill_invoice');
        $this->companymasterModel = $this->getmodel('companymaster');
    }
    public function getGardens()
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $gardens = $this->order_detailModel
            ::join('gardens', 'gardens.id', '=', 'order_details.garden_id')
            ->where('order_details.is_deleted', 0)
            ->select(
                'gardens.id as garden_id',
                'gardens.garden_name as garden_name'
            )
            ->distinct()
            ->orderBy('gardens.garden_name', 'ASC')
            ->get();

        return $this->successresponse(200, 'data', $gardens);
    }


    public function getOtherData(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $usedInvoices = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.garden_id', $request->garden_id)
            ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->leftJoin('invoices', 'invoices.id', '=', 'broker_purchases.invoice_id')
            ->leftJoin('mng_col as mc', function ($join) {
                $join->on('mc.invoice_id', '=', 'invoices.id')
                    ->on('mc.order_detail_id', '=', 'broker_purchases.order_detail_id');
            })
            ->select(
                'broker_purchases.*',
                'gardens.garden_name',
                'grades.grade',
                'invoices.inv_no',
                'invoices.inv_date',
                DB::raw("DATE_FORMAT(invoices.inv_date, '%d-%m-%Y') as inv_date"),
                DB::raw("DATE_FORMAT(broker_purchases.brokerage_date, '%d-%m-%Y') as brokerage_date"),
                // DB::raw("DATE_FORMAT(broker_bill_invoice.to_date, '%d-%m-%Y') as to_date"),
                'mc.Net_Weight_Kgs',
                'mc.shortage',
                'mc.amount',
            )
            ->get();

        return $this->successresponse(200, 'data', $usedInvoices);
    }
    public function getOtherDatanull(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $usedInvoices = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.garden_id', $request->garden_id)
            ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->leftJoin('invoices', 'invoices.id', '=', 'broker_purchases.invoice_id')
            ->leftJoin('mng_col as mc', function ($join) {
                $join->on('mc.invoice_id', '=', 'invoices.id')
                    ->on('mc.order_detail_id', '=', 'broker_purchases.order_detail_id');
            })
            ->select(
                'broker_purchases.*',
                'gardens.garden_name',
                'grades.grade',
                'invoices.inv_no',
                'invoices.inv_date',
                'mc.Net_Weight_Kgs',
                'mc.shortage',
                'mc.amount',
            )
            ->where('broker_purchases.brokerage', '=', null)
            ->get();

        return $this->successresponse(200, 'data', $usedInvoices);
    }
    public function getOtherDatanotnull(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $usedInvoices = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.garden_id', $request->garden_id)
            ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->leftJoin('invoices', 'invoices.id', '=', 'broker_purchases.invoice_id')
            ->leftJoin('mng_col as mc', function ($join) {
                $join->on('mc.invoice_id', '=', 'invoices.id')
                    ->on('mc.order_detail_id', '=', 'broker_purchases.order_detail_id');
            })
            ->select(
                'broker_purchases.*',
                'gardens.garden_name',
                'grades.grade',
                'invoices.inv_no',
                'invoices.inv_date',
                'mc.Net_Weight_Kgs',
                'mc.shortage',
                'mc.amount',
            )
            ->where('broker_purchases.brokerage', '!=', null)
            ->get();

        return $this->successresponse(200, 'data', $usedInvoices);
    }


    public function brokeragebillpdflist(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $list = $this->brokerbillinvoiceModel
            ::leftJoin('broker_bill_payment_details', function ($join) {
                $join->on('broker_bill_invoice.id', '=', 'broker_bill_payment_details.inv_id')
                    ->whereRaw('broker_bill_payment_details.id = (
                    SELECT id FROM broker_bill_payment_details
                    WHERE inv_id = broker_bill_invoice.id
                      AND is_deleted = 0
                    ORDER BY id DESC LIMIT 1
                )');
            })
            ->leftJoin('companymasters', 'companymasters.id', '=', 'broker_bill_invoice.garden_company_id')
            ->where('broker_bill_invoice.is_deleted', 0);

        // ── Standard column filters (all except garden which lives in broker_purchases) ──
        $filters = [
            'filter_payment_status' => 'broker_bill_invoice.status',
            'filter_company'        => 'broker_bill_invoice.garden_company_id',
            'filter_date_from'      => 'broker_bill_invoice.created_at',
            'filter_date_to'        => 'broker_bill_invoice.created_at',

        ];

        foreach ($filters as $requestKey => $column) {
            $value = $request->$requestKey ?? null;
            if ($value !== null && $value !== '') {
                if (in_array($requestKey, ['filter_date_from', 'filter_date_to'])) {
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $list->where($column, $operator, $value);
                } elseif (strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false) {
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $list->whereDate($column, $operator, $value);
                } else {
                    $list->whereIn($column, (array) $value);
                }
            }
        }

        // ── Garden filter — garden_id lives in broker_purchases, linked by brokerbill_no ──
        // We find all broker_bill_invoice.id values that have at least one broker_purchases
        // row matching the requested garden_id(s), then restrict the main query to those IDs.
        if (!empty($request->filter_garden) && $request->filter_garden !== '') {
            $gardenIds = (array) $request->filter_garden; // handles single value or array

            $list->whereIn('broker_bill_invoice.id', function ($query) use ($gardenIds) {
                $query->select('brokerbill_no')
                    ->from('broker_purchases')
                    ->whereIn('garden_id', $gardenIds)
                    ->where('is_deleted', 0)
                    ->whereNotNull('brokerbill_no');
            });
        }
        if (!empty($request->filter_buyer) && $request->filter_buyer !== '') {
            $buyerIds = (array) $request->filter_buyer;

            $list->whereIn('broker_bill_invoice.id', function ($query) use ($buyerIds) {
                $query->select('bp.brokerbill_no')
                    ->from('broker_purchases as bp')
                    ->leftJoin('order_details as od', 'od.id', '=', 'bp.order_detail_id')
                    ->leftJoin('orders as o', 'o.id', '=', 'od.order_id')
                    ->whereIn('o.buyer_party', $buyerIds)
                    ->where('bp.is_deleted', 0)
                    ->whereNotNull('bp.brokerbill_no');
            });
        }

        $data = $list
            ->select(
                'broker_bill_invoice.*',
                DB::raw("DATE_FORMAT(broker_bill_invoice.invoice_date, '%d-%m-%Y') as invoice_date"),
                DB::raw("DATE_FORMAT(broker_bill_invoice.from_date, '%d-%m-%Y') as from_date"),
                DB::raw("DATE_FORMAT(broker_bill_invoice.to_date, '%d-%m-%Y') as to_date"),
                'broker_bill_payment_details.id as paymentid',
                'broker_bill_payment_details.part_payment',
                'broker_bill_payment_details.pending_amount',
                'companymasters.company_name',

                // Comma-separated invoice/lot numbers linked to this bill
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT invoice_no ORDER BY id SEPARATOR ', ')
                      FROM broker_purchases
                      WHERE brokerbill_no = broker_bill_invoice.id) as lot_no"),

                // Comma-separated garden names linked to this bill
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT g.garden_name ORDER BY bp.id SEPARATOR ', ')
                      FROM broker_purchases bp
                      LEFT JOIN gardens g ON g.id = bp.garden_id
                      WHERE bp.brokerbill_no = broker_bill_invoice.id) as garden_names"),

                // Comma-separated garden IDs (useful for blade data attributes)
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT bp.garden_id ORDER BY bp.id SEPARATOR ',')
                      FROM broker_purchases bp
                      WHERE bp.brokerbill_no = broker_bill_invoice.id
                        AND bp.is_deleted = 0) as garden_ids"),

                // Total net kg across all linked purchases
                DB::raw("(SELECT ROUND(SUM(net_kg), 2)
                      FROM broker_purchases
                      WHERE brokerbill_no = broker_bill_invoice.id) as net_kg"),

                // Brokerage % (distinct, comma-separated)
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT brokerage ORDER BY id SEPARATOR ', ')
                      FROM broker_purchases
                      WHERE brokerbill_no = broker_bill_invoice.id) as brokerage"),
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ', ')
                    FROM broker_purchases bp
                    LEFT JOIN order_details od ON od.id = bp.order_detail_id
                    LEFT JOIN orders o ON o.id = od.order_id
                    LEFT JOIN partys p ON p.id = o.buyer_party
                    WHERE bp.brokerbill_no = broker_bill_invoice.id
                    AND bp.is_deleted = 0
                ) as buyer_names"),
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT p.id ORDER BY p.id SEPARATOR ',')
                    FROM broker_purchases bp
                    LEFT JOIN order_details od ON od.id = bp.order_detail_id
                    LEFT JOIN orders o ON o.id = od.order_id
                    LEFT JOIN partys p ON p.id = o.buyer_party
                    WHERE bp.brokerbill_no = broker_bill_invoice.id
                    AND bp.is_deleted = 0
                ) as buyer_ids"),
            )
            ->get();

        if ($data->isEmpty()) {
            return DataTables::of($data)
                ->with([
                    'status'  => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }

        return DataTables::of($data)
            ->with(['status' => 200])
            ->make(true);
    }
    public function index()
    {
        if ($this->rp['teamodule']['brokeragebill']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $brokerpurchase = $this->brokerpurchaseModel
            ::join('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftjoin('broker_bill_invoice', 'broker_bill_invoice.garden_id', '=', 'broker_purchases.garden_id')
            ->select(
                'broker_purchases.garden_id',
                'gardens.garden_name',
                'broker_bill_invoice.garden_id as invoice_created',
                DB::raw('SUM(broker_purchases.bags) as total_bags'),
                DB::raw('SUM(broker_purchases.net_kg) as total_net_kg'),
                DB::raw('SUM(broker_purchases.brokerage) as total_brokerage')
            )
            ->where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.brokerage', '!=', 0)
            ->groupBy('broker_purchases.garden_id', 'gardens.garden_name', 'broker_bill_invoice.garden_id')
            ->get();
        // dd($brokerpurchase);
        if ($brokerpurchase->isEmpty()) {
            return DataTables::of($brokerpurchase)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($brokerpurchase)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }

    public function store(Request $request)
    {
        if ($this->rp['teamodule']['brokeragebill']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $data = $request->all();
        $rules = [
            'user_id' => 'required|integer',
            'form_type' => 'required|in:add,edit',
            'rows' => 'required|array|min:1',
        ];
        if ($data['form_type'] === 'edit') {
            $rules['rows.*.brokerage'] = 'required|numeric|min:0|max:100';
            $rules['rows.*.brokerage_date'] = 'required|date';
        } else {
            $rules['rows.*.brokerage'] = 'nullable|numeric|min:0|max:100';
            $rules['rows.*.brokerage_date'] = 'nullable|date';
        }

        $validator = Validator::make($data, $rules, [
            'rows.*.brokerage.required' => 'Brokerage is required.',
            'rows.*.brokerage.numeric' => 'Brokerage must be a number.',
            'rows.*.brokerage.min' => 'Brokerage must be at least 0.',
            'rows.*.brokerage.max' => 'Brokerage must not be greater than 100.',
            'rows.*.brokerage_date.required' => 'Brokerage date is required.',
            'rows.*.brokerage_date.date' => 'Brokerage date must be a valid date.',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        $updated = false;

        foreach ($request->rows as $row) {

            // if ($request->form_type === 'add' && $row['brokerage'] === null) {
            //     continue;
            // }
            if ($request->form_type === 'add') {
                $brokerage = $row['brokerage'] ?? null;
                $brokerage_date = $row['brokerage'] ?? null ? $row['brokerage_date'] : null;
            } else {
                // edit mode: all required, already validated
                $brokerage = $row['brokerage'];
                $brokerage_date = $row['brokerage_date'];
            }
            $result = $this->brokerpurchaseModel
                ::where('id', $row['id'])
                ->update([
                    'brokerage'      => $brokerage,
                    'brokerage_date' => $brokerage_date,
                    'updated_by'     => $request->user_id,
                ]);

            if ($result) {
                $updated = true;
            }
        }

        if ($updated) {
            return $this->successresponse(200, 'message', 'Broker Bill successfully');
        }
        return $this->successresponse(500, 'message', 'No brokerage data to update');
    }

    public function edit($id)
    {
        if ($this->rp['teamodule']['brokeragebill']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $brokerpurchase = $this->brokerpurchaseModel::find($id);
        if ($this->rp['teamodule']['brokeragebill']['alldata'] != 1) {
            if ($brokerpurchase->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$brokerpurchase) {
            return $this->successresponse(500, 'message', 'Broker Purchase not found !');
        }
        return $this->successresponse(200, 'brokerpurchase', $brokerpurchase);
    }

    public function destroy($id)
    {
        if ($this->rp['teamodule']['brokerpurchase']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $brokerpurchase = $this->brokerpurchaseModel::find($id);
        if ($this->rp['teamodule']['brokerpurchase']['alldata'] != 1) {
            if ($brokerpurchase->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$brokerpurchase) {
            return $this->successresponse(500, 'message', 'Commission Bill not found !');
        }
        $brokerpurchase->update(
            [
                "is_deleted" => 1
            ]
        );

        return $this->successresponse(200, 'message', 'Commission Bill succesfully deleted');
    }


    public function brokeragebillpdf(Request $request)
    {
        $company_id = $request->company_id;

        // ── Bank details check ───────────────────────────────────────────────────────
        $bankdetailsController = "App\\Http\\Controllers\\" . $this->version . "\\api\\bankdetailsController";
        $jsonbankdetails       = app($bankdetailsController)->bank_details($company_id);
        $bdetails              = json_decode($jsonbankdetails->getContent());

        if ($bdetails->status != 200) {
            return $this->successresponse(
                500,
                'message',
                'No bank details are available for your company. Please add bank details to continue.'
            );
        }

        // ── Main company data ────────────────────────────────────────────────────────
        $dbname          = company::find($request->company_id);
        $mainCompanyData = company_detail::where('company_details.id', $dbname->company_details_id)
            ->join('country', 'country.id', '=', 'company_details.country_id')
            ->join('state',   'state.id',   '=', 'company_details.state_id')
            ->join('city',    'city.id',    '=', 'company_details.city_id')
            ->select(
                'company_details.*',
                'country.country_name as country_name',
                'state.state_name     as state_name',
                'city.city_name       as city_name'
            )
            ->first();

        // ── Switch to dynamic DB ─────────────────────────────────────────────────────
        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');

        // ── Parse comma-separated garden_ids and invoice_ids ────────────────────────
        //    Blade sends "12"  or  "12,15,18"  — explode handles both cases cleanly.
        $garden_id_array  = array_filter(array_map('trim', explode(',', $request->garden_id)));
        $invoice_id_array = array_filter(array_map('trim', explode(',', $request->invoice_id)));

        if (empty($garden_id_array) || empty($invoice_id_array)) {
            return $this->successresponse(500, 'message', 'Invalid garden or invoice data provided.');
        }

        // ── Garden company data — use first garden_id (all selected rows are same company) ──
        $gardenCompanyData = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->where('broker_purchases.garden_id', $garden_id_array[0])   // first is fine — same company enforced in blade
            ->leftJoin('company_garden',  'company_garden.garden_id',  '=', 'broker_purchases.garden_id')
            ->leftJoin('companymasters',  'companymasters.id',         '=', 'company_garden.company_id')
            ->select(
                'company_garden.company_id as garden_company_id',
                'companymasters.*'
            )
            ->first();

        if (!$gardenCompanyData) {
            return $this->successresponse(500, 'message', 'Garden company data not found.');
        }

        // ── Fetch all broker_purchase rows for the given garden_ids AND invoice_ids ──
        //    FIX 1: whereIn uses the proper array (not the raw comma string)
        //    FIX 2: whereIn for invoice_ids so bulk works correctly
        $usedInvoices = $this->brokerpurchaseModel
            ::where('broker_purchases.is_deleted', 0)
            ->whereIn('broker_purchases.garden_id',  $garden_id_array)   // ✅ fixed
            ->whereIn('broker_purchases.invoice_id', $invoice_id_array)  // ✅ fixed (was single ->where)
            ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftJoin('grades',  'grades.id',  '=', 'broker_purchases.grade')
            ->select(
                'broker_purchases.*',
                'gardens.garden_name as garden_name',
                'grades.grade        as grade'
            )
            ->get();

        if ($usedInvoices->isEmpty()) {
            return $this->successresponse(500, 'message', 'No brokerage data found for selected invoices.');
        }

        // ── Tax calculation ──────────────────────────────────────────────────────────
        $brokrage    = $request->line_total * $request->brokerage / 100;
        $totalAmount = $brokrage;

        $company_state_id      = $mainCompanyData->state_id;
        $garden_company_state_id = $gardenCompanyData->state_id;

        $igst = $cgst = $sgst = 0;

        if ($company_state_id === $garden_company_state_id) {
            $cgst = $totalAmount * 9 / 100;
            $sgst = $totalAmount * 9 / 100;
            $igst = 0;
        } else {
            $igst = $totalAmount * 18 / 100;
            $cgst = 0;
            $sgst = 0;
        }

        $grandTotal = round($totalAmount + $igst + $cgst + $sgst);

        $company_id_for_bill  = $mainCompanyData->company_id;
        $garden_company_id    = $gardenCompanyData->id;

        // ── Financial year ───────────────────────────────────────────────────────────
        $today = Carbon::now();

        if ($today->month >= 4) {
            $fyStart = $today->format('y');
            $fyEnd   = $today->copy()->addYear()->format('y');
        } else {
            $fyStart = $today->copy()->subYear()->format('y');
            $fyEnd   = $today->format('y');
        }
        $financialYear = $fyStart . '-' . $fyEnd;

        // ── Create the brokerbillinvoice record ──────────────────────────────────────
        $billRecord = $this->brokerbillinvoiceModel::create([
            'garden_id'          => 0,
            'company_id'         => $company_id_for_bill,
            'garden_company_id'  => $garden_company_id,
            'totalamount'        => $totalAmount,
            'igst'               => $igst,
            'sgst'               => $sgst,
            'cgst'               => $cgst,
            'grand_total'        => $grandTotal,
            'status'             => 'pending',
            'invoice_date'       => $today->format('Y-m-d'),
            'created_by'         => $request->user_id,
            'from_date'          => Carbon::yesterday()->format('Y-m-d'),
            'to_date'            => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        // Set invoice_no after we have the ID
        $this->brokerbillinvoiceModel::where('id', $billRecord->id)->update([
            'invoice_no' => "KMD/{$financialYear}/{$billRecord->id}",
        ]);

        $brokrage_per = $request->brokerage;
        // dd($billRecord->id);
        // ── FIX 3: Update brokerbill_no on ALL matching invoice_ids ─────────────────
        //    Old code looped over $linedata and used $invoice->invoice_id one by one.
        //    For bulk this is fine but unnecessarily slow. Use a single whereIn update.
        $this->brokerpurchaseModel
            ::whereIn('invoice_id', $invoice_id_array)
            ->update([
                'brokerbill_no'  => $billRecord->id,
                'brokerage'      => $brokrage_per,
                'brokerage_date' => $today->format('Y-m-d'),
            ]);

        return $this->successresponse(200, 'message', 'Commission Bill PDF successfully created.');
    }
    public function brokeragebillpdfdelete(Request $request, $id)
    {
        // Get broker bill invoice
        $company_brokrage = $this->brokerbillinvoiceModel::find($id);

        // Get company details
        $garden_company_details = $this->companymasterModel
            ::where('id', $company_brokrage->garden_company_id)
            ->first();

        if ($garden_company_details) {

            // Update broker purchase
            $this->brokerpurchaseModel
                ::where('brokerbill_no', $id)
                ->update([
                    "brokerbill_no" => null,
                    "brokerage" => $garden_company_details->brokerage
                ]);
        }


        $this->brokerbillinvoiceModel
            ::where('id', $id)
            ->update([
                'is_deleted' => 1
            ]);

        return $this->successresponse(200, 'message', 'Commission Bill Invoice succesfully deleted');
    }
    public function getpanddingpayment($id)
    {
        if ($this->rp['teamodule']['brokeragebill']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $payment = $this->brokerbillinvoiceModel::find($id);

        if ($this->rp['teamodule']['brokeragebill']['alldata'] != 1) {
            if ($payment->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$payment) {
            return $this->successresponse(500, 'message', 'payment not found !');
        }
        return $this->successresponse(200, 'payment', $payment);
    }

    // this not invoice create but bill not created thisin invoice 

   public function brokerbillinvoicelist(Request $request)
    {
        if ($this->rp['teamodule']['brokerpurchase']['view'] != 1 ||
            $this->rp['invoicemodule']['invoice']['view'] != 1) {

            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
            ]);
        }

        $lineTotalSub = DB::connection('dynamic_connection')
            ->table('mng_col')
            ->select(
                'invoice_id',
                DB::raw("ROUND(SUM(amount), 2) as line_total")
            )
            ->where('is_deleted', 0)
            ->groupBy('invoice_id');

        $brokerSub = DB::connection('dynamic_connection')
            ->table('broker_purchases')
            ->select(
                'invoice_id',
                'garden_id'
            )
            ->where('is_deleted', 0)
            ->whereNull('brokerbill_no')
            ->groupBy('invoice_id', 'garden_id');

        $invoiceres = $this->invoiceModel
            ::joinSub($brokerSub, 'broker_totals', function ($join) {
                $join->on('broker_totals.invoice_id', '=', 'invoices.id');
            })
            ->leftJoinSub($lineTotalSub, 'mc_totals', function ($join) {
                $join->on('mc_totals.invoice_id', '=', 'invoices.id');
            })
            ->select(
                'broker_totals.garden_id',
                'invoices.id as invoice_id',
                'invoices.inv_no as invoice_number',
                'invoices.company_details_id',
                'mc_totals.line_total',
                'companymasters.brokerage',
                'companymasters.company_name',
            DB::raw('ROUND(mc_totals.line_total * companymasters.brokerage / 100, 2) as brokerageAmount')
            )
            ->leftJoin('companymasters', 'invoices.company_details_id', '=', 'companymasters.id')
            ->where('invoices.is_deleted', 0)
            ->orderBy('invoices.inv_date', 'desc');

        if ($this->rp['invoicemodule']['invoice']['alldata'] != 1) {
            $invoiceres->where('invoices.created_by', $this->userId);
        }

        $invoice = $invoiceres->get();

        if ($invoice->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No Data Found',
                'data' => [],
            ]);
        }
        return response()->json([
            'status' => 200,
            'data' => $invoice,
        ]);
    }
}
