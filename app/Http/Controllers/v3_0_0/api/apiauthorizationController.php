<?php

namespace App\Http\Controllers\v3_0_0\api;

use App\Models\api_authorization;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class apiauthorizationController extends commonController
{

    public $userId, $companyId, $masterdbname;

    public function __construct(Request $request)
    {

        $this->userId = $request->user_id;
        $this->companyId = $request->company_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->checkuser();
    }

    public function checkuser()
    {
        if ($this->companyId != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized!');
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apiauth = DB::table('api_authorization')
            ->join('company', 'api_authorization.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->where('api_authorization.is_deleted', 0)
            ->select('api_authorization.*', 'company_details.name')
            ->get();

        if ($apiauth->count() > 0) {
            return $this->successresponse(200, 'apiauth', $apiauth);
        } else {
            return $this->successresponse(404, 'apiauth', 'No Records Found');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company' => 'required',
            'domain_name' => 'required',
        ]);

        if ($validator->fails()) { 
            return $this->errorresponse(422,$validator->messages());
        } else {

            do {
                $sitekey = Str::random(40);
                $serverkey = Str::random(40);

                $checkoldred = api_authorization::where('site_key', $sitekey)
                    ->where('server_key', $serverkey)
                    ->first();

                // Retry until unique keys are generated
            } while ($checkoldred);

            $creatrecord = api_authorization::create([
                'site_key' => $sitekey,
                'server_key' => $serverkey,
                'domain_name' => $request->domain_name,
                'company_id' => $request->company,
                'created_by' => $this->userId,
            ]);
            if ($creatrecord) {
                return $this->successresponse(200, 'message', 'Api authorization succesfully added');
            } else {
                return $this->successresponse(500, 'message', 'Api authorization not succesfully added');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $apiauth = api_authorization::find($id);

        if ($apiauth->count() > 0) {
            return $this->successresponse(200, 'apiauth', $apiauth);
        } else {
            return $this->successresponse(404, 'apiauth', 'No Records Found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'company' => 'required',
            'domain_name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422,$validator->messages());
        } else {
            $updaterecord = api_authorization::where('id', $id)->update([
                'domain_name' => $request->domain_name,
                'company_id' => $request->company,
                'updated_by' => $this->userId,
                'updated_at' => now(),
            ]);
            if ($updaterecord) {
                return $this->successresponse(200, 'message', 'Api authorization succesfully updated');
            } else {
                return $this->successresponse(500, 'message', 'Api authorization not succesfully updated');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $apiauth = api_authorization::find($id);

        if ($apiauth) {
            $apiauth->update([
                'is_deleted' => 1
            ]);

            return $this->successresponse(200, 'message', 'Authorization succesfully deleted');
        } else {
            return $this->successresponse(404, 'message', 'No such record found!');
        }
    }
}
