<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class customerController extends commonController
{

    public $userId, $companyId, $masterdbname;

    public function __construct(Request $request)
    {
        $this->dbname($request->company_id);
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname =  DB::connection()->getDatabaseName();
    }


    public function invoicecustomer(Request $request)
    {
        
       
            $customers = customer::join($this->masterdbname.'.country', 'customers.country_id', '=', $this->masterdbname.'.country.id')
                ->join($this->masterdbname.'.state', 'customers.state_id', '=',$this->masterdbname. '.state.id')
                ->join($this->masterdbname.'.city', 'customers.city_id', '=', $this->masterdbname.'.city.id')
                ->select('customers.id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.address', 'country.country_name', 'state.state_name', 'city.city_name', 'customers.pincode', 'customers.gst_no', 'customers.company_id', 'customers.created_by', 'customers.updated_by', 'customers.created_at', 'customers.updated_at')
                ->where('customers.is_deleted', 0)->where('customers.is_active', 1)
                ->get();

        if ($customers->count() > 0) {
            return response()->json([
                'status' => 200,
                'customer' => $customers
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'customer' => 'No Records Found'
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $customers = customer::join($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->join($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->join($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->select('customers.id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.address', 'country.country_name', 'state.state_name', 'city.city_name', 'customers.pincode', 'customers.gst_no', 'customers.company_id', 'customers.created_by', 'customers.updated_by', 'customers.created_at', 'customers.updated_at', 'customers.is_active')
            ->where('customers.is_deleted', 0)
            ->get();

        if ($customers->count() > 0) {
            return response()->json([
                'status' => 200,
                'customer' => $customers
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'customer' => 'No Records Found!'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'company_name' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'pincode' => 'required|numeric|digits:6',
            'contact_number' => 'required|numeric|digits:10',
            'address' => 'required|string|max:191',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'gst_number' => 'nullable|alpha_num',
            'user_id' => 'required|numeric',
            'updated_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            $customer = DB::connection('dynamic_connection')->table('customers')->insertGetId([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'contact_no' => $request->contact_number,
                'address' => $request->address,
                'country_id' => $request->country,
                'state_id' => $request->state,
                'city_id' => $request->city,
                'pincode' => $request->pincode,
                'gst_no' => $request->gst_number,
                'company_id' => $this->companyId,
                'created_by' => $this->userId,

            ]);

            if ($customer) {
                return response()->json([
                    'status' => 200,
                    'message' => 'customer  succesfully added',
                    'customerid' => $customer
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'customer not succesfully added !'
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $customer = customer::find($id);
        if ($customer) {
            return response()->json([
                'status' => 200,
                'customer' => $customer
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'customer' => $customer,
                'message' => "No Such Customer Found!"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $customer = customer::find($id);
        if ($customer) {
            return response()->json([
                'status' => 200,
                'customer' => $customer
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such Customer Found!"
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'company_name' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'contact_number' => 'required|numeric|digits:10',
            'address' => 'required|string|max:191',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric|digits:6',
            'gst_number' => 'nullable|alpha_num|max:50',
            'created_by',
            'user_id' => 'required|numeric',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {
            $customer = customer::find($id);

            if ($customer) {
                $customer->update([
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'company_name' => $request->company_name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_number,
                    'address' => $request->address,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                    'pincode' => $request->pincode,
                    'gst_no' => $request->gst_number,
                    'company_id' => $this->companyId,
                    'updated_by' => $this->userId,
                    'updated_at' => date('Y-m-d')
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'customer succesfully updated'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Such Customer Found!'
                ], 404);
            }
        }
    }

    // customer status update 
    public function statusupdate(Request $request, string $id)
    {
        $customer = customer::find($id);
        if ($customer) {
            $customer->update([
                'is_active' => $request->status
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'customer status succesfully updated'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such customer Found!'
            ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = customer::find($id);

        if ($customer) {
            $customer->update([
                'is_deleted' => 1

            ]);
            return response()->json([
                'status' => 200,
                'message' => 'customer succesfully deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such Customer Found!'
            ], 404);
        }
    }
}
