<?php

namespace App\Http\Controllers\v1_0_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends commonController
{

    public $userId, $companyId, $masterdbname,$invoiceModel,$payment_detailsModel;

    public function __construct(Request $request)
    {
        if (session()->get('company_id')) {
            $this->dbname(session()->get('company_id'));
        } else {
            $this->dbname($request->company_id);
        }
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->invoiceModel = $this->getmodel('invoice');
        $this->payment_detailsModel = $this->getmodel('payment_details');
    }


    public function paymentdetailsforpdf(string $id)
    {

        $paymentdetail = $this->payment_detailsModel::find($id);

        if ($paymentdetail->count() > 0) {
            return response()->json([
                'status' => 200,
                'paymentdetail' => $paymentdetail
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Records Found'
            ]);
        }
    }
    public function paymentdetail(string $id)
    {

        $paymentdetail = $this->payment_detailsModel::where('inv_id', $id)->get();

        if ($paymentdetail->count() > 0) {
            return response()->json([
                'status' => 200,
                'paymentdetail' => $paymentdetail
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Records Found'
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function pendingpayment(string $id)
    {

        $payment = DB::connection('dynamic_connection')->table('payment_details')
            ->where('inv_id', $id)
            ->orderBy('id','desc')
            ->limit(1)
            ->get();

        if ($payment->count() > 0) {
            return response()->json([
                'status' => 200,
                'payment' => $payment
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'payment' => 'No Records Found'
            ]);
        }

    }
    public function index(string $id)
    {

        $payment = DB::connection('dynamic_connection')->table('payment_details')
            ->where('inv_id', $id)
            ->get();

        if ($payment->count() > 0) {
            return response()->json([
                'status' => 200,
                'payment' => $payment
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'payment' => 'No Records Found'
            ]);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'inv_id' => 'required',
            'transid' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ],422);
        } else {

            $invoiceammount = $this->invoiceModel::find($request->inv_id);
            $invoicepaidamount = $this->payment_detailsModel::where('inv_id', $request->inv_id)->where('part_payment', 1)->get();

            if ($invoicepaidamount->count() > 0) {
                $total_paided_amount = 0;
                foreach ($invoicepaidamount as $value) {
                    $total_paided_amount = $total_paided_amount + $value->paid_amount;
                }
                if ($invoiceammount->grand_total == $total_paided_amount) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'payment Already Paided'
                    ]);
                }
                if ($invoiceammount->count() > 0) {
                    $total = $invoiceammount->grand_total;
                    $paidamount = $request->paidamount;
                    $pendingamount = $total - $paidamount - $total_paided_amount;

                    $payment_details = $this->payment_detailsModel::insert(
                        [
                            'inv_id' => $request->inv_id,
                            'transaction_id' => $request->transid,
                            'amount' => $total,
                            'paid_amount' => $paidamount,
                            'pending_amount' => $pendingamount,
                            'paid_by' => $request->paid_by,
                            'paid_type' => $request->payment_type,
                            'part_payment' => 1
                        ]
                    );

                    if ($pendingamount == 0) {
                        $invoiceammount->status = 'paid';
                        $invoiceammount->save();
                    }
                }
                if ($payment_details) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'payment details succesfully created'
                    ]);
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'payment details not succesfully create'
                    ]);
                }
            } else {
                if ($invoiceammount->count() > 0) {
                    $total = $invoiceammount->grand_total;
                    $paidamount = $request->paidamount;
                    $pendingamount = $total - $paidamount;
                    if ($pendingamount > 0) {
                        $payment_details = $this->payment_detailsModel::insert(
                            [
                                'inv_id' => $request->inv_id,
                                'transaction_id' => $request->transid,
                                'amount' => $total,
                                'paid_amount' => $paidamount,
                                'pending_amount' => $pendingamount,
                                'paid_by' => $request->paid_by,
                                'paid_type' => $request->payment_type,
                                'part_payment' => 1
                            ]
                        );
                        $invoiceammount->status = 'part_payment';
                        $invoiceammount->save();
                    } else {
                        $payment_details = $this->payment_detailsModel::insert(
                            [
                                'inv_id' => $request->inv_id,
                                'transaction_id' => $request->transid,
                                'amount' => $total,
                                'paid_amount' => $paidamount,
                                'pending_amount' => $pendingamount,
                                'paid_by' => $request->paid_by,
                                'paid_type' => $request->payment_type
                            ]
                        );

                        $invoiceammount->status = 'paid';
                        $invoiceammount->save();
                    }
                    if ($payment_details) {
                        return response()->json([
                            'status' => 200,
                            'message' => 'payment details succesfully created'
                        ]);
                    } else {
                        return response()->json([
                            'status' => 500,
                            'message' => 'payment details not succesfully create'
                        ]);
                    }
                }
            }
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
