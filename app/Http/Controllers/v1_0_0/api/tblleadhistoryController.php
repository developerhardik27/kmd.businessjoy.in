<?php

namespace App\Http\Controllers\v1_0_0\api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class tblleadhistoryController extends commonController
{


    public $userId, $companyId, $masterdbname, $rp, $tblleadModel, $tblleadhistoryModel;

    public function __construct(Request $request)
    {
        $this->dbname($request->company_id);
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->tblleadModel = $this->getmodel('tbllead');
        $this->tblleadhistoryModel = $this->getmodel('tblleadhistory');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'call_date' => 'required',
            'history_notes' => 'required',
            'call_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            if ($this->rp['leadmodule']['lead']['add'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }

            $leadhistory = $this->tblleadhistoryModel::create([
                'call_date' => $request->call_date,
                'history_notes' => $request->history_notes,
                'call_status' => $request->call_status,
                'created_by' => $this->userId,
                'leadid' => $request->leadid,
                'companyid' => $this->companyId
            ]);

            if ($leadhistory) {
                $followup = 0;
                if ($request->followup != '') {
                    $followup = $request->followup;
                }
                $lead = $this->tblleadModel::find($request->leadid);

                if ($lead) {
                    // $lead->notes = $request->history_notes;
                    $lead->number_of_follow_up = $lead->number_of_follow_up + $followup;
                    $lead->status = $request->call_status;
                    $lead->last_follow_up = $request->call_date;
                    if ($request->next_call_date != null) {
                        $lead->next_follow_up = $request->next_call_date;
                    }
                    $lead->save();
                }
                return response()->json([
                    'status' => 200,
                    'message' => 'leadhistory succesfully created'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'leadhistory not succesfully created'
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lead = DB::connection('dynamic_connection')->table('tblleadhistory')
            ->select('id', 'call_date', 'history_notes', 'call_status')
            ->where('leadid', $id)
            ->orderBy('id', 'DESC')
            ->get();


        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead[0]->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['leadmodule']['lead']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($lead->count() > 0) {
            return response()->json([
                'status' => 200,
                'leadhistory' => $lead
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'leadhistory' => $lead
            ]);
        }
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
