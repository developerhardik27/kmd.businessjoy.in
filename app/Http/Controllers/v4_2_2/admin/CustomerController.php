<?php

namespace App\Http\Controllers\v4_2_2\admin;

use App\Http\Controllers\Controller;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{

    public $version, $customerModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->customerModel = 'App\\Models\\' . $this->version . "\\customer";
        } else {
            $this->customerModel = 'App\\Models\\v4_2_2\\customer';
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view($this->version . '.admin.Customer.customer');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view($this->version . '.admin.Customer.customerform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.Customer.customerupdateform', ['company_id' => Session::get('company_id'), 'user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }

}
