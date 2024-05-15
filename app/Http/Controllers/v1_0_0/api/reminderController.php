<?php

namespace App\Http\Controllers\v1_0_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class remindercontroller extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $reminderModel;

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

        $this->reminderModel = $this->getmodel('reminder');
    }




    // chart monthly reminder counting
    public function monthlyInvoiceChart(Request $request)
    {
        $reminders = DB::connection('dynamic_connection')->table('reminder')
            ->select(DB::raw("MONTH(created_at) as month, COUNT(*) as total_reminders"))
            ->groupBy(DB::raw("MONTH(created_at)"))->where('created_by', $this->userId)
            ->where('is_deleted',0)
            ->get();

        $customers = DB::connection('dynamic_connection')->table('reminder_customer')
            ->select(DB::raw("MONTH(created_at) as month, COUNT(*) as total_customers"))
            ->groupBy(DB::raw("MONTH(created_at)"))->where('created_by', $this->userId)
            ->where('is_deleted',0)
            ->get();

        $combinedData = [
            'reminders' => $reminders,
            'customers' => $customers
        ];

        return $combinedData;
    }

    //status vise invoice list
    public function status_list(Request $request)
    {
        $reminders = DB::connection('dynamic_connection')->table('reminder')
                     ->where('created_by', $this->userId)
                     ->where('is_deleted',0)
                     ->get();
        $groupedReminders = $reminders->groupBy('reminder_status');
        return $groupedReminders;
    }

    // get reminder by by days 
    public function getRemindersByDays(Request $request)
    {
        $days = $request->days;


        // Calculate the start and end dates based on the number of days passed
        $startDate = Carbon::now()->setTimezone('Asia/Kolkata')->toDateString(); // start date is today

        // end date is 'days' days  from today
        $endDate = Carbon::now()->setTimezone('Asia/Kolkata')->addDays($days)->toDateString();


        $reminderquery = DB::connection('dynamic_connection')->table('reminder')
            ->join('reminder_customer', 'reminder.customer_id', '=', 'reminder_customer.id')
            ->select('reminder.id', 'reminder_customer.name','reminder_customer.area', 'reminder_customer.contact_no', DB::raw("DATE_FORMAT(reminder.next_reminder_date, '%d-%m-%Y')as next_reminder_date"), 'reminder.product_name')
            ->where('reminder.is_deleted', 0)
            ->whereNot('reminder_status','completed')
            ->whereDate('reminder.next_reminder_date', '>=', $startDate)
            ->whereDate('reminder.next_reminder_date', '<=', $endDate)
            ->get();

        if ($reminderquery->count() > 0) {
            return response()->json([
                'status' => 200,
                'reminder' => $reminderquery
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'reminder' => 'No Records Found'
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fromdate = $request->fromdate;
        $todate = Carbon::parse($request->todate);
        $pincode = $request->pincode;
        $reminder_status = $request->reminder_status;
        $city = $request->city;
        $area = $request->area;
        $last_service = $request->last_service;
        $next_reminder = $request->next_reminder;
        $customer = $request->customer;
        $activestatus = null;
        if (isset($request->activestatusvalue) && $request->activestatusvalue != 'all') {
            $activestatus = $request->activestatusvalue;
        }

        $reminderquery = DB::connection('dynamic_connection')->table('reminder')
            ->join('reminder_customer', 'reminder.customer_id', '=', 'reminder_customer.id')
            ->join($this->masterdbname . '.country', 'reminder_customer.country_id', '=', 'country.id')
            ->join($this->masterdbname . '.state', 'reminder_customer.state_id', '=', 'state.id')
            ->join($this->masterdbname . '.city', 'reminder_customer.city_id', '=', 'city.id')
            ->select('reminder.id', 'reminder_customer.name', 'reminder_customer.email', 'reminder_customer.contact_no', 'reminder_customer.address', 'reminder_customer.pincode', 'reminder_customer.invoice_id', 'reminder_customer.customer_type', 'reminder_customer.area', 'reminder.customer_id', DB::raw("DATE_FORMAT(reminder.next_reminder_date, '%d-%m-%Y')as next_reminder_date"), 'reminder.before_service_note', 'reminder.after_service_note', 'reminder.reminder_status', 'reminder.service_type', 'reminder.amount', DB::raw("DATE_FORMAT(reminder.service_completed_date, '%d-%m-%Y %h:%i:%s %p') as service_completed_date"), 'reminder.product_unique_id', 'reminder.product_name', 'reminder.created_by', DB::raw("DATE_FORMAT(reminder.created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'reminder.updated_at', 'reminder.is_active', 'reminder.is_deleted', 'country.country_name', 'state.state_name', 'city.city_name')
            ->where('reminder.is_deleted', 0);

        if (isset($activestatus)) {
            $reminderquery->where('reminder.service_type', $activestatus);
        }
        if (isset($fromdate) && isset($todate)) {
            $reminderquery->whereBetween('reminder.created_at', [$fromdate, $todate->addDay()]);
        }
        if (isset($area)) {
            $reminderquery->whereIn('reminder_customer.area', $area);
        }
        if (isset($customer)) {
            $reminderquery->whereIn('reminder.customer_id', $customer);
        }
        if (isset($city)) {
            $reminderquery->whereIn('reminder_customer.city_id', $city);
        }
        if (isset($reminder_status)) {
            $reminderquery->whereIn('reminder.reminder_status', $reminder_status);
        }
        if (isset($pincode)) {
            $reminderquery->where('reminder_customer.pincode', $pincode);
        }
        if (isset($next_reminder)) {
            $reminderquery->whereDate('reminder.next_reminder_date', $next_reminder);
        }
        if (isset($last_service)) {
            $reminderquery->whereDate('reminder.service_completed_date', $last_service);
        }

        if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
            $reminderquery->where('reminder.created_by', $this->userId);
        }

        $reminder = $reminderquery->orderBy('reminder.id', 'DESC')->distinct()->get();

        if ($this->rp['remindermodule']['reminder']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }
        if ($reminder->count() > 0) {
            return response()->json([
                'status' => 200,
                'reminder' => $reminder
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'reminder' => 'No Records Found'
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
            'customer_id' => 'required|numeric',
            'service_type' => 'required|string',
            'product_name' => 'string|nullable',
            'product_unique_id' => 'string|nullable',
            'amount' => 'numeric|nullable',
            'reminder_status' => 'string|nullable',
            'service_completed_date' => 'nullable',
            'next_reminder' => 'required',
            'before_services_notes' => 'nullable',
            'after_services_notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            if ($this->rp['remindermodule']['reminder']['add'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }

            $reminder = $this->reminderModel::create([
                'customer_id' => $request->customer_id,
                'service_type' => $request->service_type,
                'product_name' => $request->product_name,
                'product_unique_id' => $request->product_unique_id,
                'amount' => $request->amount,
                'reminder_status' => 'pending',
                'next_reminder_date' => $request->next_reminder,
                'before_service_note' => $request->before_services_notes,
                'after_service_note' => $request->after_services_notes,
                'created_by' => $this->userId,
            ]);

            if ($reminder) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Reminder succesfully created'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Reminder not succesfully create'
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reminder = DB::connection('dynamic_connection')->table('reminder')
            ->select('id', 'customer_id', 'next_reminder_date', 'before_service_note', 'after_service_note', 'reminder_status', 'service_type', 'amount', 'service_completed_date', 'product_unique_id', 'product_name', 'created_by', 'updated_by', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %h:%i:%s %p') as updated_at_formatted"), 'is_active', 'is_deleted')
            ->where('id', $id)
            ->get();

        if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
            if ($reminder[0]->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['remindermodule']['reminder']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($reminder->count() > 0) {
            return response()->json([
                'status' => 200,
                'reminder' => $reminder
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'reminder' => $reminder
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $reminder = $this->reminderModel::find($id);

        if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
            if ($reminder->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['remindermodule']['reminder']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }
        if ($reminder) {
            return response()->json([
                'status' => 200,
                'reminder' => $reminder
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such Reminder Found!"
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|numeric',
            'service_type' => 'required|string',
            'product_name' => 'string|nullable',
            'product_unique_id' => 'string|nullable',
            'amount' => 'numeric|nullable',
            'reminder_status' => 'string|required',
            'service_completed_date' => 'nullable',
            'next_reminder' => 'required',
            'before_services_notes' => 'nullable',
            'after_services_notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {
            $reminder = $this->reminderModel::find($id);

            if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
                if ($reminder->created_by != $this->userId) {
                    return response()->json([
                        'status' => 500,
                        'message' => "You are Unauthorized!"
                    ]);
                }
            }
            if ($this->rp['remindermodule']['reminder']['edit'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }
            if ($reminder) {
                $reminder->update([
                    'customer_id' => $request->customer_id,
                    'service_type' => $request->service_type,
                    'product_name' => $request->product_name,
                    'product_unique_id' => $request->product_unique_id,
                    'amount' => $request->amount,
                    'reminder_status' => $request->reminder_status,
                    'service_completed_date' => $request->service_completed_date,
                    'next_reminder_date' => $request->next_reminder,
                    'before_service_note' => $request->before_services_notes,
                    'after_service_note' => $request->after_services_notes,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->userId
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Reminder succesfully updated'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such Reminder Found!'
                ], 404);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $reminder = $this->reminderModel::find($request->id);

        if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
            if ($reminder->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['remindermodule']['reminder']['delete'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($reminder) {
            $reminder->update([
                'is_deleted' => 1

            ]);
            return response()->json([
                'status' => 200,
                'message' => 'reminder succesfully deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such reminder Found!'
            ], 404);
        }
    }

    public function changestatus(Request $request)
    {

        $reminder = DB::connection('dynamic_connection')->table('reminder')->where('id', $request->statusid)->get();

        if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
            if ($reminder[0]->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($this->rp['remindermodule']['reminder']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($reminder) {
            DB::connection('dynamic_connection')->table('reminder')
                ->where('id', $request->reminderstatusid)
                ->update([
                    'reminder_status' => $request->reminderstatusvalue,
                    'service_completed_date' => $request->last_service_date
                ]);

            return response()->json([
                'status' => 200,
                'message' => 'status Succesfully Updated'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such Reminder Found!'
            ], 404);
        }
    }

}
