<?php

namespace App\Http\Controllers\v4_2_0\api;

use App\Mail\Status;
use App\Models\User;
use App\Models\tech_support;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class techsupportController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp;

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

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

         if ($this->rp['adminmodule']['techsupport']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $techsupportquery = DB::table('tech_supports')
            ->leftJoin('company', 'tech_supports.company_id', 'company.id')
            ->LeftJoin('company_details', 'company.company_details_id', 'company_details.id')
            ->select(
                'tech_supports.id',
                'tech_supports.first_name',
                'tech_supports.last_name',
                'tech_supports.email',
                'tech_supports.contact_no',
                'tech_supports.module_name',
                'tech_supports.description',
                'tech_supports.attachment',
                'tech_supports.issue_type',
                'tech_supports.status',
                'tech_supports.remarks',
                'tech_supports.assigned_to',
                'tech_supports.assigned_by',
                'tech_supports.ticket',
                'tech_supports.user_id',
                'tech_supports.company_id',
                'tech_supports.created_by',
                'tech_supports.updated_by',
                DB::raw("DATE_FORMAT(tech_supports.created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"),
                'tech_supports.updated_at',
                'tech_supports.is_active',
                'tech_supports.is_deleted',
                'company_details.name as company_name'
            )
            ->where('tech_supports.is_deleted', 0);


        $user = User::find($this->userId);

        if ($this->rp['adminmodule']['techsupport']['alldata'] == 1) {
            if ($this->userId != 1) {
                $techsupportquery->where(function ($query) use ($user) {
                    $query->where('tech_supports.company_id', $this->companyId)
                        ->orWhere(function ($q) use ($user) {
                            $q->where('tech_supports.assigned_to', 'LIKE', $user->id . ',%')
                                ->orWhere('tech_supports.assigned_to', 'LIKE', '%,' . $user->id . ',%')
                                ->orWhere('tech_supports.assigned_to', 'LIKE', '%,' . $user->id)
                                ->orWhere('tech_supports.assigned_to', 'LIKE', $user->id);
                        });
                });
            }
        } else {
            $techsupportquery->where(function ($query) use ($user) {
                $query->where('tech_supports.user_id', $this->userId)
                    ->orWhere(function ($q) use ($user) {
                        $q->where('tech_supports.assigned_to', 'LIKE', $user->id . ',%')
                            ->orWhere('tech_supports.assigned_to', 'LIKE', '%,' . $user->id . ',%')
                            ->orWhere('tech_supports.assigned_to', 'LIKE', '%,' . $user->id)
                            ->orWhere('tech_supports.assigned_to', 'LIKE', $user->id);
                    });
            });
        }

         $totalcount = $techsupportquery->get()->count(); // count total record

        $filters = [
            'filter_status' => 'tech_supports.status',
            'filter_from_date' => 'tech_supports.created_at',
            'filter_to_date' => 'tech_supports.created_at',
        ];

        // Loop through the filters and apply them conditionally
        foreach ($filters as $requestKey => $column) {
            $value = $request->$requestKey;
            if (isset($value)) {
                if (str_contains($requestKey, '_from')) {
                    // Apply >= condition for "from" dates
                    $techsupportquery->whereDate($column, '>=', $value);
                } elseif (str_contains($requestKey, '_to')) {
                    // Apply <= condition for "to" dates
                    $techsupportquery->whereDate($column, '<=', $value);
                } else if ($requestKey == 'filter_status') {
                    $techsupportquery->whereIn($column, $value);
                } else {
                    // Apply exact match for non-date fields like "name"
                    $techsupportquery->where($column, $value);
                }
            }
        }

        $filter_assigned_to = $request->filter_assigned_to;
        if (isset($filter_assigned_to)) {
            $assignedtoValues = is_array($filter_assigned_to) ? $filter_assigned_to : explode(',', $filter_assigned_to);
            $techsupportquery->where(function ($query) use ($assignedtoValues) {
                foreach ($assignedtoValues as $value) {
                    // Exact match for the value surrounded by commas or being at the start/end
                    $query->orWhere(function ($q) use ($value) {
                        $q->where('tech_supports.assigned_to', 'LIKE', $value . ',%')
                            ->orWhere('tech_supports.assigned_to', 'LIKE', '%,' . $value . ',%')
                            ->orWhere('tech_supports.assigned_to', 'LIKE', '%,' . $value)
                            ->orWhere('tech_supports.assigned_to', 'LIKE', $value);
                    });
                }
            });
        }
 
        $techsupport = $techsupportquery->get();

        if ($techsupport->isEmpty()) {
             return DataTables::of($techsupport)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }
         return DataTables::of($techsupport)
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
        if ($this->rp['adminmodule']['techsupport']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'contact_no' => 'required|regex:/^\+?[0-9]{1,15}$/|max:15',
            'modulename' => 'required',
            'description' => 'required',
            'attachment.*' => 'nullable|mimes:jpg,jpeg,png,mp4,webm,pdf|max:10000',
            'assignedto',
            'issuetype' => 'required',
            'status',
            'remarks',
            'ticket',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $techsupportdata = [];
            $attachments = [];

            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $attachment) {
                    $attachmentname = $request->first_name . time() . '-' . uniqid() . '.' . $attachment->getClientOriginalExtension();

                    if (!file_exists('uploads/files/')) {
                        mkdir('uploads/files/', 0755, true);
                    }
                    // Save the file to the uploads directory
                    if ($attachment->move('uploads/files/', $attachmentname)) {
                        $attachments[] = $attachmentname;
                    }
                }
                $techsupportdata['attachment'] = json_encode($attachments); // Store as JSON or any format you prefer
            }

            $techsupports = array_merge($techsupportdata, [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'module_name' => $request->modulename,
                'description' => $request->description,
                'issue_type' => $request->issuetype,
                'user_id' => $this->userId,
                'company_id' => $this->companyId,
                'created_by' => $this->userId,
                'status' => 'pending'
            ]);

            $techsupport = tech_support::create($techsupports);

            if ($techsupport) {
                $ticket = date('Ymdhis') . $techsupport->id;
                $ticketupdate = $techsupport->update([
                    'ticket' => $ticket
                ]);
                if ($ticketupdate) {
                    try {
                        Mail::to($techsupport->email)->bcc(config('app.bcc_mail_id'))->send(new Status($techsupport));

                        // Email was successfully sent
                        $isEmailSent = true;
                    } catch (\Exception $e) {
                        // An error occurred while sending the email
                        Log::error('Failed to send email: ' . $e);

                        // Log the error or handle it accordingly
                    }
                    return $this->successresponse(200, 'message', 'Ticket succesfully created');
                } else {
                    return $this->successresponse(500, 'message', 'Ticket not succesfully created');
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
        if ($this->rp['adminmodule']['techsupport']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $techsupport = DB::table('tech_supports')
            ->select('id', 'first_name', 'last_name', 'email', 'contact_no', 'module_name', 'description', 'attachment', 'issue_type', 'status', 'remarks', 'assigned_to', 'assigned_by', 'ticket', 'user_id', 'company_id', 'created_by', 'updated_by', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %h:%i:%s %p') as updated_at_formatted"), 'is_active', 'is_deleted')
            ->where('id', $id)
            ->get();

        if ($techsupport->isEmpty()) {
            return $this->successresponse(404, 'techsupport', $techsupport);
        }
        return $this->successresponse(200, 'techsupport', $techsupport);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if ($this->rp['adminmodule']['techsupport']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $techsupport = tech_support::find($id);

        if (!$techsupport) {
            return $this->successresponse(404, 'message', "No Such Ticket Found!");
        } 
        return $this->successresponse(200, 'techsupport', $techsupport);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        if ($this->rp['adminmodule']['techsupport']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'contact_no' => 'required|regex:/^\+?[0-9]{1,15}$/|max:15',
            'modulename' => 'required',
            'description' => 'required',
            'attachment' => 'nullable|mimetypes:video/mp4,video/webm|max:10000',
            'assignedto',
            'issuetype' => 'required',
            'status' => 'required',
            'remarks',
            'ticket' => 'required',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $ticket = tech_support::find($id);
            if ($ticket) {
                $assignedto = null;
                if ($request->assignedto != null) {
                    $assignedto = implode(',', $request->assignedto);
                }
                $ticket->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_no,
                    'issue_type' => $request->issuetype,
                    'module_name' => $request->modulename,
                    'status' => $request->status,
                    'assigned_to' => $assignedto,
                    'assigned_by' => $this->userId,
                    'description' => $request->description,
                    'remarks' => $request->remarks,
                    'updated_by' => $this->userId,
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
        if ($this->rp['adminmodule']['techsupport']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $techsupport = tech_support::find($request->id);

        if (!$techsupport) {
            return $this->successresponse(404, 'message', 'No Such Ticket Found!');
        }
        if ($this->rp['adminmodule']['techsupport']['alldata'] != 1) {
            if ($techsupport->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        } 

        $techsupport->update([
            'is_deleted' => 1

        ]);
        return $this->successresponse(200, 'message', 'techsupport succesfully deleted');
    }

    // change status 

    public function changestatus(Request $request)
    {

        if ($this->rp['adminmodule']['techsupport']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $techsupport = DB::table('tech_supports')->where('id', $request->statusid)->get();

        if ($techsupport) {

            DB::table('tech_supports')
                ->where('id', $request->statusid)
                ->update(['status' => $request->statusvalue]);

            try {
                $techsupport = tech_support::find($request->statusid);
                Mail::to($techsupport->email)->bcc(config('app.bcc_mail_id'))->send(new Status($techsupport));

                // Email was successfully sent
                $isEmailSent = true;
            } catch (\Exception $e) {
                // An error occurred while sending the email
                $isEmailSent = false;
                Log::error('Failed to send email: ' . $e);
                // Log the error or handle it accordingly
            }

            return $this->successresponse(200, 'message', 'status Succesfully Updated');

        } else {
            return $this->successresponse(404, 'message', 'No Such Ticket Found!');
        }
    }

}
