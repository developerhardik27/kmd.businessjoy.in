<?php

namespace App\Http\Controllers\v4_2_3\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class ConsignorCopyController extends Controller
{
    public $version, $invoiceModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->invoiceModel = 'App\\Models\\' . $this->version . "\\invoice";
        } else {
            $this->invoiceModel = 'App\\Models\\v4_2_3\\invoice';
        }
    }

      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view($this->version . '.admin.ConsignorCopy.consignorcopy');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    { 
        return view($this->version . '.admin.ConsignorCopy.consignorcopyform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.ConsignorCopy.consignorcopyupdateform', ['company_id' => Session::get('company_id'), 'user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }

    public function othersettings()
    {
        return view($this->version . '.admin.ConsignorCopy.othersettings', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }
}
