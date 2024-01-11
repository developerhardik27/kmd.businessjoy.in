<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\tbllead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isNull;

class tblleadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fromdate = $request->fromdate;
        $todate = Carbon::parse($request->todate);
        $status = $request->status;
        $lastfollowup = $request->lastfollowupdate;
        $nextfollowup = $request->nextfollowupdate;
        $activestatus = null;
        if (isset($request->activestatusvalue) && $request->activestatusvalue != 'all') {
            $activestatus = $request->activestatusvalue;
        }

        if (isset($fromdate) && isset($todate) && isset($status)) {
            $leadquery = DB::table('tbllead')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'notes', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->whereBetween('created_at', [$fromdate, $todate->addDay()])
                ->whereIn('status', $status)
                ->where('is_deleted',0);

            if (isset($activestatus)) {
                $leadquery->where('is_active', $activestatus);
            }
            $lead = $leadquery->get();
        } elseif (isset($fromdate) && isset($todate) && isNull($status)) {
            $leadquery = DB::table('tbllead')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'notes', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->whereBetween('created_at', [$fromdate, $todate->addDay()])
                ->where('is_deleted',0);
                if (isset($activestatus)) {
                    $leadquery->where('is_active', $activestatus);
                }
                $lead = $leadquery->get();
        } elseif (isset($status) && isNull($fromdate) && isNull($todate)) {
            $leadquery = DB::table('tbllead')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'notes', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->whereIn('status', $status)
                ->where('is_deleted',0);
                if (isset($activestatus)) {
                    $leadquery->where('is_active', $activestatus);
                }
                $lead = $leadquery->get();
        }elseif (isset($lastfollowup) && isset($status)) {
            $leadquery = DB::table('tbllead')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'notes', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->whereIn('status', $status)
                ->where('last_follow_up', $lastfollowup)
                ->where('is_deleted',0);
                if (isset($activestatus)) {
                    $leadquery->where('is_active', $activestatus);
                }
                $lead = $leadquery->get();
        }elseif (isset($nextfollowup) && isset($status)) {
            $leadquery = DB::table('tbllead')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'notes', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->whereIn('status', $status)
                ->where('next_follow_up', $nextfollowup)
                ->where('is_deleted',0);
                if (isset($activestatus)) {
                    $leadquery->where('is_active', $activestatus);
                }
                $lead = $leadquery->get();
        }
        elseif (isset($lastfollowup)) {
            $leadquery = DB::table('tbllead')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'notes', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->where('last_follow_up', $lastfollowup)
                ->where('is_deleted',0);
                if (isset($activestatus)) {
                    $leadquery->where('is_active', $activestatus);
                }
                $lead = $leadquery->get();
        }elseif (isset($nextfollowup)) {
            $leadquery = DB::table('tbllead')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'notes', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->where('next_follow_up', $nextfollowup)
                ->where('is_deleted',0);
                if (isset($activestatus)) {
                    $leadquery->where('is_active',$activestatus);
                }
                $lead = $leadquery->get();
        }
         else {
            $leadquery = tbllead::orderBy('id', 'desc')
                ->where('is_deleted', 0)
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'notes', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip');
                if (isset($activestatus)) {
                    $leadquery->where('is_active', $activestatus);
                } 
                $lead = $leadquery->get();
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
            'email' => 'email',
            'contact_no' => 'required',
            'title'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            $lead = tbllead::create([
                'name' => $request->leadname,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'title' => $request->title,
                'customer_type' => $request->customer_type,
                'audience_type' => 'cool',
                'source' => 'Manual'
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
        $lead = DB::table('tbllead')
            ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_follow_up', 'next_follow_up', 'number_of_follow_up', 'notes', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"),  DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %h:%i:%s %p') as updated_at_formatted"), 'is_active', 'is_deleted', 'source', 'ip')
            ->where('id', $id)
            ->get();

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
        $lead = tbllead::find($id);
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
            'email' => 'required|email',
            'contact_no' => 'required',
            'title' ,
            'budget',
            'audience_type',
            'customer_type',
            'status',
            'last_follow_up',
            'next_follow_up',
            'number_of_follow_up',
            'notes',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted',
            'source',
            'ip'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {
            $lead = tbllead::find($id);
            $active = $request->is_active;
            if(!isset($request->is_active)){
                $active = 0 ;
            }
            if ($lead) {

                $lead->update([
                    'name'  =>  $request->name,
                    'email' =>  $request->email,
                    'contact_no' =>  $request->contact_no,
                    'title'  =>  $request->title,
                    'budget'  =>  $request->budget,
                    'status' =>  $request->status,
                    'last_follow_up' =>  $request->last_follow_up,
                    'next_follow_up'  =>  $request->next_follow_up,
                    'number_of_follow_up'  =>  $request->number_of_follow_up,
                    'notes'  =>  $request->notes,
                    'updated_at' => date('Y-m-d'),
                    'is_active' => $active,
                    'source'  =>  $request->source,
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
        $lead = tbllead::find($request->id);

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


        $lead = DB::table('tbllead')->where('id', $request->statusid)->get();

        if ($lead) {

            DB::table('tbllead')
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

}
