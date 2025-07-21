<?php

namespace App\Http\Controllers\v4_2_2\api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class blogtagController extends commonController
{


    public $userId, $companyId, $masterdbname, $rp, $blog_tagModel;

    public function __construct(Request $request)
    {
         $this->companyId = $request->company_id ?? session('company_id');
        $this->userId = $request->user_id ?? session('user_id');
        
        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();
        $this->blog_tagModel = $this->getmodel('blog_tag');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogtag = $this->blog_tagModel::where('is_deleted', 0)->get();


        if ($blogtag->isEmpty()) {
            return $this->successresponse(404, 'blogtag', 'No Records Found');
        }


        if ($this->rp['blogmodule']['blog']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        return $this->successresponse(200, 'blogtag', $blogtag);


    }

    /**
     * Display a listing of the resource.
     */
    public function blogtagdatatable()
    {

        if ($this->rp['blogmodule']['blog']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $blogtag = $this->blog_tagModel::where('is_deleted', 0)->get();

        if ($blogtag->isEmpty()) {

            return DataTables::of($blogtag)
                ->with([
                    'status' => 404,
                    'message' => 'No Records Found!'
                ])->make(true);
        }


        return DataTables::of($blogtag)
            ->with([
                'status' => 200
            ])->make(true);


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_name' => 'required|string|max:30',
        ]);

        if ($validator->fails()) {

            if ($request->tag_name) {
                $checkTag = $this->blog_tagModel::where('tag_name', $request->tag_name)->where('is_deleted', 0)->exists();

                if ($checkTag) {
                    $validator->errors()->add('tag_name', 'Duplicate tag name');
                }
            }

            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp['blogmodule']['blog']['add'] == 1) {

                $blogtag = $this->blog_tagModel::create([
                    'tag_name' => $request->tag_name,
                    'slug' => Str::slug($request->tag_name),
                    'created_by' => $this->userId,
                ]);

                if (!$blogtag || !$blogtag->id) {
                    return $this->successresponse(500, 'message', 'Blog tag not succesfully added');
                }

                return $this->successresponse(200, 'message', 'Blog tag  succesfully added');

            } else {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $blogtag = $this->blog_tagModel::find($id);


        if (!$blogtag) {
            return $this->successresponse(404, 'message', "No Such blog tag Found!");
        }

        if ($this->rp['blogmodule']['blog']['alldata'] != 1) {
            if ($blogtag->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }


        return $this->successresponse(200, 'blogtag', $blogtag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'tag_name' => 'required|string|max:30',
        ]);

        if ($validator->fails()) {

            if ($request->tag_name) {
                $checkTag = $this->blog_tagModel::where('tag_name', $request->tag_name)->where('is_deleted', 0)->where('id', $id)->exists();

                if ($checkTag) {
                    $validator->errors()->add('tag_name', 'Duplicate tag name');
                }
            }

            return $this->errorresponse(422, $validator->messages());
        } else {


            $blogtag = $this->blog_tagModel::where('tag_name', $request->tag_name)->where('id', '!=', $id)->where('is_deleted', 0)->first();

            if ($blogtag) {
                return $this->successresponse(500, 'message', 'This tag already exists!');
            }

            date_default_timezone_set('Asia/Kolkata');
            $this->blog_tagModel::where('id', $id) // Specify the condition to update the correct record
                ->update([
                    'tag_name' => $request->tag_name,
                    'slug' => Str::slug($request->tag_name),
                    'updated_by' => $this->userId,
                    'updated_at' => now(),
                ]);

            return $this->successresponse(200, 'message', 'Blog tag succesfully updated');

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blogtag = $this->blog_tagModel::find($id);

        if (!$blogtag) {
            return $this->successresponse(404, 'message', 'No Such blog tag Found!');

        }

        if ($this->rp['blogmodule']['blog']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }



        // Fetch only blogs where the tag ID exists in the tag_ids list
        $checktagexist = DB::connection('dynamic_connection')
            ->table('blogs')
            ->whereRaw("FIND_IN_SET(?, blogs.tag_ids) > 0", [$id]) // Use the tag ID to filter
            ->get();

        if ($checktagexist->isNotEmpty()) {
            //  you want to remove a specific tag ID:
            $tagIdToRemove = $id;  // Tag ID you want to remove (can be dynamic)

            foreach ($checktagexist as $blog) {
                // Get the current comma-separated list of tag IDs
                $tagIds = $blog->tag_ids;

                // Check if the tag exists in the list (using FIND_IN_SET for safety)
                if (strpos($tagIds, (string) $tagIdToRemove) !== false) {

                    // Remove the tag from the comma-separated list
                    $newTagIds = array_filter(explode(',', $tagIds), function ($tagId) use ($tagIdToRemove) {
                        return $tagId != $tagIdToRemove;
                    });

                    // Rebuild the comma-separated list
                    $newTagIdsString = implode(',', $newTagIds);

                    // Update the blog record with the new list
                    DB::connection('dynamic_connection')
                        ->table('blogs')
                        ->where('id', $blog->id)  // Assuming 'id' is the primary key
                        ->update(['tag_ids' => $newTagIdsString]);


                }
            }
        }

        $blogtag->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'blog tag succesfully deleted');

    }
}

