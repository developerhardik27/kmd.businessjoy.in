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

            $invoice = $this->invoiceModel::find($request->inv_id);
            $payments = $this->payment_detailsModel::where('inv_id', $request->inv_id)
                ->where('part_payment', 1)
                ->get();

            $receipt_number = date('dHm') . str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT) . date('is'); // Generate receipt number

            if (!$invoice) {
                return $this->successresponse(404, 'message', 'Invoice not found');
            }

            $total_paid = $payments->sum('paid_amount');
            $total = $invoice->grand_total;
            $paid_amount = (int) $request->paidamount;
            $pending_amount = $total - $total_paid - $paid_amount;

            // Check if full payment is already done
            if ($total_paid == $total) {
                return $this->successresponse(200, 'message', 'Payment already made');
            }

            // Insert payment details
            $payment_data = [
                'inv_id' => $request->inv_id,
                'receipt_number' => $receipt_number,
                'transaction_id' => $request->transid,
                'amount' => $total,
                'paid_amount' => $paid_amount,
                'pending_amount' => $pending_amount,
                'paid_by' => $request->paid_by,
                'paid_type' => $request->payment_type,
                'part_payment' => 1
            ];

            $payment_inserted = $this->payment_detailsModel::insert($payment_data);

            // Update invoice status based on payment
            if ($payment_inserted) {
                $invoice->status = $pending_amount > 0 ? 'part_payment' : 'paid';
                $invoice->save();
                return $this->successresponse(200, 'message', 'Payment details successfully created');
            } else {
                return $this->successresponse(500, 'message', 'Failed to create payment details');
            }

        }
    }
  
}
