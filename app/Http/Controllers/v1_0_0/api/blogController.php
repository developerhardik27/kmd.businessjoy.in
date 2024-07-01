<?php

namespace App\Http\Controllers\v1_0_0\api;

use App\Models\api_authorization;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class blogController extends commonController
{


    public $userId, $companyId, $masterdbname, $rp, $blogModel;

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
        $this->blogModel = $this->getmodel('blog');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $recent_post = $request->recent_post;
        $category = $request->category;
        $catids = $request->catids;

        $blogsquery = DB::connection('dynamic_connection')
            ->table('blogs')
            ->join('blog_categories', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(blog_categories.id, blogs.cat_ids)"), '>', DB::raw('0'));
            })
            ->join('blog_tags', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(blog_tags.id, blogs.tag_ids)"), '>', DB::raw('0'));
            })
            ->Join($this->masterdbname . '.users', 'blogs.created_by', '=', $this->masterdbname . '.users.id')
            ->select('users.firstname', 'users.lastname', 'blogs.id', 'blogs.title', 'blogs.img', 'blogs.content', DB::raw('GROUP_CONCAT(DISTINCT blog_categories.cat_name) AS categories'), DB::raw('GROUP_CONCAT(DISTINCT blog_tags.tag_name) AS tags'), DB::raw("DATE_FORMAT(blogs.created_at, '%d-%m-%Y')"))
            ->groupBy('users.firstname', 'users.lastname', 'blogs.id', 'blogs.title', 'blogs.img', 'blogs.content', 'blogs.created_at')
            ->where('blogs.is_deleted', 0)->orderBy('blogs.id', 'DESC');

        if (isset($catids)) {
            foreach ($catids as $value) {
                $blogsquery = $blogsquery->orWhere('blogs.cat_ids', 'LIKE', '%' . $value . '%');
            }
        }

        if (isset($category)) {
            $blogsquery = $blogsquery->where('blogs.cat_ids', 'LIKE', '%' . $category . '%');
        }

        if (isset($recent_post)) {
            $blogsquery = $blogsquery->limit($recent_post);
        }

        $blogs = $blogsquery->get();

        if ($blogs->count() > 0) {
            return response()->json([
                'status' => 200,
                'blog' => $blogs
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'blog' => 'No Records Found'
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
            'title' => 'required|string',
            'slug' => 'required|string',
            'content' => 'required|string',
            'meta_dsc' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'category' => 'required',
            'tag' => 'required',
            'blog_image' => 'required|image|mimes:jpg,jpeg,png|max:10240'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            if ($this->rp['blogmodule']['blog']['add'] == 1) {

                $blogdata = [];
                if ($request->hasFile('blog_image') && $request->hasFile('blog_image') != '') {
                    $image = $request->file('blog_image');
                    $imageName = $request->title . time() . '.' . $image->getClientOriginalExtension();

                    if (!file_exists('blog/')) {
                        mkdir('blog/', 0755, true);
                    }
                    // Save the image to the uploads directory
                    if ($image->move('blog/', $imageName)) {
                        $blogdata['img'] = $imageName;
                    }

                }

                $tags = implode(',', $request->tag);
                $categories = implode(',', $request->category);
                $blog = array_merge($blogdata, [
                    'title' => $request->title,
                    'content' => $request->content,
                    'slug' => $request->slug,
                    'tag_ids' => $tags,
                    'cat_ids' => $categories,
                    'meta_dsc' => $request->meta_dsc,
                    'meta_keywords' => $request->meta_keywords,
                    'created_by' => $this->userId,
                ]);

                $blog = $this->blogModel::create($blog);

                if ($blog) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Blog succesfully added'
                    ], 200);

                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Blog not succesfully added'
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
    public function show(Request $request, string $id)
    {
        $blogs = DB::connection('dynamic_connection')
            ->table('blogs')
            ->join('blog_categories', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(blog_categories.id, blogs.cat_ids)"), '>', DB::raw('0'));
            })
            ->join('blog_tags', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(blog_tags.id, blogs.tag_ids)"), '>', DB::raw('0'));
            })
            ->Join($this->masterdbname . '.users', 'blogs.created_by', '=', $this->masterdbname . '.users.id')
            ->select('users.firstname', 'users.lastname', 'blogs.id', 'blogs.title', 'blogs.img', 'blogs.content', 'blogs.cat_ids', DB::raw('GROUP_CONCAT(DISTINCT blog_categories.cat_name) AS categories'), DB::raw('GROUP_CONCAT(DISTINCT blog_tags.tag_name) AS tags'), DB::raw("DATE_FORMAT(blogs.created_at, '%d-%M-%Y')as created_at"))
            ->groupBy('users.firstname', 'users.lastname', 'blogs.id', 'blogs.title', 'blogs.img', 'blogs.content', 'blogs.created_at', 'blogs.cat_ids')
            ->where('blogs.is_deleted', 0)
            ->where('blogs.id', $id)
            ->get();



        if ($blogs->count() > 0) {
            return response()->json([
                'status' => 200,
                'blog' => $blogs
            ], 200);
          
        } else {
            return response()->json([
                'status' => 404,
                'blog' => 'No Records Found'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $blog = $this->blogModel::find($id);

        if ($blog->count() > 0) {
            if ($this->rp['blogmodule']['blog']['view'] == 1) {
                return response()->json([
                    'status' => 200,
                    'blog' => $blog
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
                'blog' => 'No Records Found'
            ]);
        }
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'slug' => 'required|string',
            'content' => 'required|string',
            'meta_dsc' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'category' => 'required',
            'tag' => 'required',
            'blog_image' => 'nullable|image|mimes:jpg,jpeg,png|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            $blog = $this->blogModel::find($id);
            if (!$blog) {
                return response()->json([
                    'status' => 500,
                    'message' => ' No such Blog found!'
                ]);
            }
            if ($this->rp['blogmodule']['blog']['edit'] == 1) {

                $blogdata = [];
                $blogdata['img'] = $blog->img;
                if ($request->hasFile('blog_image') && $request->hasFile('blog_image') != '') {
                    $image = $request->file('blog_image');
                    $imageName = $request->firstname . time() . '.' . $image->getClientOriginalExtension();

                    // Save the image to the uploads directory
                    if ($image->move('blog/', $imageName)) {
                        $blogdata['img'] = $imageName;
                    }

                    $oldImagePath = public_path('blog') . '/' . $blog->img;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }

                }

                $tags = implode(',', $request->tag);
                $categories = implode(',', $request->category);
                $blog = array_merge($blogdata, [
                    'title' => $request->title,
                    'content' => $request->content,
                    'slug' => $request->slug,
                    'tag_ids' => $tags,
                    'cat_ids' => $categories,
                    'meta_dsc' => $request->meta_dsc,
                    'meta_keywords' => $request->meta_keywords,
                    'updated_by' => $this->userId,
                    'updated_at' => now(),
                ]);




                $update = $this->blogModel::where('id', $id)->update($blog);

                if ($update) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Blog succesfully Updated'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Blog not succesfully Updated'
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog = $this->blogModel::find($id);


        if ($blog) {
            if ($this->rp['blogmodule']['blog']['delete'] == 1) {
                $blog->update([
                    'is_deleted' => 1
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'blog succesfully deleted'
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
                'message' => 'No Such blog category Found!'
            ]);
        }
    }
}
