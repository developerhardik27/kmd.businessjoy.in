<?php

namespace App\Http\Controllers\v4_1_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class reminderController extends commonController
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
        if(empty($permissions)){
            $this->customerrorresponse();
        }
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->reminderModel = $this->getmodel('reminder');
    }

    // chart monthly reminder counting
    public function monthlyInvoiceChart(Request $request)
    {
        $reminders = $this->reminderModel::select(DB::raw("MONTH(created_at) as month, COUNT(*) as total_reminders"))
            ->groupBy(DB::raw("MONTH(created_at)"))->where('created_by', $this->userId)
            ->where('is_deleted', 0)
            ->get();

        $customers = DB::connection('dynamic_connection')->table('reminder_customer')
            ->select(DB::raw("MONTH(created_at) as month, COUNT(*) as total_customers"))
            ->groupBy(DB::raw("MONTH(created_at)"))->where('created_by', $this->userId)
            ->where('is_deleted', 0)
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
        $reminders = $this->reminderModel::where('created_by', $this->userId)
            ->where('is_deleted', 0)
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


        $reminderquery = $this->reminderModel::join('reminder_customer', 'reminder.customer_id', '=', 'reminder_customer.id')
            ->select('reminder.id', 'reminder_customer.name', 'reminder_customer.area', 'reminder_customer.contact_no', DB::raw("DATE_FORMAT(reminder.next_reminder_date, '%d-%m-%Y')as next_reminder_date"), 'reminder.product_name')
            ->where('reminder.is_deleted', 0)
            ->whereNot('reminder_status', 'completed')
            ->whereDate('reminder.next_reminder_date', '>=', $startDate)
            ->whereDate('reminder.next_reminder_date', '<=', $endDate)
            ->get();

        if ($reminderquery->count() > 0) {
            return $this->successresponse(200, 'reminder', $reminderquery);
        } else {
            return $this->successresponse(404, 'reminder', 'No Records Found');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->rp['remindermodule']['reminder']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $reminderquery = $this->reminderModel::join('reminder_customer', 'reminder.customer_id', '=', 'reminder_customer.id')
            ->join($this->masterdbname . '.country', 'reminder_customer.country_id', '=', 'country.id')
            ->join($this->masterdbname . '.state', 'reminder_customer.state_id', '=', 'state.id')
            ->join($this->masterdbname . '.city', 'reminder_customer.city_id', '=', 'city.id')
            ->select('reminder.id', 'reminder_customer.name', 'reminder_customer.email', 'reminder_customer.contact_no', 'reminder_customer.address', 'reminder_customer.pincode', 'reminder_customer.invoice_id', 'reminder_customer.customer_type', 'reminder_customer.area', 'reminder.customer_id', DB::raw("DATE_FORMAT(reminder.next_reminder_date, '%d-%m-%Y')as next_reminder_date"), 'reminder.before_service_note', 'reminder.after_service_note', 'reminder.reminder_status', 'reminder.service_type', 'reminder.amount', DB::raw("DATE_FORMAT(reminder.service_completed_date, '%d-%m-%Y %h:%i:%s %p') as service_completed_date"), 'reminder.product_unique_id', 'reminder.product_name', 'reminder.created_by', DB::raw("DATE_FORMAT(reminder.created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), 'reminder.updated_at', 'reminder.is_active', 'reminder.is_deleted', 'country.country_name', 'state.state_name', 'city.city_name')
            ->where('reminder.is_deleted', 0);

        if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
            $reminderquery->where('reminder.created_by', $this->userId);
        }

        $totalcount = $reminderquery->get()->count(); // count total record

        //apply filters

        $filter_type = $request->filter_type;

        if (isset($filter_type) && $filter_type != 'all') {
            $reminderquery->where('reminder.service_type', $filter_type);
        }

        $filters = [
            'filter_pincode' => 'reminder_customer.pincode',
            'filter_last_service' => 'reminder.service_completed_date',
            'filter_next_reminder' => 'reminder.next_reminder_date',
            'filter_from_date' => 'reminder.created_at',
            'filter_to_date' => 'reminder.created_at'
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
                    $reminderquery->whereDate($column, $operator, $value);
                } elseif (strpos($requestKey, 'last') !== false || strpos($requestKey, 'next') !== false) {
                    $reminderquery->whereDate($column, $value);
                } else {
                    // For other filters, apply simple equality checks
                    $reminderquery->where($column, $value);
                }
            }
        }

        $multiplefilters = [
            'filter_city' => 'reminder_customer.city_id',
            'filter_area' => 'reminder_customer.area',
            'filter_customer' => 'reminder.customer_id',
            'filter_reminder_status' => 'reminder.reminder_status',
        ];

        // Loop through the filters and apply them conditionally
        foreach ($multiplefilters as $requestKey => $column) {
            $value = $request->$requestKey;

            if (isset($value)) {
                $reminderquery->whereIn($column, $value);
            }
        }

        $reminder = $reminderquery->orderBy('reminder.id', 'DESC')->distinct()->get();

        if ($reminder->isEmpty()) {
            return DataTables::of($reminder)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($reminder)
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
        if ($this->rp['remindermodule']['reminder']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

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
            return $this->errorresponse(422, $validator->messages());
        } else {

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
                return $this->successresponse(200, 'message', 'Reminder succesfully created');
            } else {
                return $this->successresponse(500, 'message', 'Reminder not succesfully create');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($this->rp['remindermodule']['reminder']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $reminder = $this->reminderModel::select('id', 'customer_id', 'next_reminder_date', 'before_service_note', 'after_service_note', 'reminder_status', 'service_type', 'amount', 'service_completed_date', 'product_unique_id', 'product_name', 'created_by', 'updated_by', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i:%s %p') as created_at_formatted"), DB::raw("DATE_FORMAT(updated_at, '%d-%m-%Y %h:%i:%s %p') as updated_at_formatted"), 'is_active', 'is_deleted')
            ->where('id', $id)
            ->get();

        if ($reminder->isEmpty()) {
            return $this->successresponse(404, 'reminder', $reminder);
        }
        if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
            if ($reminder[0]->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'reminder', $reminder);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if ($this->rp['remindermodule']['reminder']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $reminder = $this->reminderModel::find($id);

        if (!$reminder) {
            return $this->successresponse(404, 'message', "No Such Reminder Found!");
        }

        if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
            if ($reminder->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'reminder', $reminder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($this->rp['remindermodule']['reminder']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

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
            return $this->errorresponse(422, $validator->messages());
        } else {
            $reminder = $this->reminderModel::find($id);

            if (!$reminder) {
                return $this->successresponse(404, 'message', 'No Such Reminder Found!');
            }

            if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
                if ($reminder->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

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

            return $this->successresponse(200, 'message', 'Reminder succesfully updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if ($this->rp['remindermodule']['reminder']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $reminder = $this->reminderModel::find($request->id);

        if (!$reminder) {
            return $this->successresponse(404, 'message', 'No Such reminder Found!');
        }

        if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
            if ($reminder->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }


        $reminder->update([
            'is_deleted' => 1
        ]);
        return $this->successresponse(200, 'message', 'reminder succesfully deleted');
    }

    public function changestatus(Request $request)
    {
        if ($this->rp['remindermodule']['reminder']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $reminder = $this->reminderModel::where('id', $request->reminderstatusid)
            ->get();

        if ($reminder->isEmpty()) {
            return $this->successresponse(404, 'message', 'No Such Reminder Found!');
        }
        
        if ($this->rp['remindermodule']['reminder']['alldata'] != 1) {
            if ($reminder[0]->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $this->reminderModel::where('id', $request->reminderstatusid)
            ->update([
                'reminder_status' => $request->reminderstatusvalue,
                'service_completed_date' => $request->last_service_date
            ]);

        return $this->successresponse(200, 'message', 'status Succesfully Updated');
    }

}
