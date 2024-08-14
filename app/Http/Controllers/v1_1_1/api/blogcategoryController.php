<?php

namespace App\Http\Controllers\v1_1_1\api;

use App\Models\api_authorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class blogcategoryController extends commonController
{


    public $userId, $companyId, $masterdbname, $rp, $blogcategorymodel;

    public function __construct(Request $request)
    {


        if (isset($request->company_id) && isset($request->user_id)) {
            $this->dbname($request->company_id);
            $this->companyId = $request->company_id;
            $this->userId = $request->user_id;
            // **** for checking user has permission to action on all data 
            $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
            $permissions = json_decode($user_rp, true);
            $this->rp = json_decode($permissions[0]['rp'], true);
        } elseif (isset($request->site_key) && isset($request->server_key)) {
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
            } else {
                $this->dbname($company_id[0]->company_id);
                $this->companyId = $company_id[0]->company_id;
            }
        } else {
            $this->returnresponse();
        }
        $this->masterdbname = DB::connection()->getDatabaseName();
        $this->blogcategorymodel = $this->getmodel('blog_category');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogcategory = DB::connection('dynamic_connection')->table('blog_categories')->where('is_deleted', 0)->get();

        if ($blogcategory->count() > 0) {
            return $this->successresponse(200, 'blogcategory', $blogcategory);
        } else {
            return $this->successresponse(404, 'blogcategory', 'No Records Found');
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
            'category_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp['blogmodule']['blog']['add'] == 1) {

                $blogcategory = $this->blogcategorymodel::create([
                    'cat_name' => $request->category_name,
                    'created_by' => $this->userId,
                ]);

                if ($blogcategory) {
                    return $this->successresponse(200, 'message', 'Blog Category  succesfully added');
                } else {
                    return $this->successresponse(500, 'message', 'Blog Category not succesfully added');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $blogcategory = $this->blogcategorymodel::find($id);

        if ($this->rp['blogmodule']['blog']['alldata'] != 1) {
            if ($blogcategory->created_by != $this->userId) {
                return $this->successresponse(500, 'message', "You are Unauthorized!");
            }
        }

        if ($blogcategory->count() > 0) {
            return $this->successresponse(200, 'blogcategory', $blogcategory);
        } else {
            return $this->successresponse(404, 'message', "No Such blog category Found!");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {


            $blogcategory = $this->blogcategorymodel::where('cat_name', $request->category_name)->where('id', '!=', $id)->where('is_deleted', 0)->first();

            if ($blogcategory) {
                return $this->successresponse(500, 'message', 'This Category already exists!');
            } else {
                date_default_timezone_set('Asia/Kolkata');

                $this->blogcategorymodel::where('id', $id) // Specify the condition to update the correct record
                    ->update([
                        'cat_name' => $request->category_name,
                        'updated_by' => $this->userId,
                        'updated_at' => now(),
                    ]);

                return $this->successresponse(200, 'message', 'Blog category succesfully updated');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blogcategory = $this->blogcategorymodel::find($id);


        if ($blogcategory) {
            if ($this->rp['blogmodule']['blog']['delete'] == 1) {
                $blogcategory->update([
                    'is_deleted' => 1
                ]);
                return $this->successresponse(200, 'message','blog category succesfully deleted');
            } else {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        } else {
            return $this->successresponse(404, 'message',  'No Such blog category Found!');
        }
    }
}
