<?php

namespace App\Http\Controllers\v4_2_1\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class consigneeController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $consigneeModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        
        $this->dbname($request->company_id);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->consigneeModel = $this->getmodel('consignee');
    }

    /**
     * get consignee list
     */

    public function consigneelist()
    {
        $consignees = $this->consigneeModel::where('consignees.is_deleted', 0);

        if ($this->rp['logisticmodule']['consignee']['alldata'] != 1) {
            $consignees->where('consignees.created_by', $this->userId);
        }

        $consignees = $consignees->get();

        if ($consignees->isEmpty()) {
            return $this->successresponse(404, 'consignee', 'No records found');
        }

        return $this->successresponse(200, 'consignee', $consignees);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($this->rp['logisticmodule']['consignee']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $consignees = $this->consigneeModel::leftjoin($this->masterdbname . '.country', 'consignees.country_id', '=', $this->masterdbname . '.country.id')
            ->leftjoin($this->masterdbname . '.state', 'consignees.state_id', '=', $this->masterdbname . '.state.id')
            ->leftjoin($this->masterdbname . '.city', 'consignees.city_id', '=', $this->masterdbname . '.city.id')
            ->leftjoin($this->masterdbname . '.users', 'consignees.created_by', '=', $this->masterdbname . '.users.id')
            ->select(
                'consignees.id',
                'consignees.firstname',
                'consignees.lastname',
                'consignees.company_name',
                'consignees.email',
                'consignees.contact_no',
                DB::raw("
                    CONCAT_WS(', ', 
                        consignees.house_no_building_name, 
                        consignees.road_name_area_colony,
                        consignees.pincode,
                        city.city_name,
                        state.state_name,
                        country.country_name
                    ) as address
                "),
                'consignees.gst_no',
                'consignees.pan_number',
                'consignees.created_by',
                'consignees.updated_by',
                DB::raw("DATE_FORMAT(consignees.created_at, '%d-%M-%Y %h:%i %p') as created_at_formatted"),
                'consignees.updated_at',
                'consignees.is_active',
                DB::raw("CONCAT_WS(' ', 'users.firstname', 'users.lastname') as createdby"),
            )
            ->where('consignees.is_deleted', 0);

        if ($this->rp['logisticmodule']['consignee']['alldata'] != 1) {
            $consignees->where('consignees.created_by', $this->userId);
        }


        $totalcount = $consignees->get()->count(); // count total record

        $consignees = $consignees->get();


        if ($consignees->isEmpty()) {
            return DataTables::of($consignees)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }


        return DataTables::of($consignees)
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
                'created_by',
                'created_at',
                'is_active',
                'is_deleted'
            ]);
        }



        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp['logisticmodule']['consignee']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $consignee = $this->consigneeModel::create([ //insert consignee record and return consignee id
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
                'created_by' => $this->userId,
            ]);

            if ($consignee) {
                return $this->successresponse(200, 'message', 'consignee succesfully added', 'consignee_id', $consignee->id);
            } else {
                return $this->successresponse(500, 'message', 'consignee not succesfully added !');
            }


        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($this->rp['logisticmodule']['consignee']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $consignee = $this->consigneeModel::find($id);

        if (!$consignee) {
            return $this->successresponse(404, 'message', "No Such consignee Found!");
        }

        if ($this->rp['logisticmodule']['consignee']['alldata'] != 1) {
            if ($consignee->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'consignee', $consignee);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if ($this->rp['logisticmodule']['consignee']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $consignee = $this->consigneeModel::find($id);

        if (!$consignee) {
            return $this->successresponse(404, 'message', "No Such consignee Found!");
        }

        if ($this->rp['logisticmodule']['consignee']['alldata'] != 1) {
            if ($consignee->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'consignee', $consignee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        if ($this->rp['logisticmodule']['consignee']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');

        }

        // validate incoming request data 
        if (isset($request->company_name)) 
        {
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
                'pan_number' => 'nullable|alpha_num|max:10',
                'created_by',
                'user_id' => 'required|numeric',
                'created_at',
                'updated_at',
                'is_active',
                'is_deleted'
            ]);
        }

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
            $consignee = $this->consigneeModel::find($id); // find consignee record

            if (!$consignee) {
                return $this->successresponse(404, 'message', 'No Such consignee Found!');
            }

            if ($this->rp['logisticmodule']['consignee']['alldata'] != 1) {
                if ($consignee->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            $consignee->update([  // update consignee data
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

            return $this->successresponse(200, 'message', 'consignee succesfully updated');


        }
    }

    // consignee status update (active/deactive)
    public function statusupdate(Request $request, string $id)
    {
        if ($this->rp['logisticmodule']['consignee']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $consignee = $this->consigneeModel::find($id);

        if (!$consignee) {
            return $this->successresponse(404, 'message', 'No Such consignee Found!');
        }

        if ($this->rp['logisticmodule']['consignee']['alldata'] != 1) {
            if ($consignee->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $consignee->update([
            'is_active' => $request->status
        ]);

        return $this->successresponse(200, 'message', 'consignee status succesfully updated');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($this->rp['logisticmodule']['consignee']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $consignee = $this->consigneeModel::find($id);

        if (!$consignee) {
            return $this->successresponse(404, 'message', 'No Such consignee Found!');
        }

        if ($this->rp['logisticmodule']['consignee']['alldata'] != 1) {
            if ($consignee->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
      
        $consignee->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'consignee succesfully deleted');
    }
}
