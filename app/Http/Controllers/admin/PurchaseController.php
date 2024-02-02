<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\company;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.purchase');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.purchaseform', ['company_id' => Session::get('company_id'),'user_id'=>Session::get('user_id')]);
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

        $Purchase = Purchase::findOrFail($id);
        $this->authorize('view', $Purchase);

        return view('admin.purchaseupdateform', ['company_id' => Session::get('company_id'),'user_id'=>Session::get('user_id'), 'edit_id' => $id]);
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
