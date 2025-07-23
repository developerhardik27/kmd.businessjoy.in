<?php

namespace App\Http\Controllers\v4_2_3\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class BlogController extends Controller
{

    public $version, $blogModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->blogModel = 'App\\Models\\' . $this->version . "\\blog";
        } else {
            $this->blogModel = 'App\\Models\\v4_2_3\\blog';
        }

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }

        return view($this->version . '.admin.Blog.blog', ["search" => $search]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.Blog.blogform', ['company_id' => Session::get('company_id')]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.Blog.blogupdateform', ['company_id' => Session::get('company_id'), 'user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }

    public function blogcategory()
    {
        return view($this->version . '.admin.Blog.blogcategory', ['company_id' => Session::get('company_id')]);

    }
    public function blogtag()
    {
        return view($this->version . '.admin.Blog.blogtag', ['company_id' => Session::get('company_id')]);

    }

    /**
     * Summary of blogsettings
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function blogsettings(){
        return view($this->version . '.admin.Blog.blogsettings');
    }

    /**
     * Display a listing of the resource.
     * return blogapi table view
     */
    public function blogapi()
    {
        return view($this->version . '.admin.otherapi', ['module' => 'blog']);
    }

}
