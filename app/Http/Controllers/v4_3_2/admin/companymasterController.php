<?php

namespace App\Http\Controllers\v4_3_2\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class companymasterController extends Controller
{
    public $version, $companymasterModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->companymasterModel = 'App\\Models\\' . $this->version . "\\companymaster";
        } else {
            $this->companymasterModel = 'App\\Models\\v4_3_2\\companymaster';
        }
    }
    public function index(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        return view($this->version . '.admin.companymaster.companymaster', ["search" => $search]);
    }
    public function create()
    {
        return view($this->version . '.admin.companymaster.companymasterform', ['company_id' => Session::get('company_id')]);
    }
    public function edit($id)
    {
        return view($this->version . '.admin.companymaster.companymasterupdateform', ['edit_id' => $id]);
    }

    public function gardenindex(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        return view($this->version . '.admin.garden.garden', ["search" => $search]);
    }
    public function gardencreate()
    {
        return view($this->version . '.admin.garden.gardenform', ['company_id' => Session::get('company_id')]);
    }
    public function gardenedit($id)
    {
        return view($this->version . '.admin.garden.gardenupdateform', ['edit_id' => $id]);
    }
      public function bank_masterindex(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        return view($this->version . '.admin.bank_master.bank_master', ["search" => $search]);
    }
     public function bank_masteredit($id)
    {
        return view($this->version . '.admin.bank_master.bank_masterupdateform', ['edit_id' => $id]);
    }
}
