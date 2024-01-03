<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\bank_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BankDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.bank');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.bankform',['company_id'=> Session::get('company_id')]);
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

        $bank_detail = bank_detail::findOrFail($id);
        $this->authorize('view', $bank_detail);

        return view('admin.bankview',['id',$id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bank_detail = bank_detail::findOrFail($id);
        $this->authorize('view', $bank_detail);

        return view('admin.bankupdateform',['company_id'=> Session::get('company_id'),'edit_id' => $id]);
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
