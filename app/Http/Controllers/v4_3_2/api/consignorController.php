<?php

namespace App\Http\Controllers\v4_3_2\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class consignorController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $consignorModel, $logistic_settingsModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        // $this->consignorModel = $this->getmodel('consignor');
        //12/29/2025 Changed merge consignor model to customers model
        $this->consignorModel = $this->getmodel('customer');
        $this->logistic_settingsModel = $this->getmodel('logistic_setting');
    }


    /**
     * get consignor list.
     */
    public function consignorlist(Request $request)
    {
    $customers = $this->consignorModel::where('customers.is_deleted', 0)->where('customers.customer_type', 'consignor');

        if ($this->rp['logisticmodule']['consignor']['alldata'] != 1) {
            $customers->where('customers.created_by', $this->userId);
        }

        $customers = $customers->get();

        if ($customers->isEmpty()) {
            return $this->successresponse(404, 'consignor', 'No records found');
        }

        return $this->successresponse(200, 'consignor', $customers);
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($this->rp['logisticmodule']['consignor']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }
        $dropdown = $this->logistic_settingsModel::where('id', 1)->value('customer_dropdown');
        $dropdown = json_decode($dropdown, true);
          array_push($dropdown, 'consignor');
        $customers = $this->consignorModel::leftjoin($this->masterdbname . '.country', 'customers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftjoin($this->masterdbname . '.state', 'customers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftjoin($this->masterdbname . '.city', 'customers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftjoin($this->masterdbname . '.users', 'customers.created_by', '=', $this->masterdbname . '.users.id')
            ->select(
                'customers.id',
                'customers.firstname',
                'customers.lastname',
                'customers.company_name',
                'customers.email',
                'customers.contact_no',
                DB::raw("
                    CONCAT_WS(', ', 
                        customers.house_no_building_name, 
                        customers.road_name_area_colony,
                        customers.pincode,
                        city.city_name,
                        state.state_name,
                        country.country_name
                    ) as address
                "),
                'customers.gst_no',
                'customers.pan_number',
                'customers.created_by',
                'customers.updated_by',
                DB::raw("DATE_FORMAT(customers.created_at, '%d-%M-%Y %h:%i %p') as created_at_formatted"),
                'customers.updated_at',
                'customers.is_active',
                DB::raw("CONCAT_WS(' ', 'users.firstname', 'users.lastname') as createdby"),
            )
            ->where('customers.is_deleted', 0)->whereIn('customers.customer_type', $dropdown);

        if ($this->rp['logisticmodule']['consignor']['alldata'] != 1) {
            $customers->where('customers.created_by', $this->userId);
        }

        $totalcount = $customers->get()->count(); // count total record

        $customers = $customers->get();

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dynamically validate incoming request data (company name/first name required)

        if (isset($request->company_name)) {
            $validator = Validator::make($request->all(), [
                'firstname' => 'nullable|string|max:50',
                'lastname' => 'nullable|string|max:50',
                'company_name' => 'required|string|max:50',
                'gst_number' => 'nullable|alpha_num|max:50',
                'pan_number' => 'nullable|alpha_num|max:10',
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
        } else {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:50',
                'lastname' => 'nullable|string|max:50',
                'company_name' => 'nullable|string|max:50',
                'gst_number' => 'nullable|alpha_num|max:50',
                'pan_number' => 'nullable|alpha_num|max:10',
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

            if ($this->rp['logisticmodule']['consignor']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $consignor = $this->consignorModel::create([ //insert consignor record and return consignor id
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'contact_no' => $request->contact_number,
                'house_no_building_name' => $request->house_no_building_name,
                'road_name_area_colony' => $request->road_name_area_colony,
                'country_id' => $request->country,
                'customer_type' => 'consignor',
                'state_id' => $request->state,
                'city_id' => $request->city,
                'pincode' => $request->pincode,
                'gst_no' => $request->gst_number,
                'pan_number' => $request->pan_number,
                'created_by' => $this->userId,
                'company_id' => $this->companyId,
            ]);

            if ($consignor) {
                return $this->successresponse(200, 'message', 'consignor succesfully added', 'consignor_id', $consignor->id);
            } else {
                return $this->successresponse(500, 'message', 'consignor not succesfully added !');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $consignor = $this->consignorModel::find($id);

        if (!$consignor) {
            return $this->successresponse(404, 'message', "No Such consignor Found!");
        }

        if ($this->rp['logisticmodule']['consignor']['alldata'] != 1) {
            if ($consignor->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        if ($this->rp['logisticmodule']['consignor']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        return $this->successresponse(200, 'consignor', $consignor);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $consignor = $this->consignorModel::find($id);

        if (!$consignor) {
            return $this->successresponse(404, 'message', "No Such consignor Found!");
        }

        if ($this->rp['logisticmodule']['consignor']['alldata'] != 1) {
            if ($consignor->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        if ($this->rp['logisticmodule']['consignor']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }


        return $this->successresponse(200, 'consignor', $consignor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // validate incoming request data
        if (isset($request->company_name)) {
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
                'pan_number' => 'nullable|alpha_num|max:10',
                'user_id' => 'required|numeric',
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
                'pan_number' => 'nullable|alpha_num|max:10',
                'user_id' => 'required|numeric',
            ]);
        }



        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
            $consignor = $this->consignorModel::find($id); // find consignor record

            if (!$consignor) {
                return $this->successresponse(404, 'message', 'No Such consignor Found!');
            }

            if ($this->rp['logisticmodule']['consignor']['alldata'] != 1) {
                if ($consignor->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            if ($this->rp['logisticmodule']['consignor']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }



            $consignor->update([  // update consignor data
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
                'pan_number' => $request->pan_number,
                'updated_by' => $this->userId,
                'updated_at' => date('Y-m-d')
            ]);

            return $this->successresponse(200, 'message', 'consignor succesfully updated');
        }
    }

    // consignor status update (active/deactive)
    public function statusupdate(Request $request, string $id)
    {
        $consignor = $this->consignorModel::find($id);

        if (!$consignor) {
            return $this->successresponse(404, 'message', 'No Such consignor Found!');
        }

        if ($this->rp['logisticmodule']['consignor']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($this->rp['logisticmodule']['consignor']['alldata'] != 1) {
            if ($consignor->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $consignor->update([
            'is_active' => $request->status
        ]);

        return $this->successresponse(200, 'message', 'consignor status succesfully updated');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $consignor = $this->consignorModel::find($id);
        if (!$consignor) {
            return $this->successresponse(404, 'message', 'No Such consignor Found!');
        }
        if ($this->rp['logisticmodule']['consignor']['alldata'] != 1) {
            if ($consignor->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($this->rp['logisticmodule']['consignor']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $consignor->update([
            'is_deleted' => 1
        ]);
        return $this->successresponse(200, 'message', 'consignor succesfully deleted');
    }
}
