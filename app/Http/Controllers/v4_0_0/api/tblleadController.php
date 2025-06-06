<?php

namespace App\Http\Controllers\v4_0_0\api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
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
        if(empty($permissions)){
            $this->customerrorresponse();
        }
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->tblleadModel = $this->getmodel('tbllead');
    }

    public function leadstatusname(Request $request)
    {
        if ($this->rp['leadmodule']['lead']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $leadstatus = DB::table('leadstatus_name')
            ->get();

        if ($leadstatus->isEmpty()) {
            return $this->successresponse(404, 'leadstatus', $leadstatus);
        }
        return $this->successresponse(200, 'leadstatus', $leadstatus);
    }

    public function leadstagename(Request $request)
    {
        if ($this->rp['leadmodule']['lead']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $lead = DB::table('leadstage')
            ->get();

        if ($lead->isEmpty()) {
            return $this->successresponse(404, 'lead', $lead);
        }
        return $this->successresponse(200, 'lead', $lead);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->rp['leadmodule']['lead']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $leadquery = $this->tblleadModel::select(
            'id',
            DB::raw("CONCAT_WS(' ', first_name, last_name)as name"),
            'email',
            'contact_no',
            'title',
            'budget',
            'company',
            'audience_type',
            'customer_type',
            'status',
            'last_follow_up',
            'next_follow_up',
            'number_of_follow_up',
            'attempt_lead',
            'notes',
            'lead_stage',
            'web_url',
            'assigned_to',
            'created_by',
            DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"),
            'updated_at',
            'is_active',
            'is_deleted',
            'source',
            'ip'
        )
            ->where('is_deleted', 0);

        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            $leadquery->where('created_by', $this->userId);
        }

        $totalcount = $leadquery->get()->count(); // count total record


        // assigned to filter
        $filter_assigned_to = $request->filter_assigned_to;
        if (isset($filter_assigned_to)) {
            $leadquery->where(function ($query) use ($filter_assigned_to) {
                foreach ($filter_assigned_to as $value) {
                    $query->orWhere('assigned_to', 'LIKE', '%' . $value . '%');
                }
            });
        }

        // applyfilters

        $filters = [
            'filter_followup_count' => 'number_of_follow_up',
            'filter_last_followup' => 'last_follow_up',
            'filter_next_followup' => 'next_follow_up',
            'filter_from_date' => 'created_at',
            'filter_to_date' => 'created_at',
        ];

        // Loop through the filters and apply them conditionally
        foreach ($filters as $requestKey => $column) {
            $value = $request->$requestKey;

            if (isset($value)) {
                if (
                    strpos($requestKey, 'from') !== false || strpos($requestKey, 'to') !== false
                ) {
                    // For date filters (loading_date, stuffing_date), we apply range conditions
                    $operator = strpos($requestKey, 'from') !== false ? '>=' : '<=';
                    $leadquery->whereDate($column, $operator, $value);
                } elseif (strpos($requestKey, 'last') !== false || strpos($requestKey, 'next') !== false) {
                    $leadquery->whereDate($column, $value);
                } else {
                    // For other filters, apply simple equality checks
                    $leadquery->where($column, $value);
                }
            }
        }

        //mulitple select filters

        $mulitplefilters = [
            'filter_lead_status' => 'status',
            'filter_lead_stage_status' => 'lead_stage',
            'filter_source' => 'source'
        ];

        // Loop through the filters and apply them conditionally
        foreach ($mulitplefilters as $requestKey => $column) {
            $value = $request->$requestKey;

            if (isset($value)) {
                $leadquery->whereIn($column, $value);
            }
        }

        $lead = $leadquery->orderBy('id', 'DESC')->distinct()->get();

        if ($lead->isEmpty()) {
            return DataTables::of($lead)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($lead)
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
        if ($this->rp['leadmodule']['lead']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email',
            'contact_no' => 'required|regex:/^\+?[0-9]{1,15}$/|max:15',
            'budget',
            'title',
            'company',
            'customer_type',
            'status',
            'last_follow_up',
            'next_follow_up',
            'number_of_follow_up',
            'web_url',
            'assignedto' => 'required',
            'notes',
            'leadstage',
            'created_at',
            'number_of_attempt',
            'source',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else { 
            $assignedto = implode(',', $request->assignedto);
            $lead = $this->tblleadModel::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'title' => $request->title,
                'budget' => $request->budget,
                'status' => $request->status ? $request->status : 'New Lead',
                'company' => $request->company,
                'customer_type' => $request->customer_type,
                'last_follow_up' => $request->last_follow_up,
                'next_follow_up' => $request->next_follow_up,
                'number_of_follow_up' => $request->number_of_follow_up,
                'source' => $request->source,
                'lead_stage' => $request->leadstage ? $request->leadstage : 'New Lead',
                'web_url' => $request->web_url,
                'assigned_to' => $assignedto,
                'notes' => $request->notes,
                'assigned_by' => $this->userId,
                'created_by' => $this->userId,
                'audience_type' => 'cool',
                'attempt_lead' => 0
            ]);

            if ($lead) {
                return $this->successresponse(200, 'message', 'lead succesfully created');
            } else {
                return $this->successresponse(500, 'message', 'lead not succesfully create');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($this->rp['leadmodule']['lead']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $lead = $this->tblleadModel::select('id', 'first_name', 'last_name', 'email', 'contact_no', 'title', 'budget', 'company', 'audience_type', 'assigned_to', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'attempt_lead', 'notes', 'lead_stage', 'web_url', 'created_by', 'updated_by', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %h:%i:%s %p') as updated_at_formatted"), 'is_active', 'is_deleted', 'source', 'ip')
            ->where('id', $id)
            ->get();

        if ($lead->isEmpty()) {
            return $this->successresponse(404, 'lead', 'No lead found!');
        }
        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead[0]->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'lead', $lead);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if ($this->rp['leadmodule']['lead']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $lead = $this->tblleadModel::find($id);

        if (!$lead) {
            return $this->successresponse(404, 'lead', "No Such lead Found!");
        }
        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        return $this->successresponse(200, 'lead', $lead);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($this->rp['leadmodule']['lead']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email',
            'contact_no' => 'required|regex:/^\+?[0-9]{1,15}$/|max:15',
            'title',
            'budget',
            'company',
            'audience_type',
            'customer_type',
            'status',
            'last_follow_up',
            'next_follow_up',
            'number_of_follow_up',
            'web_url',
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
            return $this->errorresponse(422, $validator->messages());
        } else {
            $lead = $this->tblleadModel::find($id);

            if (!$lead) {
                return $this->successresponse(404, 'message', 'No Such lead Found!');
            }
            if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
                if ($lead->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }
            $assignedto = implode(',', $request->assignedto);
            $lead->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
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
                'web_url' => $request->web_url,
                'updated_at' => date('Y-m-d'),
                'updated_by' => $this->userId,
                'source' => $request->source,
                'assigned_to' => $assignedto,
                'assigned_by' => $this->userId
            ]);
            return $this->successresponse(200, 'message', 'lead succesfully updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if ($this->rp['leadmodule']['lead']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $lead = $this->tblleadModel::find($request->id);

        if (!$lead) {
            return $this->successresponse(404, 'message', 'No Such lead Found!');
        }
        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $lead->update([
            'is_deleted' => 1
        ]);
        return $this->successresponse(200, 'message', 'lead succesfully deleted');
    }

    // change status 
    public function changestatus(Request $request)
    {

        if ($this->rp['leadmodule']['lead']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $lead = $this->tblleadModel::find($request->statusid);

        if (!$lead) {
            return $this->successresponse(404, 'message', 'No Such lead Found!');
        }
        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $lead->update([
            'status' => $request->statusvalue
        ]);

        return $this->successresponse(200, 'message', 'status Succesfully Updated');
    }

    public function changeleadstage(Request $request)
    {
        if ($this->rp['leadmodule']['lead']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $lead = $this->tblleadModel::find($request->leadstageid);
        if (!$lead) {
            return $this->successresponse(404, 'message', 'No Such Lead Stage Found!');
        }
        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        if ($request->leadstagevalue == 'Disqualified') {
            $lead->update(['lead_stage' => $request->leadstagevalue, 'is_active' => 0]);
        } else {
            $lead->update(['lead_stage' => $request->leadstagevalue, 'is_active' => 1]);
        }
        return $this->successresponse(200, 'message', 'Lead Stage Succesfully Updated');
    }

    public function sourcevalue()
    {

        $uniqueSources = $this->tblleadModel::distinct()->pluck('source');

        if ($uniqueSources->isEmpty()) {
            return $this->successresponse(404, 'message', 'No any source value  Found!');
        }
        return $this->successresponse(200, 'sourcecolumn', $uniqueSources);

    }

}
