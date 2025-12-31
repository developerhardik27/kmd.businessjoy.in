<?php

namespace App\Http\Controllers\v4_3_1\api;

use App\Models\uuid_company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class apiserverkeyController extends commonController
{
    public $userId, $companyId, $masterdbname, $rp, $api_server_keyModel;

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
        $this->api_server_keyModel = $this->getmodel('api_server_key');

    }

    /**
     * Summary of index
     * return server key list
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($this->rp[$request->module . 'module'][$request->module . 'api']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $apiserverkeys = $this->api_server_keyModel::where('is_deleted', 0)
            ->select(
                'id',
                'server_key',
                'module',
                'title',
                'remarks',
                DB::raw('DATE_FORMAT(created_at,"%d-%M-%Y %h:%i %p") as created_at_formatted')
            );

        if ($this->rp[$request->module . 'module'][$request->module . 'api']['alldata'] != 1) {
            $apiserverkeys->where('created_by', $this->userId);
        }

        $totalcount = $apiserverkeys->get()->count(); // count total record

        $apiserverkeys = $apiserverkeys->get();

        $companyuuid = uuid_company::where('company_id', $this->companyId)->value('uuid');

        if ($apiserverkeys->isEmpty()) {
            return DataTables::of($apiserverkeys)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'companyuuid' => $companyuuid,
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($apiserverkeys)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
                'companyuuid' => $companyuuid,
            ])
            ->make(true);

    }


    /**
     * Summary of store
     * generate company uuid if not exits
     * generate server key
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if ($this->rp[$request->module . 'module'][$request->module . 'api']['add'] != 1) {
            return $this->successresponse('500', 'message', 'You are unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'remarks' => 'nullable',
            'module' => 'nullable',
            'company_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp[$request->module . 'module'][$request->module . 'api']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $checkCompanyUUId = uuid_company::where('company_id', $this->companyId)->exists();

            if (!$checkCompanyUUId) {
                // Generate UUID and ensure it's unique
                do {
                    $uuid = Str::uuid()->toString();
                } while (uuid_company::where('uuid', $uuid)->exists());

                // Create the record
                uuid_company::create([
                    'company_id' => $this->companyId,
                    'uuid' => $uuid
                ]);
            }

            $rawKey = Str::random(40);
            $hashedKey = hash('sha256', $rawKey);
            $serverKey = $this->api_server_keyModel::create([
                'server_key' => $hashedKey,
                'title' => $request->title,
                'module' => $request->module,
                'remarks' => $request->remarks,
                'created_by' => $this->userId,
            ]);

            if ($serverKey) {
                return $this->successresponse(200, 'message', 'Server Key generated successfully');
            } else {
                return $this->successresponse(500, 'message', 'Server Key not generated successfully');
            }

        }


    }


    /**
     * Summary of update
     * update title and remarks
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        if ($this->rp[$request->module . 'module'][$request->module . 'api']['edit'] != 1) {
            return $this->successresponse('500', 'message', 'You are unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'remarks' => 'nullable',
            'company_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $serverKey = $this->api_server_keyModel::find($id);

            if (!$serverKey) {
                return $this->successresponse(404, 'message', 'No such record found!');
            }

            if ($this->rp[$request->module . 'module'][$request->module . 'api']['alldata'] != 1) {
                if ($serverKey->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', "You are unauthorized!");
                }
            }

            $serverKey = $serverKey->update([
                'title' => $request->title,
                'remarks' => $request->remarks,
                'updated_by' => $this->userId,
            ]);

            if ($serverKey) {
                return $this->successresponse(200, 'message', 'Record updated successfully');
            } else {
                return $this->successresponse(500, 'message', 'Record not updated successfully');
            }

        }
    }

    /**
     * Summary of destroy
     * delete server key
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, int $id)
    {

        if ($this->rp[$request->module . 'module'][$request->module . 'api']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $serverKey = $this->api_server_keyModel::find($id);

        if (!$serverKey) {
            return $this->successresponse(404, 'message', 'No such record found!');
        }

        if ($this->rp[$request->module . 'module'][$request->module . 'api']['alldata'] != 1) {
            if ($serverKey->created_by != $this->userId) {
                return $this->successresponse(500, 'message', "You are unauthorized!");
            }
        }

        $serverKey->update([
            'is_deleted' => 1,
            'updated_by' => $this->userId
        ]);

        return $this->successresponse(200, 'message', 'Server Key succesfully deleted');

    }

}
