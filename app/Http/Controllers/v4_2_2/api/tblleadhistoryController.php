<?php

namespace App\Http\Controllers\v4_2_2\api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class tblleadhistoryController extends commonController
{


    public $userId, $companyId, $masterdbname, $rp, $tblleadModel, $tblleadhistoryModel, $lead_recent_activityModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id ?? session('company_id');
        $this->userId = $request->user_id ?? session('user_id');
        
        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->tblleadModel = $this->getmodel('tbllead');
        $this->tblleadhistoryModel = $this->getmodel('tblleadhistory');
        $this->lead_recent_activityModel = $this->getmodel('lead_recent_activity');
    }

    public function getcalendardata()
    {

        $events = collect();

        if ($this->rp['leadmodule']['calendar']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        // 1. Call History (can be many per lead)
        $callHistories = $this->tblleadhistoryModel::join('tbllead', 'tbllead.id', '=', 'tblleadhistory.leadid')
            ->select(
                'tblleadhistory.call_date',
                'tblleadhistory.history_notes',
                'tblleadhistory.call_status',
                'tbllead.lead_title',
                DB::raw("CONCAT_WS(' ', tbllead.first_name, tbllead.last_name) as lead_name")
            )->where('tblleadhistory.is_deleted', 0);

        if ($this->rp['leadmodule']['calendar']['alldata'] != 1) {
            $callHistories->where('tblleadhistory.created_by', $this->userId);
        }

       $callHistories = $callHistories->get();

        foreach ($callHistories as $history) {
            $events->push([
                'title' => $history->lead_title ?: $history->lead_name,
                'start' => $history->call_date,
                'extendedProps' => [
                    'description' => "<b>Lead Title:</b> {$history->lead_title}<br><b>Lead Name:</b> {$history->lead_name}<br><b>Call Date:</b> {$history->call_date}<br><b>Notes:</b> {$history->history_notes}",
                    'type' => 'call_history',
                ],
            ]);
        }

        // 2. Next Follow-Up (should only be 1 per lead)
        $nextFollowUps = $this->tblleadModel::select(
            'next_follow_up',
            'lead_title',
            DB::raw("CONCAT_WS(' ', first_name, last_name) as lead_name")
        )
            ->whereNotNull('next_follow_up')
            ->distinct()->where('is_deleted', 0); // ensures no duplicates even if queried multiple times

        if ($this->rp['leadmodule']['calendar']['alldata'] != 1) {
            $nextFollowUps->where('tbllead.created_by', $this->userId);
        }

        $nextFollowUps = $nextFollowUps->get();

        foreach ($nextFollowUps as $followUp) {
            $events->push([
                'title' => $followUp->lead_title ?: $followUp->lead_name,
                'start' => $followUp->next_follow_up,
                'extendedProps' => [
                    'description' => "<b>Lead Title:</b> {$followUp->lead_title}<br><b>Lead Name:</b> {$followUp->lead_name}<br><b>Next Follow-Up Date:</b> {$followUp->next_follow_up}",
                    'type' => 'next_follow_up',
                ],
                'type' => 'next_follow_up',
            ]);
        }

         return $this->successresponse(200, 'leaddetails', $events);
      
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
            'call_date' => 'required',
            'history_notes' => 'required',
            'call_status' => 'required',
            'attachment.*' => 'nullable|mimes:jpg,jpeg,png,mp4,webm,pdf|max:10000'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

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
                    $lead->next_follow_up = $request->next_call_date;
                    $lead->save();
                }

                if ($request->hasFile('attachment')) {
                    $timestamp = date('dmY');
                    $attachments = [];
                    foreach ($request->file('attachment') as $attachment) {
                        $attachmentname = $lead->first_name . $timestamp . '-' . uniqid() . '.' . $attachment->getClientOriginalExtension();

                        $dirPath = public_path('uploads/') . $this->companyId . '/lead/callhistory/' . $timestamp;

                        if (!file_exists($dirPath)) {
                            mkdir($dirPath, 0755, true);
                        }

                        // Save the file to the uploads directory
                        if ($attachment->move($dirPath, $attachmentname)) {
                            $attachments[] = $this->companyId . '/lead/callhistory/' . $timestamp . '/' . $attachmentname;
                        }
                    }

                    $leadhistory->attachment = json_encode($attachments); // Store as JSON or any format you prefer
                    $leadhistory->save();
                }

                $this->managerecentactivity($request->leadidid, 'call_added', 'Call history added');

                return $this->successresponse(200, 'message', 'leadhistory succesfully created');
            } else {
                return $this->successresponse(500, 'message', 'leadhistory not succesfully created');
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
        $lead = $this->tblleadhistoryModel::select('id', 'call_date', 'history_notes', 'call_status','attachment','created_by')
            ->where('leadid', $id)
            ->orderBy('id', 'DESC')
            ->get();

        if ($lead->isEmpty()) {
            return $this->successresponse(404, 'leadhistory', $lead);
        }

        if ($this->rp['leadmodule']['lead']['alldata'] != 1) {
            if ($lead[0]->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }


        return $this->successresponse(200, 'leadhistory', $lead);
    }

    public function managerecentactivity($leadId, $action, $description)
    {

        if ($this->rp['leadmodule']['recentactivity']['add'] != 1) {
            return false;
        }

        if (empty($leadId) || empty($action) || empty($description)) {
            return false;
        }

        // 1. Remove all activity logs older than 90 days
        $this->lead_recent_activityModel::where('created_at', '<', now()->subDays(config('app.recent_activity_retention_days.lead_activity')) ?? 90)->update([
            'is_deleted' => 1
        ]);

        Log::info('Retention days:', [
            'days' => config('app.recent_activity_retention_days.lead_activity') ?? 90
        ]);

        // 2. Add the new activity
        $this->lead_recent_activityModel::create([
            'lead_id' => $leadId,
            'action' => $action,
            'description' => $description,
            'created_by' => $this->userId,
        ]);

        return true;
    }

}
