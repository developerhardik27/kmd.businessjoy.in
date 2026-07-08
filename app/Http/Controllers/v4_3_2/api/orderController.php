<?php

namespace App\Http\Controllers\v4_3_2\api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\v4_3_2\api\commonController;

class orderController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $orderModel, $order_detailModel ,$brokerPurchaseModel, $invoiceModel, $mngcolModel, $invoice_other_settingModel, $payment_detailsModel;

    public function __construct(Request $request)
    {

        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();
        $this->orderModel = $this->getmodel('order');
        $this->order_detailModel = $this->getmodel('order_detail');
        $this->brokerPurchaseModel = $this->getmodel('broker_purchase');
        $this->invoiceModel = $this->getmodel('invoice');
        $this->mngcolModel = $this->getmodel('mng_col');
        $this->invoice_other_settingModel = $this->getmodel('invoice_other_setting');
        $this->payment_detailsModel = $this->getmodel('payment_details');
    }
    public function index(Request $request)
    {
        // Check user permissions
        if ($this->rp['teamodule']['order']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        // Base query
        $order = $this->orderModel::leftJoin('partys as buyer', 'buyer.id', 'orders.buyer_party')
            ->leftJoin('partys as transport', 'transport.id', 'orders.transport')
            ->leftJoin('partys as reference', 'reference.id', 'orders.reference')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('gardens', 'gardens.id', 'order_details.garden_id')
            ->leftJoin('broker_purchases', function ($join) {
                $join->on('broker_purchases.order_detail_id', '=', 'order_details.id')
                    ->where('broker_purchases.is_deleted', 0);
            })
            ->leftJoin('grades', 'grades.id', 'order_details.grade')
            ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'order_details.garden_id')
            ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->where('orders.is_deleted', 0);

        // -------------------------------------------------------
        // Blank filters — show records where column is NULL/empty
        // -------------------------------------------------------
        if (($request->filter_buyer ?? null) === 'blank_buyer') {
            $order->where(function ($q) {
                $q->whereNull('orders.buyer_party')
                ->orWhere('orders.buyer_party', '');
            });
        }

        if (($request->filter_company ?? null) === 'blank_company') {
            $order->where(function ($q) {
                $q->whereNull('company_garden.company_id')
                ->orWhere('company_garden.company_id', '');
            });
        }

        // -------------------------------------------------------
        // Filters mapping
        // -------------------------------------------------------
        $filters = [
            'filter_transport'                   => 'orders.transport',
            'filter_buyer'                        => 'orders.buyer_party',
            'filter_reference'                    => 'orders.reference',
            'filter_expected_dispatch_date_from'  => 'orders.expected_dispatch_date',
            'filter_expected_dispatch_date_to'    => 'orders.expected_dispatch_date',
            'filter_dispatch_status'              => 'orders.dispatch_status',
            'filter_garden'                       => 'order_details.garden_id',
            'filter_grade'                        => 'order_details.grade',
            'filter_credit_days_from'             => 'orders.credit_days',
            'filter_credit_days_to'               => 'orders.credit_days',
            'filter_final_amount_from'            => 'orders.finalAmount',
            'filter_final_amount_to'              => 'orders.finalAmount',
            'filter_date_from'                    => 'orders.created_at',
            'filter_date_to'                      => 'orders.created_at',
        ];

        // Apply filters
        foreach ($filters as $requestKey => $column) {
            $value = $request->$requestKey ?? null;

            // Skip: null, empty string, 0/"0", or blank_* sentinel values
            if ($value === null || $value === '' || $value == 0 || str_starts_with((string) $value, 'blank_')) {
                continue;
            }

            if (in_array($requestKey, [
                'filter_credit_days_from',
                'filter_credit_days_to',
                'filter_final_amount_from',
                'filter_final_amount_to',
                'filter_expected_dispatch_date_from',
                'filter_expected_dispatch_date_to',
            ])) {
                $operator = str_contains($requestKey, 'from') ? '>=' : '<=';
                $order->where($column, $operator, $value);

            } elseif (in_array($requestKey, ['filter_date_from', 'filter_date_to'])) {
                $operator = str_contains($requestKey, 'from') ? '>=' : '<=';
                $order->whereDate($column, $operator, $value);

            } else {
                $order->whereIn($column, (array) $value);
            }
        }

        // -------------------------------------------------------
        // Company filter (normal — non-blank)
        // -------------------------------------------------------
        if (
            !empty($request->filter_company) &&
            $request->filter_company !== '' &&
            $request->filter_company != 0 &&
            $request->filter_company !== 'blank_company'
        ) {
            $companyIds = (array) $request->filter_company;

            $order->whereExists(function ($query) use ($companyIds) {
                $query->select(DB::raw(1))
                    ->from('order_details as od_sub')
                    ->join('company_garden as cg_sub', 'cg_sub.garden_id', '=', 'od_sub.garden_id')
                    ->whereColumn('od_sub.order_id', 'orders.id')
                    ->whereIn('cg_sub.company_id', $companyIds)
                    ->limit(1);
            });
        }

        // -------------------------------------------------------
        // Fetch & group data
        // -------------------------------------------------------
        $orderData = $order
            ->select(
                'orders.id as order_id',
                'buyer.name as buyer_name',
                'transport.name as transport_name',
                'reference.name as reference_name',
                'orders.*',
                DB::raw("DATE_FORMAT(orders.order_date, '%d-%m-%Y') as order_date"),
                'order_details.*',
                'gardens.garden_name as garden_name',
                'grades.grade as grade_name',
                'companymasters.id as company_id',
                'companymasters.company_name as company_name',
                'broker_purchases.id as broker_purchase_id',
                'broker_purchases.source as broker_purchase_source',
                'broker_purchases.brokerbill_no as brokerbill_no',
            )
            ->get()
            ->groupBy('order_id')
            ->map(function ($details, $orderId) {
                $first = $details->first();

                // Determine invoice status
                $invoiceIds = $details->pluck('invoice_id');
                if ($invoiceIds->every(fn($id) => empty($id))) {
                    $invoiceStatus = 'Pending';
                } elseif ($invoiceIds->contains(fn($id) => empty($id))) {
                    $invoiceStatus = 'Half Invoice';
                } else {
                    $invoiceStatus = 'Invoices Created';
                }

                // Determine sample status
                $sampleIds = $details->pluck('broker_purchase_id');
                $sampleSources = $details->pluck('broker_purchase_source');
                    if($sampleSources->contains('invoice')){
                        $sampleStatus = 'Pending';
                    }
                    else{
                        if ($sampleIds->every(fn($id) => empty($id))) {
                            $sampleStatus = 'Pending';
                        } elseif ($sampleIds->contains(fn($id) => empty($id))) {
                            $sampleStatus = 'Half Sample';
                        } else {
                            $sampleStatus = 'Sample Created';
                        }
                    }
                return [
                    'id'                     => $orderId,
                    'buyer_name'             => $first->buyer_name,
                    'transport_name'         => $first->transport_name,
                    'reference_name'         => $first->reference_name,
                    'expected_dispatch_date' => $first->expected_dispatch_date,
                    'dispatch_status'        => $first->dispatch_status,
                    'discount'               => $first->discount,
                    'totalNetKg'             => $first->totalNetKg,
                    'credit_days'            => $first->credit_days,
                    'final_amount'           => $first->finalAmount,
                    'order_date'             => $first->order_date,
                    'invoice_status'         => $invoiceStatus,
                    'sample_status'          => $sampleStatus,
                    'brokerbill_no'          => $first->brokerbill_no,
                    'company_names'          => $details
                        ->map(fn($item) => $item->company_name ?? '-')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'garden_names'           => $details
                        ->filter(fn($item) => !empty($item->garden_name))
                        ->pluck('garden_name', 'garden_id')
                        ->values()
                        ->implode(', '),
                    'invoice_nos'            => $details
                        ->filter(fn($item) => !empty($item->invoice_no))
                        ->pluck('invoice_no')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'grades'                 => $details
                        ->filter(fn($item) => !empty($item->grade_name))
                        ->pluck('grade_name')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'rate'                   => $details
                        ->filter(fn($item) => !empty($item->grade_name))
                        ->pluck('rate')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'details'                => $details->map(function ($item) {
                        return [
                            'garden_name'  => $item->garden_name,
                            'grade_name'   => $item->grade_name,
                            'invoice_no'   => $item->invoice_no,
                            'bags'         => $item->bags,
                            'kg'           => $item->kg,
                            'net_kg'       => $item->net_kg,
                            'rate'         => $item->rate,
                            'amount'       => $item->amount,
                            'company_name' => $item->company_name ?? null,
                        ];
                    })->toArray(),
                ];
            })
            ->values();

        // -------------------------------------------------------
        // Post-group filters (invoice_status, sample_status)
        // -------------------------------------------------------
        if (!empty($request->filter_invoice_status) && $request->filter_invoice_status !== '') {
            $status    = $request->filter_invoice_status;
            $orderData = $orderData->filter(fn($order) => $order['invoice_status'] === $status)->values();
        }

        if (!empty($request->filter_sample_status) && $request->filter_sample_status !== '') {
            $status    = $request->filter_sample_status;
            $orderData = $orderData->filter(fn($order) => $order['sample_status'] === $status)->values();
        }

        // -------------------------------------------------------
        // Return via DataTables
        // -------------------------------------------------------
        if ($orderData->isEmpty()) {
            return DataTables::of($orderData)
                ->with([
                    'status'  => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }

        return DataTables::of($orderData)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }
    public function store(Request $request)
    {
        // dd($request->all());
        if ($this->rp['teamodule']['order']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'buyer_party'    => 'nullable|integer',
            'transport'      => 'nullable|integer',
            'credit_days'    => 'required|string|in:CD,15,30,45,60,90',
            'discount'       => 'nullable|numeric|min:0|max:100',
            'order_date'     => 'required|date',
            'totalNetKg'     => 'required|numeric|min:0',
            'totalAmount'    => 'required|numeric|min:0',
            'discountAmount' => 'nullable|numeric|min:0',
            'finalAmount'    => 'required|numeric|min:0',
        ]);

        $errors = [];
        $invoiceNumbers = [];
        $invoiceGardenMap = [];
        foreach ($request->rows as $index => $row) {

            if (empty($row['garden_id'])) {
                $errors["rows.$index.garden_id"] = ['Select at least one garden.'];
            }
            if (empty($row['invoice_no'])) {
            $errors["rows.$index.invoice_no"] = ['The invoice number is required.'];
            } else {

                $key = $row['garden_id'] . '_' . $row['invoice_no'];

                // Check duplicate in same request (garden-wise)
                if (in_array($key, $invoiceGardenMap)) {
                    $errors["rows.$index.invoice_no"] = ['Duplicate invoice number for same garden in request.'];
                } else {
                    $invoiceGardenMap[] = $key;
                }

                // Check duplicate in DB (garden-wise)
                $exists = $this->order_detailModel
                    ::where('garden_id', $row['garden_id'])
                    ->where('invoice_no', $row['invoice_no'])
                    ->exists();

                if ($exists) {
                    $errors["rows.$index.invoice_no"] = ['This invoice number already exists for the selected garden.'];
                }
            }
            if (!isset($row['bags']) || $row['bags'] < 0) {
                $errors["rows.$index.bags"] = ['Enter Bags cannot be negative!'];
            }
            if (!isset($row['kg']) || $row['kg'] < 0) {
                $errors["rows.$index.kg"] = ['Enter kg cannot be negative!'];
            }
            if (!isset($row['net_kg']) || $row['net_kg'] < 0) {
                $errors["rows.$index.net_kg"] = ['Net kg cannot be negative!'];
            }
            if (!isset($row['rate']) || $row['rate'] < 0) {
                $errors["rows.$index.rate"] = ['Enter rate cannot be negative!'];
            }
            if (!isset($row['amount']) || $row['amount'] < 0) {
                $errors["rows.$index.amount"] = ['Amount cannot be negative!'];
            }
        }
        if ($validator->fails() || !empty($errors)) {
            $validationErrors = $validator->errors()->toArray();
            $allErrors = array_merge($validationErrors, $errors);
            return $this->errorresponse(422, $allErrors);
        }

        $create = $this->orderModel::create([
            'buyer_party' => $request->buyer_party,
            'transport' => $request->transport,
            'reference' => $request->reference,
            'expected_dispatch_date' => $request->expected_dispatch_date,
            'credit_days' => $request->credit_days,
            'discount' => $request->discount ?? 0,
            'order_date' => $request->order_date,
            'totalNetKg' => $request->totalNetKg,
            'totalAmount' => $request->totalAmount,
            'discountAmount' => $request->discountAmount,
            'finalAmount' => $request->finalAmount,
            'created_by' => $request->user_id,
        ]);
        foreach ($request->rows as $row) {
            $this->order_detailModel::create([
                'order_id'   => $create->id,
                'garden_id'  => $row['garden_id'],
                'invoice_no' => $row['invoice_no'],
                'grade'      => $row['grade'] ?? null,
                'bags'       => $row['bags'],
                'kg'         => $row['kg'],
                'net_kg'     => $row['net_kg'],
                'rate'       => $row['rate'],
                'amount'     => $row['amount'],
                'created_by' => $request->user_id,
            ]);
        }
        if ($create) {
            return $this->successresponse(200, 'message', 'order succesfully create');
        } else {
            return $this->successresponse(500, 'message', 'order not succesfully added !');
        }
    }
    public function edit($id)
    {
        if ($this->rp['teamodule']['order']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $order = $this->orderModel::find($id);
        if ($this->rp['teamodule']['order']['alldata'] != 1) {
            if ($order->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $order_details = $this->order_detailModel::where('order_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        // ✅ Check if order details have associated sample purchases or invoices
        $hasSamplePurchase = false;
        $hasInvoice = false;
        $warningMessage = '';

        foreach ($order_details as $detail) {
            // Check for sample purchases
            $samplePurchase = $this->brokerPurchaseModel::where('order_detail_id', $detail->id)
                ->where('is_deleted', 0)
                ->exists();
            if ($samplePurchase) {
                $hasSamplePurchase = true;
            }

            // Check for invoices
            if ($detail->invoice_id) {
                $hasInvoice = true;
            }
        }

        // Build warning message
        if ($hasSamplePurchase || $hasInvoice) {
            $messages = [];
            if ($hasSamplePurchase) {
                $messages[] = 'sample purchases';
            }
            if ($hasInvoice) {
                $messages[] = 'invoices';
            }
            $warningMessage = 'This order has associated ' . implode(' and ', $messages) . '. Updating this order will also update the bag details and any other related information. If an invoice has already been created, the invoice will also be updated with these changes.';
        }

        $order = [
            'order' => $order,
            'order_details' => $order_details,
            'has_sample_purchase' => $hasSamplePurchase,
            'has_invoice' => $hasInvoice,
            'warning_message' => $warningMessage
        ];
        return $this->successresponse(200, 'orders', $order);
    }
    public function update(Request $request, $id)
    {
        if ($this->rp['teamodule']['order']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'buyer_party'    => 'nullable|integer',
            'transport'      => 'nullable|integer',
            'credit_days'    => 'required|string|in:CD,15,30,45,60,90',
            'discount'       => 'nullable|numeric|min:0|max:100',
            'order_date'     => 'required|date',
            'totalNetKg'     => 'required|numeric|min:0',
            'totalAmount'    => 'required|numeric|min:0',
            'discountAmount' => 'nullable|numeric|min:0',
            'finalAmount'    => 'required|numeric|min:0',
        ]);

        $errors = [];
        $invoiceGardenMap = [];

        foreach ($request->rows as $index => $row) {

            if (empty($row['garden_id'])) {
                $errors["rows.$index.garden_id"] = ['Select at least one garden.'];
            }

            if (empty($row['invoice_no'])) {
                $errors["rows.$index.invoice_no"] = ['The invoice number is required.'];
            } else {

                $gardenId = $row['garden_id'];
                $key = $gardenId . '_' . $row['invoice_no'];

                if (in_array($key, $invoiceGardenMap)) {
                    $errors["rows.$index.invoice_no"] = ['Duplicate invoice number for same garden in request.'];
                } else {
                    $invoiceGardenMap[] = $key;
                }

                $exists = $this->order_detailModel
                    ::where('garden_id', $gardenId)
                    ->where('invoice_no', $row['invoice_no'])
                    ->where('order_id', '!=', $id)
                    ->exists();

                if ($exists) {
                    $errors["rows.$index.invoice_no"] = ['This invoice number already exists for the selected garden.'];
                }
            }

            if (!isset($row['bags']) || $row['bags'] < 0) {
                $errors["rows.$index.bags"] = ['Enter Bags cannot be negative!'];
            }
            if (!isset($row['kg']) || $row['kg'] < 0) {
                $errors["rows.$index.kg"] = ['Enter kg cannot be negative!'];
            }
            if (!isset($row['net_kg']) || $row['net_kg'] < 0) {
                $errors["rows.$index.net_kg"] = ['Net kg cannot be negative!'];
            }
            if (!isset($row['rate']) || $row['rate'] < 0) {
                $errors["rows.$index.rate"] = ['Enter rate cannot be negative!'];
            }
            if (!isset($row['amount']) || $row['amount'] < 0) {
                $errors["rows.$index.amount"] = ['Amount cannot be negative!'];
            }
        }

        if ($validator->fails() || !empty($errors)) {
            $validationErrors = $validator->errors()->toArray();
            $allErrors = array_merge($validationErrors, $errors);
            return $this->errorresponse(422, $allErrors);
        }

        $order = $this->orderModel::find($id);

        if (!$order) {
            return $this->successresponse(500, 'message', 'Order not found!');
        }

        if ($this->rp['teamodule']['order']['alldata'] != 1) {
            if ($order->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        // ✅ Update order
        $order->update([
            'buyer_party'    => $request->buyer_party,
            'transport'      => $request->transport,
            'reference'      => $request->reference,
            'expected_dispatch_date' => $request->expected_dispatch_date,
            'credit_days'    => $request->credit_days,
            'discount'       => $request->discount ?? 0,
            'totalNetKg'     => $request->totalNetKg,
            'order_date'     => $request->order_date,
            'totalAmount'    => $request->totalAmount,
            'discountAmount' => $request->discountAmount,
            'finalAmount'    => $request->finalAmount,
            'updated_by'     => $request->user_id,
        ]);

        $existingIds = [];

        foreach ($request->rows as $row) {

            // ✅ UPDATE
            if (!empty($row['order_detail_id'])) {

                $detail = $this->order_detailModel::where('id', $row['order_detail_id'])
                    ->where('order_id', $id)
                    ->first();

                if ($detail) {

                    $detail->update([
                        'garden_id'  => $row['garden_id'],
                        'invoice_no' => $row['invoice_no'],
                        'grade'      => $row['grade'] ?? null,
                        'bags'       => $row['bags'],
                        'kg'         => $row['kg'],
                        'net_kg'     => $row['net_kg'],
                        'rate'       => $row['rate'],
                        'amount'     => $row['amount'],
                    ]);

                    $existingIds[] = $row['order_detail_id'];

                    // ✅ Broker Purchase UPDATE
                    $this->brokerPurchaseModel::where('order_detail_id', $row['order_detail_id'])->update([
                        'garden_id'  => $row['garden_id'],
                        'invoice_no' => $row['invoice_no'],
                        'bags'       => $row['bags'],
                        'net_kg'     => $row['net_kg'],
                        'rate'       => $row['rate'],
                    ]);

                    // ✅ Update mng_col if order_detail_id exists
                    $kgPerBag = $row['bags'] > 0 ? $row['net_kg'] / $row['bags'] : 0;
                    $mngCol = $this->mngcolModel::where('order_detail_id', $row['order_detail_id'])->first();
                    if ($mngCol) {
                        $shortage = $mngCol->shortage ?? 0;
                        $mngCol->update([
                            'No_Of_Pkags'      => $row['bags'],
                            'Rate_per_kg'      => $row['rate'],
                            'Net_Weight_Kgs'   => $row['net_kg'],
                            'Net_Oty_Per_Pkg'  => $kgPerBag,
                            'shortage'         => $shortage,
                            'discount'         => 0,
                            'amount'           => $row['amount']
                        ]);

                        // ✅ Update broker purchase with shortage and invoice_grand_total
                        $this->brokerPurchaseModel::where('order_detail_id', $row['order_detail_id'])->update([
                            'shortage'            => $shortage,
                            'final_net_kg'        => $row['net_kg'] - $shortage,
                            'invoice_grand_total' => $row['amount'],
                        ]);
                    }

                    // ✅ Update invoice if order_detail has invoice_id
                    if ($detail->invoice_id) {
                        $invoice = $this->invoiceModel::where('id', $detail->invoice_id)->first();
                        if ($invoice) {
                            // Recalculate invoice totals from all order_details for this invoice
                            $orderDetailsForInvoice = $this->order_detailModel::where('invoice_id', $detail->invoice_id)->get();

                            $totalAmount = 0;
                            foreach ($orderDetailsForInvoice as $od) {
                                $totalAmount += ($od->net_kg * $od->rate);
                            }

                            // Calculate GST amounts using invoice_other_setting percentages
                            if($invoice->sgst == 0.00 && $invoice->cgst == 0.00){
                                // Inter-state - use IGST percentage
                                $igst_per = $this->invoice_other_settingModel::value('igst') ?? 0;
                                $igst = ($totalAmount * $igst_per) / 100;
                                $sgst = 0;
                                $cgst = 0;
                            } else {
                                // Intra-state - use CGST and SGST percentages
                                $cgst_per = $this->invoice_other_settingModel::value('cgst') ?? 0;
                                $sgst_per = $this->invoice_other_settingModel::value('sgst') ?? 0;
                                $cgst = ($totalAmount * $cgst_per) / 100;
                                $sgst = ($totalAmount * $sgst_per) / 100;
                                $igst = 0;
                            }

                            $gst = $sgst + $cgst + $igst;
                            $grandTotal = $totalAmount + $gst;

                            $invoice->update([
                                'total'       => $totalAmount,
                                'sgst'        => $sgst,
                                'cgst'        => $cgst,
                                'igst'        => $igst,
                                'gst'         => $gst,
                                'grand_total' => $grandTotal
                            ]);

                            // ✅ Update brokerpurchase table with invoice_grand_total
                            $this->brokerPurchaseModel::where('invoice_no', $invoice->invoice_no)
                                ->where('is_deleted', 0)
                                ->update(['invoice_grand_total' => $grandTotal]);

                            // ✅ Handle payment status and payment_details when invoice total changes
                            $paymentDetails = $this->payment_detailsModel::where('inv_id', $invoice->id)
                                ->where('is_deleted', 0)
                                ->get();

                            if ($paymentDetails && count($paymentDetails) > 0) {
                                $totalPaidAmount = 0;
                                foreach ($paymentDetails as $pay) {
                                    $totalPaidAmount += ($pay->paid_amount + $pay->tds_amount);
                                }

                                // Calculate new pending amount
                                $newPendingAmount = $grandTotal - $totalPaidAmount;

                                // Update payment_details with new amounts
                                foreach ($paymentDetails as $pay) {
                                    $pay->amount = $grandTotal;
                                    $pay->pending_amount = $newPendingAmount;
                                    $pay->part_payment = ($newPendingAmount > 0) ? 1 : 0;
                                    $pay->updated_by = $request->user_id;
                                    $pay->save();
                                }

                                // Update invoice status based on payment
                                $newInvoiceStatus = ($newPendingAmount <= 0) ? 'paid' : 'part_payment';
                                $invoice->status = ($invoice->status != 'pending') ? $newInvoiceStatus : 'pending';
                                $invoice->save();
                            }
                        }
                    }
                }

            } else {

                $newDetail = $this->order_detailModel::create([
                    'order_id'   => $id,
                    'garden_id'  => $row['garden_id'],
                    'invoice_no' => $row['invoice_no'],
                    'grade'      => $row['grade'] ?? null,
                    'bags'       => $row['bags'],
                    'kg'        => $row['kg'],
                    'net_kg'     => $row['net_kg'],
                    'rate'       => $row['rate'],
                    'amount'     => $row['amount'],
                ]);

                $existingIds[] = $newDetail->id;

                // ✅ Update mng_col if order_detail_id exists
                $kgPerBag = $row['bags'] > 0 ? $row['net_kg'] / $row['bags'] : 0;
                $mngCol = $this->mngcolModel::where('order_detail_id', $newDetail->id)->first();
                if ($mngCol) {
                    $shortage = $mngCol->shortage ?? 0;
                    $mngCol->update([
                        'No_Of_Pkags'      => $row['bags'],
                        'Rate_per_kg'      => $row['rate'],
                        'Net_Weight_Kgs'   => $row['net_kg'],
                        'Net_Oty_Per_Pkg'  => $kgPerBag,
                        'shortage'         => $shortage,
                        'discount'         => 0,
                        'amount'           => $row['amount']
                    ]);

                    // ✅ Update broker purchase with shortage and invoice_grand_total
                    $this->brokerPurchaseModel::where('order_detail_id', $newDetail->id)->update([
                        'shortage'            => $shortage,
                        'final_net_kg'        => $row['net_kg'] - $shortage,
                        'invoice_grand_total' => $row['amount'],
                    ]);
                }

                // ✅ Update invoice if order_detail has invoice_id
                if ($newDetail->invoice_id) {
                    $invoice = $this->invoiceModel::where('id', $newDetail->invoice_id)->first();
                    if ($invoice) {
                        // Recalculate invoice totals from all order_details for this invoice
                        $orderDetailsForInvoice = $this->order_detailModel::where('invoice_id', $newDetail->invoice_id)->get();

                        $totalAmount = 0;
                        foreach ($orderDetailsForInvoice as $od) {
                            $totalAmount += ($od->net_kg * $od->rate);
                        }

                        // Calculate GST amounts using invoice_other_setting percentages
                        if($invoice->sgst == 0.00 && $invoice->cgst == 0.00){
                            // Inter-state - use IGST percentage
                            $igst_per = $this->invoice_other_settingModel::value('igst') ?? 0;
                            $igst = ($totalAmount * $igst_per) / 100;
                            $sgst = 0;
                            $cgst = 0;
                        } else {
                            // Intra-state - use CGST and SGST percentages
                            $cgst_per = $this->invoice_other_settingModel::value('cgst') ?? 0;
                            $sgst_per = $this->invoice_other_settingModel::value('sgst') ?? 0;
                            $cgst = ($totalAmount * $cgst_per) / 100;
                            $sgst = ($totalAmount * $sgst_per) / 100;
                            $igst = 0;
                        }

                        $gst = $sgst + $cgst + $igst;
                        $grandTotal = $totalAmount + $gst;

                        $invoice->update([
                            'total'       => $totalAmount,
                            'sgst'        => $sgst,
                            'cgst'        => $cgst,
                            'igst'        => $igst,
                            'gst'         => $gst,
                            'grand_total' => $grandTotal
                        ]);

                        // ✅ Update brokerpurchase table with invoice_grand_total
                        $this->brokerPurchaseModel::where('invoice_no', $invoice->invoice_no)
                            ->where('is_deleted', 0)
                            ->update(['invoice_grand_total' => $grandTotal]);

                        // ✅ Handle payment status and payment_details when invoice total changes
                        $paymentDetails = $this->payment_detailsModel::where('inv_id', $invoice->id)
                            ->where('is_deleted', 0)
                            ->get();

                        if ($paymentDetails && count($paymentDetails) > 0) {
                            $totalPaidAmount = 0;
                            foreach ($paymentDetails as $pay) {
                                $totalPaidAmount += ($pay->paid_amount + $pay->tds_amount);
                            }

                            // Calculate new pending amount
                            $newPendingAmount = $grandTotal - $totalPaidAmount;

                            // Update payment_details with new amounts
                            foreach ($paymentDetails as $pay) {
                                $pay->amount = $grandTotal;
                                $pay->pending_amount = $newPendingAmount;
                                $pay->part_payment = ($newPendingAmount > 0) ? 1 : 0;
                                $pay->updated_by = $request->user_id;
                                $pay->save();
                            }

                            // Update invoice status based on payment
                            $newInvoiceStatus = ($newPendingAmount <= 0) ? 'paid' : 'part_payment';
                            $invoice->status = ($invoice->status != 'pending') ? $newInvoiceStatus : 'pending';
                            $invoice->save();
                        }
                    }
                }
            }
        }

        // ✅ DELETE removed rows
        $this->order_detailModel::where('order_id', $id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        // $this->brokerPurchaseModel::whereNotIn('order_detail_id', $existingIds)
        //     ->delete();

        return $this->successresponse(200, 'message', 'Order successfully updated');
    }
    public function destroy($id)
    {
        if ($this->rp['teamodule']['order']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $order = $this->orderModel::find($id);
        if ($this->rp['teamodule']['order']['alldata'] != 1) {
            if ($order->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$order) {
            return $this->successresponse(500, 'message', 'order not found !');
        }
        $order->update(
            [
                "is_deleted" => 1
            ]
        );
        $this->order_detailModel::where('order_id', $id)->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'order succesfully deleted');
    }
    public function  totalorder()
    {
        if ($this->rp['invoicemodule']['invoicedashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $order = $this->orderModel::where('is_deleted', 0);

        if ($this->rp['invoicemodule']['invoicedashboard']['alldata'] != 1) {
            $order->where('created_by', $this->userId);
        }

        $order = $order->count();

        return $this->successresponse(200, 'order', $order);
    }
    public function orderChart(Request $request)
    {
        $month = $request->input('month');

        $query = $this->orderModel::where('is_deleted', 0)
            ->whereYear('order_date', now()->year); // ✅ Only current year

        if ($month && strtolower($month) !== 'all') {
            // Specific month of current year
            $data = $query
                ->select(
                    DB::raw('MONTH(order_date) as month'),
                    DB::raw('COUNT(id) as total_orders'),
                    DB::raw('SUM(totalNetKg) as total_kg'),
                    DB::raw('SUM(finalAmount) as total_amount')
                )
                ->whereMonth('order_date', $month)
                ->groupBy(DB::raw('MONTH(order_date)'))
                ->orderBy('month', 'ASC')
                ->get();
        } else {
            // All months of current year
            $data = $query
                ->select(
                    DB::raw('MONTH(order_date) as month'),
                    DB::raw('COUNT(id) as total_orders'),
                    DB::raw('SUM(totalNetKg) as total_kg'),
                    DB::raw('SUM(finalAmount) as total_amount')
                )
                ->groupBy(DB::raw('MONTH(order_date)'))
                ->orderBy('month', 'ASC')
                ->get();
        }
        return response()->json($data);
    }

    public function getGardensByOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        // Check if order exists
        $orderExists = $this->orderModel::where('id', $request->order_id)->exists();
        if (!$orderExists) {
            return $this->errorresponse(404, 'Order not found');
        }

        $gardens = $this->order_detailModel::where('order_id', $request->order_id)
            ->leftJoin('gardens', 'gardens.id', 'order_details.garden_id')
            ->select('order_details.garden_id', 'gardens.garden_name')
            ->distinct()
            ->get();

        $data = [];
        foreach ($gardens as $garden) {
            if ($garden->garden_id) {
                $data[] = [
                    'garden_id' => $garden->garden_id,
                    'garden_name' => $garden->garden_name ?? 'Unknown Garden'
                ];
            }
        }

        if (empty($data)) {
            return $this->errorresponse(404, 'No gardens found for this order. The order may not have any order details or garden IDs.');
        }

        return $this->successresponse(200, 'Gardens retrieved successfully', $data);
    }

    public function getOrderDetailsForInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
            'filter_company_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        // Check if order exists
        $order = $this->orderModel::where('id', $request->order_id)->first();
        if (!$order) {
            return $this->errorresponse(404, 'Order not found');
        }

        // Get order details with garden and company information
        $query = $this->order_detailModel::where('order_id', $request->order_id)
            ->leftJoin('gardens', 'gardens.id', 'order_details.garden_id')
            ->leftJoin('company_garden', 'company_garden.garden_id', 'gardens.id')
            ->leftJoin('companymasters', 'companymasters.id', 'company_garden.company_id')
            ->select(
                'order_details.id as order_detail_id',
                'order_details.garden_id',
                'order_details.invoice_no',
                'companymasters.id as company_id',
                'companymasters.company_name'
            )
            ->where('order_details.invoice_id', '=', null)
            ->where('order_details.is_deleted', 0);

        // Filter by filter_company_id if provided (this is for filtering, not authentication)
        if ($request->has('filter_company_id') && !empty($request->filter_company_id)) {
            $query->where('companymasters.id', $request->filter_company_id);
        }

        $orderDetails = $query->get();

        if ($orderDetails->isEmpty()) {
            return $this->errorresponse(404, 'No order details found for this order');
        }

        // If filter_company_id is provided, return filtered data directly
        if ($request->has('filter_company_id') && !empty($request->filter_company_id)) {
            // Check if buyer_id exists
            if (empty($order->buyer_party)) {
                return $this->errorresponse(400, 'Buyer party is required. Please select a buyer for this order before creating an invoice.');
            }

            $gardenIds = $orderDetails->pluck('garden_id')->unique()->filter()->values();
            $invoiceNos = $orderDetails->pluck('invoice_no')->unique()->filter()->values();
            $orderDetailIds = $orderDetails->pluck('order_detail_id')->values();

            // Check if company has valid data
            if ($gardenIds->isEmpty() && $invoiceNos->isEmpty()) {
                return $this->errorresponse(404, 'No valid order details found for the selected company.');
            }

            return $this->successresponse(200, 'Order details retrieved successfully', [
                'has_multiple_companies' => false,
                'company_id' => $request->filter_company_id,
                'buyer_id' => $order->buyer_party,
                'garden_ids' => $gardenIds,
                'invoice_nos' => $invoiceNos,
                'order_detail_ids' => $orderDetailIds
            ]);
        }

        // Get unique companies from order details
        $companies = $orderDetails->pluck('company_id')->unique()->filter()->values();
        if ($companies->isEmpty()) {
            return $this->errorresponse(400, 'The selected garden is not assigned to any company. Please assign a company to the garden first.');
        }
        // If only one company, return data directly
        if ($companies->count() === 1) {
            $companyId = $companies->first();
            $companyDetails = $orderDetails->where('company_id', $companyId);

            $gardenIds = $companyDetails->pluck('garden_id')->unique()->filter()->values();
            $invoiceNos = $companyDetails->pluck('invoice_no')->unique()->filter()->values();
            $orderDetailIds = $companyDetails->pluck('order_detail_id')->values();
            if($order->buyer_party == null){
                return $this->errorresponse(400, 'Buyer party is required. Please select a buyer for this order before creating an invoice.');
            }
            return $this->successresponse(200, 'Order details retrieved successfully', [
                'has_multiple_companies' => false,
                'company_id' => $companyId,
                'buyer_id' => $order->buyer_party,
                'garden_ids' => $gardenIds,
                'invoice_nos' => $invoiceNos,
                'order_detail_ids' => $orderDetailIds
            ]);
        }

        // Multiple companies - return company options
        $companyOptions = [];
        foreach ($companies as $companyId) {
            $companyDetails = $orderDetails->where('company_id', $companyId)->first();
            $companyOptions[] = [
                'company_id' => $companyId,
                'company_name' => $companyDetails->company_name ?? 'Unknown Company'
            ];
        }

        return $this->successresponse(200, 'Multiple companies found', [
            'has_multiple_companies' => true,
            'companies' => $companyOptions,
            'buyer_id' => $order->buyer_party
        ]);
    }

    public function expectedDispatchReportData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filter_expected_dispatch_date_from' => 'nullable|date',
            'filter_expected_dispatch_date_to' => 'nullable|date',
            'filter_dispatch_status' => 'nullable|in:Pending,Completed',
            'filter_company' => 'nullable|integer',
            'filter_garden' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        // Base query - matching order list pattern
        $order = $this->orderModel::leftJoin('partys as buyer', 'buyer.id', 'orders.buyer_party')
            ->leftJoin('partys as reference', 'reference.id', 'orders.reference')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('gardens', 'gardens.id', 'order_details.garden_id')
            ->leftJoin('broker_purchases', function ($join) {
                $join->on('broker_purchases.order_detail_id', '=', 'order_details.id')
                    ->where('broker_purchases.is_deleted', 0);
            })
            ->leftJoin('grades', 'grades.id', 'order_details.grade')
            ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'order_details.garden_id')
            ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->where('orders.is_deleted', 0);

        // Apply filters
        if ($request->filter_expected_dispatch_date_from) {
            $order->where('orders.expected_dispatch_date', '>=', $request->filter_expected_dispatch_date_from);
        }
        if ($request->filter_expected_dispatch_date_to) {
            $order->where('orders.expected_dispatch_date', '<=', $request->filter_expected_dispatch_date_to);
        }
        if ($request->filter_dispatch_status) {
            $order->where('orders.dispatch_status', $request->filter_dispatch_status);
        }
        if ($request->filter_garden) {
            $order->where('order_details.garden_id', $request->filter_garden);
        }

        // Company filter using whereExists
        if (!empty($request->filter_company) && $request->filter_company !== '') {
            $companyIds = (array) $request->filter_company;
            $order->whereExists(function ($query) use ($companyIds) {
                $query->select(DB::raw(1))
                    ->from('order_details as od_sub')
                    ->join('company_garden as cg_sub', 'cg_sub.garden_id', '=', 'od_sub.garden_id')
                    ->whereColumn('od_sub.order_id', 'orders.id')
                    ->whereIn('cg_sub.company_id', $companyIds)
                    ->limit(1);
            });
        }

        // Fetch data
        $orderData = $order
            ->select(
                'orders.id as order_id',
                'buyer.name as buyer_name',
                'reference.name as reference_name',
                'orders.*',
                DB::raw("DATE_FORMAT(orders.order_date, '%d-%m-%Y') as order_date"),
                'order_details.*',
                'gardens.garden_name as garden_name',
                'grades.grade as grade_name',
                'companymasters.id as company_id',
                'companymasters.id as company_master_id',
                'companymasters.company_name as company_name',
                'companymasters.email as company_email',
                'broker_purchases.id as broker_purchase_id',
                'broker_purchases.source as broker_purchase_source',
            )
            ->get()
            ->groupBy('order_id')
            ->map(function ($details, $orderId) {
                $first = $details->first();

                // Determine invoice status
                $invoiceIds = $details->pluck('invoice_id');
                if ($invoiceIds->every(fn($id) => empty($id))) {
                    $invoiceStatus = 'Pending';
                } elseif ($invoiceIds->contains(fn($id) => empty($id))) {
                    $invoiceStatus = 'Half Invoice';
                } else {
                    $invoiceStatus = 'Invoices Created';
                }

                // Determine sample status
                $sampleIds = $details->pluck('broker_purchase_id');
                $sampleSources = $details->pluck('broker_purchase_source');
                    if($sampleSources->contains('invoice')){
                        $sampleStatus = 'Pending';
                    }
                    else{
                        if ($sampleIds->every(fn($id) => empty($id))) {
                            $sampleStatus = 'Pending';
                        } elseif ($sampleIds->contains(fn($id) => empty($id))) {
                            $sampleStatus = 'Half Sample';
                        } else {
                            $sampleStatus = 'Sample Created';
                        }
                    }

                return [
                    'id' => $orderId,
                    'order_date' => $first->order_date,
                    'buyer_name' => $first->buyer_name,
                    'reference_name' => $first->reference_name,
                    'company_names' => $details
                        ->map(fn($item) => $item->company_name ?? '  -  ')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'garden_names' => $details
                        ->filter(fn($item) => !empty($item->garden_name))
                        ->pluck('garden_name', 'garden_id')
                        ->values()
                        ->implode(', '),
                    'invoice_nos' => $details
                        ->filter(fn($item) => !empty($item->invoice_no))
                        ->pluck('invoice_no')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'grades' => $details
                        ->filter(fn($item) => !empty($item->grade_name))
                        ->pluck('grade_name')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'invoice_status' => $invoiceStatus,
                    'sample_status' => $sampleStatus,
                    'net_kg' => $details->sum('net_kg'),
                    'rate' => $first->rate,
                    'amount' => $details->sum('amount'),
                    'credit_days' => $first->credit_days,
                    'dispatch_status' => $first->dispatch_status,
                    'expected_dispatch_date' => $first->expected_dispatch_date ? date('d-m-Y', strtotime($first->expected_dispatch_date)) : '',
                ];
            })
            ->values();

        // Return via DataTables
        if ($orderData->isEmpty()) {
            return DataTables::of($orderData)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }

        return DataTables::of($orderData)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }

    public function updateDispatchStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dispatch_status' => 'required|in:Pending,Completed',
            'expected_dispatch_date' => 'required_if:dispatch_status,Completed|date|nullable'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        $order = $this->orderModel::where('id', $request->order_id)
            ->where('is_deleted', 0)
            ->first();

        if (!$order) {
            return $this->errorresponse(404, 'Order not found');
        }

        $updateData = [
            'dispatch_status' => $request->dispatch_status,
            'updated_by' => $this->userId
        ];

        // Only update expected_dispatch_date if status is Completed
        if ($request->dispatch_status === 'Completed') {
            $updateData['expected_dispatch_date'] = $request->expected_dispatch_date;
        }

        $order->update($updateData);

        return $this->successresponse(200,'message', 'Dispatch status updated successfully');
    }

    public function pendingInvoiceReportData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filter_order_date_from' => 'nullable|date',
            'filter_order_date_to' => 'nullable|date',
            'filter_invoice_status' => 'nullable|in:Pending,Half Invoice,Invoices Created',
            'filter_company' => 'nullable|integer',
            'filter_buyer' => 'nullable|integer',
            'filter_sample_date_from' => 'nullable|date',
            'filter_sample_date_to' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        // Base query - matching order list pattern
        $order = $this->orderModel::leftJoin('partys as buyer', 'buyer.id', 'orders.buyer_party')
            ->leftJoin('partys as reference', 'reference.id', 'orders.reference')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('gardens', 'gardens.id', 'order_details.garden_id')
            ->leftJoin('broker_purchases', function ($join) {
                $join->on('broker_purchases.order_detail_id', '=', 'order_details.id')
                    ->where('broker_purchases.is_deleted', 0);
            })
            ->leftJoin('grades', 'grades.id', 'order_details.grade')
            ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'order_details.garden_id')
            ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->where('orders.is_deleted', 0);

        // Apply filters
        if ($request->filter_order_date_from) {
            $order->whereDate('orders.order_date', '>=', $request->filter_order_date_from);
        }
        if ($request->filter_order_date_to) {
            $order->whereDate('orders.order_date', '<=', $request->filter_order_date_to);
        }
        if ($request->filter_buyer) {
            $order->where('orders.buyer_party', $request->filter_buyer);
        }
        if ($request->filter_sample_date_from) {
            $order->where('broker_purchases.sample_purchase_date', '>=', $request->filter_sample_date_from);
        }
        if ($request->filter_sample_date_to) {
            $order->where('broker_purchases.sample_purchase_date', '<=', $request->filter_sample_date_to);
        }

        // Company filter using whereExists
        if (!empty($request->filter_company) && $request->filter_company !== '') {
            $companyIds = (array) $request->filter_company;
            $order->whereExists(function ($query) use ($companyIds) {
                $query->select(DB::raw(1))
                    ->from('order_details as od_sub')
                    ->join('company_garden as cg_sub', 'cg_sub.garden_id', '=', 'od_sub.garden_id')
                    ->whereColumn('od_sub.order_id', 'orders.id')
                    ->whereIn('cg_sub.company_id', $companyIds)
                    ->limit(1);
            });
        }

        // Fetch data
        $orderData = $order
            ->select(
                'orders.id as order_id',
                'buyer.name as buyer_name',
                'reference.name as reference_name',
                'orders.*',
                DB::raw("DATE_FORMAT(orders.order_date, '%d-%m-%Y') as order_date"),
                'order_details.*',
                'gardens.garden_name as garden_name',
                'grades.grade as grade_name',
                'companymasters.id as company_id',
                'companymasters.company_name as company_name',
                'broker_purchases.id as broker_purchase_id',
                'broker_purchases.source as broker_purchase_source',
            )
            ->get()
            ->groupBy('order_id')
            ->map(function ($details, $orderId) use ($request) {
                $first = $details->first();

                // Determine invoice status based on broker_purchase_id
                $invoiceIds = $details->pluck('invoice_id');
                if ($invoiceIds->every(fn($id) => empty($id))) {
                    $invoiceStatus = 'Pending';
                } elseif ($invoiceIds->contains(fn($id) => empty($id))) {
                    $invoiceStatus = 'Half Invoice';
                } else {
                    $invoiceStatus = 'Invoices Created';
                }

                // Apply invoice status filter if provided
                if ($request->filter_invoice_status && $invoiceStatus !== $request->filter_invoice_status) {
                    return null; // Skip this order if it doesn't match the filter
                }

                // Determine sample status
                $sampleIds = $details->pluck('broker_purchase_id');
                $sampleSources = $details->pluck('broker_purchase_source');
                    if($sampleSources->contains('invoice')){
                        $sampleStatus = 'Pending';
                    }
                    else{
                        if ($sampleIds->every(fn($id) => empty($id))) {
                            $sampleStatus = 'Pending';
                        } elseif ($sampleIds->contains(fn($id) => empty($id))) {
                            $sampleStatus = 'Half Sample';
                        } else {
                            $sampleStatus = 'Sample Created';
                        }
                    }

                return [
                    'id' => $orderId,
                    'order_date' => $first->order_date,
                    'buyer_name' => $first->buyer_name,
                    'reference_name' => $first->reference_name,
                    'company_names' => $details
                        ->map(fn($item) => $item->company_name ?? '  -  ')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'garden_names' => $details
                        ->filter(fn($item) => !empty($item->garden_name))
                        ->pluck('garden_name', 'garden_id')
                        ->values()
                        ->implode(', '),
                    'invoice_nos' => $details
                        ->filter(fn($item) => !empty($item->invoice_no))
                        ->pluck('invoice_no')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'grades' => $details
                        ->filter(fn($item) => !empty($item->grade_name))
                        ->pluck('grade_name')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'invoice_status' => $invoiceStatus,
                    'sample_status' => $sampleStatus,
                    'net_kg' => $details->sum('net_kg'),
                    'rate' => $first->rate,
                    'amount' => $details->sum('amount'),
                    'credit_days' => $first->credit_days,
                    'dispatch_status' => $first->dispatch_status,
                    'expected_dispatch_date' => $first->expected_dispatch_date ? date('d-m-Y', strtotime($first->expected_dispatch_date)) : '',
                ];
            })
            ->filter() // Remove null values from invoice status filter
            ->values();

        // Return via DataTables
        if ($orderData->isEmpty()) {
            return DataTables::of($orderData)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }

        return DataTables::of($orderData)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }

    public function pendingSamplePurchaseReportData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filter_order_date_from' => 'nullable|date',
            'filter_order_date_to' => 'nullable|date',
            'filter_sample_status' => 'nullable|in:Pending,Half Sample,Sample Created',
            'filter_company' => 'nullable|integer',
            'filter_buyer' => 'nullable|integer',
            'filter_sample_date_from' => 'nullable|date',
            'filter_sample_date_to' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        // Base query - matching order list pattern
        $order = $this->orderModel::leftJoin('partys as buyer', 'buyer.id', 'orders.buyer_party')
            ->leftJoin('partys as reference', 'reference.id', 'orders.reference')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('gardens', 'gardens.id', 'order_details.garden_id')
            ->leftJoin('broker_purchases', function ($join) {
                $join->on('broker_purchases.order_detail_id', '=', 'order_details.id')
                    ->where('broker_purchases.is_deleted', 0);
            })
            ->leftJoin('grades', 'grades.id', 'order_details.grade')
            ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'order_details.garden_id')
            ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->where('orders.is_deleted', 0);

        // Apply filters
        if ($request->filter_order_date_from) {
            $order->whereDate('orders.order_date', '>=', $request->filter_order_date_from);
        }
        if ($request->filter_order_date_to) {
            $order->whereDate('orders.order_date', '<=', $request->filter_order_date_to);
        }
        if ($request->filter_buyer) {
            $order->where('orders.buyer_party', $request->filter_buyer);
        }
        if ($request->filter_sample_date_from) {
            $order->where('broker_purchases.sample_purchase_date', '>=', $request->filter_sample_date_from);
        }
        if ($request->filter_sample_date_to) {
            $order->where('broker_purchases.sample_purchase_date', '<=', $request->filter_sample_date_to);
        }

        // Company filter using whereExists
        if (!empty($request->filter_company) && $request->filter_company !== '') {
            $companyIds = (array) $request->filter_company;
            $order->whereExists(function ($query) use ($companyIds) {
                $query->select(DB::raw(1))
                    ->from('order_details as od_sub')
                    ->join('company_garden as cg_sub', 'cg_sub.garden_id', '=', 'od_sub.garden_id')
                    ->whereColumn('od_sub.order_id', 'orders.id')
                    ->whereIn('cg_sub.company_id', $companyIds)
                    ->limit(1);
            });
        }

        // Fetch data
        $orderData = $order
            ->select(
                'orders.id as order_id',
                'buyer.name as buyer_name',
                'reference.name as reference_name',
                'orders.*',
                DB::raw("DATE_FORMAT(orders.order_date, '%d-%m-%Y') as order_date"),
                'order_details.*',
                'gardens.garden_name as garden_name',
                'grades.grade as grade_name',
                'companymasters.id as company_id',
                'companymasters.company_name as company_name',
                'broker_purchases.id as broker_purchase_id',
                'broker_purchases.source as broker_purchase_source',
            )
            ->get()
            ->groupBy('order_id')
            ->map(function ($details, $orderId) use ($request) {
                $first = $details->first();

                // Determine invoice status based on broker_purchase_id
                $brokerPurchaseIds = $details->pluck('broker_purchase_id');
                if ($brokerPurchaseIds->every(fn($id) => empty($id))) {
                    $invoiceStatus = 'Pending';
                } elseif ($brokerPurchaseIds->contains(fn($id) => empty($id))) {
                    $invoiceStatus = 'Half Invoice';
                } else {
                    $invoiceStatus = 'Invoices Created';
                }

                // Determine sample status
                $sampleIds = $details->pluck('broker_purchase_id');
                $sampleSources = $details->pluck('broker_purchase_source');
                    if($sampleSources->contains('invoice')){
                        $sampleStatus = 'Pending';
                    }
                    else{
                        if ($sampleIds->every(fn($id) => empty($id))) {
                            $sampleStatus = 'Pending';
                        } elseif ($sampleIds->contains(fn($id) => empty($id))) {
                            $sampleStatus = 'Half Sample';
                        } else {
                            $sampleStatus = 'Sample Created';
                        }
                    }

                // Apply sample status filter if provided
                if ($request->filter_sample_status && $sampleStatus !== $request->filter_sample_status) {
                    return null; // Skip this order if it doesn't match the filter
                }

                return [
                    'id' => $orderId,
                    'order_date' => $first->order_date,
                    'buyer_name' => $first->buyer_name,
                    'reference_name' => $first->reference_name,
                    'company_names' => $details
                        ->map(fn($item) => $item->company_name ?? '  -  ')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'garden_names' => $details
                        ->filter(fn($item) => !empty($item->garden_name))
                        ->pluck('garden_name', 'garden_id')
                        ->values()
                        ->implode(', '),
                    'invoice_nos' => $details
                        ->filter(fn($item) => !empty($item->invoice_no))
                        ->pluck('invoice_no')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'grades' => $details
                        ->filter(fn($item) => !empty($item->grade_name))
                        ->pluck('grade_name')
                        ->unique()
                        ->values()
                        ->implode(', '),
                    'invoice_status' => $invoiceStatus,
                    'sample_status' => $sampleStatus,
                    'net_kg' => $details->sum('net_kg'),
                    'rate' => $first->rate,
                    'amount' => $details->sum('amount'),
                    'credit_days' => $first->credit_days,
                    'dispatch_status' => $first->dispatch_status,
                    'expected_dispatch_date' => $first->expected_dispatch_date ? date('d-m-Y', strtotime($first->expected_dispatch_date)) : '',
                ];
            })
            ->filter() // Remove null values from sample status filter
            ->values();

        // Return via DataTables
        if ($orderData->isEmpty()) {
            return DataTables::of($orderData)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }

        return DataTables::of($orderData)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }

    public function turnoverReportData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filter_order_date_from' => 'nullable|date',
            'filter_order_date_to' => 'nullable|date',
            'filter_company' => 'nullable|integer',
            'filter_buyer' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        // Base query - group by company and buyer, sum net_kg
        $query = $this->orderModel::leftJoin('partys as buyer', 'buyer.id', 'orders.buyer_party')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('gardens', 'gardens.id', 'order_details.garden_id')
            ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'order_details.garden_id')
            ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->where('orders.is_deleted', 0);

        // Apply filters
        if ($request->filter_order_date_from) {
            $query->whereDate('orders.order_date', '>=', $request->filter_order_date_from);
        }
        if ($request->filter_order_date_to) {
            $query->whereDate('orders.order_date', '<=', $request->filter_order_date_to);
        }
        if ($request->filter_company) {
            $query->where('companymasters.id', $request->filter_company);
        }
        if ($request->filter_buyer) {
            $query->where('orders.buyer_party', $request->filter_buyer);
        }

        // Group by company and buyer, sum net_kg
        $turnoverData = $query->select(
                'companymasters.company_name',
                'buyer.name as buyer_name',
                DB::raw('SUM(order_details.net_kg) as total_net_kg')
            )
            ->groupBy('companymasters.company_name', 'buyer.name')
            ->get()
            ->map(function ($item) {
                return [
                    'company_name' => $item->company_name ?? '-',
                    'buyer_name' => $item->buyer_name ?? '-',
                    'total_net_kg' => $item->total_net_kg ?? 0,
                ];
            });

        // Return via DataTables
        if ($turnoverData->isEmpty()) {
            return DataTables::of($turnoverData)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }

        return DataTables::of($turnoverData)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }
}
