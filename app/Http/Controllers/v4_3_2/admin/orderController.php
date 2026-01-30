<?php

namespace App\Http\Controllers\v4_3_2\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class orderController extends Controller
{
    public $version, $orderModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->orderModel = 'App\\Models\\' . $this->version . "\\order";
        } else {
            $this->orderModel = 'App\\Models\\v4_3_2\\order';
        }
    }
    public function index(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        return view($this->version . '.admin.order.order', ["search" => $search]);
    }
    public function create()
    {
        return view($this->version . '.admin.order.orderform', ['company_id' => Session::get('company_id')]);
    }
    public function edit($id)
    {
        return view($this->version . '.admin.order.orderupdateform', ['edit_id' => $id]);
    }

    
}
