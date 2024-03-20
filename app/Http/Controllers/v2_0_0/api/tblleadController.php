<?php

namespace App\Http\Controllers\v2_0_0\api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class tblleadController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $tblleadModel;

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
    }


    public function leadstatusname(Request $request)
    {
        $leadstatus = DB::table('leadstatus_name')
                ->get();

        if ($this->rp['leadmodule']['lead']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($leadstatus->count() > 0) {
            return response()->json([
                'status' => 200,
                'leadstatus' => $leadstatus
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'leadstatus' => $leadstatus
            ]);
        }
    }
    public function leadstagename(Request $request)
    {
        $lead = DB::table('leadstage')
                ->get();

        if ($this->rp['leadmodule']['lead']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($lead->count() > 0) {
            return response()->json([
                'status' => 200,
                'lead' => $lead
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'lead' => $lead
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fromdate = $request->fromdate;
        $todate = Carbon::parse($request->todate);
        $status = $request->status;
        $source = $request->source;
        $leadstagestatus = $request->leadstagestatus;
        $lastfollowup = $request->lastfollowupdate;
        $nextfollowup = $request->nextfollowupdate;
        $assignedto = $request->assignedto;
        $activestatus = null;
        if (isset ($request->activestatusvalue) && $request->activestatusvalue != 'all') {
            $activestatus = $request->activestatusvalue;
        }

        $leadquery = DB::connection('dynamic_connection')->table('tbllead')
            ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'company', 'audience_type', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'attempt_lead', 'notes', 'lead_stage', 'assigned_to', 'created_by', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
            ->where('is_deleted', 0);

        if (isset ($activestatus)) {
            $leadquery->where('is_active', $activestatus);
        }
        if (isset ($fromdate) && isset ($todate)) {
            $leadquery->whereBetween('created_at', [$fromdate, $todate->addDay()]);
        }
        if (isset ($leadstagestatus)) {
            $leadquery->whereIn('lead_stage', $leadstagestatus);
        }
        if (isset ($status)) {
            $leadquery->whereIn('status', $status);
        }
        if (isset ($source)) {
            $leadquery->whereIn('source', $source);
        }
        if (isset ($leadstagestatus)) {
            $leadquery->whereIn('lead_stage', $leadstagestatus);
        }
        if (isset ($leadstagestatus)) {
            $leadquery->whereIn('lead_stage', $leadstagestatus);
        }
        if (isset ($nextfollowup)) {
            $leadquery->where('next_follow_up', $nextfollowup);
        }
        if (isset ($lastfollowup)) {
            $leadquery->where('last_follow_up', $lastfollowup);
        }
        if (isset ($assignedto)) {
            $leadquery->where(function ($query) use ($assignedto) {
                foreach ($assignedto as $value) {
                    $query->orWhere('assigned_to', 'LIKE', '%' . $value . '%');
                }
            });
        }

        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            $leadquery->where('created_by', $this->userId);
        }

        $lead = $leadquery->orderBy('created_at', 'DESC')->get();

        if ($this->rp['leadmodule']['lead']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }
        if ($lead->count() > 0) {
            return response()->json([
                'status' => 200,
                'lead' => $lead
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'lead' => 'No Records Found'
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
            'leadname' => 'required|string',
            'email',
            'contact_no' => 'required',
            'budget',
            'title',
            'company',
            'customer_type',
            'status',
            'last_follow_up',
            'next_follow_up',
            'number_of_follow_up',
            'assignedto' => 'required',
            'notes',
            'leadstage',
            'created_at',
            'number_of_attempt',
            'source',
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

            $assignedto = implode(',', $request->assignedto);
            $lead = $this->tblleadModel::create([
                'name' => $request->leadname,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'title' => $request->title,
                'budget' => $request->budget,
                'status' => $request->status,
                'company' => $request->company,
                'customer_type' => $request->customer_type,
                'last_follow_up' => $request->last_follow_up,
                'next_follow_up' => $request->next_follow_up,
                'number_of_follow_up' => $request->number_of_follow_up,
                'source' => $request->source,
                'lead_stage' => $request->leadstage,
                'assigned_to' => $assignedto,
                'notes' => $request->notes,
                'assigned_by' => $this->userId,
                'created_by' => $this->userId,
                'audience_type' => 'cool',
                'attempt_lead' => 0
            ]);

            if ($lead) {
                return response()->json([
                    'status' => 200,
                    'message' => 'lead succesfully created'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'lead not succesfully create'
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lead = DB::connection('dynamic_connection')->table('tbllead')
            ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'company', 'audience_type', 'assigned_to', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'attempt_lead', 'notes', 'lead_stage', 'created_by', 'updated_by', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %h:%i:%s %p') as updated_at_formatted"), 'is_active', 'is_deleted', 'source', 'ip')
            ->where('id', $id)
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
                'lead' => $lead
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'lead' => $lead
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lead = $this->tblleadModel::find($id);

        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['leadmodule']['lead']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }
        if ($lead) {
            return response()->json([
                'status' => 200,
                'lead' => $lead
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such lead Found!"
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email',
            'contact_no' => 'required',
            'title',
            'budget',
            'company',
            'audience_type',
            'customer_type',
            'status',
            'last_follow_up',
            'next_follow_up',
            'number_of_follow_up',
            'assignedto' => 'required',
            'notes',
            'leadstage',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted',
            'source',
            'ip',
            'number_of_attempt'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {
            $lead = $this->tblleadModel::find($id);

            if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
                if ($lead->created_by != $this->userId) {
                    return response()->json([
                        'status' => 500,
                        'message' => "You are Unauthorized!"
                    ]);
                }
            }
            if ($this->rp['leadmodule']['lead']['edit'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }
            if ($lead) {
                $assignedto = implode(',', $request->assignedto);
                $lead->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_no,
                    'title' => $request->title,
                    'budget' => $request->budget,
                    'company' => $request->company,
                    'status' => $request->status,
                    'audience_type' => $request->audience_type,
                    'customer_type' => $request->customer_type,
                    'last_follow_up' => $request->last_follow_up,
                    'next_follow_up' => $request->next_follow_up,
                    'number_of_follow_up' => $request->number_of_follow_up,
                    'attempt_lead' => $request->number_of_attempt,
                    'notes' => $request->notes,
                    'lead_stage' => $request->leadstage,
                    'updated_at' => date('Y-m-d'),
                    'updated_by' => $this->userId,
                    'source' => $request->source,
                    'assigned_to' => $assignedto,
                    'assigned_by' => $this->userId
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'lead succesfully updated'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such lead Found!'
                ], 404);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $lead = $this->tblleadModel::find($request->id);

        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['leadmodule']['lead']['delete'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($lead) {
            $lead->update([
                'is_deleted' => 1

            ]);
            return response()->json([
                'status' => 200,
                'message' => 'lead succesfully deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such lead Found!'
            ], 404);
        }
    }

    // change status 

    public function changestatus(Request $request)
    {

        $lead = DB::connection('dynamic_connection')->table('tbllead')->where('id', $request->statusid)->get();

        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead[0]->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['leadmodule']['lead']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($lead) {
            DB::connection('dynamic_connection')->table('tbllead')
                ->where('id', $request->statusid)
                ->update(['status' => $request->statusvalue]);

            return response()->json([
                'status' => 200,
                'message' => 'status Succesfully Updated'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such lead Found!'
            ], 404);
        }
    }

    public function changeleadstage(Request $request)
    {
        $lead = DB::connection('dynamic_connection')->table('tbllead')->where('id', $request->leadstageid)->get();
        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead[0]->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['leadmodule']['lead']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($lead) {

            $leadstage = DB::connection('dynamic_connection')->table('tbllead')
                ->where('id', $request->leadstageid);

            if ($request->leadstagevalue == 'Disqualified') {
                $leadstage->update(['lead_stage' => $request->leadstagevalue, 'is_active' => 0]);
            } else {
                $leadstage->update(['lead_stage' => $request->leadstagevalue, 'is_active' => 1]);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Lead Stage Succesfully Updated'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such Lead Stage Found!'
            ], 404);
        }
    }

    public function sourcevalue()
    {

        $uniqueSources = $this->tblleadModel::distinct()->pluck('source');

        if ($uniqueSources->count() > 0) {
            return response()->json([
                'status' => 200,
                'sourcecolumn' => $uniqueSources
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No any source value  Found!'
            ], 404);
        }

    }

}
