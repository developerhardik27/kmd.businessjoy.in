<?php

namespace App\Http\Controllers\v3_0_0\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BankDetailsController extends Controller
{
    public $version, $bankdetailModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->bankdetailModel = 'App\\Models\\' . $this->version . "\\bank_detail";
        } else {
            $this->bankdetailModel = 'App\\Models\\v3_0_0\\bank_detail';
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        return view($this->version . '.admin.bank');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.bankform', ['company_id' => Session::get('company_id')]);
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

        $bank_detail = $this->bankdetailModel::findOrFail($id);
        $this->authorize('view', $bank_detail);

        return view($this->version . '.admin.bankview', ['id', $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bank_detail = $this->bankdetailModel::findOrFail($id);
        $this->authorize('view', $bank_detail);

        return view($this->version . '.admin.bankupdateform', ['company_id' => Session::get('company_id'), 'edit_id' => $id]);
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
