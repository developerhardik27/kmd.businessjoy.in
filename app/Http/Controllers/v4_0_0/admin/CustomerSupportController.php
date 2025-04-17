<?php

namespace App\Http\Controllers\v4_0_0\admin;

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
            $this->customersupportModel = 'App\\Models\\v4_0_0\\customersupporthistory';
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
        return view($this->version . '.admin.CustomerSupport.customersupport', ['search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.CustomerSupport.customersupportform');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.CustomerSupport.customersupportupdateform', ['edit_id' => $id]);
    }

}
