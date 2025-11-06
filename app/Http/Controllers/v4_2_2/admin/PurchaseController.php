<?php

namespace App\Http\Controllers\v4_2_2\admin;

use App\Http\Controllers\Controller;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PurchaseController extends Controller
{

    public $version, $purchaseModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();

        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->purchaseModel = 'App\\Models\\' . $this->version . "\\Purchase";
        } else {
            $this->purchaseModel = 'App\\Models\\v4_2_2\\Purchase';
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        return view($this->version . '.admin.Purchase.purchase', ['search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.Purchase.purchaseform', ['company_id' => Session::get('company_id'), 'user_id' => Session::get('user_id')]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Purchase = $this->purchaseModel::findOrFail($id); 
        return view($this->version . '.admin.Purchase.purchaseview', ['company_id' => Session::get('company_id'), 'user_id' => Session::get('user_id'), 'id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    
        $Purchase = $this->purchaseModel::findOrFail($id); 

        return view($this->version . '.admin.Purchase.purchaseupdateform', ['edit_id' => $id]);
    }

}
