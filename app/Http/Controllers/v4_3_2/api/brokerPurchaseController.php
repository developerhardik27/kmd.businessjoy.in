<?php

namespace App\Http\Controllers\v4_3_2\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class brokerPurchaseController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $brokerpurchaseModel, $order_detailModel, $gradenModel, $orderModel;

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
            ->pluck('invoice_no')
            ->toArray();

        $allInvoices = $this->order_detailModel
            ::where('is_deleted', 0)
            ->where('garden_id', $request->garden_id)
            ->whereNotIn('invoice_no', $usedInvoices)
            ->orderBy('invoice_no', 'ASC')
            ->get();

        return $this->successresponse(200, 'data', $allInvoices);
    }
    public function getOtherDetails(Request $request)
    {
        if ($this->rp['teamodule']['brokerpurchase']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $order = $this->order_detailModel
            ::join('grades', 'grades.id', '=', 'order_details.grade')
            ->where('order_details.is_deleted', 0)
            ->where('order_details.invoice_no', $request->invoice_no)
            ->select(
                'order_details.id as order_id',
                'order_details.garden_id',
                'order_details.invoice_no',
                'order_details.bags',
                'order_details.net_kg',
                'grades.id as grade_id',
                'grades.grade as grade_name'
            )
            ->orderBy('grades.grade', 'ASC')
            ->get();

        if (!$order) {
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


        $data1 = $this->brokerpurchaseModel
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

            ->select(
                'broker_purchases.*',
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
            ->where('broker_purchases.is_deleted', 0)
            ->whereIn('companymasters.id', $companyIds)   // Filter by selected companies
            ->whereIn('orders.buyer_party', $buyerParties)
            ->get();
        $maindata = [
            'maindata' => [
                'companymaster_id' => $companyIds,
                'buyer_id' => $buyerParties,
            ],
            "line_items" => $data1,
        ];

        // Continue your invoice creation here
        return $this->successresponse(200, 'message', 'invoice created data you get properly', 'data', $maindata);
    }

    public function index(Request $request)
    {
        if ($this->rp['teamodule']['brokerpurchase']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $brokerpurchase = $this->brokerpurchaseModel::where('is_deleted', 0)->get();
        if ($brokerpurchase->isEmpty()) {
            return $this->successresponse(404, 'message', 'No Data Found');
        }
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
            ->leftJoin('invoices', function ($join) {
                $join->on('invoices.customer_id', '=', 'orders.buyer_party')
                    ->on('invoices.company_details_id', '=', 'companymasters.id');
            })
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
                'invoices.id as invoice_id',
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
        if ($this->rp['teamodule']['brokerpurchase']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $data = $request->all();

        $validator = Validator::make($data, [
            'garden_id'  => 'required|string|max:255',
            'invoice_no' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'bags' => 'required|string|max:255',
            'net_kg' => 'required|nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }
        $create = $this->brokerpurchaseModel::create([
            'garden_id' => $request->garden_id,
            'invoice_no' => $request->invoice_no,
            'grade' => $request->grade,
            'bags' => $request->bags,
            'net_kg' => $request->net_kg,
            'created_by' => $request->user_id,
        ]);
        if ($create) {
            return $this->successresponse(200, 'message', 'Broker Purchase succesfully Created');
        } else {
            return $this->successresponse(500, 'message', 'Broker Purchase not succesfully Created !');
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
            return $this->successresponse(500, 'message', 'Broker Purchase not found !');
        }
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
            return response()->json(['status' => 'error', 'message' => 'Broker Purchase not found'], 404);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'garden_id'  => 'required|string|max:255',
            'invoice_no' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'bags' => 'required|string|max:255',
            'net_kg' => 'required|nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }
        $details = $this->order_detailModel::where('garden_id', $request->garden_id)->where('invoice_no', $request->invoice_no)->get();
        $update = $this->brokerpurchaseModel::where('id', $id)->update([
            'garden_id' => $request->garden_id,
            'invoice_no' => $request->invoice_no,
            'grade' => $details[0]->grade,
            'bags' => $details[0]->bags,
            'net_kg' => $details[0]->net_kg,
            'updated_by' => $request->user_id,
        ]);

        if ($update) {
            return $this->successresponse(200, 'message', 'Broker Purchase succesfully update');
        } else {
            return $this->successresponse(500, 'message', 'Broker Purchase not succesfully update !');
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
            return $this->successresponse(500, 'message', 'Broker Purchase not found !');
        }
        $brokerpurchase->update(
            [
                "is_deleted" => 1
            ]
        );

        return $this->successresponse(200, 'message', 'Broker Purchase succesfully deleted');
    }
}
