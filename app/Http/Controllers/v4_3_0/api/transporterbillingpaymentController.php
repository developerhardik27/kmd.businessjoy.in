<?php

namespace App\Http\Controllers\v4_3_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class transporterbillingpaymentController extends commonController
{
    public $userId, $companyId, $masterdbname, $transporter_billingModel, $payment_detailsModel, $rp;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->transporter_billingModel = $this->getmodel('transporter_billing');
        $this->payment_detailsModel = $this->getmodel('transporter_billing_payment');
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
        $paymentdetail = $this->payment_detailsModel::where('transporter_billing_id', $id)->get();

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
        $payment = $this->payment_detailsModel::where('transporter_billing_id', $id)
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
        $payment = $this->payment_detailsModel::where('transporter_billing_id', $id)
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
            'bill_id' => 'required',
            'paidamount' => 'required|numeric',
            'transid' => 'nullable|string|max:50',
            'paid_by' => 'nullable|string|max:30',
            'payment_type' => 'nullable|string|max:30|in:Online Payment,Cash,Check',
            'remarks' => 'nullable|string|max:400',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $bill = $this->transporter_billingModel::find($request->bill_id);
            $payments = $this->payment_detailsModel::where('transporter_billing_id', $request->bill_id)
                ->where('part_payment', 1)
                ->get();

            $receipt_number = date('dHm') . str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT) . date('is'); // Generate receipt number

            if (!$bill) {
                return $this->successresponse(404, 'message', 'Bill not found');
            }

            $total_paid = $payments->sum('paid_amount') ?? 0;
            $total = $bill->amount;
            $paid_amount = (int) $request->paidamount;
            $pending_amount = $total - $total_paid - $paid_amount;

            // Check if full payment is already done
            if ($total_paid == $total) {
                return $this->successresponse(200, 'message', 'Payment already made');
            }

            // Insert payment details
            $payment_data = [
                'transporter_billing_id' => $request->bill_id,
                'receipt_number' => $receipt_number,
                'transaction_id' => $request->transid,
                'amount' => $total,
                'paid_amount' => $paid_amount,
                'pending_amount' => $pending_amount,
                'paid_by' => $request->paid_by,
                'paid_type' => $request->payment_type,
                'part_payment' => $total_paid > 0 || $pending_amount > 0 ? 1 : 0,
                'remarks' => $request->remarks,
            ];

            $payment_inserted = $this->payment_detailsModel::insert($payment_data);

            // Update bill status based on payment
            if ($payment_inserted) {
                $bill->status = $pending_amount > 0 ? 'part_payment' : 'paid';
                $bill->save();
                return $this->successresponse(200, 'message', 'Payment details successfully created');
            } else {
                return $this->successresponse(500, 'message', 'Failed to create payment details');
            }
        }
    }
}
