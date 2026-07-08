<?php

namespace App\Http\Controllers\v4_3_2\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class brokerPurchaseController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $brokerpurchaseModel, $order_detailModel, $gradenModel, $orderModel, $companygardenModel, $companymasterMode,$invoice_other_settingModel,$mngcolModel,$invoiceModel,$payment_detailsModel;

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

        $this->brokerpurchaseModel = $this->getmodel('broker_purchase');
        $this->orderModel = $this->getmodel('order');
        $this->order_detailModel = $this->getmodel('order_detail');
        $this->gradenModel = $this->getmodel('graden');
        $this->companymasterModel = $this->getmodel('companymaster');
        $this->companygardenModel = $this->getmodel('company_garden');
        $this->invoice_other_settingModel = $this->getmodel('invoice_other_setting');
        $this->mngcolModel = $this->getmodel('mng_col');
        $this->invoiceModel = $this->getmodel('invoice');
        $this->payment_detailsModel = $this->getmodel('payment_details');
    }
    public function getGardens()
    {
        if ($this->rp['teamodule']['brokerpurchase']['view'] != 1) {
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
        // dd($gardens);
        return $this->successresponse(200, 'data', $gardens);
    }
    public function getupdateInvoices(Request $request)
    {
        if ($this->rp['teamodule']['brokerpurchase']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $invoices = $this->order_detailModel
            ::where('is_deleted', 0)
            ->where('garden_id', $request->garden_id)
            ->select('invoice_no')
            ->distinct()
            ->orderBy('invoice_no', 'ASC')
            ->get();

        return $this->successresponse(200, 'data', $invoices);
    }
    public function checkInvoice(Request $request)
    {
        $id = $request->id;

        $exists = $this->brokerpurchaseModel::where('invoice_no', $request->invoice_no)->where('id', '!=', $id)->exists();

        if ($exists) {
            return $this->errorresponse(422,  ['invoice_no' => ['This Invoice number  already purchase created ']]);
        }

        return $this->successresponse(200, 'invoice_no', $request->invoice_no, 'message', 'Invoice number available',);
    }

    public function getInvoices(Request $request)
    {
        if ($this->rp['teamodule']['brokerpurchase']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $usedInvoices = $this->brokerpurchaseModel
        ::where('is_deleted', 0)
        ->where('garden_id', $request->garden_id)
        ->where('source', 'purchase')
        ->pluck('invoice_no')
        ->toArray();

        $query = $this->order_detailModel
            ::where('is_deleted', 0)
            ->where('garden_id', $request->garden_id)
            ->whereNotIn('invoice_no', $usedInvoices);

        // Filter by order_id if provided in request
        if ($request->has('order_id') && !empty($request->order_id)) {
            $query->where('order_id', $request->order_id);
        }

        $allInvoices = $query->orderBy('invoice_no', 'ASC')->get();
        // dd($allInvoices);
        return $this->successresponse(200, 'data', $allInvoices);
    }
    public function getorderInvoices(Request $request)
    {
        if ($this->rp['teamodule']['brokerpurchase']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        // Get used invoice_nos from broker_purchases
        $usedInvoices = $this->brokerpurchaseModel
            ::where('is_deleted', 0)
            ->where('garden_id', $request->garden_id)
            ->where('source', 'purchase')
            ->pluck('invoice_no')
            ->toArray();

        // Get used order_detail_ids from broker_purchases
        $usedOrderDetailIds = $this->brokerpurchaseModel
            ::where('is_deleted', 0)
            ->where('garden_id', $request->garden_id)
            ->where('source', 'purchase')
            ->pluck('order_detail_id')
            ->toArray();

        $query = $this->order_detailModel
            ::where('is_deleted', 0)
            ->where('garden_id', $request->garden_id)
            ->whereNotIn('invoice_no', $usedInvoices)
            ->whereNotIn('id', $usedOrderDetailIds);  // exclude already used order_detail ids

        // Filter by order_id if provided
        if ($request->has('order_id') && !empty($request->order_id)) {
            $query->where('order_id', $request->order_id);
        }

        $allInvoices = $query->orderBy('invoice_no', 'ASC')->get();

        return $this->successresponse(200, 'data', $allInvoices);
    }
    public function getOtherDetails(Request $request)
    {
        if ($this->rp['teamodule']['brokerpurchase']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $invoiceNos = is_array($request->invoice_nos)
            ? $request->invoice_nos
            : explode(',', $request->invoice_nos);

        $order = $this->order_detailModel
            ::join('grades', 'grades.id', '=', 'order_details.grade')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')         // join orders to get buyer_party
            ->leftJoin('partys', 'partys.id', '=', 'orders.buyer_party')             // join partys to get buyer name
            ->where('order_details.is_deleted', 0)
            ->whereIn('order_details.invoice_no', $invoiceNos)
            ->where('order_details.garden_id', $request->garden_id)
            ->select(
                'order_details.id as order_detail_id',
                'order_details.order_id',
                'order_details.garden_id',
                'order_details.invoice_no',
                'order_details.bags',
                'order_details.net_kg',
                'order_details.rate',
                'grades.id as grade_id',
                'grades.grade as grade_name',
                'orders.id as order_id',
                'orders.buyer_party as buyer_party_id',          // buyer_party (nullable)
                'partys.name as buyer_name',                     // null if buyer_party is null
            )
            ->orderBy('grades.grade', 'ASC')
            ->get();

        // `get()` always returns a Collection, never null — check isEmpty() instead
        if ($order->isEmpty()) {
            return $this->successresponse(404, 'message', 'Order details not found');
        }

        return $this->successresponse(200, 'data', $order);
    }
    public function createInvoice(Request $request)
    {
        if ($this->rp['teamodule']['brokerpurchase']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $data = $request->all();
        
        $buyerParties = explode(',', $request->buyer_parties);
        $companyIds = explode(',', $request->company_ids);
        $sampleIds = explode(',', $request->sampleIds);
        $orderDetailIds = explode(',', $request->orderDetailIds);

        $data1 = $this->brokerpurchaseModel
            ::join('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->join('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')

            ->join('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
            ->join('companymasters', 'companymasters.id', '=', 'company_garden.company_id')

            ->join('order_details', function ($join) {
                $join->on('order_details.garden_id', '=', 'broker_purchases.garden_id')
                    ->on('order_details.id', '=', 'broker_purchases.order_detail_id');
            })
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('partys as buyer', 'buyer.id', '=', 'orders.buyer_party')
            ->join('partys as transporter', 'transporter.id', '=', 'orders.transport')

            ->select(
                'broker_purchases.*',
                'order_details.id as order_detail_id',
                'order_details.bags as No_Of_Pkags',
                'order_details.invoice_no as Invoice_no',
                'order_details.net_kg as Net_Weight_Kgs',
                'order_details.rate as Rate_per_kg',
                'order_details.kg as Net_Oty_Per_Pkg',
                'grades.grade as Grade',
                'gardens.garden_name as Garden',
                'companymasters.company_name',
                'companymasters.id  as companymaster_id',
                'orders.id as order_id',
                'orders.discount',
                'orders.buyer_party as buyer_id',
                'orders.transport as transport_id',
                'buyer.name as buyer_name',
                'transporter.name as transport_name'
            )
            ->whereIn('broker_purchases.id', $sampleIds)
            ->where('broker_purchases.is_deleted', 0)
            ->whereIn('companymasters.id', $companyIds)
            ->whereIn('orders.buyer_party', $buyerParties)
            ->get();

        $maindata = [
            'maindata' => [
                'companymaster_id' => $companyIds,
                'buyer_id' => $buyerParties,
                'sampleIds' => $sampleIds,
                'orderDetailIds' => $orderDetailIds,
            ],
            "line_items" => $data1,
        ];


        // Continue your invoice creation here
        return $this->successresponse(200, 'message', 'invoice created data you get properly', 'data', $maindata);
    }
    public function lot_no_createInvoice(Request $request)
    {

        if ($this->rp['teamodule']['brokerpurchase']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $buyerParties = $request->buyer_parties;
        $companyIds   = $request->company_ids;
        $invoice_no   = $request->invoice_no;
        $orderDetailIds = $request->order_detail_ids;



        if (is_string($invoice_no)) {
            $invoice_no = explode(',', $invoice_no);
        }
        $invoice_no = (array) $invoice_no;


        if (is_string($companyIds)) {
            $companyIds = explode(',', $companyIds);
        }
        $companyIds = (array) $companyIds;


        if (is_string($buyerParties)) {
            $buyerParties = explode(',', $buyerParties);
        }
        $buyerParties = (array) $buyerParties;
        if (is_string($orderDetailIds)) {
            $orderDetailIds = explode(',', $orderDetailIds);
        }
        $orderDetailIds = (array) $orderDetailIds;


        $data1 = $this->order_detailModel
            ::join('grades', 'grades.id', '=', 'order_details.grade')
            ->join('gardens', 'gardens.id', '=', 'order_details.garden_id')
            ->join('company_garden', 'company_garden.garden_id', '=', 'order_details.garden_id')
            ->join('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('partys as buyer', 'buyer.id', '=', 'orders.buyer_party')
            ->leftJoin('partys as transporter', 'transporter.id', '=', 'orders.transport')

            ->select(
                'order_details.id as order_detail_id',
                'order_details.bags as No_Of_Pkags',
                'order_details.invoice_no as Invoice_no',
                'order_details.net_kg as Net_Weight_Kgs',
                'order_details.rate as Rate_per_kg',
                'order_details.kg as Net_Oty_Per_Pkg',
                'grades.grade as Grade',
                'gardens.garden_name as Garden',
                'companymasters.company_name',
                'companymasters.id as companymaster_id',
                'orders.id as order_id',
                'orders.discount',
                'orders.buyer_party as buyer_id',
                'orders.transport as transport_id',
                'buyer.name as buyer_name',
                'transporter.name as transport_name'
            )

            ->whereIn('order_details.id', $orderDetailIds)
            ->where('order_details.is_deleted', 0)
            ->get();

        $maindata = [
            'maindata' => [
                'companymaster_id' => $companyIds,
                'buyer_id'         => $buyerParties,
                'invoice_no'       => $invoice_no,
                'orderDetailIds' => $orderDetailIds,
            ],
            'line_items' => $data1
        ];


        return $this->successresponse(
            200,
            'message',
            'Invoice created data fetched successfully',
            'data',
            $maindata
        );
    }
    public function index(Request $request)
    {
        if ($this->rp['teamodule']['brokerpurchase']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        // $brokerpurchase = $this->brokerpurchaseModel::where('is_deleted', 0)->get();
        // if ($brokerpurchase->isEmpty()) {
        //     return $this->successresponse(404, 'message', 'No Data Found');
        // }
        $brokerpurchase = $this->brokerpurchaseModel
            ::leftJoin('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->leftJoin('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->leftJoin('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
            ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->leftJoin('order_details', function ($join) {
                $join->on('order_details.garden_id', '=', 'broker_purchases.garden_id')
                    ->on('order_details.id', '=', 'broker_purchases.order_detail_id');
            })
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('partys as buyer', 'buyer.id', '=', 'orders.buyer_party')
            ->leftJoin('partys as transporter', 'transporter.id', '=', 'orders.transport')
            ->where('broker_purchases.source', 'purchase')
            ->where('broker_purchases.is_deleted', 0);
        $filters = [
            'filter_company'      => 'companymasters.id',
            'filter_buyer'        => 'orders.buyer_party',
            'filter_garden'       => 'broker_purchases.garden_id',
            'filter_grade'        => 'broker_purchases.grade',
            'filter_net_kg_from'  => 'broker_purchases.net_kg',
            'filter_net_kg_to'    => 'broker_purchases.net_kg',
            'filter_bags_from'    => 'broker_purchases.bags',
            'filter_bags_to'      => 'broker_purchases.bags',
            'filter_from_date'    => 'broker_purchases.created_at',
            'filter_to_date'      => 'broker_purchases.created_at',
        ];

        foreach ($filters as $requestKey => $column) {
            $value = $request->$requestKey;

            if (isset($value)) {
                if ($requestKey == 'filter_net_kg_from' || $requestKey == 'filter_net_kg_to' || $requestKey == 'filter_bags_from' || $requestKey == 'filter_bags_to') {
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $brokerpurchase->where($column, $operator, $value);
                } else if (strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false) {
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $brokerpurchase->whereDate($column, $operator, $value);
                } else {

                    $brokerpurchase->whereIn($column, $value);
                }
            }
        }

        $brokerpurchase = $brokerpurchase
            ->select(

                'broker_purchases.*',
                'grades.grade as grade_name',
                'gardens.garden_name as garden_name',
                'companymasters.company_name',
                'companymasters.id as company_id',
                'orders.id as order_id',
                'orders.buyer_party',
                'orders.transport',
                'buyer.name as buyer_name',
                'transporter.name as transport_name'
            )
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
    public function list_sample(Request $request)
    {
        if ($this->rp['teamodule']['brokerpurchase']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        // $brokerpurchase = $this->brokerpurchaseModel::where('is_deleted', 0)->get();
        // if ($brokerpurchase->isEmpty()) {
        //     return $this->successresponse(404, 'message', 'No Data Found');
        // }
        $brokerpurchase = $this->brokerpurchaseModel
            ::join('grades', 'grades.id', '=', 'broker_purchases.grade')
            ->join('gardens', 'gardens.id', '=', 'broker_purchases.garden_id')
            ->join('company_garden', 'company_garden.garden_id', '=', 'broker_purchases.garden_id')
            ->join('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->join('order_details', function ($join) {
                $join->on('order_details.garden_id', '=', 'broker_purchases.garden_id')
                    ->on('order_details.invoice_no', '=', 'broker_purchases.invoice_no');
            })
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('partys as buyer', 'buyer.id', '=', 'orders.buyer_party')
            ->join('partys as transporter', 'transporter.id', '=', 'orders.transport')
            ->where('broker_purchases.is_deleted', 0);

        $filters = [
            'filter_company'      => 'companymasters.id',
            'filter_buyer'        => 'orders.buyer_party',
            'filter_garden'       => 'broker_purchases.garden_id',
            'filter_grade'        => 'broker_purchases.grade',
            'filter_net_kg_from'  => 'broker_purchases.net_kg',
            'filter_net_kg_to'    => 'broker_purchases.net_kg',
            'filter_bags_from'    => 'broker_purchases.bags',
            'filter_bags_to'      => 'broker_purchases.bags',
            'filter_from_date'    => 'broker_purchases.created_at',
            'filter_to_date'      => 'broker_purchases.created_at',
        ];

        foreach ($filters as $requestKey => $column) {
            $value = $request->$requestKey;

            if (isset($value)) {
                if ($requestKey == 'filter_net_kg_from' || $requestKey == 'filter_net_kg_to' || $requestKey == 'filter_bags_from' || $requestKey == 'filter_bags_to') {
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $brokerpurchase->where($column, $operator, $value);
                } else if (strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false) {
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $brokerpurchase->whereDate($column, $operator, $value);
                } else {

                    $brokerpurchase->whereIn($column, $value);
                }
            }
        }

        $brokerpurchase = $brokerpurchase
            ->select(
                'broker_purchases.*',
                'grades.grade as grade_name',
                'gardens.garden_name as garden_name',
                'companymasters.company_name',
                'companymasters.id as company_id',
                'orders.id as order_id',
                'orders.buyer_party',
                'orders.transport',
                'buyer.name as buyer_name',
                'transporter.name as transport_name'
            )
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
        // Check permission
        if ($this->rp['teamodule']['brokerpurchase']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $details = $request->details;
        if (!$details || !is_array($details) || count($details) === 0) {
            return $this->errorresponse(422, ['details' => 'No invoice details provided']);
        }

        // Get company brokerage
        $company_id_from_garden = $this->companygardenModel::where('garden_id', $request->garden_id)->value('company_id');
        $brokerage = $this->companymasterModel::where('id', $company_id_from_garden)->value('brokerage');

        $successCount = 0;
        $errors = [];

        foreach ($details as $index => $detail) {

            // Validate each invoice detail
            $validator = Validator::make($detail, [
                'invoice_no'      => 'required|string|max:255',
                'grade_name'      => 'required|string|max:255',
                'bags'            => 'required|numeric',
                'net_kg'          => 'required|numeric',
                'rate'            => 'nullable|numeric',
                'order_detail_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $errors[$index] = $validator->messages();
                continue;
            }

            // ── Check if this order_detail_id + invoice_no already exists ──
            $existing = $this->brokerpurchaseModel::where('order_detail_id', $detail['order_detail_id'])
                ->where('invoice_no', $detail['invoice_no'])
                ->where('is_deleted', 0)
                ->first();

            if ($existing) {
                // ── UPDATE existing record ──
                $updated = $existing->update([
                    'sample_purchase_date' => $request->sample_purchase_date,
                    'source'     => 'purchase',
                    'updated_by' => $request->user_id,
                    'bags'            => $detail['bags'],
                    'net_kg'          => $detail['net_kg'],
                    'rate'            => $detail['rate'] ?? null,
                    'final_net_kg' => $detail['net_kg'] - $existing['shortage'],
                ]);

                if ($updated) {
                    $successCount++;
                } else {
                    $errors[$index] = ['Failed to update invoice ' . $detail['invoice_no']];
                }

            } else {
                // ── CREATE new record ──
                $create = $this->brokerpurchaseModel::create([
                    'garden_id'       => $request->garden_id,
                    'sample_purchase_date' => $request->sample_purchase_date,
                    'invoice_no'      => $detail['invoice_no'],
                    'grade'           => $detail['garde'],   // fix: was $detail['garde'] (typo)
                    'bags'            => $detail['bags'],
                    'net_kg'          => $detail['net_kg'],
                    'rate'            => $detail['rate'] ?? null,
                    'order_detail_id' => $detail['order_detail_id'],
                    'created_by'      => $request->user_id,
                    'brokerage'       => $brokerage,
                    'source'          => 'purchase',
                ]);

                if ($create) {
                    $successCount++;
                } else {
                    $errors[$index] = ['Failed to save invoice ' . $detail['invoice_no']];
                }
            }

            // ── Update order_details ──
            $orderDetail = $this->order_detailModel::where('id', $detail['order_detail_id'])->first();
            if ($orderDetail) {
                $orderId = $orderDetail->order_id;
                $kgPerBag = $detail['bags'] > 0 ? $detail['net_kg'] / $detail['bags'] : 0;
                $calculatedAmount = $detail['net_kg'] * $detail['rate'];

                $orderDetail->update([
                    'bags'   => $detail['bags'],
                    'net_kg' => $detail['net_kg'],
                    'rate'   => $detail['rate'],
                    'amount' => $calculatedAmount,
                    'kg'     => $kgPerBag
                ]);

                // ── Update mng_col if order_detail_id exists ──
                $mngCol = $this->mngcolModel::where('order_detail_id', $detail['order_detail_id'])->first();
                if ($mngCol) {
                    $mngCol->update([
                        'No_Of_Pkags'      => $detail['bags'],
                        'Rate_per_kg'      => $detail['rate'],
                        'Net_Weight_Kgs'   => $detail['net_kg'],
                        'Net_Oty_Per_Pkg'  => $kgPerBag,
                        'discount'         => $detail['discount'] ?? 0,
                        'amount'           => $calculatedAmount
                    ]);
                }

                // ── Update invoice if order_detail has invoice_id ──
                if ($orderDetail->invoice_id) {
                    $invoice = $this->invoiceModel::where('id', $orderDetail->invoice_id)->first();
                    if ($invoice) {
                        // Recalculate invoice totals from all order_details for this invoice
                        $orderDetailsForInvoice = $this->order_detailModel::where('invoice_id', $orderDetail->invoice_id)->get();
                        
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

                        // ── Update brokerpurchase table with invoice_grand_total ──
                        $this->brokerpurchaseModel::where('order_detail_id', $detail['order_detail_id'])
                            ->where('is_deleted', 0)
                            ->update(['invoice_grand_total' => $grandTotal]);

                        // ── Update broker purchase amount for current order_detail ──
                        $this->brokerpurchaseModel::where('order_detail_id', $detail['order_detail_id'])
                            ->update(['amount' => $calculatedAmount]);

                        // ── Handle payment status and payment_details when invoice total changes ──
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

                // ── Update order table with recalculated totals ──
                $order = $this->orderModel::where('id', $orderId)->first();
                if ($order) {
                    // Get all order_details for this order
                    $allOrderDetails = $this->order_detailModel::where('order_id', $orderId)->get();
                    
                    $totalNetKg = 0;
                    $totalAmount = 0;
                    
                    foreach ($allOrderDetails as $od) {
                        $totalNetKg += $od->net_kg;
                        $totalAmount += ($od->net_kg * $od->rate);
                    }

                    $discount = $order->discount ?? 0;
                    $discountAmount = ($totalAmount * $discount) / 100;
                    $finalAmount = $totalAmount - $discountAmount;

                    $order->update([
                        'discount'        => $discount,
                        'totalNetKg'      => $totalNetKg,
                        'totalAmount'     => $totalAmount,
                        'discountAmount'  => $discountAmount,
                        'finalAmount'     => $finalAmount
                    ]);
                }
            }
        }
        // Prepare response
        if ($successCount === count($details)) {
            return $this->successresponse(200, 'message', 'All Sample Purchases successfully created');
        } elseif ($successCount > 0) {
            return $this->successresponse(207, 'message', "$successCount of " . count($details) . " records created successfully", ['errors' => $errors]);
        } else {
            return $this->errorresponse(500, 'message', 'Failed to create Sample Purchases', ['errors' => $errors]);
        }
    }
    public function edit($id)
    {
        if ($this->rp['teamodule']['brokerpurchase']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $brokerpurchase = $this->brokerpurchaseModel::find($id);
        if ($this->rp['teamodule']['brokerpurchase']['alldata'] != 1) {
            if ($brokerpurchase->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$brokerpurchase) {
            return $this->successresponse(500, 'message', 'Sample Purchase not found !');
        }

        // ✅ Check if sample purchase has associated invoice
        $hasInvoice = false;
        $warningMessage = '';

        if ($brokerpurchase->invoice_id) {
            $hasInvoice = true;
            $warningMessage = 'This sample purchase has an associated invoice. Updating this sample purchase will also update the bag details and any other related information. The invoice will also be updated with these changes.';
        }

        $brokerpurchase->has_invoice = $hasInvoice;
        $brokerpurchase->warning_message = $warningMessage;

        return $this->successresponse(200, 'brokerpurchase', $brokerpurchase);
    }
    public function update(Request $request, $id)
    {
        if ($this->rp['teamodule']['brokerpurchase']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $exists = $this->brokerpurchaseModel
            ::where('invoice_no', $request->invoice_no)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return $this->errorresponse(422,  ['invoice_no' => ['This Invoice number  already purchase created ']]);
        }
        $find_data = $this->brokerpurchaseModel::find($id);
        if ($this->rp['teamodule']['brokerpurchase']['alldata'] != 1) {
            if ($find_data->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$find_data) {
            return response()->json(['status' => 'error', 'message' => 'Sample Purchase not found'], 404);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'grade' => 'required|string|max:255',
            'bags' => 'required|string|max:255',
            'rate' => 'required|string|max:255',
            'net_kg' => 'required|nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }
        $details = $this->order_detailModel::where('id', $find_data->order_detail_id)->get();
        
        if ($details && count($details) > 0) {
            $orderDetail = $details[0];
            $orderId = $orderDetail->order_id;
            $kgPerBag = $request->bags > 0 ? $request->net_kg / $request->bags : 0;
            
            // ── Update order_details ──
            $calculatedAmount = $request->net_kg * $request->rate;
            $orderDetail->update([
                'bags'   => $request->bags,
                'net_kg' => $request->net_kg,
                'rate'   => $request->rate,
                'amount' => $calculatedAmount,
                'kg'     => $kgPerBag
            ]);

            // ── Update mng_col if order_detail_id exists ──
            $mngCol = $this->mngcolModel::where('order_detail_id', $orderDetail->id)->first();
            if ($mngCol) {
                $mngCol->update([
                    'No_Of_Pkags'      => $request->bags,
                    'Rate_per_kg'      => $request->rate,
                    'Net_Weight_Kgs'   => $request->net_kg,
                    'Net_Oty_Per_Pkg'  => $kgPerBag,
                    'discount'         => 0,
                    'amount'           => $calculatedAmount
                ]);
            }

            // ── Update invoice if order_detail has invoice_id ──
            if ($orderDetail->invoice_id) {
                $invoice = $this->invoiceModel::where('id', $orderDetail->invoice_id)->first();
                if ($invoice) {
                    // Recalculate invoice totals from all order_details for this invoice
                    $orderDetailsForInvoice = $this->order_detailModel::where('invoice_id', $orderDetail->invoice_id)->get();
                    
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

                    // ── Update brokerpurchase table with invoice_grand_total and amount ──
                    $this->brokerpurchaseModel::where('order_detail_id', $detail['order_detail_id'])
                        ->where('is_deleted', 0)
                        ->update(['invoice_grand_total' => $grandTotal]);

                    // ── Update current broker purchase amount ──
                    $this->brokerpurchaseModel::where('id', $id)
                        ->update(['amount' => $calculatedAmount]);

                    // ── Handle payment status and payment_details when invoice total changes ──
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

            // ── Update order table with recalculated totals ──
            $order = $this->orderModel::where('id', $orderId)->first();
            if ($order) {
                // Get all order_details for this order
                $allOrderDetails = $this->order_detailModel::where('order_id', $orderId)->get();
                
                $totalNetKg = 0;
                $totalAmount = 0;
                
                foreach ($allOrderDetails as $od) {
                    $totalNetKg += $od->net_kg;
                    $totalAmount += ($od->net_kg * $od->rate);
                }

                $discount = $order->discount ?? 0;
                $discountAmount = ($totalAmount * $discount) / 100;
                $finalAmount = $totalAmount - $discountAmount;

                $order->update([
                    'discount'        => $discount,
                    'totalNetKg'      => $totalNetKg,
                    'totalAmount'     => $totalAmount,
                    'discountAmount'  => $discountAmount,
                    'finalAmount'     => $finalAmount
                ]);
            }
        }

        $update = $this->brokerpurchaseModel::where('id', $id)->update([
            'sample_purchase_date' => $request->sample_purchase_date,
            'bags' => $request->bags,
            'rate' => $request->rate,
            'net_kg' => $request->net_kg,
            'final_net_kg' => $request->net_kg - $find_data->shortage,
            'updated_by' => $request->user_id,
        ]);

        if ($update) {
            return $this->successresponse(200, 'message', 'Sample Purchase succesfully update');
        } else {
            return $this->successresponse(500, 'message', 'Sample Purchase not succesfully update !');
        }
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
            return $this->successresponse(500, 'message', 'Sample Purchase not found !');
        }
        $brokerpurchase->update(
            [
                "is_deleted" => 1
            ]
        );

        return $this->successresponse(200, 'message', 'Sample Purchase succesfully deleted');
    }
}
