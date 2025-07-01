<?php

namespace App\Http\Controllers\v4_2_0\api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\LeadRequest;


class tblleadController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $tblleadModel, $lead_recent_activityModel,$tblleadhistoryModel;

    public function __construct(Request $request)
    {
        $this->dbname($request->company_id);
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        if (empty($permissions)) {
            $this->customerrorresponse();
        }
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->tblleadModel = $this->getmodel('tbllead');
        $this->lead_recent_activityModel = $this->getmodel('lead_recent_activity');
        $this->tblleadhistoryModel = $this->getmodel('tblleadhistory');
    }


    /**
     * Summary of leadstatusname
     * get all lead status name from main db
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
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

    /**
     * Summary of leadstagename
     * get all lead stage name from main db
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
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

    // monthly lead chart
    // using in dashboard
    public function monthlyLeadChart(Request $request)
    {
        if ($this->rp['leadmodule']['leaddashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $leads = $this->tblleadModel::select(DB::raw("MONTH(created_at) as month, COUNT(*) as total_leads"))->where('is_deleted', 0);

        if ($this->rp['leadmodule']['leaddashboard']['alldata'] != 1) {
            $leads->where('created_by', $this->userId);
        }

        $leads = $leads->groupBy(DB::raw("MONTH(created_at)"))->get();

        return $leads;
    }

    /**
     * Summary of piechart
     * using in dashboard
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function piechart()
    {
        if ($this->rp['leadmodule']['leaddashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $lead = $this->tblleadModel::where('is_deleted', 0)
            ->select(
                'lead_stage as name',
                DB::raw('count(*) as value')
            );
        if ($this->rp['leadmodule']['leaddashboard']['alldata'] != 1) {
            $lead->where('created_by', $this->userId);
        }

        $lead = $lead->groupBy('lead_stage')->get();

        if ($lead->isEmpty()) {
            return $this->successresponse(404, 'lead', $lead);
        }
        return $this->successresponse(200, 'lead', $lead);
    }

    /**
     * Summary of leadstagechart
     * using in analysis page
     */

    public function leadStageChart(Request $request)
    {
        if ($this->rp['leadmodule']['analysis']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $leadStages = $this->tblleadModel::select('lead_stage', DB::raw('COUNT(*) as total'))
            ->where('is_deleted', 0);

        if ($this->rp['leadmodule']['analysis']['alldata'] != 1) {
            $leadStages->where('created_by', $this->userId);
        }

        $leadStages = $leadStages->groupBy('lead_stage')->get();

        if ($leadStages->isEmpty()) {
            return $this->successresponse(404, 'lead', $leadStages);
        }

        return $this->successresponse(200, 'lead', $leadStages);
    }

    /**
     * Summary of piechart
     * using in lead analysis
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function sourcepiechart()
    {
        if ($this->rp['leadmodule']['analysis']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $lead = $this->tblleadModel::where('is_deleted', 0)
            ->select(
                'source as name',
                DB::raw('count(*) as value')
            );

        if ($this->rp['leadmodule']['analysis']['alldata'] != 1) {
            $lead->where('created_by', $this->userId);
        }

        $lead = $lead->groupBy('source')->get();

        if ($lead->isEmpty()) {
            return $this->successresponse(404, 'lead', $lead);
        }
        return $this->successresponse(200, 'lead', $lead);
    }

    // using in dashboard
    public function leaddashboardhelper()
    {
        if ($this->rp['leadmodule']['leaddashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $today = date('Y-m-d');

        // Total leads (not deleted)
        $totalLeads = $this->tblleadModel::where('is_deleted', 0);

        if ($this->rp['leadmodule']['analysis']['alldata'] != 1) {
            $totalLeads->where('created_by', $this->userId);
        }

        $totalLeads = $totalLeads->count();

        // Converted leads (lead_stage = 'Sale')
        $convertedLeads = $this->tblleadModel::where('is_deleted', 0)
            ->where('lead_stage', 'Sale');

        if ($this->rp['leadmodule']['analysis']['alldata'] != 1) {
            $convertedLeads->where('created_by', $this->userId);
        }

        $convertedLeads = $convertedLeads->count();

        // Due follow-ups (date part of next_follow_up is today)
        $dueFollowUps = $this->tblleadModel::where('is_deleted', 0)
            ->whereDate('next_follow_up', $today);

        if ($this->rp['leadmodule']['analysis']['alldata'] != 1) {
            $dueFollowUps->where('created_by', $this->userId);
        }

        $dueFollowUps = $dueFollowUps->count();

        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;

        return $this->successresponse(200, 'lead', [
            'due_followups' => $dueFollowUps,
            'conversion_rate' => $conversionRate
        ]);
    }

    // using in dashboard
    public function newleadcount(Request $request)
    {
        if ($this->rp['leadmodule']['leaddashboard']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $leadRange = $request->filter_leadrange;

        $query = $this->tblleadModel::where('is_deleted', 0);

        if ($this->rp['leadmodule']['leaddashboard']['alldata'] != 1) {
            $query->where('created_by', $this->userId);
        }

        switch ($leadRange) {
            case 'today':
                $query->whereDate('created_at', now()->toDateString());
                break;

            case 'this_week':
                $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
                break;

            case 'this_month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;

            default:
                // No additional filter
                break;
        }

        $totalLeads = $query->count();

        return $this->successresponse(200, 'newleadcount', $totalLeads);
    }


    /**
     * Summary of userWiseLeadCount
     * using in analysis
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function userWiseLeadCount(Request $request)
    {
        if ($this->rp['leadmodule']['analysis']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        // Get all assigned_to values (comma-separated names)
        $leads = $this->tblleadModel::where('is_deleted', 0)
            ->when($this->rp['leadmodule']['analysis']['alldata'] != 1, function ($q) {
                $q->where('created_by', $this->userId);
            })
            ->pluck('assigned_to');

        // Initialize counters
        $userCounts = collect();
        $unassignedCount = 0;

        foreach ($leads as $assigned) {
            if (empty($assigned)) {
                $unassignedCount++;
                continue;
            }

            foreach (explode(',', $assigned) as $name) {
                $name = trim($name);
                if ($name) {
                    $userCounts[$name] = ($userCounts[$name] ?? 0) + 1;
                }
            }
        }

        // Prepare final data
        $data = $userCounts->map(function ($count, $name) {
            return [
                'user_id' => null,
                'user_name' => $name,
                'lead_count' => $count
            ];
        })->values()->toArray();

        // Add unassigned
        $data[] = [
            'user_id' => null,
            'user_name' => 'Unassigned',
            'lead_count' => $unassignedCount
        ];

        return $this->successresponse(count($data) ? 200 : 404, 'lead', $data);
    }


    /**
     * Summary of userLeadSummary
     * using in lead owner performance
     * @param \Illuminate\Http\Request $request
     */
    public function userLeadSummary(Request $request)
    {
        if ($this->rp['leadmodule']['leadownerperformance']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $leads = $this->tblleadModel::where('is_deleted', 0)
            ->when($this->rp['leadmodule']['leadownerperformance']['alldata'] != 1, function ($q) {
                $q->where('created_by', $this->userId);
            })->get();
        $totalLeads = $leads->count();
        $summary = [];

        foreach ($leads as $lead) {
            $assignedNames = $lead->assigned_to
                ? array_map('trim', explode(',', $lead->assigned_to))
                : ['Unassigned'];

            foreach ($assignedNames as $name) {
                if (!isset($summary[$name])) {
                    $summary[$name] = [
                        'lead_count' => 0,
                        'converted_count' => 0,
                        'delays' => [],
                    ];
                }

                $summary[$name]['lead_count']++;

                if ($lead->lead_stage == 'Sale') {
                    $summary[$name]['converted_count']++;
                }

                if (
                    !empty($lead->next_follow_up) &&
                    Carbon::parse($lead->next_follow_up)->isToday()
                ) {
                    $summary[$name]['delays'][] = $lead;
                }
            }
        }

        // Convert summary to array with percentages
        $data = [];

        foreach ($summary as $owner => $info) {
            $leadCount = $info['lead_count'];
            $converted = $info['converted_count'];
            $leadPercent = $totalLeads > 0 ? round(($leadCount / $totalLeads) * 100, 2) : 0;
            $conversionPercent = $leadCount > 0 ? round(($converted / $leadCount) * 100, 2) : 0;

            $data[] = [
                'owner' => $owner,
                'lead_count' => $leadCount,
                'lead_percent' => $leadPercent,
                'converted_count' => $converted,
                'conversion_percent' => $conversionPercent,
                'delays' => $info['delays']
            ];
        }

        return $this->successresponse(count($data) ? 200 : 404, 'lead', $data);
    }


    /**
     * Summary of followupdueleads
     * using in lead owner performance page
     */

    public function followupDueLeads(Request $request)
    {
        $owner = $request->owner;

        $query = $this->tblleadModel::select(
            'lead_title',
            'first_name',
            'last_name',
            'assigned_to',
            DB::raw("DATE_FORMAT(next_follow_up, '%d-%m-%Y %h:%i:%s %p') as next_follow_up_formatted")
        )
            ->where('is_deleted', 0)
            ->whereDate('next_follow_up', Carbon::today());

        if ($owner && $owner !== 'Unassigned') {
            $query->whereRaw("FIND_IN_SET(?, assigned_to)", [$owner]);
        } else {
            $query->where(function ($q) {
                $q->whereNull('assigned_to')->orWhere('assigned_to', '');
            });
        }

        $leads = $query->get();

        return $this->successresponse(count($leads) ? 200 : 404, 'lead', $leads);
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
            'lead_title',
            'title',
            'budget',
            'company',
            'audience_type',
            'customer_type',
            'status',
            'last_follow_up',
            DB::raw("DATE_FORMAT(next_follow_up, '%d-%m-%Y %h:%i:%s %p') as next_follow_up_formatted"),
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


        $pagetype = $request->page_type; // lead/upcomingfollowup

        if (isset($pagetype)) {
            $leadquery->whereNotNull('next_follow_up');
            if ($this->rp['leadmodule']['upcomingfollowup']['alldata'] != 1) {
                $leadquery->where('created_by', $this->userId);
            }
        } else {
            if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
                $leadquery->where('created_by', $this->userId);
            }
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
            'filter_last_followup_from_date' => 'last_follow_up',
            'filter_last_followup_to_date' => 'last_follow_up',
            'filter_next_followup_from_date' => 'next_follow_up',
            'filter_next_followup_to_date' => 'next_follow_up',
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

        $lead = $leadquery->distinct()->get();

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

        // validate request;
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email',
            'contact_no' => 'nullable|regex:/^\+?[0-9]{1,15}$/|max:15',
            'budget',
            'lead_title' => 'nullable|max:255',
            'title',
            'company',
            'customer_type',
            'status',
            'last_follow_up',
            'next_follow_up',
            'number_of_follow_up',
            'web_url',
            'assignedto',
            'notes',
            'leadstage',
            'created_at',
            'number_of_attempt',
            'source',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }


        $assignedto = implode(',', $request->assignedto ?? []);
        $lead = $this->tblleadModel::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'lead_title' => $request->lead_title,
            'title' => $request->title,
            'budget' => $request->budget,
            'status' => $request->status ?? 'New Lead',
            'company' => $request->company,
            'customer_type' => $request->customer_type,
            'last_follow_up' => $request->last_follow_up,
            'next_follow_up' => $request->next_follow_up,
            'number_of_follow_up' => $request->number_of_follow_up ?? 0,
            'source' => $request->source,
            'lead_stage' => $request->leadstage ?? 'New Lead',
            'web_url' => $request->web_url,
            'assigned_to' => $assignedto,
            'notes' => $request->notes,
            'assigned_by' => $this->userId,
            'created_by' => $this->userId,
            'audience_type' => 'cool',
            'attempt_lead' => 0
        ]);

        if ($lead) {
            $addrecentactivity = $this->managerecentactivity($lead->id, 'Add', 'New Lead Added');
            return $this->successresponse(200, 'message', 'lead succesfully created');
        } else {
            return $this->successresponse(500, 'message', 'lead not succesfully create');
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

        $lead = $this->tblleadModel::select('id', 'first_name', 'last_name', 'email', 'contact_no', 'lead_title', 'title', 'budget', 'company', 'audience_type', 'assigned_to', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'attempt_lead', 'notes', 'lead_stage', 'web_url', 'created_by', 'updated_by', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %h:%i:%s %p') as updated_at_formatted"), 'is_active', 'is_deleted', 'source', 'ip')
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
            'contact_no' => 'nullable|regex:/^\+?[0-9]{1,15}$/|max:15',
            'lead_title' => 'nullable|max:255',
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
            'assignedto',
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
        }


        $lead = $this->tblleadModel::find($id);

        if (!$lead) {
            return $this->successresponse(404, 'message', 'No Such lead Found!');
        }
        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $assignedto = implode(',', $request->assignedto ?? []);
        $lead->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'lead_title' => $request->lead_title,
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

        // add activity log
        $addrecentactivity = $this->managerecentactivity($id, 'Update', 'Lead details updated');

        return $this->successresponse(200, 'message', 'lead succesfully updated');

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

        $this->tblleadhistoryModel::where('leadid', $request->id)->update([
            'is_deleted' => 1
        ]);

        // add activity log
        $addrecentactivity = $this->managerecentactivity($request->id, 'Delete', 'Lead deleted');

        return $this->successresponse(200, 'message', 'lead succesfully deleted');
    }


    /**
     * Summary of bulkdestroy
     * remove lead in bulk
     * @param \Illuminate\Http\Request $request
     * 
     */
    public function bulkdestroy(Request $request)
    {
        if ($this->rp['leadmodule']['lead']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $lead = $this->tblleadModel::whereIn('id', $request->id);

        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            $lead->where('created_by', $this->userId);
        }

        $lead = $lead->get();

        if ($lead->isEmpty()) {
            return $this->successresponse(404, 'message', 'No Such lead Found!');
        }

        // Perform soft delete
        $this->tblleadModel::whereIn('id', $lead->pluck('id'))->update([
            'is_deleted' => 1
        ]);

        $this->tblleadhistoryModel::whereIn('leadid', $lead->pluck('id'))->update([
            'is_deleted' => 1
        ]);

        

        // add activity log
        $addrecentactivity = $this->managerecentactivity($lead->pluck('id')->toArray(), 'Delete', 'Lead deleted');

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

        // add activity log
        $addrecentactivity = $this->managerecentactivity($request->statusid, 'Lead status change', 'Status changed to ' . $request->statusvalue);

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

        // add activity log
        $addrecentactivity = $this->managerecentactivity($request->leadstageid, 'Lead stage status change', 'Lead stage status changed to ' . $request->leadstagevalue);

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


    /**
     * Summary of leadrecentactivityindex
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function leadrecentactivityindex(Request $request)
    {
        if ($this->rp['leadmodule']['recentactivity']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $leadquery = $this->lead_recent_activityModel::leftJoin('tbllead', 'tbllead.id', 'lead_recent_activities.lead_id')
            ->leftJoin($this->masterdbname . '.users', $this->masterdbname . '.users.id', 'lead_recent_activities.created_by')
            ->select(
                'lead_recent_activities.id',
                DB::raw("CONCAT_WS(' ', tbllead.first_name, tbllead.last_name)as lead_name"),
                'tbllead.lead_title',
                'lead_recent_activities.action',
                'lead_recent_activities.description',
                DB::raw("CONCAT_WS(' ', users.firstname, users.lastname)as created_by_name"),
                DB::raw("DATE_FORMAT(lead_recent_activities.created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"),
            )
            ->where('lead_recent_activities.is_deleted', 0);


        $pagetype = $request->page_type; // lead/upcomingfollowup


        if ($this->rp['leadmodule']['recentactivity']['alldata'] != 1) {
            $leadquery->where('lead_recent_activities.created_by', $this->userId);
        }


        $totalcount = $leadquery->get()->count(); // count total record

        $leadrecentactivity = $leadquery->distinct()->get();

        if ($leadrecentactivity->isEmpty()) {
            return DataTables::of($leadrecentactivity)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($leadrecentactivity)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
            ])
            ->make(true);
    }

    /**
     * Summary of managerecentactivity
     * manage lead recent activity
     * @param mixed $leadId
     * @param mixed $action
     * @param mixed $description
     * @return bool
     */
    public function managerecentactivity($leadId, $action, $description)
    {

        if ($this->rp['leadmodule']['recentactivity']['add'] != 1) {
            return false;
        }

        if (empty($leadId) || empty($action) || empty($description)) {
            return false;
        }

        $retentionDays = config('app.recent_activity_retention_days.lead_activity', 90);

        // 1. Remove all activity logs older than 90 days
        $this->lead_recent_activityModel::where('created_at', '<', now()->subDays($retentionDays))->update([
            'is_deleted' => 1
        ]);

        \Log::info('Retention days:', [
            'days' => $retentionDays
        ]);

        // 2. Add the new activity (support array of IDs)
        $leadIds = is_array($leadId) ? $leadId : [$leadId];

        foreach ($leadIds as $id) {
            $this->lead_recent_activityModel::create([
                'lead_id' => $id,
                'action' => $action,
                'description' => $description,
                'created_by' => $this->userId,
            ]);
        }


        return true;
    }


    /**
     * Summary of leadrecentactivitydestroy
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function leadrecentactivitydestroy(Request $request)
    {
        if ($this->rp['leadmodule']['recentactivity']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $recentactivity = $this->lead_recent_activityModel::find($request->id);

        if (!$recentactivity) {
            return $this->successresponse(404, 'message', 'No such activity found!');
        }

        if ($this->rp['leadmodule']['recentactivity']['alldata'] != 1) {
            if ($recentactivity->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $recentactivity->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'Activity succesfully deleted');
    }

}
