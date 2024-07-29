<?php

namespace App\Http\Controllers\v1_1_0\admin;

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
            $this->blogModel = 'App\\Models\\v1_1_0\\blog';
        }

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view($this->version . '.admin.blog');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.blogform', ['company_id' => Session::get('company_id')]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        return view($this->version . '.admin.blogupdateform', ['company_id' => Session::get('company_id'),'user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function blogcategory()
    {
        return view($this->version . '.admin.blogcategory', ['company_id' => Session::get('company_id')]);

    }
    public function blogtag()
    {
        return view($this->version . '.admin.blogtag', ['company_id' => Session::get('company_id')]);

    }
}
