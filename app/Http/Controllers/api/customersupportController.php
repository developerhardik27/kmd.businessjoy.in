<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\customer_support;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isNull;

class customersupportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fromdate = $request->fromdate;
        $todate = Carbon::parse($request->todate);
        $status = $request->status;
        $lastcall = $request->lastcall;
        // if (isset($request->activestatusvalue) && $request->activestatusvalue != 'all') {
        //     $activestatus = $request->activestatusvalue;
        // }

        if (isset($fromdate) && isset($todate) && isset($status)) {
            $customersupportquery = DB::table('customer_support')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_call', 'number_of_call', 'notes', 'ticket', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->whereBetween('created_at', [$fromdate, $todate->addDay()])
                ->whereIn('status', $status)
                ->where('is_deleted', 0);

            // if (isset($activestatus)) {
            //     $customersupportquery->where('is_active', $activestatus);
            // }
            $customersupport = $customersupportquery->get();
        } elseif (isset($fromdate) && isset($todate) && isNull($status)) {
            $customersupportquery = DB::table('customer_support')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_call', 'number_of_call', 'notes', 'ticket', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->whereBetween('created_at', [$fromdate, $todate->addDay()])
                ->where('is_deleted', 0);
            // if (isset($activestatus)) {
            //     $customersupportquery->where('is_active', $activestatus);
            // }
            $customersupport = $customersupportquery->get();
        } elseif (isset($status) && isNull($fromdate) && isNull($todate)) {
            $customersupportquery = DB::table('customer_support')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_call', 'number_of_call', 'notes', 'ticket', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->whereIn('status', $status)
                ->where('is_deleted', 0);
            // if (isset($activestatus)) {
            //     $customersupportquery->where('is_active', $activestatus);
            // }
            $customersupport = $customersupportquery->get();
        } elseif (isset($lastcall) && isset($status)) {
            $customersupportquery = DB::table('customer_support')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_call', 'number_of_call', 'notes', 'ticket', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->whereIn('status', $status)
                ->where('last_call', $lastcall)
                ->where('is_deleted', 0);
            // if (isset($activestatus)) {
            //     $customersupportquery->where('is_active', $activestatus);
            // }
            $customersupport = $customersupportquery->get();
        }  elseif (isset($lastcall)) {
            $customersupportquery = DB::table('customer_support')
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_call', 'number_of_call', 'notes', 'ticket', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip')
                ->where('last_call', $lastcall)
                ->where('is_deleted', 0);
            // if (isset($activestatus)) {
            //     $customersupportquery->where('is_active', $activestatus);
            // }
            $customersupport = $customersupportquery->get();
        } else {
            $customersupportquery = customer_support::orderBy('id', 'desc')
                ->where('is_deleted', 0)
                ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_call', 'number_of_call', 'notes', 'ticket', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted', 'source', 'ip');
            // if (isset($activestatus)) {
            //     $customersupportquery->where('is_active', $activestatus);
            // }
            $customersupport = $customersupportquery->get();
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
            'email' => 'required|email',
            'contact_no' => 'required',
            'status',
            'last_call',
            'number_of_call',
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

            $customersupport = customer_support::insertGetId([
                'name'  =>  $request->name,
                'email' =>  $request->email,
                'contact_no' =>  $request->contact_no,
                'status' =>  $request->status,
                'last_call' =>  $request->last_call,
                'number_of_call'  =>  $request->number_of_call,
                'notes'  =>  $request->notes,
            ]);

            if ($customersupport) {
                $customersupportid = customer_support::find($customersupport);
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
        $customersupport = DB::table('customer_support')
            ->select('id', 'name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_call', 'number_of_call', 'notes','ticket', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"),  DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %h:%i:%s %p') as updated_at_formatted"), 'is_active', 'is_deleted')
            ->where('id', $id)
            ->get();

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
        $customersupport = customer_support::find($id);
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
            'email' => 'required|email',
            'contact_no' => 'required',
            'status',
            'last_call',
            'number_of_call',
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
            $ticket = customer_support::find($id);
            if ($ticket) {
                $ticket->update([
                    'name'  =>  $request->name,
                    'email' =>  $request->email,
                    'contact_no' =>  $request->contact_no,
                    'status' =>  $request->status,
                    'last_call' =>  $request->last_call,
                    'number_of_call'  =>  $request->number_of_call,
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
        $customersupport = customer_support::find($request->id);

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
        $customersupport = DB::table('customer_support')->where('id', $request->statusid)->get();
        if ($customersupport) {

            DB::table('customer_support')
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
        $customersupport = DB::table('customer_support')->where('id', $request->customersupportstageid)->get();
        if ($customersupport) {

            DB::table('customer_support')
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
