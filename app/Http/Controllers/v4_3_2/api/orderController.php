<?php

namespace App\Http\Controllers\v4_3_2\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\v4_3_2\api\commonController;
use Illuminate\Support\Facades\Validator;

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
    public function index()
    {
        if ($this->rp['teamodule']['order']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $order = $this->orderModel::join('partys as buyer', 'buyer.id', 'orders.buyer_party')
            ->join('partys as transport', 'transport.id', 'orders.transport')
            ->select('buyer.name as buyer_name', 'transport.name as transport_name', 'orders.*')
            ->where("orders.is_deleted", 0)->get();

        if ($order->isEmpty()) {
            return DataTables::of($order)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($order)
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
}
