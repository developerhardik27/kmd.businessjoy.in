<?php

namespace App\Http\Controllers\v4_2_3\api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class blogController extends commonController
{


    public $userId, $companyId, $masterdbname, $rp, $blogModel, $blog_settingModel;

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
        $this->blogModel = $this->getmodel('blog');
        $this->blog_settingModel = $this->getmodel('blog_setting');
    }

    /**
     * Summary of getSlug
     * return unique slug base on blog title 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getSlug(Request $request)
    {
        $slug = '';

        if ($request->slug) {
            $checkSlug = $this->blogModel::where('slug', $request->slug)->where('is_deleted', 0);

            if ($request->edit_id) {
                $checkSlug = $checkSlug->whereNot('id', $request->edit_id);
            }

            $checkSlug = $checkSlug->exists();

            if ($checkSlug) {
                return response()->json([
                    'status' => 422,
                    'slug' => str::slug($request->slug),
                    'message' => 'Slug is already in use.'
                ], status: 200);
            }

            return response()->json([
                'status' => 200,
                'slug' => str::slug($request->slug)
            ], 200);

        }

        return response()->json([
            'status' => 500,
            'slug' => $slug,
            'message' => 'slug is empty'
        ], 200);
    }

    /**
     * Summary of index
     * return blog list without content because its not necessary
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (isset($request->otherapi)) {
            if ($this->rp['blogmodule']['blogapi']['show'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $recent_post = $request->recent_post;
        $category = $request->category;
        $tag = $request->tag;
        $catids = $request->catids;


        $blogsquery = $this->blogModel::leftJoin('blog_categories', function ($join) {
            $join->on(DB::raw("FIND_IN_SET(blog_categories.id, blogs.cat_ids)"), '>', DB::raw('0'));
        })
            ->leftJoin('blog_tags', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(blog_tags.id, blogs.tag_ids)"), '>', DB::raw('0'));
            })
            ->leftJoin($this->masterdbname . '.users', 'blogs.created_by', '=', $this->masterdbname . '.users.id')
            ->select(
                'users.firstname',
                'users.lastname',
                'blogs.id',
                'blogs.title',
                'blogs.img',
                'blogs.slug',
                'blogs.short_desc',
                DB::raw('GROUP_CONCAT(DISTINCT blog_categories.cat_name) AS categories'),
                DB::raw('GROUP_CONCAT(DISTINCT blog_tags.tag_name) AS tags'),
                DB::raw("DATE_FORMAT(blogs.created_at, '%d-%m-%Y') as created_at_formatted")
            )
            ->groupBy(
                'users.firstname',
                'users.lastname',
                'blogs.id',
                'blogs.title',
                'blogs.img',
                'blogs.slug',
                'blogs.short_desc',
                'blogs.created_at'
            )
            ->where('blogs.is_deleted', 0)->orderBy('blogs.id', 'DESC');

        if (isset($catids)) {
            foreach ($catids as $value) {
                $blogsquery = $blogsquery->orWhere('blog_categories.id', $value)->where('blogs.is_deleted', 0);
            }
        }

        if (isset($category)) {
            $blogsquery = $blogsquery->where('blog_categories.slug', $category);
        }

        if (isset($tag)) {
            $blogsquery = $blogsquery->where('blog_tags.slug', $tag);
        }

        if (isset($recent_post)) {
            $blogsquery = $blogsquery->limit($recent_post);
        }

        $blogs = $blogsquery->get();

        if ($blogs->isEmpty()) {
            return $this->successresponse(404, 'blog', 'No Records Found');
        }
        return $this->successresponse(200, 'blog', $blogs);

    }

    /**
     * Summary of blogdatatable
     * return blog list without content because its not necessary
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function blogdatatable(Request $request)
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

        $blogsquery = $this->blogModel::leftJoin('blog_categories', function ($join) {
            $join->on(DB::raw("FIND_IN_SET(blog_categories.id, blogs.cat_ids)"), '>', DB::raw('0'));
        })
            ->leftJoin('blog_tags', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(blog_tags.id, blogs.tag_ids)"), '>', DB::raw('0'));
            })
            ->leftJoin($this->masterdbname . '.users', 'blogs.created_by', '=', $this->masterdbname . '.users.id')
            ->select(
                DB::raw("CONCAT_WS(' ', users.firstname, users.lastname) AS author"),
                'blogs.id',
                'blogs.title',
                'blogs.img',
                'blogs.slug',
                'blogs.short_desc',
                DB::raw('GROUP_CONCAT(DISTINCT blog_categories.cat_name) AS categories'),
                DB::raw('GROUP_CONCAT(DISTINCT blog_tags.tag_name) AS tags'),
                DB::raw("DATE_FORMAT(blogs.created_at, '%d-%m-%Y') as created_at_formatted")
            )
            ->groupBy(
                'users.firstname',
                'users.lastname',
                'blogs.id',
                'blogs.title',
                'blogs.img',
                'blogs.slug',
                'blogs.short_desc',
                'blogs.created_at'
            )
            ->where('blogs.is_deleted', 0)->orderBy('blogs.id', 'DESC');

        $totalcount = $blogsquery->get()->count();

        if ($this->rp['blogmodule']['blog']['alldata'] != 1) {
            $blogsquery->where('blogs.created_by', $this->userId);
        }

        $blogs = $blogsquery->get();

        if ($blogs->isEmpty()) {
            return DataTables::of($blogs)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        $blogSettings = $this->blog_settingModel::first();

        $blogDetailsEndpoint = null;

        if($blogSettings){
            $blogDetailsEndpoint = $blogSettings->details_endpoint ?? null ;
        }

        return DataTables::of($blogs)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
                'blogSettings' => $blogDetailsEndpoint
            ])->make(true);

    }




    /**
     * Summary of store
     * store new blog
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $fileAllowedType = 'jpg,jpeg,png';
        $fileMaxSize = 10 * 1024;
        $fileWidthDimension = 600;
        $fileHeightDimension = 400;
        $fileThumbnailWidthDimension = 400;
        $fileThumbnailHeightDimension = 266;

        $blogSettings = $this->blog_settingModel::first();

        if ($blogSettings) {
            if (!empty($blogSettings->img_allowed_filetype)) {
                 $fileAllowedType = implode(',', array_filter(array_map('trim', explode(',', $blogSettings->img_allowed_filetype))));
            }

            if (!empty($blogSettings->img_max_size)) {
                $fileMaxSize = $blogSettings->img_max_size * 1024; // from MB to KB
            }

            if (!empty($blogSettings->img_width)) {
                $fileWidthDimension = $blogSettings->img_width;
            }

            if (!empty($blogSettings->img_height)) {
                $fileHeightDimension = $blogSettings->img_height;
            }

            if (!empty($blogSettings->thumbnail_img_width)) {
                $fileThumbnailWidthDimension = $blogSettings->thumbnail_img_width;
            }

            if (!empty($blogSettings->thumbnail_img_height)) {
                $fileThumbnailHeightDimension = $blogSettings->thumbnail_img_height;
            }
        }

        // Build dynamic blog_image validation rule
        $imageValidationRules = [
            'nullable',
            'image',
            'mimes:' . $fileAllowedType,
            'max:' . $fileMaxSize,
        ];

        if($blogSettings && $blogSettings->validate_dimension == 1){
            // Only apply dimensions rule if width and height are both set
            if ($fileWidthDimension && $fileHeightDimension) {
                $imageValidationRules[] = "dimensions:width={$fileWidthDimension},height={$fileHeightDimension}";
            }
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'slug' => 'required|string|max:100',
            'content' => 'required|string',
            'meta_dsc' => 'nullable|string|max:200',
            'meta_keywords' => 'nullable|string|max:200',
            'short_description' => 'nullable|string|max:250',
            'category' => 'required',
            'tag' => 'required',
            'blog_image' => $imageValidationRules,
        ]);

        if ($validator->fails()) {

            if ($request->slug) {
                $checkSlug = $this->blogModel::where('slug', $request->slug)->where('is_deleted', 0)->exists();

                if ($checkSlug) {
                    $validator->errors()->add('slug', 'Slug is already in use.');
                }
            }

            return $this->errorresponse(422, $validator->messages());

        } else {

            if ($this->rp['blogmodule']['blog']['add'] == 1) {

                $blogdata = [];
                if ($request->hasFile('blog_image') && $request->hasFile('blog_image') != '') {
                    $image = $request->file('blog_image');
                    $imageName = Str::random('5') . time() . '.' . $image->getClientOriginalExtension();

                    $dateFolder = date('dmY');

                    $imgdestinationPath = public_path('uploads/') . $this->companyId . '/blog/' . $dateFolder;

                    //check directories exist
                    if (!file_exists($imgdestinationPath)) {
                        mkdir($imgdestinationPath, 0755, true);
                    }

                    // Save the image to the uploads directory
                    if ($image->move($imgdestinationPath, $imageName)) {
                        $blogdata['img'] = "{$this->companyId}/blog/{$dateFolder}/{$imageName}";
                        $blogdata['thumbnail_img'] = "{$this->companyId}/blog/{$dateFolder}/thumbnail/{$imageName}";
                    }

                    //save thumbnail img
                    $thumnaildestinationPath = public_path('uploads/') . $this->companyId . '/blog/' . $dateFolder . '/thumbnail';

                    //check directories exist
                    if (!file_exists($thumnaildestinationPath)) {
                        mkdir($thumnaildestinationPath, 0755, true);
                    }

                    $manager = new ImageManager(new Driver());
                    $thumnailimage = $manager->read($imgdestinationPath . '/' . $imageName);
                    $thumnailimage->resize($fileThumbnailWidthDimension, $fileThumbnailHeightDimension);
                    $thumnailimage->save($thumnaildestinationPath . '/' . $imageName);
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
                    'short_desc' => $request->short_description,
                    'created_by' => $this->userId,
                ]);

                $blog = $this->blogModel::create($blog);

                if (!$blog || !$blog->id) {
                    return $this->successresponse(500, 'message', 'Blog not successfully added');
                }

                return $this->successresponse(200, 'message', 'Blog succesfully added');

            } else {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
    }

    /**
     * Summary of show 
     * @param \Illuminate\Http\Request $request
     * @param string $slug
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $slug)
    {
        if (isset($request->otherapi)) {
            if ($this->rp['blogmodule']['blogapi']['show'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $blogs = $this->blogModel::join('blog_categories', function ($join) {
            $join->on(DB::raw("FIND_IN_SET(blog_categories.id, blogs.cat_ids)"), '>', DB::raw('0'));
        })
            ->join('blog_tags', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(blog_tags.id, blogs.tag_ids)"), '>', DB::raw('0'));
            })
            ->Join($this->masterdbname . '.users', 'blogs.created_by', '=', $this->masterdbname . '.users.id')
            ->select('users.firstname', 'users.lastname', 'blogs.id', 'blogs.title', 'blogs.img', 'blogs.short_desc', 'blogs.meta_dsc', 'meta_keywords', 'blogs.content', 'blogs.cat_ids', DB::raw('GROUP_CONCAT(DISTINCT blog_categories.cat_name) AS categories'), DB::raw('GROUP_CONCAT(DISTINCT blog_tags.tag_name) AS tags'), DB::raw("DATE_FORMAT(blogs.created_at, '%d-%M-%Y')as created_at_formatted"))
            ->groupBy('users.firstname', 'users.lastname', 'blogs.id', 'blogs.title', 'blogs.img', 'blogs.short_desc', 'blogs.meta_dsc', 'meta_keywords', 'blogs.content', 'blogs.created_at', 'blogs.cat_ids')
            ->where('blogs.is_deleted', 0)
            ->where('blogs.slug', $slug)
            ->get();



        if ($blogs->isEmpty()) {
            return $this->successresponse(404, 'blog', 'No Records Found');
        }
        return $this->successresponse(200, 'blog', $blogs);
    }

    /**
     * Summary of edit
     * return blog details for edit blog
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function edit(string $id)
    {
        $blog = $this->blogModel::find($id);

        if (!$blog) {
            return $this->successresponse(404, 'blog', 'No Records Found');
        }

        if ($this->rp['blogmodule']['blog']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($this->rp['blogmodule']['blog']['alldata'] != 1) {
            if ($blog->created_by != $this->userId) {
                return $this->successresponse(500, 'message', "You are Unauthorized!");
            }
        }



        return $this->successresponse(200, 'blog', $blog);



    }

    /**
     * Summary of update
     * update blog
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {

        $fileAllowedType = 'jpg,jpeg,png';
        $fileMaxSize = 10 * 1024;
        $fileWidthDimension = 600;
        $fileHeightDimension = 400;
        $fileThumbnailWidthDimension = 400;
        $fileThumbnailHeightDimension = 266;

        $blogSettings = $this->blog_settingModel::first();

        if ($blogSettings) {
            if (!empty($blogSettings->img_allowed_filetype)) {
                 $fileAllowedType = implode(',', array_filter(array_map('trim', explode(',', $blogSettings->img_allowed_filetype))));
            }

            if (!empty($blogSettings->img_max_size)) {
                $fileMaxSize = $blogSettings->img_max_size * 1024; // from MB to KB
            }

            if (!empty($blogSettings->img_width)) {
                $fileWidthDimension = $blogSettings->img_width;
            }

            if (!empty($blogSettings->img_height)) {
                $fileHeightDimension = $blogSettings->img_height;
            }

            if (!empty($blogSettings->thumbnail_img_width)) {
                $fileThumbnailWidthDimension = $blogSettings->thumbnail_img_width;
            }

            if (!empty($blogSettings->thumbnail_img_height)) {
                $fileThumbnailHeightDimension = $blogSettings->thumbnail_img_height;
            }
        }

        // Build dynamic blog_image validation rule
        $imageValidationRules = [
            'nullable',
            'image',
            'mimes:' . $fileAllowedType,
            'max:' . $fileMaxSize,
        ];

        if($blogSettings && $blogSettings->validate_dimension == 1){
            // Only apply dimensions rule if width and height are both set
            if ($fileWidthDimension && $fileHeightDimension) {
                $imageValidationRules[] = "dimensions:width={$fileWidthDimension},height={$fileHeightDimension}";
            }
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'slug' => 'required|string|max:100',
            'content' => 'required|string',
            'meta_dsc' => 'nullable|string|max:200',
            'meta_keywords' => 'nullable|string|max:200',
            'short_description' => 'nullable|string|max:250',
            'category' => 'required',
            'tag' => 'required',
            'blog_image' => $imageValidationRules
        ]);

        if ($validator->fails()) {

            if ($request->slug) {
                $checkSlug = $this->blogModel::where('slug', $request->slug)->where('is_deleted', 0);

                if ($request->edit_id) {
                    $checkSlug = $checkSlug->whereNot('id', $request->edit_id);
                }

                $checkSlug = $checkSlug->exists();

                if ($checkSlug) {
                    $validator->errors()->add('slug', 'Slug is already in use.');
                }
            }

            return $this->errorresponse(422, $validator->messages());
        } else {

            $blog = $this->blogModel::find($id);
            if (!$blog) {
                return $this->successresponse(500, 'message', 'No such Blog found!');
            }

            if ($this->rp['blogmodule']['blog']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            if ($this->rp['blogmodule']['blog']['alldata'] != 1) {
                if ($blog->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', "You are Unauthorized!");
                }
            }

            $blogdata = [];
            $blogdata['img'] = $blog->img;
            if ($request->hasFile('blog_image') && $request->hasFile('blog_image') != '') {
                $image = $request->file('blog_image');
                $imageName = Str::random('5') . time() . '.' . $image->getClientOriginalExtension();

                $dateFolder = date('dmY');

                $imgdestinationPath = public_path('uploads/') . $this->companyId . '/blog/' . $dateFolder;

                //check directories exist
                if (!file_exists($imgdestinationPath)) {
                    mkdir($imgdestinationPath, 0755, true);
                }

                // Save the image to the uploads directory
                if ($image->move($imgdestinationPath, $imageName)) {
                    $blogdata['img'] = "{$this->companyId}/blog/{$dateFolder}/{$imageName}";
                    $blogdata['thumbnail_img'] = "{$this->companyId}/blog/{$dateFolder}/thumbnail/{$imageName}";
                }

                //save thumbnail img
                $thumnaildestinationPath = public_path('uploads/') . $this->companyId . '/blog/' . $dateFolder . '/thumbnail';

                //check directories exist
                if (!file_exists($thumnaildestinationPath)) {
                    mkdir($thumnaildestinationPath, 0755, true);
                }

                $manager = new ImageManager(new Driver());
                $thumnailimage = $manager->read($imgdestinationPath . '/' . $imageName);
                $thumnailimage->resize($fileThumbnailWidthDimension, $fileThumbnailHeightDimension);
                $thumnailimage->save($thumnaildestinationPath . '/' . $imageName);

                //remove old img and thumbnail
                $oldImagePath = public_path('uploads/') . $blog->img;
                $oldThumnailImagePath = public_path('uploads/') . $blog->thumbnail_img;

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }

                if (file_exists($oldThumnailImagePath)) {
                    unlink($oldThumnailImagePath);
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
                'short_desc' => $request->short_description,
                'updated_by' => $this->userId,
                'updated_at' => now(),
            ]);

            $update = $this->blogModel::where('id', $id)->update($blog);

            if (!$update) {
                return $this->successresponse(500, 'message', 'Blog not succesfully Updated');
            }
            return $this->successresponse(200, 'message', 'Blog succesfully Updated');

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog = $this->blogModel::find($id);

        if (!$blog) {
            return $this->successresponse(404, 'message', 'No Such blog category Found!');

        }

        if ($this->rp['blogmodule']['blog']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($this->rp['blogmodule']['blog']['alldata'] != 1) {
            if ($blog->created_by != $this->userId) {
                return $this->successresponse(500, 'message', "You are Unauthorized!");
            }
        }

        $blog->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'blog succesfully deleted');
    }


    public function getblogsettings()
    {

        if ($this->rp['blogmodule']['blogsettings']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $settings = $this->blog_settingModel::first();

        if (!$settings) {
            return response()->json(['status' => 404, 'message' => 'Settings not found'], 404);
        }

        return $this->successresponse(200, 'blogsettings', $settings);
    }

    public function updateBlogSettings(Request $request)
    {
        if ($this->rp['blogmodule']['blogsettings']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'blog_details_endpoint' => 'required|url',
            'blog_image_allowed_filetype' => 'required|string',
            'blog_image_max_size' => 'required|numeric|min:1|max:20',
            'blog_image_width' => 'required|integer|min:50|max:2000',
            'blog_image_height' => 'required|integer|min:50|max:2000',
            'blog_thumbnail_image_width' => 'required|integer|min:50|max:2000',
            'blog_thumbnail_image_height' => 'required|integer|min:50|max:2000',
            'validate_dimenstion' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        $updateBlogSettings = $this->blog_settingModel::where('id', 1)->update([
            'details_endpoint' => $request->blog_details_endpoint,
            'img_allowed_filetype' => $request->blog_image_allowed_filetype,
            'img_max_size' => $request->blog_image_max_size,
            'img_width' => $request->blog_image_width,
            'img_height' => $request->blog_image_height,
            'thumbnail_img_width' => $request->blog_thumbnail_image_width,
            'thumbnail_img_height' => $request->blog_thumbnail_image_height,
            'validate_dimension' => $request->validate_dimenstion,
            'updated_by' => $this->userId
        ]);

        if (!$updateBlogSettings) {
            return $this->successresponse(500, 'message', 'Blog settings not succesfully Updated');
        }

        return $this->successresponse(200, 'message', 'Blog settings succesfully Updated');
    }
}
