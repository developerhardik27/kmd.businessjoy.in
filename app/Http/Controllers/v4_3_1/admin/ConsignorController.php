<?php

namespace App\Http\Controllers\v4_3_1\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class ConsignorController extends Controller
{
    public $version, $consignorModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->consignorModel = 'App\\Models\\' . $this->version . "\\consignor";
        } else {
            $this->consignorModel = 'App\\Models\\v4_3_1\\consignor';
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view($this->version . '.admin.Consignor.consignor');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    { 
        return view($this->version . '.admin.Consignor.consignorform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.Consignor.consignorupdateform', ['company_id' => Session::get('company_id'), 'user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }
}

