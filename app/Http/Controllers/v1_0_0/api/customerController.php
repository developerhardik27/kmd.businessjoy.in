<?php

namespace App\Http\Controllers\v1_0_0\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class customerController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $customerModel ,$invoice_other_settingModel;

    public function __construct(Request $request)
    {
        $this->dbname($request->company_id);
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->customerModel = $this->getmodel('customer');
        $this->invoice_other_settingModel = $this->getmodel('invoice_other_setting');
    }


    public function invoicecustomer(Request $request)
    {
        $customersres = $this->customerModel::leftjoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftjoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftjoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->select('customers.id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.address', 'country.country_name', 'state.state_name', 'city.city_name', 'customers.pincode', 'customers.gst_no', 'customers.company_id', 'customers.created_by', 'customers.updated_by', 'customers.created_at', 'customers.updated_at')
            ->where('customers.is_deleted', 0)->where('customers.is_active', 1);

        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            $customersres->where('customers.created_by', $this->userId);
        }
        $customers = $customersres->get();

        if ($customers->count() > 0) {
            if ($this->rp['invoicemodule']['customer']['view'] == 1) {
                return response()->json([
                    'status' => 200,
                    'customer' => $customers
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }
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


        $customersres = $this->customerModel::leftjoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftjoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftjoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->select('customers.id', 'customers.firstname', 'customers.lastname', 'customers.company_name', 'customers.email', 'customers.contact_no', 'customers.address', 'country.country_name', 'state.state_name', 'city.city_name', 'customers.pincode', 'customers.gst_no', 'customers.company_id', 'customers.created_by', 'customers.updated_by', 'customers.created_at', 'customers.updated_at', 'customers.is_active')
            ->where('customers.is_deleted', 0);

        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            $customersres->where('customers.created_by', $this->userId);
        }

        $customers = $customersres->get();

        if ($customers->count() > 0) {
            if ($this->rp['invoicemodule']['customer']['view'] == 1) {
                return response()->json([
                    'status' => 200,
                    'customer' => $customers
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }

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

        if ($request->gst_number) {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:50',
                'lastname' => 'required|string|max:50',
                'company_name' => 'required|string|max:50',
                'gst_number' => 'required|alpha_num|max:50',
                'email' => 'required|email|max:50',
                'pincode' => 'required|numeric',
                'contact_number' => 'required|numeric|digits:10',
                'address' => 'required|string|max:191',
                'country' => 'required|numeric',
                'state' => 'required|numeric',
                'city' => 'required|numeric',
                'user_id' => 'required|numeric',
                'updated_by',
                'created_at',
                'updated_at',
                'is_active',
                'is_deleted'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:50',
                'lastname' => 'required|string|max:50',
                'company_name' => 'nullable|string|max:50',
                'gst_number' => 'nullable|alpha_num|max:50',
                'email' => 'nullable|email|max:50',
                'pincode' => 'nullable|numeric',
                'contact_number' => 'nullable|numeric|digits:10',
                'address' => 'nullable|string|max:191',
                'country' => 'nullable|numeric',
                'state' => 'nullable|numeric',
                'city' => 'nullable|numeric',
                'user_id' => 'nullable|numeric',
            ]);
        }



        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            if ($this->rp['invoicemodule']['customer']['add'] == 1) {
                

                $customerid = $this->invoice_other_settingModel::find(1);

                $customer = DB::connection('dynamic_connection')->table('customers')->insertGetId([
                    'customer_id' => $customerid->current_customer_id,
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
                    $customerid->current_customer_id += 1 ;
                    $customerid->save();
                    return response()->json([
                        'status' => 200,
                        'customer' => $customer,
                        'message' => 'customer succesfully added'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'customer not succesfully added !'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = $this->customerModel::find($id);
        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($customer) {
            if ($this->rp['invoicemodule']['customer']['view'] == 1) {
                return response()->json([
                    'status' => 200,
                    'customer' => $customer
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }
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
        $customer = $this->customerModel::find($id);
        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($customer) {
            if ($this->rp['invoicemodule']['customer']['edit'] == 1) {
                return response()->json([
                    'status' => 200,
                    'customer' => $customer
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }
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

        if($request->gst_number){
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
                'pincode' => 'required|numeric',
                'gst_number' => 'required|alpha_num|max:50',
                'created_by',
                'user_id' => 'required|numeric',
                'created_at',
                'updated_at',
                'is_active',
                'is_deleted'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:50',
                'lastname' => 'required|string|max:50',
                'company_name' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:50',
                'contact_number' => 'nullable|numeric|digits:10',
                'address' => 'nullable|string|max:191',
                'country' => 'nullable|numeric',
                'state' => 'nullable|numeric',
                'city' => 'nullable|numeric',
                'pincode' => 'nullable|numeric',
                'gst_number' => 'nullable|alpha_num|max:50',
                'user_id' => 'required|numeric',
            ]);
        }
        

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {
            $customer = $this->customerModel::find($id);
            if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
                if ($customer->created_by != $this->userId) {
                    return response()->json([
                        'status' => 500,
                        'message' => "You are Unauthorized!"
                    ]);
                }
            }
            if ($customer) {
                if ($this->rp['invoicemodule']['customer']['edit'] == 1) {
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
                        'status' => 500,
                        'message' => 'You are Unauthorized'
                    ]);
                }

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
        $customer = $this->customerModel::find($id);
        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($customer) {
            if ($this->rp['invoicemodule']['customer']['edit'] == 1) {
                $customer->update([
                    'is_active' => $request->status
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'customer status succesfully updated'
                ]);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }
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
        $customer = $this->customerModel::find($id);
        if ($this->rp['invoicemodule']['customer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }
        if ($customer) {
            if ($this->rp['invoicemodule']['customer']['delete'] == 1) {
                $customer->update([
                    'is_deleted' => 1
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'customer succesfully deleted'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such Customer Found!'
            ], 404);
        }
    }
}
