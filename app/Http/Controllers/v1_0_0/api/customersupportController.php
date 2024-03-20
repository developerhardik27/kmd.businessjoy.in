<?php

namespace App\Http\Controllers\v1_0_0\api;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class customersupportController extends commonController
{

    public $userId, $companyId, $masterdbname,$rp,$customer_supportModel;

    public function __construct(Request $request)
    {
        $this->dbname($request->company_id);
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname =  DB::connection()->getDatabaseName();

         // **** for checking user has permission to action on all data 
         $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
         $permissions = json_decode($user_rp, true);
         $this->rp = json_decode($permissions[0]['rp'], true);

        $this->customer_supportModel = $this->getmodel('customer_support');

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fromdate = $request->fromdate;
        $todate = Carbon::parse($request->todate);
        $status = $request->status;
        $lastcall = $request->lastcall;
        $assignedto = $request->assignedto;
        // if (isset($request->activestatusvalue) && $request->activestatusvalue != 'all') {
        //     $activestatus = $request->activestatusvalue;
        // }

        $customersupportquery = DB::connection('dynamic_connection')->table('customer_support')
            ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_call','assigned_to', 'number_of_call', 'notes', 'ticket','created_by','updated_by', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
            ->where('is_deleted', 0)->orderBy('id', 'DESC');

        if (isset($fromdate) && isset($todate)) {
            $customersupportquery->whereBetween('created_at', [$fromdate, $todate->addDay()]);
        }
        if (isset($status)) {
            $customersupportquery->whereIn('status', $status);
        }
        if (isset($assignedto)) {
            $customersupportquery->where(function ($query) use ($assignedto) {
                foreach ($assignedto as $value) {
                    $query->orWhere('assigned_to', 'LIKE', '%' . $value . '%');
                }
            });
        }
        if (isset($lastcall)) {
            $customersupportquery->where('last_call', $lastcall);
        }
        // if (isset($activestatus)) {
        //     $customersupportquery->where('is_active', $activestatus);
        // }

        if($this->rp['customersupportmodule']['customersupport']['alldata'] != 1){
            $customersupportquery->where('created_by',$this->userId);
        }
        $customersupport = $customersupportquery->get();

        if($this->rp['customersupportmodule']['customersupport']['view'] != 1){
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($customersupport->count() > 0) {
            return response()->json([
                'status' => 200,
                'customersupport' => $customersupport
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'customersupport' => 'No Records Found'
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
            'name' => 'required|string',
            'email' ,
            'contact_no' => 'required',
            'status',
            'last_call',
            'number_of_call',
            'assignedto' => 'required',
            'notes',
            'ticket',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            if($this->rp['customersupportmodule']['customersupport']['add'] != 1){
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }


            $assignedto = implode(',', $request->assignedto);
            $customersupport = $this->customer_supportModel::insertGetId([
                'name'  =>  $request->name,
                'email' =>  $request->email,
                'contact_no' =>  $request->contact_no,
                'status' =>  $request->status,
                'last_call' =>  $request->last_call,
                'number_of_call'  =>  $request->number_of_call,
                'assigned_to' => $assignedto,
                'assigned_by' => $this->userId,
                'created_by' => $this->userId,
                'notes'  =>  $request->notes,
            ]);

            if ($customersupport) {
                $customersupportid = $this->customer_supportModel::find($customersupport);
                if ($customersupportid) {
                    $ticket = date('Ymdhis') . $customersupport;
                    $ticketupdate =  $customersupportid->update([
                        'ticket' => $ticket
                    ]);
                    if ($ticketupdate) {
                        return response()->json([
                            'status' => 200,
                            'message' => 'Ticket succesfully created'
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => 422,
                            'message' => 'Ticket not succesfully created'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Ticket not succesfully created'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Ticket not succesfully created'
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customersupport = DB::connection('dynamic_connection')->table('customer_support')
            ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_call', 'number_of_call','assigned_to', 'notes', 'ticket','created_by','updated_by',DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"),  DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %h:%i:%s %p') as updated_at_formatted"), 'is_active', 'is_deleted')
            ->where('id', $id)
            ->get();
        
            if ($this->rp['customersupportmodule']['customersupport']['alldata'] != 1) {
                if ($customersupport[0]->created_by != $this->userId) {
                    return response()->json([
                        'status' => 500,
                        'message' => "You are Unauthorized!"
                    ]);
                }
            }
            if ($this->rp['customersupportmodule']['customersupport']['view'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }

        if ($customersupport->count() > 0) {
            return response()->json([
                'status' => 200,
                'customersupport' => $customersupport
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'customersupport' => $customersupport
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customersupport = $this->customer_supportModel::find($id);

        if ($this->rp['customersupportmodule']['customersupport']['alldata'] != 1) {
            if ($customersupport->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['customersupportmodule']['customersupport']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }
        if ($customersupport) {
            return response()->json([
                'status' => 200,
                'customersupport' => $customersupport
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such customersupport Found!"
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
            'email' ,
            'contact_no' => 'required',
            'status',
            'last_call',
            'number_of_call',
            'assignedto' => 'required',
            'notes',
            'ticket',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            if ($this->rp['customersupportmodule']['customersupport']['edit'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }

            $ticket = $this->customer_supportModel::find($id);
            if ($ticket) {
                $assignedto = implode(',', $request->assignedto);
                $ticket->update([
                    'name'  =>  $request->name,
                    'email' =>  $request->email,
                    'contact_no' =>  $request->contact_no,
                    'status' =>  $request->status,
                    'last_call' =>  $request->last_call,
                    'number_of_call'  =>  $request->number_of_call,
                    'assigned_to' => $assignedto,
                    'assigned_by' => $this->userId,
                    'updated_by' => $this->userId,
                    'notes'  =>  $request->notes,
                    'ticket'  =>  $request->ticket,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Ticekt succesfully updated'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such Ticket Found!'
                ], 404);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $customersupport = $this->customer_supportModel::find($request->id);

        if ($this->rp['customersupportmodule']['customersupport']['alldata'] != 1) {
            if ($customersupport->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['customersupportmodule']['customersupport']['delete'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($customersupport) {
            $customersupport->update([
                'is_deleted' => 1

            ]);
            return response()->json([
                'status' => 200,
                'message' => 'customersupport succesfully deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such customersupport Found!'
            ], 404);
        }
    }

    // change status 

    public function changestatus(Request $request)
    {
        $customersupport = DB::connection('dynamic_connection')->table('customer_support')->where('id', $request->statusid)->get();
        
        if ($this->rp['customersupportmodule']['customersupport']['alldata'] != 1) {
            if ($customersupport[0]->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['customersupportmodule']['customersupport']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }
       
        if ($customersupport) {

            DB::connection('dynamic_connection')->table('customer_support')
                ->where('id', $request->statusid)
                ->update(['status' => $request->statusvalue]);

            return response()->json([
                'status' => 200,
                'message' => 'status Succesfully Updated'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such customersupport Found!'
            ], 404);
        }
    }

    public function changecustomersupportstage(Request $request)
    {
        $customersupport = DB::connection('dynamic_connection')->table('customer_support')->where('id', $request->customersupportstageid)->get();
        
        if ($this->rp['customersupportmodule']['customersupport']['alldata'] != 1) {
            if ($customersupport[0]->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['customersupportmodule']['customersupport']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($customersupport) {

            DB::connection('dynamic_connection')->table('customer_support')
                ->where('id', $request->customersupportstageid)
                ->update(['customersupport_stage' => $request->customersupportstagevalue]);

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
}
