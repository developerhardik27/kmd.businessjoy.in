<?php

namespace App\Http\Controllers\v4_3_1\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class ConsigneeController extends Controller
{

    public $version, $consigneeModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->consigneeModel = 'App\\Models\\' . $this->version . "\\consignee";
        } else {
            $this->consigneeModel = 'App\\Models\\v4_3_1\\consignee';
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view($this->version . '.admin.Consignee.consignee');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    { 
        return view($this->version . '.admin.Consignee.consigneeform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.Consignee.consigneeupdateform', ['company_id' => Session::get('company_id'), 'user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }
}
