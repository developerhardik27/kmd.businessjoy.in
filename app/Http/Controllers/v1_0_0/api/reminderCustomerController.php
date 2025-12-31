<?php

namespace App\Http\Controllers\v1_0_0\api;

use App\Http\Controllers\Controller;
use App\Models\city;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class reminderCustomerController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $customerModel;

    public function __construct(Request $request)
    {
        $this->dbname($request->company_id);
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->customerModel = $this->getmodel('reminder_customer');
    }



    public function customerreminders(Request $request,string $id)
    {
        $customers = $this->customerModel::join($this->masterdbname . '.city', 'reminder_customer.city_id', '=', $this->masterdbname . '.city.id')
            ->join('reminder', 'reminder_customer.id', '=', 'reminder.customer_id')
            ->join($this->masterdbname.'.users','reminder.created_by','=','users.id')
            ->select('reminder_customer.id','reminder.reminder_status', 'reminder.amount', 'reminder.service_type', 'reminder.product_name',DB::raw("DATE_FORMAT(reminder.next_reminder_date, '%d-%m-%Y')as next_reminder_date"),DB::raw("DATE_FORMAT(reminder.created_at, '%d-%m-%Y %h:%i:%s %p') as created_on"),'users.firstname','users.lastname')
            ->where('reminder_customer.is_deleted', 0)->where('reminder.customer_id',$id)->get();

        if ($customers->count() > 0) {
            if ($this->rp['remindermodule']['reminder']['view'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            } 
            return $this->successresponse(200, 'customer', $customers);

        } else {
            return $this->successresponse(404, 'customer', 'No Records Found!');
        }
    }

    public function counttotalcustomer(Request $request)
    {
        $totalcustomer = $this->customerModel::where('is_deleted', 0)->count();
            return $this->successresponse(200, 'customer', $totalcustomer);
    }
    public function remindercustomer(Request $request)
    {
        $customersres = $this->customerModel::where('is_deleted', 0);

        if ($this->rp['remindermodule']['remindercustomer']['alldata'] != 1) {
            $customersres->where('reminder_customer.created_by', $this->userId);
        }

        $customers = $customersres->get();

        if ($customers->count() > 0) {
            return $this->successresponse(200, 'customer',$customers);
        } else {
            return $this->successresponse(404, 'customer', 'No Records Found!');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $customersres = $this->customerModel::join($this->masterdbname . '.country', 'reminder_customer.country_id', '=', $this->masterdbname . '.country.id')
            ->join($this->masterdbname . '.state', 'reminder_customer.state_id', '=', $this->masterdbname . '.state.id')
            ->join($this->masterdbname . '.city', 'reminder_customer.city_id', '=', $this->masterdbname . '.city.id')
            ->select('reminder_customer.id', 'reminder_customer.name', 'reminder_customer.email', 'reminder_customer.contact_no', 'reminder_customer.address', 'reminder_customer.area', 'country.country_name', 'state.state_name', 'city.city_name', 'reminder_customer.pincode', 'reminder_customer.invoice_id', 'reminder_customer.company_id', 'reminder_customer.customer_type', 'reminder_customer.created_by', 'reminder_customer.updated_by', 'reminder_customer.created_at', 'reminder_customer.updated_at', 'reminder_customer.is_active')
            ->where('reminder_customer.is_deleted', 0);

        if ($this->rp['remindermodule']['remindercustomer']['alldata'] != 1) {
            $customersres->where('reminder_customer.created_by', $this->userId);
        }

        $customers = $customersres->get();

        if ($customers->count() > 0) {
            if ($this->rp['remindermodule']['reminder']['view'] == 1) {
                return $this->successresponse(200, 'customer', $customers);
            } else {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

        } else {
            return $this->successresponse(404, 'customer','No Records Found!');
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
            'name' => 'required|string|max:50',
            'customer_type' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'contact_number' => 'required|numeric|digits:10',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'invocieid' => 'nullable|alpha_num',
            'area' => 'required|string',
            'pincode' => 'required|numeric',
            'address' => 'required|string|max:191',
            'service_type' => 'nullable|string|max:191',
            'product_name' => 'nullable|string|max:191',
            'product_unique_id' => 'nullable|string|max:191',
            'amount' => 'nullable|numeric',
            'reminder_status' => 'nullable|string',
            'service_completed_date',
            'next_reminder',
            'before_services_notes' => 'nullable|string|max:191',
            'after_services_notes' => 'nullable|string|max:191'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422,$validator->messages());
        } else {
            if ($this->rp['remindermodule']['reminder']['add'] == 1) {
                $customer = DB::connection('dynamic_connection')->table('reminder_customer')->insertGetId([
                    'name' => $request->name,
                    'customer_type' => $request->customer_type,
                    'email' => $request->email,
                    'contact_no' => $request->contact_number,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                    'pincode' => $request->pincode,
                    'invoice_id' => $request->invoiceid,
                    'area' => $request->area,
                    'address' => $request->address,
                    'company_id' => $this->companyId,
                    'created_by' => $this->userId,
                ]);

                if ($customer) {

                    if (isset($request->service_type)) {
                        $reminder = DB::connection('dynamic_connection')->table('reminder')->insert([
                            'customer_id' => $customer,
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
                            return $this->successresponse(200, 'message', 'customer succesfully added');
                        } else {
                            return $this->successresponse(500, 'message', 'customer not succesfully added !');
                        }

                    } else {
                        return $this->successresponse(200, 'message', 'customer succesfully added');
                    }

                } else {
                    return $this->successresponse(500, 'message', 'customer not succesfully added !');
                }
            } else {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = $this->customerModel::find($id);
        if ($this->rp['remindermodule']['remindercustomer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($customer) {
            if ($this->rp['remindermodule']['remindercustomer']['view'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
            return $this->successresponse(200, 'customer',  $customer);
        } else {
            return $this->successresponse(404, 'message',  "No Such Customer Found!");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customer = $this->customerModel::find($id);
        if ($this->rp['remindermodule']['remindercustomer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($customer) {
            if ($this->rp['remindermodule']['remindercustomer']['edit'] == 1) {
                return $this->successresponse(200, 'customer',  $customer);
            } else {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        } else {
            return $this->successresponse(404, 'message', "No Such Customer Found!");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'customer_type' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'contact_number' => 'required|numeric|digits:10',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'invocieid' => 'nullable|alpha_num',
            'area' => 'required|string',
            'pincode' => 'required|numeric',
            'address' => 'required|string|max:191',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422,$validator->messages());
        } else {
            $customer = $this->customerModel::find($id);
            if ($this->rp['remindermodule']['remindercustomer']['alldata'] != 1) {
                if ($customer->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }
            if ($customer) {
                if ($this->rp['remindermodule']['remindercustomer']['edit'] == 1) {
                    $customer->update([
                        'name' => $request->name,
                        'customer_type' => $request->customer_type,
                        'email' => $request->email,
                        'contact_no' => $request->contact_number,
                        'country_id' => $request->country,
                        'state_id' => $request->state,
                        'city_id' => $request->city,
                        'pincode' => $request->pincode,
                        'invoice_id' => $request->invoiceid,
                        'area' => $request->area,
                        'address' => $request->address,
                        'company_id' => $this->companyId,
                        'updated_by' => $this->userId,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    return $this->successresponse(200, 'message', 'customer succesfully updated');
                } else {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }

            } else {
                return $this->successresponse(404, 'message', 'No Such Customer Found!');
            }
        }
    }

    // customer status update 
    public function statusupdate(Request $request, string $id)
    {
        $customer = $this->customerModel::find($id);
        if ($this->rp['remindermodule']['remindercustomer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($customer) {
            if ($this->rp['remindermodule']['remindercustomer']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
            $customer->update([
                'is_active' => $request->status
            ]);
            return $this->successresponse(200, 'message', 'customer status succesfully updated');
        } else {
            return $this->successresponse(404, 'message', 'No Such customer Found!');
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = $this->customerModel::find($id);
        if ($this->rp['remindermodule']['remindercustomer']['alldata'] != 1) {
            if ($customer->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($customer) {
            if ($this->rp['remindermodule']['remindercustomer']['delete'] == 1) {
                $customer->update([
                    'is_deleted' => 1
                ]);
                return $this->successresponse(200, 'message', 'customer succesfully deleted');
            } else {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        } else {
            return $this->successresponse(404, 'message', 'No Such Customer Found!');
        }
    }

    public function area()
    {
        $uniqareas = $this->customerModel::distinct()->orderBy('area')->pluck('area');

        if ($uniqareas->count() > 0) {
            return $this->successresponse(200, 'area', $uniqareas);
        } else {
            return $this->successresponse(404, 'message', 'No any area Found!');
        }

    }

    public function cities()
    {
        $citiesid = $this->customerModel::distinct()->pluck('city_id');


        $cities = city::whereIn('id', $citiesid)->select('id', 'city_name')->orderBy('city_name')->get();

        if ($cities->count() > 0) {
            return $this->successresponse(200, 'city', $cities);
        } else {
            return $this->successresponse(404, 'message', 'No any city Found!');
        }

    }
}