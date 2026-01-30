<?php

namespace App\Http\Controllers\v4_3_2\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class partyController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $partyModel, $gradeModel;

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

        $this->partyModel = $this->getmodel('party');
        $this->gradeModel = $this->getmodel('grade');
    }
    public function partyindex()
    {
        if ($this->rp['teamodule']['party']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $party = $this->partyModel::where("is_delete", 0)->get();
        if ($party->isEmpty()) {
            return DataTables::of($party)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($party)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }
    public function buyerindex()
    {
        if ($this->rp['teamodule']['party']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $party = $this->partyModel::where("is_delete", 0)
            ->where('party_type', 'Buyer')
            ->get();
        if ($party->isEmpty()) {
            return DataTables::of($party)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($party)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }
    public function transportindex()
    {
        if ($this->rp['teamodule']['party']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $party = $this->partyModel::where("is_delete", 0)
            ->where('party_type', 'Transport')
            ->get();
        if ($party->isEmpty()) {
            return DataTables::of($party)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($party)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }
    public function partystore(Request $request)
    {
        if ($this->rp['teamodule']['party']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $data = $request->all();

        $validator = Validator::make($data, [
            'name'                => 'required|string|max:255',
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
            'party_type'          => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }
        $create = $this->partyModel::create([
            'name' => $request->name,
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
            'party_type' => $request->party_type,
            'created_by' => $request->user_id,
        ]);
        if ($create) {
            return $this->successresponse(200, 'message', 'party succesfully added', 'party_id', $create->id);
        } else {
            return $this->successresponse(500, 'message', 'party not succesfully added !');
        }
    }
    public function partyedit($id)
    {
        if ($this->rp['teamodule']['party']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
         $party = $this->partyModel::find($id);
        
      
        if ($this->rp['teamodule']['grade']['alldata'] != 1) {
            if ($party->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$party) {
            return $this->successresponse(500, 'message', 'party not found !');
        }
        return $this->successresponse(200, 'party', $party);
    }
    public function partydetailspdf($id)
    {
        // $party = $this->partyModel::find($id);
         $party = $this->partyModel::leftJoin($this->masterdbname . '.country as c', 'partys.country_id', '=', 'c.id')
            ->leftJoin($this->masterdbname . '.state as s', 'partys.state_id', '=', 's.id')
            ->leftJoin($this->masterdbname . '.city as ci', 'partys.city_id', '=', 'ci.id')
            ->select(
                'partys.*',
                'c.country_name',
                's.state_name',
                'ci.city_name'
            )
            ->where('partys.id', $id)
            ->first(); 
        if (!$party) {
            return $this->successresponse(500, 'message', 'party not found !');
        }
        return $this->successresponse(200, 'party', $party);
    }
    public function partyupdate(Request $request, $id)
    {
        if ($this->rp['teamodule']['party']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $find_data = $this->partyModel::find($id);
        if ($this->rp['teamodule']['grade']['alldata'] != 1) {
            if ($find_data->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$find_data) {
            return response()->json(['status' => 'error', 'message' => 'party not found'], 404);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'                => 'required|string|max:255',
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
            'party_type'          => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        $update = $this->partyModel::where('id', $id)->update([
            'name' => $request->name,
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
            'party_type' => $request->party_type,
            'updated_by' => $request->user_id,
        ]);

        if ($update) {
            return $this->successresponse(200, 'message', 'party succesfully update');
        } else {
            return $this->successresponse(500, 'message', 'party not succesfully update !');
        }
    }
    public function partydestroy($id)
    {
        if ($this->rp['teamodule']['party']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $party = $this->partyModel::find($id);
        if ($this->rp['teamodule']['grade']['alldata'] != 1) {
            if ($party->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$party) {
            return $this->successresponse(500, 'message', 'party not found !');
        }
        $party->update(
            [
                "is_delete" => 1
            ]
        );

        return $this->successresponse(200, 'message', 'party succesfully deleted');
    }

    public function gradeindex()
    {
        if ($this->rp['teamodule']['grade']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $grade = $this->gradeModel::where("is_delete", 0)->get();
        if ($grade->isEmpty()) {
            return DataTables::of($grade)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }
        return DataTables::of($grade)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }
    public function gradestore(Request $request)
    {
        if ($this->rp['teamodule']['grade']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $data = $request->all();

        $validator = Validator::make($data, [
            'grade' => 'required|string|max:255',
        ], [
            'grade.required' => 'Grade is required.',
            'grade.string'   => 'Grade must be a string.',
            'grade.max'      => 'Grade cannot exceed 255 characters.',
        ]);
        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        $create = $this->gradeModel::create([
            'grade' => $request->grade,
            'created_by' => $request->user_id,
        ]);
        if ($create) {
            return $this->successresponse(200, 'message', 'Garde succesfully added');
        } else {
            return $this->successresponse(500, 'message', 'Garde not succesfully added !');
        }
    }
    public function gradeedit($id)
    {
        if ($this->rp['teamodule']['grade']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $Garde = $this->gradeModel::find($id);
        if ($this->rp['teamodule']['grade']['alldata'] != 1) {
            if ($Garde->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$Garde) {
            return $this->successresponse(500, 'message', 'Garde not found !');
        }
        return $this->successresponse(200, 'garde', $Garde);
    }
    public function gradeupdate(Request $request, $id)
    {
        if ($this->rp['teamodule']['grade']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $find_data = $this->gradeModel::find($id);
        if ($this->rp['teamodule']['grade']['alldata'] != 1) {
            if ($find_data->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$find_data) {
            return response()->json(['status' => 'error', 'message' => 'Garde not found'], 404);
        }
        $data = $request->all();

        $validator = Validator::make($data, [
            'grade' => 'required|string|max:255',
        ], [
            'grade.required' => 'Grade is required.',
            'grade.string'   => 'Grade must be a string.',
            'grade.max'      => 'Grade cannot exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }
        $update = $this->gradeModel::where('id', $id)->update([
            'grade' => $request->grade,
            'updated_by' => $request->user_id,
        ]);

        if ($update) {
            return $this->successresponse(200, 'message', 'Garde succesfully update');
        } else {
            return $this->successresponse(500, 'message', 'Garde not succesfully update !');
        }
    }
    public function gradedestroy($id)
    {
        if ($this->rp['teamodule']['grade']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $grade = $this->gradeModel::find($id);
        if ($this->rp['teamodule']['grade']['alldata'] != 1) {
            if ($grade->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if (!$grade) {
            return $this->successresponse(500, 'message', 'Garde not found !');
        }
        $grade->update(
            [
                "is_delete" => 1
            ]
        );

        return $this->successresponse(200, 'message', 'Garde succesfully deleted');
    }
}
