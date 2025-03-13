<?php

namespace App\Http\Controllers\v3_0_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends commonController
{

    public $userId, $companyId, $masterdbname, $invoiceModel, $payment_detailsModel;

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

    // use for pdf
    public function paymentdetailsforpdf(string $id)
    {

        $paymentdetail = $this->payment_detailsModel::find($id);

        if (!$paymentdetail) {
            return $this->successresponse(404, 'message', 'No Records Found');
        }


        return $this->successresponse(200, 'paymentdetail', $paymentdetail);

    }
    // use for pdf
    public function paymentdetail(string $id)
    {

        $paymentdetail = $this->payment_detailsModel::where('inv_id', $id)->get();

        if ($paymentdetail->isEmpty()) { 
            return $this->successresponse(404, 'message', 'No Records Found');
        }
        return $this->successresponse(200, 'paymentdetail', $paymentdetail);
    }


    /**
     * pending payment.
     */
    public function pendingpayment(string $id)
    {

        $payment = $this->payment_detailsModel::where('inv_id', $id)
            ->orderBy('id', 'desc')
            ->limit(1)
            ->get();

        if ($payment->isEmpty()) { 
            return $this->successresponse(404, 'payment', 'No Records Found');
        }
        return $this->successresponse(200, 'payment', $payment);

    }


    public function index(string $id)
    {

        $payment = $this->payment_detailsModel::where('inv_id', $id)
            ->get();

        if ($payment->isEmpty()) { 
            return $this->successresponse(404, 'payment', 'No Records Found');
        }
        return $this->successresponse(200, 'payment', $payment);

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

        // validate incoming request data
        $validator = Validator::make($request->all(), [
            'inv_id' => 'required',
            'paidamount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $invoiceammount = $this->invoiceModel::find($request->inv_id);
            $invoicepaidamount = $this->payment_detailsModel::where('inv_id', $request->inv_id)->where('part_payment', 1)->get();

            $receipt_number = date('dHm') . str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT) . date('is'); // generate receipt number
            if ($invoicepaidamount->isNotEmpty()) {
                $total_paided_amount = 0;
                foreach ($invoicepaidamount as $value) {
                    $total_paided_amount = $total_paided_amount + $value->paid_amount;
                }
                if ($invoiceammount->grand_total == $total_paided_amount) {
                    return $this->successresponse(200, 'message', 'payment Already Paided');
                }
                if ($invoiceammount) {
                    $total = $invoiceammount->grand_total;
                    $paidamount = $request->paidamount;
                    $pendingamount = $total - $paidamount - $total_paided_amount;

                    $payment_details = $this->payment_detailsModel::insert(
                        [
                            'inv_id' => $request->inv_id,
                            'receipt_number' => $receipt_number,
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
                    return $this->successresponse(200, 'message', 'payment details succesfully created');
                } else {
                    return $this->successresponse(500, 'message', 'payment details not succesfully create');
                }
            } else {
                if ($invoiceammount) {
                    $total = $invoiceammount->grand_total;
                    $paidamount = $request->paidamount;
                    $pendingamount = $total - $paidamount;
                    if ($pendingamount > 0) {
                        $payment_details = $this->payment_detailsModel::insert(
                            [
                                'inv_id' => $request->inv_id,
                                'receipt_number' => $receipt_number,
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
                                'receipt_number' => $receipt_number,
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
                        return $this->successresponse(200, 'message', 'payment details succesfully created');
                    } else {
                        return $this->successresponse(500, 'message', 'payment details not succesfully create');
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
