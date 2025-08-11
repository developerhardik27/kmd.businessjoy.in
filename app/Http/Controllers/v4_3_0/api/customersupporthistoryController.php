<?php

namespace App\Http\Controllers\v4_3_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class customersupporthistoryController extends commonController
{
    public $userId, $companyId, $masterdbname, $customer_supportModel, $customersupporthistoryModel,$rp;

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
        $this->customer_supportModel = $this->getmodel('customer_support');
        $this->customersupporthistoryModel = $this->getmodel('customersupporthistory');
    }
 
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'call_date' => 'required',
            'history_notes' => 'required',
            'call_status' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
            $customersupporthistory = $this->customersupporthistoryModel::create([
                'call_date' => $request->call_date,
                'history_notes' => $request->history_notes,
                'call_status' => $request->call_status,
                'created_by' => $this->userId,
                'csid' => $request->csid,
                'companyid' => $this->companyId
            ]);

            if ($customersupporthistory) {

                $followup = 0;
                if ($request->no_of_calls != '') {
                    $followup = $request->no_of_calls;
                }
                $customersupport = $this->customer_supportModel::find($request->csid);

                if ($customersupport) {
                    $customersupport->last_call = $request->call_date;
                    $customersupport->status = $request->call_status;
                    $customersupport->notes = $request->history_notes;
                    $customersupport->number_of_call = $customersupport->number_of_call + $followup;
                    $customersupport->save();
                }

                return $this->successresponse(200, 'message', 'customer history succesfully created');
            } else {
                return $this->successresponse(500, 'message', 'customer history not succesfully created');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customersupporthistory = $this->customersupporthistoryModel::select('id', 'call_date', 'history_notes', 'call_status')
            ->where('csid', $id)
            ->orderBy('id', 'DESC')
            ->get();

        if ($customersupporthistory->isEmpty()) {
            return $this->successresponse(404, 'customersupporthistory', $customersupporthistory);
        }
        
        return $this->successresponse(200, 'customersupporthistory', $customersupporthistory);
    }
  
}
