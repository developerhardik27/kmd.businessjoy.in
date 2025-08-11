<?php

namespace App\Http\Controllers\v4_2_3\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class supplierController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $supplierModel;

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

        $this->supplierModel = $this->getmodel('supplier');
    }

    public function datatable()
    {
        if ($this->rp['inventorymodule']['supplier']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $suppliersres = $this->supplierModel::leftjoin($this->masterdbname . '.country', 'suppliers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftjoin($this->masterdbname . '.state', 'suppliers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftjoin($this->masterdbname . '.city', 'suppliers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftjoin($this->masterdbname . '.users', 'suppliers.created_by', '=', $this->masterdbname . '.users.id')
            ->select(
                'suppliers.id',
                DB::raw("
                    CONCAT_WS(' ' , suppliers.firstname , suppliers.lastname) as suppliername
                "),
                'suppliers.company_name',
                'suppliers.email',
                'suppliers.contact_no',
                'suppliers.house_no_building_name',
                'suppliers.road_name_area_colony',
                'country.country_name',
                'state.state_name',
                'city.city_name',
                'suppliers.pincode',
                'suppliers.gst_no',
                'suppliers.company_id',
                'suppliers.created_by',
                'suppliers.updated_by',
                DB::raw("DATE_FORMAT(suppliers.created_at, '%d-%M-%Y %h:%i %p') as created_at_formatted"),
                'suppliers.updated_at',
                'suppliers.is_active',
                'users.firstname as createdby_fname',
                'users.lastname as createdby_lname'
            )
            ->where('suppliers.is_deleted', 0);

        if ($this->rp['inventorymodule']['supplier']['alldata'] != 1) {
            $suppliersres->where('suppliers.created_by', $this->userId);
        }

        $totalcount = $suppliersres->get()->count(); // count total record

        $suppliers = $suppliersres->get();

        if ($suppliers->isEmpty()) {
            return DataTables::of($suppliers)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($suppliers)
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
        if ($this->rp['inventorymodule']['supplier']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $suppliersres = $this->supplierModel::leftjoin($this->masterdbname . '.country', 'suppliers.country_id', '=', $this->masterdbname . '.country.id')
            ->leftjoin($this->masterdbname . '.state', 'suppliers.state_id', '=', $this->masterdbname . '.state.id')
            ->leftjoin($this->masterdbname . '.city', 'suppliers.city_id', '=', $this->masterdbname . '.city.id')
            ->leftjoin($this->masterdbname . '.users', 'suppliers.created_by', '=', $this->masterdbname . '.users.id')
            ->select(
                'suppliers.id',
                DB::raw("
                    CONCAT_WS(' ' , suppliers.firstname , suppliers.lastname) as suppliername
                "),
                'suppliers.company_name',
                'suppliers.email',
                'suppliers.contact_no',
                'suppliers.house_no_building_name',
                'suppliers.road_name_area_colony',
                'country.country_name',
                'state.state_name',
                'city.city_name',
                'suppliers.pincode',
                'suppliers.gst_no',
                'suppliers.company_id',
                'suppliers.created_by',
                'suppliers.updated_by',
                DB::raw("DATE_FORMAT(suppliers.created_at, '%d-%M-%Y %h:%i %p') as created_at_formatted"),
                'suppliers.updated_at',
                'suppliers.is_active',
                'users.firstname as createdby_fname',
                'users.lastname as createdby_lname'
            )
            ->where('suppliers.is_deleted', 0);

        if ($this->rp['inventorymodule']['supplier']['alldata'] != 1) {
            $suppliersres->where('suppliers.created_by', $this->userId);
        }

        $suppliers = $suppliersres->get();

        if ($suppliers->isEmpty()) {
            return $this->successresponse(404, 'supplier', 'No Records Found!');
        }
        
        return $this->successresponse(200, 'supplier', $suppliers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($this->rp['inventorymodule']['supplier']['add'] != 1) {
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
 
            $supplier = $this->supplierModel::create([ //insert supplier record and return supplier id
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
                'created_by' => $this->userId,
            ]);

            if ($supplier) {
                return $this->successresponse(200, 'message', 'supplier succesfully added', 'supplier_id', $supplier->id);
            } else {
                return $this->successresponse(500, 'message', 'supplier not succesfully added !');
            }


        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($this->rp['inventorymodule']['supplier']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $supplier = $this->supplierModel::find($id);
        if (!$supplier) {
            return $this->successresponse(404, 'message', "No Such supplier Found!");
        }
        if ($this->rp['inventorymodule']['supplier']['alldata'] != 1) {
            if ($supplier->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        return $this->successresponse(200, 'supplier', $supplier);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if ($this->rp['inventorymodule']['supplier']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $supplier = $this->supplierModel::find($id);
        if (!$supplier) {
            return $this->successresponse(404, 'message', "No Such supplier Found!");
        }
        if ($this->rp['inventorymodule']['supplier']['alldata'] != 1) {
            if ($supplier->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        return $this->successresponse(200, 'supplier', $supplier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($this->rp['inventorymodule']['supplier']['edit'] != 1) {
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
            $supplier = $this->supplierModel::find($id); // find supplier record
            if (!$supplier) {
                return $this->successresponse(404, 'message', 'No Such supplier Found!');
            }
            if ($this->rp['inventorymodule']['supplier']['alldata'] != 1) {
                if ($supplier->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }  
            $supplier->update([  // update supplier data
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
            return $this->successresponse(200, 'message', 'supplier succesfully updated');
        }
    }

    // supplier status update (active/deactive)
    public function statusupdate(Request $request, string $id)
    {
        if ($this->rp['inventorymodule']['supplier']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $supplier = $this->supplierModel::find($id);
        if (!$supplier) {
            return $this->successresponse(404, 'message', 'No Such supplier Found!');
        }
        if ($this->rp['inventorymodule']['supplier']['alldata'] != 1) {
            if ($supplier->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $supplier->update([
            'is_active' => $request->status
        ]);
        return $this->successresponse(200, 'message', 'supplier status succesfully updated');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($this->rp['inventorymodule']['supplier']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $supplier = $this->supplierModel::find($id);
        if (!$supplier) {
            return $this->successresponse(404, 'message', 'No Such supplier Found!');
        }
        if ($this->rp['inventorymodule']['supplier']['alldata'] != 1) {
            if ($supplier->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $supplier->update([
            'is_deleted' => 1
        ]);
        return $this->successresponse(200, 'message', 'supplier succesfully deleted');
    }
}

