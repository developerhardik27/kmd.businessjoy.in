<?php

namespace App\Http\Controllers\v4_3_0\admin;

use App\Models\company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CompanyController extends Controller
{ 
    public $version;
    public function __construct()
    {
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
        if(isset($_SESSION['folder_name'])){
            $this->version =  $_SESSION['folder_name'];
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view($this->version.'.admin.Company.company');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {    
        return view($this->version.'.admin.Company.companyform',['user_id'=> Session::get('user_id') ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    { 
        $company = company::findOrFail($id);
        $this->authorize('view', $company);
        return view($this->version.'.admin.Company.companyupdateform',['user_id'=> Session::get('user_id') ,'edit_id' => $id ]);
    }
    public function companyprofile(string $id)
    {     
        $company = company::findOrFail($id);
        $this->authorize('view', $company);
        return view($this->version.'.admin.Company.companyprofile',['user_id'=> Session::get('user_id')]);
    }
    public function editcompany(string $id)
    {     
        $company = company::findOrFail($id);
        $this->authorize('view', $company);
        return view($this->version.'.admin.Company.editcompany',['user_id'=> Session::get('user_id') ,'edit_id' =>  $id  ]);
    }

    public function api_authorization(){

       if(session('admin_role') != 1){
            abort(403);
        }

        return view($this->version . '.admin.Company.api_authorization', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }
}
