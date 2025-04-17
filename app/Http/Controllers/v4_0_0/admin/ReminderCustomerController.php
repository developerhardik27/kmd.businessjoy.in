<?php

namespace App\Http\Controllers\v4_0_0\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ReminderCustomerController extends Controller
{

    public $version, $remindercustomerModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->remindercustomerModel = 'App\\Models\\' . $this->version . "\\remindercustomer";
        } else {
            $this->remindercustomerModel = 'App\\Models\\v4_0_0\\remindercustomer';
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view($this->version . '.admin.ReminderCustomer.remindercustomer');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.ReminderCustomer.remindercustomerform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.ReminderCustomer.remindercustomerupdateform', ['company_id' => Session::get('company_id'), 'user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }

}
