<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\tblleadhistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class tblleadhistoryController extends Controller
{
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
            $leadhistory = tblleadhistory::create([
                'call_date' => $request->call_date,
                'history_notes' => $request->history_notes,
                'call_status' => $request->call_status,
                'created_by' => $request->created_by, 
                'leadid' => $request->leadid,
                'companyid' => $request->company_id
            ]);

            if ($leadhistory) {
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
        $lead = DB::table('tblleadhistory')
        ->select('id', 'call_date', 'history_notes','call_status')
        ->where('leadid', $id)
        ->get();

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
