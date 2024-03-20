<?php

namespace App\Http\Controllers\v1_0_0\admin;

use App\Http\Controllers\Controller;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PurchaseController extends Controller
{

    public $version,$purchaseModel;
    public function __construct()
    {
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
        $this->version =  $_SESSION['folder_name'];

        if (isset($_SESSION['folder_name'])) {
            $this->purchaseModel = 'App\\Models\\' . $this->version . "\\Purchase";
        } else {
            $this->purchaseModel = 'App\\Models\\v1_0_0\\Purchase';
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view($this->version.'.admin.purchase');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version.'.admin.purchaseform', ['company_id' => Session::get('company_id'),'user_id'=>Session::get('user_id')]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    { 
        
        $dbname = company::find(Session::get('company_id'));
        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');

        $Purchase = $this->purchaseModel::findOrFail($id);
        $this->authorize('view', $Purchase);

        return view($this->version.'.admin.purchaseupdateform', ['company_id' => Session::get('company_id'),'user_id'=>Session::get('user_id'), 'edit_id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
