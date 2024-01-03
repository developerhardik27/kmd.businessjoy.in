<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
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
