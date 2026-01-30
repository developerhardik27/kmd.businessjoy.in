<?php

namespace App\Http\Controllers\v4_3_2\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class brokerPurchaseController extends Controller
{
    public $version, $brokerpurchaseModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->brokerpurchaseModel = 'App\\Models\\' . $this->version . "\\broker_purchase";
        } else {
            $this->brokerpurchaseModel = 'App\\Models\\v4_3_2\\broker_purchase';
        }
    }

    public function index(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        return view($this->version . '.admin.brokerpurchase.brokerpurchase', ["search" => $search]);
    }
    public function create()
    {
        return view($this->version . '.admin.brokerpurchase.brokerpurchaseform', ['company_id' => Session::get('company_id')]);
    }
    public function edit($id)
    {
        return view($this->version . '.admin.brokerpurchase.brokerpurchaseupdateform', ['edit_id' => $id]);
    }
    public function storeInvoiceSession(Request $request)
    {
         session()->flash('invoice_data', $request->data);
        //session(['invoice_data' => $request->data]);
        return response()->json(['status' => 200]);
    }
}
