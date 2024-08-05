<?php

namespace App\Http\Controllers\v1_1_1\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerSupportController extends Controller
{

    public $version, $customersupportModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->customersupportModel = 'App\\Models\\' . $this->version . "\\customersupporthistory";
        } else {
            $this->customersupportModel = 'App\\Models\\v1_1_1\\customersupporthistory';
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
        return view($this->version . '.admin.customersupport', ['search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.customersupportform');
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
        return view($this->version . '.admin.customersupportupdateform', ['edit_id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
