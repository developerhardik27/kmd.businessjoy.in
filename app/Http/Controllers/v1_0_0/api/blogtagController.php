<?php

namespace App\Http\Controllers\v1_0_0\api;

use App\Models\api_authorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class blogtagController extends commonController
{


    public $userId, $companyId, $masterdbname, $rp, $blogtagModel;

    public function __construct(Request $request)
    {

        if(isset($request->company_id) && isset($request->user_id)){
            $this->dbname($request->company_id);
            $this->companyId = $request->company_id;
            $this->userId = $request->user_id;
            // **** for checking user has permission to action on all data 
            $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
            $permissions = json_decode($user_rp, true);
            $this->rp = json_decode($permissions[0]['rp'], true);
        }elseif(isset($request->site_key) && isset($request->server_key)){
            if ($request->ajax()) {
                // Request was made via Ajax
                $domainName = $_SERVER['HTTP_ORIGIN'];
            } else {
                $domainName = $request->header('X-Custom-Origin');
            }
            $parsed_origin = parse_url($domainName);
            $hostname = isset($parsed_origin['host']) ? $parsed_origin['host'] : null;

            $company_id = api_authorization::where('site_key', $request->site_key)
                ->where('server_key', $request->server_key)
                ->where('domain_name', 'LIKE', '%' . $hostname . '%')
                ->select('company_id')
                ->get();

            if ($company_id->isEmpty()) {
                // Handle case where no record is found
                $this->returnresponse();
            }else{
                $this->dbname($company_id[0]->company_id);
                $this->companyId = $company_id[0]->company_id;
            }
        }else{
            $this->returnresponse();
        }
        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->blogtagModel = $this->getmodel('blog_tag');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogtag = DB::connection('dynamic_connection')->table('blog_tags')->where('is_deleted', 0)->get();

        if ($blogtag->count() > 0) {
            if ($this->rp['blogmodule']['blog']['view'] == 1) {
                return response()->json([
                    'status' => 200,
                    'blogtag' => $blogtag
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
                'blogtag' => 'No Records Found'
            ]);
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
            'tag_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            if ($this->rp['blogmodule']['blog']['add'] == 1) {

                $blogtag = $this->blogtagModel::create([
                    'tag_name' => $request->tag_name,
                    'created_by' => $this->userId,
                ]);

                if ($blogtag) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Blog tag  succesfully added'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Blog tag not succesfully added'
                    ]);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $blogtag = $this->blogtagModel::find($id);

        if ($this->rp['blogmodule']['blog']['alldata'] != 1) {
            if ($blogtag->created_by != $this->userId) {
                return response()->json([
                    'status' => 500,
                    'message' => "You are Unauthorized!"
                ]);
            }
        }

        if ($blogtag->count() > 0) {
            return response()->json([
                'status' => 200,
                'blogtag' => $blogtag
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No Such blog tag Found!"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'tag_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {


            $blogtag = $this->blogtagModel::where('tag_name', $request->tag_name)->where('id', '!=', $id)->where('is_deleted', 0)->first();

            if ($blogtag) {
                return response()->json([
                    'status' => 500,
                    'message' => 'This tag already exists!'
                ]);
            } else {
                date_default_timezone_set('Asia/Kolkata');
                $this->blogtagModel::where('id', $id) // Specify the condition to update the correct record
                    ->update([
                        'tag_name' => $request->tag_name,
                        'updated_by' => $this->userId,
                        'updated_at' => now(),
                    ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Blog tag succesfully updated'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blogtag = $this->blogtagModel::find($id);


        if ($blogtag) {
            if ($this->rp['blogmodule']['blog']['delete'] == 1) {
                $blogtag->update([
                    'is_deleted' => 1
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'blog tag succesfully deleted'
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
                'message' => 'No Such blog tag Found!'
            ]);
        }
    }
}
