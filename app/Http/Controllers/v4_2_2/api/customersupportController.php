<?php

namespace App\Http\Controllers\v4_2_2\api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class customersupportController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $customer_supportModel;

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

        $this->customer_supportModel = $this->getmodel('customer_support');

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($this->rp['customersupportmodule']['customersupport']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $customersupportquery = $this->customer_supportModel::select(
            'id',
            DB::raw("CONCAT_WS(' ', first_name, last_name) as name"),
            'email',
            'contact_no',
            'title',
            'budget',
            'audience_type',
            'customer_type',
            'status',
            'last_call',
            'assigned_to',
            'number_of_call',
            'notes',
            'ticket',
            'web_url',
            'created_by',
            'updated_by',
            DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"),
            'updated_at',
            'is_active',
            'is_deleted',
            'source',
            'ip'
        )
            ->where('is_deleted', 0);

        if ($this->rp['customersupportmodule']['customersupport']['alldata'] != 1) {
            $customersupportquery->where('created_by', $this->userId);
        }

        $totalcount = $customersupportquery->get()->count(); // count total record   

        // apply filters
        $filter_assigned_to = $request->filter_assigned_to;

        if (isset($filter_assigned_to)) {
            $customersupportquery->where(function ($query) use ($filter_assigned_to) {
                foreach ($filter_assigned_to as $value) {
                    $query->orWhere('assigned_to', 'LIKE', '%' . $value . '%');
                }
            });
        }

        $filters = [
            'filter_status' => 'status',
            'filter_call_count' => 'number_of_call',
            'filter_last_call' => 'last_call',
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
                    $customersupportquery->whereDate($column, $operator, $value);
                } else if (strpos($requestKey, 'last') !== false) {
                    $customersupportquery->whereDate($column, $value);
                } else if ($requestKey == 'filter_status') {
                    $customersupportquery->whereIn($column, $value);
                } else {
                    // For other filters, apply simple equality checks
                    $customersupportquery->where($column, $value);
                }
            }
        }

        $customersupport = $customersupportquery->get();

        if ($customersupport->isEmpty()) {
            return DataTables::of($customersupport)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($customersupport)
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
        if ($this->rp['customersupportmodule']['customersupport']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email',
            'contact_no' => 'required|regex:/^\+?[0-9]{1,15}$/|max:15',
            'status',
            'last_call',
            'number_of_call',
            'assignedto' => 'required',
            'notes',
            'ticket',
            'web_url',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
            $assignedto = implode(',', $request->assignedto);
            $customersupport = $this->customer_supportModel::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'status' => $request->status,
                'last_call' => $request->last_call,
                'number_of_call' => $request->number_of_call,
                'web_url' => $request->web_url,
                'assigned_to' => $assignedto,
                'assigned_by' => $this->userId,
                'created_by' => $this->userId,
                'notes' => $request->notes,
            ]);

            if ($customersupport) {

                $ticket = date('Ymdhis') . $customersupport->id;
                $ticketupdate = $customersupport->update([
                    'ticket' => $ticket
                ]);
                if ($ticketupdate) {
                    return $this->successresponse(200, 'message', 'Ticket succesfully created');
                } else {
                    return $this->successresponse(422, 'message', 'Ticket not succesfully created');
                }

            } else {
                return $this->successresponse(500, 'message', 'Ticket not succesfully created');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($this->rp['customersupportmodule']['customersupport']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $customersupport = $this->customer_supportModel::select('id', 'first_name', 'last_name', 'email', 'contact_no', 'title', 'budget', 'audience_type', 'customer_type', 'status', 'last_call', 'number_of_call', 'assigned_to', 'notes', 'ticket', 'web_url', 'created_by', 'updated_by', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %h:%i:%s %p') as updated_at_formatted"), 'is_active', 'is_deleted')
            ->where('id', $id)
            ->get();

        if ($customersupport->isEmpty()) {
            return $this->successresponse(404, 'customersupport', $customersupport);
        }

        if ($this->rp['customersupportmodule']['customersupport']['alldata'] != 1) {
            if ($customersupport[0]->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        return $this->successresponse(200, 'customersupport', $customersupport);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if ($this->rp['customersupportmodule']['customersupport']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $customersupport = $this->customer_supportModel::find($id);

        if (!$customersupport) {
            return $this->successresponse(404, 'message', "No Such customersupport Found!");
        }

        if ($this->rp['customersupportmodule']['customersupport']['alldata'] != 1) {
            if ($customersupport->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'customersupport', $customersupport);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($this->rp['customersupportmodule']['customersupport']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email',
            'contact_no' => 'required|regex:/^\+?[0-9]{1,15}$/|max:15',
            'status',
            'last_call',
            'number_of_call',
            'assignedto' => 'required',
            'notes',
            'ticket',
            'web_url',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
            $ticket = $this->customer_supportModel::find($id);
            if ($ticket) {
                $assignedto = implode(',', $request->assignedto);
                $ticket->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_no,
                    'status' => $request->status,
                    'last_call' => $request->last_call,
                    'number_of_call' => $request->number_of_call,
                    'web_url' => $request->web_url,
                    'assigned_to' => $assignedto,
                    'assigned_by' => $this->userId,
                    'updated_by' => $this->userId,
                    'notes' => $request->notes,
                    'ticket' => $request->ticket,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return $this->successresponse(200, 'message', 'Ticekt succesfully updated');
            } else {
                return $this->successresponse(404, 'message', 'No Such Ticket Found!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if ($this->rp['customersupportmodule']['customersupport']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $customersupport = $this->customer_supportModel::find($request->id);

        if (!$customersupport) {
            return $this->successresponse(404, 'message', 'No Such customersupport Found!');
        }

        if ($this->rp['customersupportmodule']['customersupport']['alldata'] != 1) {
            if ($customersupport->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $customersupport->update([
            'is_deleted' => 1

        ]);
        return $this->successresponse(200, 'message', 'customersupport succesfully deleted');
    }

    // change status  
    public function changestatus(Request $request)
    {
        if ($this->rp['customersupportmodule']['customersupport']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $customersupport = $this->customer_supportModel::find($request->statusid);


        if (!$customersupport) {
            return $this->successresponse(404, 'message', 'No Such customersupport Found!');
        }

        if ($this->rp['customersupportmodule']['customersupport']['alldata'] != 1) {
            if ($customersupport[0]->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $this->customer_supportModel::where('id', $request->statusid)
            ->update(['status' => $request->statusvalue]);

        return $this->successresponse(200, 'message', 'status Succesfully Updated');

    }

    public function changecustomersupportstage(Request $request)
    {
        if ($this->rp['customersupportmodule']['customersupport']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $customersupport = $this->customer_supportModel::find($request->customersupportstageid);

        if (!$customersupport) {
            return $this->successresponse(404, 'message', 'No Such Lead Stage Found!');
        }

        if ($this->rp['customersupportmodule']['customersupport']['alldata'] != 1) {
            if ($customersupport[0]->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $this->customer_supportModel::where('id', $request->customersupportstageid)
            ->update(['customersupport_stage' => $request->customersupportstagevalue]);

        return $this->successresponse(200, 'message', 'Lead Stage Succesfully Updated');
    }
}
