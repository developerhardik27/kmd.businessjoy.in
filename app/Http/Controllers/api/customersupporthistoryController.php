<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\customer_support;
use App\Models\customersupporthistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class customersupporthistoryController extends commonController
{
    public $userId, $companyId, $masterdbname;

    public function __construct(Request $request)
    {
        $this->dbname($request->company_id);
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname =  DB::connection()->getDatabaseName();
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
            $customersupporthistory = customersupporthistory::create([
                'call_date' => $request->call_date,
                'history_notes' => $request->history_notes,
                'call_status' => $request->call_status,
                'created_by' => $this->userId,
                'csid' => $request->csid,
                'companyid' => $this->companyId
            ]);

            if ($customersupporthistory) {

                $followup  = 0 ;
                if($request->no_of_calls != ''){
                   $followup = $request->no_of_calls ;
                }
               $customersupport = customer_support::find($request->csid);
              
               if($customersupport){
                   $customersupport->last_call = $request->call_date;
                   $customersupport->status = $request->call_status;
                   $customersupport->notes = $request->history_notes;
                   $customersupport->number_of_call = $customersupport->number_of_call +  $followup;
                   $customersupport->save();
               }
                return response()->json([
                    'status' => 200,
                    'message' => 'customer history succesfully created'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'customer history not succesfully created'
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customersupporthistory = DB::connection('dynamic_connection')->table('customersupporthistory')
            ->select('id', 'call_date', 'history_notes', 'call_status')
            ->where('csid', $id)
            ->orderBy('id', 'DESC')
            ->get();

        if ($customersupporthistory->count() > 0) {
            return response()->json([
                'status' => 200,
                'customersupporthistory' => $customersupporthistory
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'customersupporthistory' => $customersupporthistory
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
