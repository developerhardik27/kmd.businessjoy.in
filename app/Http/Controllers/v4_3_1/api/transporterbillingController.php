<?php

namespace App\Http\Controllers\v4_3_1\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class transporterbillingController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $transporter_billingModel, $payment_detailsModel;

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
        $this->transporter_billingModel = $this->getmodel('transporter_billing');
        $this->payment_detailsModel = $this->getmodel('transporter_billing_payment');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->rp['logisticmodule']['transporterbilling']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $transporterbill = $this->transporter_billingModel::leftJoin('transporter_billing_party', 'transporter_billing.party', '=', 'transporter_billing_party.id')
            ->leftJoin('transporter_billing_payment', function ($join) {
                $join->on('transporter_billing.id', '=', 'transporter_billing_payment.transporter_billing_id')
                    ->whereRaw('transporter_billing_payment.id = (SELECT id FROM transporter_billing_payment WHERE transporter_billing_id = transporter_billing.id and is_deleted=0 ORDER BY id DESC LIMIT 1)');
            })
            ->select(
                'transporter_billing.id',
                'transporter_billing.bill_no',
                DB::raw("DATE_FORMAT(transporter_billing.bill_date, '%d-%m-%Y') as bill_date_formatted"),
                'transporter_billing.lr_no',
                'transporter_billing.con_no',
                'transporter_billing.vehicle_no',
                'transporter_billing.amount',
                'transporter_billing.status',
                'transporter_billing_payment.part_payment',
                'transporter_billing_payment.pending_amount',
                DB::raw("
                    CASE 
                        WHEN transporter_billing_party.firstname IS NULL AND transporter_billing_party.lastname IS NULL THEN transporter_billing_party.company_name
                        ELSE CONCAT_WS(' ', transporter_billing_party.firstname, transporter_billing_party.lastname)
                    END as party
                "),
            )
            ->where('transporter_billing.is_deleted', 0)
            ->orderBy('transporter_billing.bill_date', 'desc');

        if ($this->rp['logisticmodule']['transporterbilling']['alldata'] != 1) {
            $transporterbill->where('transporter_billing.created_by', $this->userId);
        }

        $totalcount = $transporterbill->get()->count(); // count total record

        $filters = [
            'filter_lr_no' => 'lr_no',
            'filter_container_no' => 'con_no',
            'filter_bill_date_from' => 'bill_date',
            'filter_bill_date_to' => 'loading_date',
            'filter_vehicle_no' => 'truck_number',
            'filter_party' => 'party',
        ];

        // Loop through the filters and apply them conditionally
        foreach ($filters as $requestKey => $column) {
            $value = $request->$requestKey;

            if (isset($value)) {
                if (strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false) {
                    // For date filters (loading_date, stuffing_date), we apply range conditions
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $transporterbill->whereDate("transporter_billing.$column", $operator, $value);
                } else {
                    // For other filters, apply simple equality checks
                    $transporterbill->where("transporter_billing.$column", $value);
                }
            }
        }

        $transporterbill = $transporterbill->get();

        if ($transporterbill->isEmpty()) {
            return DataTables::of($transporterbill)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($transporterbill)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
            ])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($this->rp['logisticmodule']['transporterbilling']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'bill_number' => 'required|string',
            'bill_date' => 'required|date',
            "party" => "required|numeric",
            'lr_number' => 'nullable|string',
            'container_number' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            "amount" => "nullable|numeric"
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        $transporterbill = $this->transporter_billingModel::create([ //insert transporterbill record 
            'bill_no' => $request->bill_number,
            'bill_date' => $request->bill_date,
            'lr_no' => $request->lr_number,
            'con_no' => $request->container_number,
            'vehicle_no' => $request->vehicle_number,
            'party' => $request->party,
            "amount" => $request->amount ?? 0,
            "status" => 'pending',
            'created_by' => $this->userId
        ]);

        if ($transporterbill) {
            return $this->successresponse(200, 'message', 'transporter bill succesfully added');
        } else {
            return $this->successresponse(500, 'message', 'transporter bill not succesfully added !');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if ($this->rp['logisticmodule']['transporterbilling']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $transporterbill = $this->transporter_billingModel::find($id);

        if (!$transporterbill) {
            return $this->successresponse(404, 'message', "No such transporter bill found!");
        }

        if ($this->rp['logisticmodule']['transporterbilling']['alldata'] != 1) {
            if ($transporterbill->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'transporterbill', $transporterbill);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($this->rp['logisticmodule']['transporterbilling']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'bill_number' => 'required|string',
            'bill_date' => 'required|date',
            "party" => "required|numeric",
            'lr_number' => 'nullable|string',
            'container_number' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            "amount" => "nullable|numeric"
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $transporterbill = $this->transporter_billingModel::find($id); // find transporter bill record

            if (!$transporterbill) {
                return $this->successresponse(404, 'message', 'No such transporter bill found!');
            }

            if ($this->rp['logisticmodule']['transporterbilling']['alldata'] != 1) {
                if ($transporterbill->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are unauthorized');
                }
            }

            $payment = $this->payment_detailsModel::where('transporter_billing_id', $id)
                ->where('is_deleted', 0)
                ->orderBy('id', 'desc')
                ->first();

            if ($payment) {

                $oldTotalAmount   = $payment->amount;
                $pendingAmount    = $payment->pending_amount;
                $totalPaidAmount  = $oldTotalAmount - $pendingAmount;   // already paid

                // If user enters less than already paid
                if ($totalPaidAmount > $request->amount) {
                    return $this->errorresponse(422, [
                        'amount' => [
                            "You already paid $totalPaidAmount, so this amount is not valid.Please enter correct amount or delete payment entry."
                        ]
                    ]);
                }

                // Only proceed if the new amount is >= already paid
                if ($request->amount >= $totalPaidAmount) {

                    // Fetch all payment rows for this bill
                    $payments = $this->payment_detailsModel::where('transporter_billing_id', $id)
                        ->where('is_deleted', 0)
                        ->get();

                    // Update each record
                    $totalpaid = 0;
                    foreach ($payments as $pay) {
                        $totalpaid += $pay->paid_amount;
                        $pay->amount         = $request->amount;
                        $pay->pending_amount = $request->amount - $totalpaid;
                        $pay->part_payment   = ($request->amount > $totalPaidAmount) ? 1 : 0;
                        $pay->updated_by   = $this->userId;
                        $pay->save();
                    }

                    // Update bill status
                    $billstatus = ($request->amount == $totalPaidAmount)
                        ? 'paid'
                        : 'part_payment';

                    // Do not change status if it's already "cancel"
                    $transporterbill->status = ($transporterbill->status != 'cancel')
                        ? $billstatus
                        : 'cancel';

                    $transporterbill->save();
                }
            }

            $transporterbill->update([  // update transporter bill data
                'bill_no' => $request->bill_number,
                'bill_date' => $request->bill_date,
                'lr_no' => $request->lr_number,
                'con_no' => $request->container_number,
                'vehicle_no' => $request->vehicle_number,
                'party' => $request->party,
                "amount" => $request->amount ?? 0,
                'updated_by' => $this->userId,
            ]);

            return $this->successresponse(200, 'message', 'transporter bill succesfully updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($this->rp['logisticmodule']['transporterbilling']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $transporterbill = $this->transporter_billingModel::find($id);

        if (!$transporterbill) {
            return $this->successresponse(404, 'message', 'No such transporter bill found!');
        }

        if ($this->rp['logisticmodule']['transporterbilling']['alldata'] != 1) {
            if ($transporterbill->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $transporterbill->update([
            'is_deleted' => 1
        ]);
        

        return $this->successresponse(200, 'message', 'Transporter bill succesfully deleted');
    }

    /**
     * Summary of status
     * update invoice status 
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function statusupdate(Request $request, string $id)
    {
        if ($this->rp['logisticmodule']['transporterbilling']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $transporterbill = $this->transporter_billingModel::where('id', $id)
            ->update([
                'status' => $request->status
            ]);

        if ($transporterbill) {
            return $this->successresponse(200, 'message', 'status updated');
        } else {
            return $this->successresponse(404, 'message', 'status not succesfully updated!');
        }
    }
}
