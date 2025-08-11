<?php

namespace App\Http\Controllers\v4_2_3\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class customerController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $customerModel, $invoice_other_settingModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        
        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->customerModel = $this->getmodel('customer');
        $this->invoice_other_settingModel = $this->getmodel('invoice_other_setting');
    }


    //customer list who has invoice module permission
    public function invoicecustomer(Request $request)
    {
        if ($this->rp['invoicemodule']['customer']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $customersres = $this->customerModel::leftjoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftjoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftjoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->select('customers.id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.house_no_building_name', 'customers.road_name_area_colony', 'country.country_name', 'state.state_name', 'city.city_name', 'customers.pincode', 'customers.gst_no', 'customers.company_id', 'customers.created_by', 'customers.updated_by', 'customers.created_at', 'customers.updated_at')
            ->where('customers.is_deleted', 0)->where('customers.is_active', 1);

        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            $customersres->where('customers.created_by', $this->userId);
        }

        $customers = $customersres->get();


        if ($customers->isEmpty()) {
            return $this->successresponse(404, 'customer', 'No Records Found');

        }

        return $this->successresponse(200, 'customer', $customers);

    }

    //customer list who has quotation module permission
    public function quotationcustomer(Request $request)
    {
        if ($this->rp['quotationmodule']['quotationcustomer']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $customersres = $this->customerModel::leftjoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftjoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftjoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->select('customers.id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.house_no_building_name', 'customers.road_name_area_colony', 'country.country_name', 'state.state_name', 'city.city_name', 'customers.pincode', 'customers.gst_no', 'customers.company_id', 'customers.created_by', 'customers.updated_by', 'customers.created_at', 'customers.updated_at')
            ->where('customers.is_deleted', 0)->where('customers.is_active', 1);

        if ($this->rp['quotationmodule']['quotationcustomer']['alldata'] != 1) {
            $customersres->where('customers.created_by', $this->userId);
        }

        $customers = $customersres->get();

        if ($customers->isEmpty()) {
            return $this->successresponse(404, 'customer', 'No records found');
        }

        return $this->successresponse(200, 'customer', $customers);


    }

    public function datatable()
    {

        if ($this->rp['invoicemodule']['customer']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $customersres = $this->customerModel::leftjoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftjoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftjoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftjoin($this->masterdbname . '.users', 'customers.created_by', '=', $this->masterdbname . '.users.id')
            ->select('customers.id', 'customers.customer_id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.house_no_building_name', 'customers.road_name_area_colony', 'country.country_name', 'state.state_name', 'city.city_name', 'customers.pincode', 'customers.gst_no', 'customers.company_id', 'customers.created_by', 'customers.updated_by', DB::raw("DATE_FORMAT(customers.created_at, '%d-%M-%Y %h:%i %p') as created_at_formatted"), 'customers.updated_at', 'customers.is_active', 'users.firstname as createdby_fname', 'users.lastname as createdby_lname')
            ->where('customers.is_deleted', 0);

        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            $customersres->where('customers.created_by', $this->userId);
        }

        $totalcount = $customersres->get()->count(); // count total record

        $customers = $customersres->get();

        if ($customers->isEmpty()) {
            return DataTables::of($customers)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($customers)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
            ])
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->rp['invoicemodule']['customer']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are unauthorized');
        }

        $customersres = $this->customerModel::leftjoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftjoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftjoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftjoin($this->masterdbname . '.users', 'customers.created_by', '=', $this->masterdbname . '.users.id')
            ->select('customers.id', 'customers.customer_id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.house_no_building_name', 'customers.road_name_area_colony', 'country.country_name', 'state.state_name', 'city.city_name', 'customers.pincode', 'customers.gst_no', 'customers.company_id', 'customers.created_by', 'customers.updated_by', DB::raw("DATE_FORMAT(customers.created_at, '%d-%M-%Y %h:%i %p') as created_at_formatted"), 'customers.updated_at', 'customers.is_active', 'users.firstname as createdby_fname', 'users.lastname as createdby_lname')
            ->where('customers.is_deleted', 0);

        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            $customersres->where('customers.created_by', $this->userId);
        }

        $customers = $customersres->get();

        if ($customers->isEmpty()) {
            return $this->successresponse(404, 'customer', 'No records found');
        }

        return $this->successresponse(200, 'customer', $customers);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($this->rp['invoicemodule']['customer']['add'] != 1 && $this->rp['quotationmodule']['quotationcustomer']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        // dynamically validate incoming request data (company name/first name required)
        if ($request->company_name) {
            $validator = Validator::make($request->all(), [
                'firstname' => 'nullable|string|max:50',
                'lastname' => 'nullable|string|max:50',
                'company_name' => 'required|string|max:50',
                'gst_number' => 'nullable|alpha_num|max:50',
                'email' => 'nullable|email|max:50',
                'pincode' => 'nullable|numeric',
                'contact_number' => 'nullable|numeric|digits:10',
                'house_no_building_name' => 'nullable|string|max:191',
                'road_name_area_colony' => 'nullable|string|max:191',
                'country' => 'nullable|numeric',
                'state' => 'nullable|numeric',
                'city' => 'nullable|numeric',
                'user_id' => 'nullable|numeric',
                'created_by',
                'created_at',
                'is_active',
                'is_deleted'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:50',
                'lastname' => 'nullable|string|max:50',
                'company_name' => 'nullable|string|max:50',
                'gst_number' => 'nullable|alpha_num|max:50',
                'email' => 'nullable|email|max:50',
                'pincode' => 'nullable|numeric',
                'contact_number' => 'nullable|numeric|digits:10',
                'house_no_building_name' => 'nullable|string|max:191',
                'road_name_area_colony' => 'nullable|string|max:191',
                'country' => 'nullable|numeric',
                'state' => 'nullable|numeric',
                'city' => 'nullable|numeric',
                'user_id' => 'nullable|numeric',
            ]);
        }

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $customerid = $this->invoice_other_settingModel::find(1);

            $customerType = 'invoice';

            if ($request->customer_type) {
                $customerType = $request->customer_type;
            }

            $customer = $this->customerModel::create([ //insert customer record and return customer id
                'customer_id' => $customerid->current_customer_id,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'contact_no' => $request->contact_number,
                'house_no_building_name' => $request->house_no_building_name,
                'road_name_area_colony' => $request->road_name_area_colony,
                'country_id' => $request->country,
                'state_id' => $request->state,
                'city_id' => $request->city,
                'pincode' => $request->pincode,
                'gst_no' => $request->gst_number,
                'customer_type' => $customerType,
                'company_id' => $this->companyId,
                'created_by' => $this->userId,
            ]);

            if ($customer) {
                $customerid->current_customer_id += 1; // update customer id in other setting table
                $customerid->save();
                return $this->successresponse(200, 'message', 'customer succesfully added', 'customer_id', $customer->id);
            } else {
                return $this->successresponse(500, 'message', 'customer not succesfully added !');
            }


        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($this->rp['invoicemodule']['customer']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $customer = $this->customerModel::find($id);

        if (!$customer) {
            return $this->successresponse(404, 'message', "No Such Customer Found!");
        }

        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'customer', $customer);


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if ($this->rp['invoicemodule']['customer']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $customer = $this->customerModel::find($id);

        if (!$customer) {
            return $this->successresponse(404, 'message', "No Such Customer Found!");
        }

        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        } 

        return $this->successresponse(200, 'customer', $customer);


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($this->rp['invoicemodule']['customer']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        // validate incoming request data
        if ($request->company_name) {
            $validator = Validator::make($request->all(), [
                'firstname' => 'nullable|string|max:50',
                'lastname' => 'nullable|string|max:50',
                'company_name' => 'required|string|max:50',
                'email' => 'nullable|email|max:50',
                'contact_number' => 'nullable|numeric|digits:10',
                'house_no_building_name' => 'nullable|string|max:191',
                'road_name_area_colony' => 'nullable|string|max:191',
                'country' => 'nullable|numeric',
                'state' => 'nullable|numeric',
                'city' => 'nullable|numeric',
                'pincode' => 'nullable|numeric',
                'gst_number' => 'nullable|alpha_num|max:50',
                'created_by',
                'user_id' => 'required|numeric',
                'created_at',
                'updated_at',
                'is_active',
                'is_deleted'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:50',
                'lastname' => 'nullable|string|max:50',
                'company_name' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:50',
                'contact_number' => 'nullable|numeric|digits:10',
                'house_no_building_name' => 'nullable|string|max:191',
                'road_name_area_colony' => 'nullable|string|max:191',
                'country' => 'nullable|numeric',
                'state' => 'nullable|numeric',
                'city' => 'nullable|numeric',
                'pincode' => 'nullable|numeric',
                'gst_number' => 'nullable|alpha_num|max:50',
                'user_id' => 'required|numeric',
            ]);
        }


        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
            $customer = $this->customerModel::find($id); // find customer record

            if (!$customer) {
                return $this->successresponse(404, 'message', 'No Such Customer Found!');
            }

            if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
                if ($customer->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            } 

            $customer->update([  // update customer data
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'contact_no' => $request->contact_number,
                'house_no_building_name' => $request->house_no_building_name,
                'road_name_area_colony' => $request->road_name_area_colony,
                'country_id' => $request->country,
                'state_id' => $request->state,
                'city_id' => $request->city,
                'pincode' => $request->pincode,
                'gst_no' => $request->gst_number,
                'company_id' => $this->companyId,
                'updated_by' => $this->userId,
                'updated_at' => date('Y-m-d')
            ]);

            return $this->successresponse(200, 'message', 'customer succesfully updated');


        }
    }

    // customer status update (active/deactive)
    public function statusupdate(Request $request, string $id)
    {
        if ($this->rp['invoicemodule']['customer']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $customer = $this->customerModel::find($id);

        if (!$customer) {
            return $this->successresponse(404, 'message', 'No Such customer Found!');
        }

        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $customer->update([
            'is_active' => $request->status
        ]);

        return $this->successresponse(200, 'message', 'customer status succesfully updated');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($this->rp['invoicemodule']['customer']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $customer = $this->customerModel::find($id);

        if (!$customer) {
            return $this->successresponse(404, 'message', 'No Such Customer Found!');
        }

        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $customer->update([
            'is_deleted' => 1
        ]);
        
        return $this->successresponse(200, 'message', 'customer succesfully deleted');
    }
}
