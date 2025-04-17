<?php

namespace App\Http\Controllers\v4_0_0\api;

use Illuminate\Support\Str;
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
     * Summary of index
     * return blog category list
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $blogcategory = $this->blogcategorymodel::where('is_deleted', 0)->get();

        if ($blogcategory->count() > 0) {
            return $this->successresponse(200, 'blogcategory', $blogcategory);
        } else {
            return $this->successresponse(404, 'blogcategory', 'No Records Found');
        }
    }
 
    /**
     * Summary of store
     * store new blog category
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {

            if ($request->category_name) {
                $checkcategory = $this->blogcategorymodel::where('cat_name', $request->category_name)->where('is_deleted', 0)->exists();

                if ($checkcategory) {
                    $validator->errors()->add('cat_name', 'Duplicate category name');
                }
            }

            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp['blogmodule']['blog']['add'] == 1) {

                $blogcategory = $this->blogcategorymodel::create([
                    'cat_name' => $request->category_name,
                    'slug' => Str::slug($request->category_name),
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
     * Summary of edit
     * edit blog category
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function edit(string $id)
    {
        $blogcategory = $this->blogcategorymodel::find($id);


        if (!$blogcategory) {
            return $this->successresponse(404, 'message', "No Such blog category Found!");
        }

        if ($this->rp['blogmodule']['blog']['alldata'] != 1) {
            if ($blogcategory->created_by != $this->userId) {
                return $this->successresponse(500, 'message', "You are Unauthorized!");
            }
        }


        return $this->successresponse(200, 'blogcategory', $blogcategory);

    }

    /**
     * Summary of update
     * update blog category
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {

            if ($request->category_name) {
                $checkcategory = $this->blogcategorymodel::where('cat_name', $request->category_name)->where('is_deleted', 0)->where('id', $id)->exists();

                if ($checkcategory) {
                    $validator->errors()->add('cat_name', 'Duplicate category name');
                }
            }

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
                        'slug' => Str::slug($request->category_name),
                        'updated_by' => $this->userId,
                        'updated_at' => now(),
                    ]);

                return $this->successresponse(200, 'message', 'Blog category succesfully updated');
            }
        }
    }

    /**
     * Summary of destroy
     * destroy blog category
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $blogcategory = $this->blogcategorymodel::find($id);


        if (!$blogcategory) {
            return $this->successresponse(404, 'message', 'No Such blog category Found!');
        }

        if ($this->rp['blogmodule']['blog']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        // Fetch only blogs where the cat ID exists in the cat_ids list
        $checkcatexist = DB::connection('dynamic_connection')
            ->table('blogs')
            ->whereRaw("FIND_IN_SET(?, blogs.cat_ids) > 0", [$id]) // Use the cat ID to filter
            ->get();

        if ($checkcatexist->isNotEmpty()) {
            // you want to remove a specific category ID:
            $catIdToRemove = $id;  // cat ID you want to remove (can be dynamic)

            foreach ($checkcatexist as $blog) {
                // Get the current comma-separated list of cat IDs
                $catIds = $blog->cat_ids;

                // Check if the cat exists in the list (using FIND_IN_SET for safety)
                if (strpos($catIds, (string) $catIdToRemove) !== false) {

                    // Remove the cat from the comma-separated list
                    $newCatIds = array_filter(explode(',', $catIds), function ($catId) use ($catIdToRemove) {
                        return $catId != $catIdToRemove;
                    });

                    // Rebuild the comma-separated list
                    $newCatIdsString = implode(',', $newCatIds);

                    // Update the blog record with the new list
                    DB::connection('dynamic_connection')
                        ->table('blogs')
                        ->where('id', $blog->id)  // Assuming 'id' is the primary key
                        ->update(['cat_ids' => $newCatIdsString]);


                }
            }
        }

        $blogcategory->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'blog category succesfully deleted');


    }
}
