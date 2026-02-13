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
    public $userId, $companyId, $masterdbname, $rp, $orderModel, $order_detailModel;

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
    }
    public function index(Request $request)
    {
        if ($this->rp['teamodule']['order']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $order = $this->orderModel::join('partys as buyer', 'buyer.id', 'orders.buyer_party')
            ->join('partys as transport', 'transport.id', 'orders.transport')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('gardens', 'gardens.id', 'order_details.garden_id')
            ->join('grades', 'grades.id', 'order_details.grade')
            ->where("orders.is_deleted", 0);

        $filters = [
            'filter_transport'         => 'orders.transport',
            'filter_buyer'             => 'orders.buyer_party',
            'filter_garden'            => 'order_details.garden_id',
            'filter_grade'             => 'order_details.grade',
            'filter_credit_days_from'  => 'orders.credit_days',
            'filter_credit_days_to'    => 'orders.credit_days',
            'filter_final_amount_from' => 'orders.finalAmount',
            'filter_final_amount_to'   => 'orders.finalAmount',
        ];
        foreach ($filters as $requestKey => $column) {
            $value = $request->$requestKey ?? null;

            if ($value !== null) {
                if (in_array($requestKey, [
                    'filter_credit_days_from',
                    'filter_credit_days_to',
                    'filter_final_amount_from',
                    'filter_final_amount_to'
                ])) {
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $order->where($column, $operator, $value);
                } else if (strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false) {
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $order->whereDate($column, $operator, $value);
                } else {
                    $order->whereIn($column, (array)$value);
                }
            }
        }

        $orderData = $order
            ->select(
                'orders.id as order_id',
                'buyer.name as buyer_name',
                'transport.name as transport_name',
                'orders.*',
                'order_details.*',
                'gardens.garden_name as garden_name',
                'grades.grade as grade_name'
            )
            ->get()
            ->groupBy('order_id')
            ->map(function ($details, $orderId) {
                // Map each order to an 'auto-tuple' style array
                $first = $details->first();
                return [
                    'id' => $orderId,
                    'buyer_name' => $first->buyer_name,
                    'transport_name' => $first->transport_name,
                    'discount' => $first->discount,
                    'totalNetKg' => $first->totalNetKg,
                    'credit_days' => $first->credit_days,
                    'final_amount' => $first->finalAmount,
                    'details' => $details->map(function ($item) {
                        return [
                            'garden_name' => $item->garden_name,
                            'grade_name' => $item->grade_name,
                            'invoice_no' => $item->invoice_no,
                            'bags' => $item->bags,
                            'kg' => $item->kg,
                            'net_kg' => $item->net_kg,
                            'rate' => $item->rate,
                            'amount' => $item->amount,
                        ];
                    })->toArray()
                ];
            })
            ->values();

        // return $orderData;

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
    public function store(Request $request)
    {
        if ($this->rp['teamodule']['order']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'buyer_party'    => 'required|integer',
            'transport'      => 'required|integer',
            'credit_days'    => 'required|string|in:CD,15,30,45,60,90',
            'discount'       => 'nullable|numeric|min:0|max:100',
            'totalNetKg'     => 'required|numeric|min:0',
            'totalAmount'    => 'required|numeric|min:0',
            'discountAmount' => 'nullable|numeric|min:0',
            'finalAmount'    => 'required|numeric|min:0',
        ]);

        $errors = [];
        $invoiceNumbers = [];

        foreach ($request->rows as $index => $row) {

            if (empty($row['garden_id'])) {
                $errors["rows.$index.garden_id"] = ['Select at least one garden.'];
            }
            if (empty($row['invoice_no'])) {
                $errors["rows.$index.invoice_no"] = ['The invoice number is required.'];
            } else {
                if (in_array($row['invoice_no'], $invoiceNumbers)) {
                    $errors["rows.$index.invoice_no"] = ['This invoice number already exists in the request.'];
                } else {
                    $invoiceNumbers[] = $row['invoice_no'];
                }
                $exists = $this->order_detailModel
                    ::where('invoice_no', $row['invoice_no'])
                    ->exists();
                if ($exists) {
                    $errors["rows.$index.invoice_no"] = ['This invoice number already exists in the system.'];
                }
            }
            if (!isset($row['bags']) || $row['bags'] < 1) {
                $errors["rows.$index.bags"] = ['Enter Bags greater than 1!'];
            }
            if (!isset($row['kg']) || $row['kg'] < 1) {
                $errors["rows.$index.kg"] = ['Enter kg greater than 1!'];
            }
            if (!isset($row['net_kg']) || $row['net_kg'] < 0) {
                $errors["rows.$index.net_kg"] = ['Net kg cannot be negative!'];
            }
            if (!isset($row['rate']) || $row['rate'] < 1) {
                $errors["rows.$index.rate"] = ['Enter rate greater than 1!'];
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
            'credit_days' => $request->credit_days,
            'discount' => $request->discount ?? 0,
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
        $order = [
            'order' => $order,
            'order_details' => $order_details
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
            'buyer_party'    => 'required|integer',
            'transport'      => 'required|integer',
            'credit_days'    => 'required|string|in:CD,15,30,45,60,90',
            'discount'       => 'nullable|numeric|min:0|max:100',
            'totalNetKg'     => 'required|numeric|min:0',
            'totalAmount'    => 'required|numeric|min:0',
            'discountAmount' => 'nullable|numeric|min:0',
            'finalAmount'    => 'required|numeric|min:0',
        ]);

        $errors = [];
        $invoiceNumbers = [];

        foreach ($request->rows as $index => $row) {

            if (empty($row['garden_id'])) {
                $errors["rows.$index.garden_id"] = ['Select at least one garden.'];
            }
            if (empty($row['invoice_no'])) {
                $errors["rows.$index.invoice_no"] = ['The invoice number is required.'];
            } else {
                if (in_array($row['invoice_no'], $invoiceNumbers)) {
                    $errors["rows.$index.invoice_no"] = ['This invoice number already exists in the request.'];
                } else {
                    $invoiceNumbers[] = $row['invoice_no'];
                }
                $exists = $this->order_detailModel
                    ::where('invoice_no', $row['invoice_no'])
                    ->where('order_id', '!=', $id)
                    ->exists();
                if ($exists) {
                    $errors["rows.$index.invoice_no"] = ['This invoice number already exists in the system.'];
                }
            }
            if (!isset($row['bags']) || $row['bags'] < 1) {
                $errors["rows.$index.bags"] = ['Enter Bags greater than 1!'];
            }
            if (!isset($row['kg']) || $row['kg'] < 1) {
                $errors["rows.$index.kg"] = ['Enter kg greater than 1!'];
            }
            if (!isset($row['net_kg']) || $row['net_kg'] < 0) {
                $errors["rows.$index.net_kg"] = ['Net kg cannot be negative!'];
            }
            if (!isset($row['rate']) || $row['rate'] < 1) {
                $errors["rows.$index.rate"] = ['Enter rate greater than 1!'];
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
        if ($this->rp['teamodule']['order']['alldata'] != 1) {
            if ($order->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$order) {
            return $this->successresponse(500, 'message', 'Order not found!');
        }
        $order->update([
            'buyer_party'    => $request->buyer_party,
            'transport'      => $request->transport,
            'credit_days'    => $request->credit_days,
            'discount' => $request->discount ?? 0,
            'totalNetKg'     => $request->totalNetKg,
            'totalAmount'    => $request->totalAmount,
            'discountAmount' => $request->discountAmount,
            'finalAmount'    => $request->finalAmount,
            'updated_by'     => $request->user_id,
        ]);
        $this->order_detailModel::where('order_id', $id)->delete();
        foreach ($request->rows as $row) {
            $this->order_detailModel::create([
                'order_id'   => $id,
                'garden_id'  => $row['garden_id'],
                'invoice_no' => $row['invoice_no'],
                'grade'      => $row['grade'] ?? null,
                'bags'       => $row['bags'],
                'kg'         => $row['kg'],
                'net_kg'     => $row['net_kg'],
                'rate'       => $row['rate'],
                'amount'     => $row['amount'],
            ]);
        }
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
            ->whereYear('created_at', now()->year); // ✅ Only current year

        if ($month && strtolower($month) !== 'all') {
            // Specific month of current year
            $data = $query
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(id) as total_orders'),
                    DB::raw('SUM(totalNetKg) as total_kg'),
                    DB::raw('SUM(finalAmount) as total_amount')
                )
                ->whereMonth('created_at', $month)
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy('month', 'ASC')
                ->get();
        } else {
            // All months of current year
            $data = $query
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(id) as total_orders'),
                    DB::raw('SUM(totalNetKg) as total_kg'),
                    DB::raw('SUM(finalAmount) as total_amount')
                )
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy('month', 'ASC')
                ->get();
        }
        return response()->json($data);
    }
}
