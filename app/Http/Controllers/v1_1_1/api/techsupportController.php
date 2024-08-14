<?php

namespace App\Http\Controllers\v1_1_1\api;

use App\Http\Controllers\Controller;
use App\Events\StatusChanged;
use App\Listeners\SendStatusChangedEmail;
use App\Mail\PendingReminder;
use App\Mail\Status;
use App\Models\tech_support;
use App\Models\v1_1_1\reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        $this->rp = json_decode($permissions[0]['rp'], true);

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fromdate = $request->fromdate;
        $todate = Carbon::parse($request->todate);
        $status = $request->status;
        $assignedto = $request->assignedto;
        // if (isset($request->activestatusvalue) && $request->activestatusvalue != 'all') {
        //     $activestatus = $request->activestatusvalue;
        // }

        $techsupportquery = DB::table('tech_supports')
            ->select('id', 'first_name', 'last_name', 'email', 'contact_no', 'module_name', 'description', 'attachment', 'issue_type', 'status', 'remarks', 'assigned_to', 'assigned_by', 'ticket', 'user_id', 'company_id', 'created_by', 'updated_by', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'updated_at', 'is_active', 'is_deleted')
            ->where('is_deleted', 0)->orderBy('id', 'DESC');

        if (isset($fromdate) && isset($todate)) {
            $techsupportquery->whereBetween('created_at', [$fromdate, $todate->addDay()]);
        }
        if (isset($status)) {
            $techsupportquery->whereIn('status', $status);
        }
        if (isset($assignedto)) {
            $techsupportquery->where(function ($query) use ($assignedto) {
                foreach ($assignedto as $value) {
                    $query->orWhere('assigned_to', 'LIKE', '%' . $value . '%');
                }
            });
        }

        if ($this->rp['adminmodule']['techsupport']['alldata'] != 1) {
            $techsupportquery->where('user_id', $this->userId);
        }
        $techsupport = $techsupportquery->get();


        if ($techsupport->count() > 0) {
             return $this->successresponse(200, 'techsupport',  $techsupport);
        } else {
             return $this->successresponse(404, 'techsupport', 'No Records Found');
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
            return $this->errorresponse(422,$validator->messages());
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
            ]);

            $techsupportid = tech_support::insertGetId($techsupports);



            if ($techsupportid) {
                $techsupport = tech_support::find($techsupportid);

                if ($techsupport) {
                    $ticket = date('Ymdhis') . $techsupportid;
                    $ticketupdate = $techsupport->update([
                        'ticket' => $ticket
                    ]);
                    if ($ticketupdate) { 
                        try {
                            Mail::to($techsupport->email)
                                ->send(new Status($techsupport));

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
        $techsupport = DB::table('tech_supports')
            ->select('id', 'first_name', 'last_name', 'email', 'contact_no', 'module_name', 'description', 'attachment', 'issue_type', 'status', 'remarks', 'assigned_to', 'assigned_by', 'ticket', 'user_id', 'company_id', 'created_by', 'updated_by', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %h:%i:%s %p') as updated_at_formatted"), 'is_active', 'is_deleted')
            ->where('id', $id)
            ->get();

        if ($techsupport->count() > 0) {
             return $this->successresponse(200, 'techsupport', $techsupport);
        } else {
             return $this->successresponse(404, 'techsupport', $techsupport);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $techsupport = tech_support::find($id);

        if ($this->rp['adminmodule']['techsupport']['edit'] != 1) {
             return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        if ($techsupport) {
             return $this->successresponse(200, 'techsupport', $techsupport);
        } else {
             return $this->successresponse(404, 'message',  "No Such Ticket Found!");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

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
            return $this->errorresponse(422,$validator->messages());
        } else {

            if ($this->rp['adminmodule']['techsupport']['edit'] != 1) {
                 return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

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
        $techsupport = tech_support::find($request->id);

        if ($this->rp['adminmodule']['techsupport']['alldata'] != 1) {
            if ($techsupport->created_by != $this->userId) {
                 return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($this->rp['adminmodule']['techsupport']['delete'] != 1) {
             return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($techsupport) {
            $techsupport->update([
                'is_deleted' => 1

            ]);
             return $this->successresponse(200, 'message', 'techsupport succesfully deleted');
        } else {
             return $this->successresponse(404, 'message', 'No Such Ticket Found!');
        }
    }

    // change status 

    public function changestatus(Request $request)
    {

        $techsupport = DB::table('tech_supports')->where('id', $request->statusid)->get();

        if ($this->rp['adminmodule']['techsupport']['edit'] != 1) {
             return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($techsupport) {

            DB::table('tech_supports')
                ->where('id', $request->statusid)
                ->update(['status' => $request->statusvalue]);

            try {
                $techsupport = tech_support::find($request->statusid);
                Mail::to($techsupport->email)
                    ->send(new Status($techsupport));

                // Email was successfully sent
                $isEmailSent = true;
            } catch (\Exception $e) {
                // An error occurred while sending the email
                $isEmailSent = false;
                Log::error('Failed to send email: ' . $e);
                // Log the error or handle it accordingly
            }

             return $this->successresponse(200, 'message',  'status Succesfully Updated');

        } else {
             return $this->successresponse(404, 'message', 'No Such Ticket Found!');
        }
    }


}
