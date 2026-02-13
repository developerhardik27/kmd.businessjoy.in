<?php

namespace App\Http\Controllers\v4_3_2\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class companymasterController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $companymasterModel, $gardenModel, $companygardenModel, $bank_detail_masterModel;

    public function __construct(Request $request)
    {

        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->companymasterModel = $this->getmodel('companymaster');
        $this->gardenModel = $this->getmodel('garden');
        $this->companygardenModel = $this->getmodel('company_garden');
        $this->bank_detail_masterModel = $this->getmodel('bank_detail_master');
    }
    public function index()
    {
        if ($this->rp['teamodule']['companymaster']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $companymaster = $this->companymasterModel::leftJoin('company_garden', 'companymasters.id', '=', 'company_garden.company_id')
            ->leftJoin('gardens', 'company_garden.garden_id', '=', 'gardens.id')
            ->leftJoin($this->masterdbname . '.country', 'companymasters.country_id', '=', 'country.id')
            ->leftJoin($this->masterdbname . '.state', 'companymasters.state_id', '=', 'state.id')
            ->leftJoin($this->masterdbname . '.city', 'companymasters.city_id', '=', 'city.id')
            ->select(
                'companymasters.id',
                'companymasters.company_name',
                'companymasters.email',
                'companymasters.contact_person_name',
                'companymasters.mobile_1',
                'companymasters.mobile_2',
                'companymasters.country_id',
                'companymasters.state_id',
                'companymasters.city_id',
                'companymasters.pincode',
                'companymasters.address',
                'companymasters.gst_no',
                'companymasters.pan',
                'country.country_name',
                'state.state_name',
                'city.city_name',
                'companymasters.created_by',
                'companymasters.updated_by',
                'companymasters.is_active',
                'companymasters.is_deleted',
                'companymasters.created_at',
                'companymasters.updated_at',

                DB::raw('GROUP_CONCAT(gardens.garden_name SEPARATOR ", ") as garden_names')
            )
            ->where('companymasters.is_deleted', 0)
            ->groupBy(
                'companymasters.id',
                'companymasters.company_name',
                'companymasters.email',
                'companymasters.contact_person_name',
                'companymasters.mobile_1',
                'companymasters.mobile_2',
                'companymasters.country_id',
                'country.country_name',
                'state.state_name',
                'city.city_name',
                'companymasters.state_id',
                'companymasters.city_id',
                'companymasters.pincode',
                'companymasters.address',
                'companymasters.gst_no',
                'companymasters.pan',
                'companymasters.created_by',
                'companymasters.updated_by',
                'companymasters.is_active',
                'companymasters.is_deleted',
                'companymasters.created_at',
                'companymasters.updated_at'
            )
            ->get();

        if ($companymaster->isEmpty()) {
            return DataTables::of($companymaster)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($companymaster)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }
    public function store(Request $request)
    {
        if ($this->rp['teamodule']['companymaster']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $data = $request->all();

        $validator = Validator::make($data, [
            'company_name'        => 'required|string|max:255',
            'email'               => 'nullable|email|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'mobile_1'            => 'nullable|numeric|digits_between:10,15',
            'mobile_2'            => 'nullable|numeric|digits_between:10,15',
            'country'             => 'nullable|integer|exists:country,id',
            'state'               => 'nullable|integer|exists:state,id',
            'city'                => 'nullable|integer|exists:city,id',
            'pincode'             => 'nullable|digits_between:4,8',
            'address'             => 'nullable|string',
            'gst_no'              => 'nullable|string|max:20',
            'pan'                 => 'nullable|string|max:20',
            'garden_id'           => 'required|array|min:1',
            'garden_id.*'         => 'integer',
        ], [
            'company_name.required' => 'Company name is required.',
            'company_name.string'   => 'Company name must be a string.',
            'company_name.max'      => 'Company name cannot exceed 255 characters.',
            'garden_id.required'    => 'Select at least one garden.',
            'garden_id.array'       => 'Invalid garden selection.',
            'garden_id.min'         => 'Select at least one garden.',
            'garden_id.*.integer'   => 'Invalid garden ID.',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }
        $create = $this->companymasterModel::create([
            'company_name' => $request->company_name,
            'email' => $request->email,
            'contact_person_name' => $request->contact_person_name,
            'mobile_1' => $request->mobile_1,
            'mobile_2' => $request->mobile_2,
            'country_id' => $request->country,
            'state_id' => $request->state,
            'city_id' => $request->city,
            'pincode' => $request->pincode,
            'gst_no' => $request->gst_no,
            'address' => $request->address,
            'pan' => $request->pan,
            'created_by' => $request->user_id,

        ]);
        if ($create  && $request->has('garden_id')) {
            $insertData = [];
            foreach ($request->garden_id as $gardenId) {
                $insertData[] = [
                    'company_id' => $create->id,
                    'garden_id'  => $gardenId,
                ];
            }
            $this->companygardenModel::insert($insertData);
            return $this->successresponse(200, 'message', 'companymaster succesfully added');
        } else {
            return $this->successresponse(500, 'message', 'companymaster not succesfully added !');
        }
    }
    public function edit($id)
    {
        if ($this->rp['teamodule']['companymaster']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $companymaster = $this->companymasterModel::find($id);
        if ($this->rp['teamodule']['companymaster']['alldata'] != 1) {
            if ($companymaster->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $gardenId = $this->companygardenModel::where('company_id', $id)
            ->pluck('garden_id');

        if (!$companymaster) {
            return $this->successresponse(500, 'message', 'companymaster not found !');
        }
        return $this->successresponse(200, 'companymaster', $companymaster, 'gardenId', $gardenId);
    }
    public function update(Request $request, $id)
    {
        if ($this->rp['teamodule']['companymaster']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $find_data = $this->companymasterModel::find($id);
        if ($this->rp['teamodule']['companymaster']['alldata'] != 1) {
            if ($find_data->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$find_data) {
            return response()->json(['status' => 'error', 'message' => 'companymaster not found'], 404);
        }
        $data = $request->all();

        $validator = Validator::make($data, [
            'company_name'        => 'required|string|max:255',
            'email'               => 'nullable|email|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'mobile_1'            => 'nullable|numeric|digits_between:10,15',
            'mobile_2'            => 'nullable|numeric|digits_between:10,15',
            'country'             => 'nullable|integer|exists:country,id',
            'state'               => 'nullable|integer|exists:state,id',
            'city'                => 'nullable|integer|exists:city,id',
            'pincode'             => 'nullable|digits_between:4,8',
            'address'             => 'nullable|string',
            'gst_no'              => 'nullable|string|max:20',
            'pan'                 => 'nullable|string|max:20',
            'garden_id'           => 'required|array|min:1',
            'garden_id.*'         => 'integer',
        ], [
            'company_name.required' => 'Company name is required.',
            'company_name.string'   => 'Company name must be a string.',
            'company_name.max'      => 'Company name cannot exceed 255 characters.',
            'garden_id.required'    => 'Select at least one garden.',
            'garden_id.array'       => 'Invalid garden selection.',
            'garden_id.min'         => 'Select at least one garden.',
            'garden_id.*.integer'   => 'Invalid garden ID.',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }
        $update = $this->companymasterModel::where('id', $id)->update([
            'company_name' => $request->company_name,
            'email' => $request->email,
            'contact_person_name' => $request->contact_person_name,
            'mobile_1' => $request->mobile_1,
            'mobile_2' => $request->mobile_2,
            'country_id' => $request->country,
            'state_id' => $request->state,
            'city_id' => $request->city,
            'pincode' => $request->pincode,
            'gst_no' => $request->gst_no,
            'address' => $request->address,
            'pan' => $request->pan,
            'updated_by' => $request->user_id,
        ]);
        $this->companygardenModel::where('company_id', $id)->delete();
        if ($update  && $request->has('garden_id')) {
            $insertData = [];
            foreach ($request->garden_id as $gardenId) {
                $insertData[] = [
                    'company_id' => $id,
                    'garden_id'  => $gardenId,
                ];
            }
            $this->companygardenModel::insert($insertData);
            return $this->successresponse(200, 'message', 'companymaster succesfully update');
        } else {
            return $this->successresponse(500, 'message', 'companymaster not succesfully update !');
        }
    }
    public function destroy($id)
    {
        if ($this->rp['teamodule']['companymaster']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $companymaster = $this->companymasterModel::find($id);
        if ($this->rp['teamodule']['companymaster']['alldata'] != 1) {
            if ($companymaster->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$companymaster) {
            return $this->successresponse(500, 'message', 'companymaster not found !');
        }
        $companymaster->update(
            [
                "is_deleted" => 1
            ]
        );
        $this->companygardenModel::where('company_id', $id)->delete();
        return $this->successresponse(200, 'message', 'companymaster succesfully deleted');
    }


    public function gardenindex()
    {
        if ($this->rp['teamodule']['garden']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $garden = $this->gardenModel::leftJoin('company_garden', 'company_garden.garden_id', '=', 'gardens.id')
            ->leftJoin('companymasters', 'companymasters.id', '=', 'company_garden.company_id')
            ->where('gardens.is_deleted', 0)
            ->select(
                'gardens.*',
                'companymasters.company_name'
            )
            ->get();

        if ($garden->isEmpty()) {
            return DataTables::of($garden)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($garden)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }
    public function gardenstore(Request $request)
    {
        if ($this->rp['teamodule']['garden']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $data = $request->all();

        $validator = Validator::make($data, [
            'garden_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|numeric|digits_between:10,15',
            'mobile_2' => 'nullable|numeric|digits_between:10,15',
            'country' => 'nullable|integer|exists:country,id',
            'state'   => 'nullable|integer|exists:state,id',
            'city'    => 'nullable|integer|exists:city,id',
            'pincode' => 'nullable|digits_between:4,8',
            'address' => 'nullable|string',
            'gst_no' => 'nullable|string|max:20',
            'pan'    => 'nullable|string|max:20',
        ], [
            'garden_name.required' => 'Garden name is required.',
            'garden_name.string'   => 'Garden name must be a string.',
            'garden_name.max'      => 'Garden name cannot exceed 255 characters.',

        ]);
        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }
        $exists = $this->gardenModel::where('garden_name', $request->garden_name)->exists();
        if ($exists) {
            return $this->errorresponse(422, ['garden_name' => ['This garden name has already been taken.']]);
        }
        $create = $this->gardenModel::create([
            'garden_name' => $request->garden_name,
            'email' => $request->email,
            'contact_person_name' => $request->contact_person_name,
            'mobile_1' => $request->mobile_1,
            'mobile_2' => $request->mobile_2,
            'country_id' => $request->country,
            'state_id' => $request->state,
            'city_id' => $request->city,
            'pincode' => $request->pincode,
            'gst_no' => $request->gst_no,
            'address' => $request->address,
            'pan' => $request->pan,
            'created_by' => $request->user_id,
        ]);

        if ($create) {
            return $this->successresponse(200, 'message', 'garden succesfully added', 'garden_id', $create->id);
        } else {
            return $this->successresponse(500, 'message', 'garden not succesfully added !');
        }
    }
    public function gardenedit($id)
    {
        if ($this->rp['teamodule']['garden']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $garden = $this->gardenModel::find($id);
        if ($this->rp['teamodule']['garden']['alldata'] != 1) {
            if ($garden->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$garden) {
            return $this->successresponse(404, 'message', "No Such garden Found!");
        }
        return $this->successresponse(200, 'garden', $garden);
    }
    public function gardenupdate(Request $request, $id)
    {
        if ($this->rp['teamodule']['garden']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $find_data = $this->gardenModel::find($id);
        if ($this->rp['teamodule']['garden']['alldata'] != 1) {
            if ($find_data->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$find_data) {
            return response()->json(['status' => 'error', 'message' => 'garden not found'], 404);
        }
        $data = $request->all();

        $validator = Validator::make($data, [
            'garden_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'mobile_1' => 'nullable|numeric|digits_between:10,15',
            'mobile_2' => 'nullable|numeric|digits_between:10,15',
            'country' => 'nullable|integer|exists:country,id',
            'state'   => 'nullable|integer|exists:state,id',
            'city'    => 'nullable|integer|exists:city,id',
            'pincode' => 'nullable|digits_between:4,8',
            'address' => 'nullable|string',
            'gst_no' => 'nullable|string|max:20',
            'pan'    => 'nullable|string|max:20',
        ], [
            'garden_name.required' => 'Garden name is required.',
            'garden_name.string'   => 'Garden name must be a string.',
            'garden_name.max'      => 'Garden name cannot exceed 255 characters.',

        ]);
        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }
        $exists = $this->gardenModel::where('garden_name', $request->garden_name)->exists();
        if ($exists) {
            return $this->errorresponse(422, ['garden_name' => ['This garden name has already been taken.']]);
        }
        $update = $this->gardenModel::where('id', $id)->update([
            'garden_name' => $request->garden_name,
            'email' => $request->email,
            'contact_person_name' => $request->contact_person_name,
            'mobile_1' => $request->mobile_1,
            'mobile_2' => $request->mobile_2,
            'country_id' => $request->country,
            'state_id' => $request->state,
            'city_id' => $request->city,
            'pincode' => $request->pincode,
            'gst_no' => $request->gst_no,
            'address' => $request->address,
            'pan' => $request->pan,
            'updated_by' => $request->user_id,
        ]);
        if ($update) {
            return $this->successresponse(200, 'message', 'garden succesfully update');
        } else {
            return $this->successresponse(500, 'message', 'garden not succesfully update !');
        }
    }
    public function gardendestroy($id)
    {
        if ($this->rp['teamodule']['garden']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $garden = $this->gardenModel::find($id);
        if ($this->rp['teamodule']['garden']['alldata'] != 1) {
            if ($garden->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$garden) {
            return $this->successresponse(500, 'message', 'garden not found !');
        }
        $garden->update(
            [
                "is_deleted" => 1
            ]
        );
        $this->companygardenModel::where('garden_id', $id)->delete();
        return $this->successresponse(200, 'message', 'garden succesfully deleted');
    }
    // this in create invoice list time show dropdown list 
    public function bank_detailindex(Request $request)
    {
        // dd($request->all());
        $bank = $this->bank_detail_masterModel::where('is_active', 1)->where('companymaster_id', $request->companymaster_id)
            ->where('is_deleted', 0)
            ->get();

        if ($bank->isEmpty()) {
            return $this->successresponse(404, 'bank', 'No Records Found');
        }
        return $this->successresponse(200, 'bank', $bank);
    }
    // this bank details list page api
    public function bank_detailslist()
    {

        // dd($request->all());
        $bank = $this->bank_detail_masterModel
            ::select(
                'bank_detail_master.*',
                'companymasters.company_name',
                 DB::raw("DATE_FORMAT(bank_detail_master.created_at, '%d-%m-%Y %h:%i %p') as created_at_formatted")
            )
            ->join('companymasters', 'companymasters.id', '=', 'bank_detail_master.companymaster_id')
            ->where('bank_detail_master.is_active', 1)
            ->where('bank_detail_master.is_deleted', 0)
            ->get();


        if ($bank->isEmpty()) {
            return DataTables::of($bank)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($bank)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }


    public function bank_detailstore(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'bank_companymaster_id' => 'required|string|max:255',
            'holder_name'           => 'required|string|max:255',
            'account_number'        => 'required|string|max:50',
            'swift_code'            => 'nullable|string|max:20',
            'ifsc_code'             => 'required|string|max:20',
            'bank_name'             => 'required|string|max:255',
            'branch_name'           => 'nullable|string|max:255',
            'user_id'               => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        $insertData = [
            'companymaster_id' => $data['bank_companymaster_id'],
            'holder_name'      => $data['holder_name'],
            'account_no'   => $data['account_number'],
            'swift_code'       => $data['swift_code'] ?? null,
            'ifsc_code'        => $data['ifsc_code'],
            'bank_name'        => $data['bank_name'],
            'branch_name'      => $data['branch_name'] ?? null,
            'created_by'       => $data['user_id'],
        ];

        // Insert and get ID
        $insertId = $this->bank_detail_masterModel::insertGetId($insertData);

        if ($insertId) {
            return $this->successresponse(200, 'message', 'bank Details succesfully added', 'insertId', $insertId);
        } else {
            return $this->successresponse(500, 'message', 'bank Details not succesfully added !');
        }
    }
    public function bankdetailspdf(string $id)
    {
        $bankdetailres = $this->bank_detail_masterModel::where('id', $id);

        $bankdetail = $bankdetailres->get();

        if ($bankdetail->isEmpty()) {
            return $this->successresponse(404, 'bankdetail', 'No Records Found');
        }

        if ($this->rp['invoicemodule']['bank']['view'] != 1 && $this->rp['reportmodule']['report']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        return $this->successresponse(200, 'bankdetail', $bankdetail);
    }
     public function bank_detailupdate(Request $request, string $id)
    {

        if ($this->rp['invoicemodule']['bank']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $bankdetail = $this->bank_detail_masterModel::find($id);


        if (!$bankdetail) {
            return $this->successresponse(404, 'message', 'No Such bank Found!');
        }

        if ($this->rp['invoicemodule']['bank']['alldata'] != 1) {
            if ($bankdetail->created_by != $this->userId) {
                return $this->successresponse(500, 'message', "You are Unauthorized!");
            }
        }

        $bankdetail->update([
            'is_active' => $request->status
        ]);

        return $this->successresponse(200, 'message', 'status succesfully updated');
    }
     public function bank_detaildestroy(Request $request, string $id)
    {

        if ($this->rp['invoicemodule']['bank']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $bankdetail = $this->bank_detail_masterModel::find($id);

        if (!$bankdetail) {
            return $this->successresponse(404, 'message', 'No Such bank Found!');
        }

        if ($this->rp['invoicemodule']['bank']['alldata'] != 1) {
            if ($bankdetail->created_by != $this->userId) {
                return $this->successresponse(500, 'message', "You are Unauthorized!");
            }
        }

        $bankdetail->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'bankdetail succesfully deleted');
    }
}
