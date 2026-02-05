<?php

namespace App\Http\Controllers\v4_3_2\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class brokragebillpaymentController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $broker_payment_detailsModel, $broker_bill_invoiceModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->broker_payment_detailsModel = $this->getmodel('broker_bill_payment_detail');
        $this->broker_bill_invoiceModel = $this->getmodel('broker_bill_invoice');
    }

    // use for pdf
    public function paymentdetailsforpdf(string $id)
    {
        $paymentdetail = $this->broker_payment_detailsModel::where('id', $id)->where('is_deleted', 0)->get();

        if (!$paymentdetail) {
            return $this->successresponse(404, 'message', 'No Records Found');
        }


        return $this->successresponse(200, 'paymentdetail', $paymentdetail);
    }
     public function broker_paymentdetail_status(Request $request, string $id)
    {
       
        $invoices = $this->broker_bill_invoiceModel::where('id', $id)
            ->update([
                'status' => $request->status
            ]);

        if ($invoices) {
            return $this->successresponse(200, 'message', 'status updated');
        } else {
            return $this->successresponse(404, 'message', 'invoice  status not succesfully updated!');
        }
    }

    public function paymentdetail(string $id)
    {
        
        $paymentdetail = $this->broker_payment_detailsModel::select(
            'id',
            'amount',
            'tds_amount',
            'challan_no',
            'tds_status',
            'tds_credited',
            'paid_amount',
            'paid_by',
            'paid_type',
            'part_payment',
            'pending_amount',
            'receipt_number',
            'transaction_id',
            'inv_id',
            'is_active',
            'is_deleted',
            DB::raw("DATE_FORMAT(datetime, '%d-%m-%Y') as datetime"),
            DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as created_at"),
            DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y') as updated_at")
        )->where('inv_id', $id)->where('is_deleted', 0)->get();
      
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

        $payment = $this->broker_payment_detailsModel::where('inv_id', $id)
            ->where('is_deleted', 0)
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

        $payment = $this->broker_payment_detailsModel::where('inv_id', $id)
            ->where('is_deleted', 0)->get();

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
            'inv_id' => 'required|integer',
            'transid' => 'nullable|string|max:50',
            'payment_date' => 'required|date',
            'paidamount' => 'required|numeric',
            'paid_by' => 'nullable|string|max:30',
            'payment_type' => 'nullable|string|max:30',
            'tds_amount' => 'nullable|numeric',
            'challan_no' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $invoice = $this->broker_bill_invoiceModel::find($request->inv_id);
          
            $payments = $this->broker_payment_detailsModel::where('inv_id', $request->inv_id)
                ->where('is_deleted', 0)
                ->where('part_payment', 1)
                ->get();
          
            $receipt_number = date('dHm') . str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT) . date('is'); // Generate receipt number

            if (!$invoice) {
                return $this->successresponse(404, 'message', 'Invoice not found');
            }

            $total_paid = $payments->sum('paid_amount') + $payments->sum('tds_amount');
          
            $total = $invoice->grand_total;
   
            $paid_amount = (int) $request->paidamount ?? 0;
            $tds_paid_amount = (int) $request->tds_amount ?? 0;
            $pending_amount = $total - $total_paid - $paid_amount - $tds_paid_amount;
            // Check if full payment is already done
            if ($total_paid == $total) {
                return $this->successresponse(500, 'message', 'Payment already made');
            }
            if ($pending_amount < 0) {
                return $this->successresponse(500, 'message', 'Entered amount exceeds the total payable.');
            }

            // Insert payment details
            $payment_data = [
                'inv_id' => $request->inv_id,
                'receipt_number' => $receipt_number,
                'transaction_id' => $request->transid,
                'datetime' => $request->payment_date ?? now(),
                'amount' => $total,
                'tds_amount' => $tds_paid_amount,
                'challan_no' => $request->challan_no,
                'tds_status' => $request->status,
                'paid_amount' => $paid_amount,
                'pending_amount' => $pending_amount,
                'paid_by' => $request->paid_by,
                'paid_type' => $request->payment_type,
                'part_payment' => $total_paid > 0 || $pending_amount > 0 ? 1 : 0
            ];

            $payment_inserted = $this->broker_payment_detailsModel::insert($payment_data);

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

    /**
     * destroy resource in storage.
     */
    public function destroy($id)
    {
        $payment = $this->broker_payment_detailsModel::where('id', $id)
            ->where('is_deleted', 0)
            ->first();

        if (!$payment) {
            return $this->successresponse(404, 'message', 'Invoice payment not found');
        }

        $invoice = $this->broker_bill_invoiceModel::find($payment->inv_id);

        // Fetch all payment rows for this bill
        $payments = $this->broker_payment_detailsModel::where('inv_id', $invoice->id)
            ->where('is_deleted', 0)
            ->get();

        // dd($payments);   
        $payment->is_deleted = 1;
        $payment->updated_by = $this->userId;
        $payment->updated_at = now();
        $payment->save();

        if (count($payments) == 1) {
            // Do not change status if it's already "cancel"
            $invoice->status = ($invoice->status != 'cancel')
                ? 'pending'
                : 'cancel';
            $invoice->updated_by = $this->userId;
            $invoice->updated_at = now();
            $invoice->save();
            return $this->successresponse(200, 'message', 'Payment details successfully deleted.');
        }

        // Update each record
        $totalpaid = 0;
        foreach ($payments as $pay) {
            if ($pay->id == $id) {
                continue;
            }
            $totalpaid += $pay->paid_amount +  $pay->tds_amount;
            $pay->pending_amount = $pay->amount - $totalpaid;
            $pay->updated_by = $this->userId;
            $pay->updated_at = now();
            $pay->save();
        }

        // Update bill status
        $billstatus = ($invoice->amount == $totalpaid)
            ? 'paid'
            : 'part_payment';

        // Do not change status if it's already "cancel"
        $invoice->status = ($invoice->status != 'cancel')
            ? $billstatus
            : 'cancel';

        $invoice->save();

        return $this->successresponse(200, 'message', 'Payment details successfully deleted.');
    }
}
