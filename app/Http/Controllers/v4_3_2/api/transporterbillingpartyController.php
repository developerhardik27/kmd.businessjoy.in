<?php

namespace App\Http\Controllers\v4_3_2\api;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class transporterbillingpartyController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $transporterbillingpartyModel;

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

        $this->transporterbillingpartyModel = $this->getmodel('transporter_billing_party');
    }


    /**
     * get party list.
     */
    public function partylist(Request $request)
    {
        $parties = $this->transporterbillingpartyModel::where('is_deleted', 0);

        if ($this->rp['logisticmodule']['transporterbilling']['alldata'] != 1) {
            $parties->where('created_by', $this->userId);
        }

        $parties = $parties->get();

        if ($parties->isEmpty()) {
            return $this->successresponse(404, 'party', 'No records found');
        }

        return $this->successresponse(200, 'party', $parties);
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($this->rp['logisticmodule']['transporterbilling']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $party = $this->transporterbillingpartyModel::leftjoin($this->masterdbname . '.country', 'transporter_billing_party.country_id', '=', $this->masterdbname . '.country.id')
            ->leftjoin($this->masterdbname . '.state', 'transporter_billing_party.state_id', '=', $this->masterdbname . '.state.id')
            ->leftjoin($this->masterdbname . '.city', 'transporter_billing_party.city_id', '=', $this->masterdbname . '.city.id')
            ->leftjoin($this->masterdbname . '.users', 'transporter_billing_party.created_by', '=', $this->masterdbname . '.users.id')
            ->select(
                'transporter_billing_party.id',
                'transporter_billing_party.firstname',
                'transporter_billing_party.lastname',
                'transporter_billing_party.company_name',
                'transporter_billing_party.email',
                'transporter_billing_party.contact_no',
                DB::raw("
                    CONCAT_WS(', ', 
                        transporter_billing_party.house_no_building_name, 
                        transporter_billing_party.road_name_area_colony,
                        transporter_billing_party.pincode,
                        city.city_name,
                        state.state_name,
                        country.country_name
                    ) as address
                "),
                'transporter_billing_party.gst_no',
                'transporter_billing_party.pan_number',
                'transporter_billing_party.created_by',
                'transporter_billing_party.updated_by',
                DB::raw("DATE_FORMAT(transporter_billing_party.created_at, '%d-%M-%Y %h:%i %p') as created_at_formatted"),
                'transporter_billing_party.updated_at',
                'transporter_billing_party.is_active',
                DB::raw("CONCAT_WS(' ', 'users.firstname', 'users.lastname') as createdby"),
            )
            ->where('transporter_billing_party.is_deleted', 0);

        if ($this->rp['logisticmodule']['transporterbilling']['alldata'] != 1) {
            $party->where('transporter_billing_party.created_by', $this->userId);
        }

        $totalcount = $party->get()->count(); // count total record

        $party = $party->get();

        if ($party->isEmpty()) {
            return DataTables::of($party)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }



        return DataTables::of($party)
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

            if ($this->rp['logisticmodule']['transporterbilling']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $party = $this->transporterbillingpartyModel::create([ //insert party record and return party id
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

            if ($party) {
                return $this->successresponse(200, 'message', 'party succesfully added', 'party_id', $party->id);
            } else {
                return $this->successresponse(500, 'message', 'party not succesfully added !');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $party = $this->transporterbillingpartyModel::find($id);

        if (!$party) {
            return $this->successresponse(404, 'message', "No Such party Found!");
        }

        if ($this->rp['logisticmodule']['transporterbilling']['alldata'] != 1) {
            if ($party->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        if ($this->rp['logisticmodule']['transporterbilling']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        return $this->successresponse(200, 'party', $party);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $party = $this->transporterbillingpartyModel::find($id);

        if (!$party) {
            return $this->successresponse(404, 'message', "No Such party Found!");
        }

        if ($this->rp['logisticmodule']['transporterbilling']['alldata'] != 1) {
            if ($party->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        if ($this->rp['logisticmodule']['transporterbilling']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }


        return $this->successresponse(200, 'party', $party);
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
            $party = $this->transporterbillingpartyModel::find($id); // find party record

            if (!$party) {
                return $this->successresponse(404, 'message', 'No Such party Found!');
            }

            if ($this->rp['logisticmodule']['transporterbilling']['alldata'] != 1) {
                if ($party->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            if ($this->rp['logisticmodule']['transporterbilling']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $party->update([  // update party data
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

            return $this->successresponse(200, 'message', 'party succesfully updated');
        }
    }

    // party status update (active/deactive)
    public function statusupdate(Request $request, string $id)
    {
        $party = $this->transporterbillingpartyModel::find($id);

        if (!$party) {
            return $this->successresponse(404, 'message', 'No Such party Found!');
        }

        if ($this->rp['logisticmodule']['transporterbilling']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($this->rp['logisticmodule']['transporterbilling']['alldata'] != 1) {
            if ($party->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $party->update([
            'is_active' => $request->status
        ]);

        return $this->successresponse(200, 'message', 'party status succesfully updated');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $party = $this->transporterbillingpartyModel::find($id);
        if (!$party) {
            return $this->successresponse(404, 'message', 'No Such party Found!');
        }
        if ($this->rp['logisticmodule']['transporterbilling']['alldata'] != 1) {
            if ($party->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($this->rp['logisticmodule']['transporterbilling']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $party->update([
            'is_deleted' => 1
        ]);
        return $this->successresponse(200, 'message', 'party succesfully deleted');
    }
}
