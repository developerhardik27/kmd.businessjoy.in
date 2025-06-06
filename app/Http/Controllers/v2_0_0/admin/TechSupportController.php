<?php

namespace App\Http\Controllers\v2_0_0\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TechSupportController extends Controller
{
    public $version, $customersupportModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->customersupportModel = 'App\\Models\\' . $this->version . "\\tech_support";
        } else {
            $this->customersupportModel = 'App\\Models\\v2_0_0\\tech_support';
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       
        return view($this->version . '.admin.techsupport');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.techsupportform');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.techsupportupdateform', ['edit_id' => $id]);
    }
}
