<?php

namespace App\Http\Controllers\v4_2_2\admin;

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
            $this->bankdetailModel = 'App\\Models\\v4_2_2\\bank_detail';
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        return view($this->version . '.admin.Bank.bank');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.Bank.bankform', ['company_id' => Session::get('company_id')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bank_detail = $this->bankdetailModel::findOrFail($id);
        $this->authorize('view', $bank_detail);

        return view($this->version . '.admin.Bank.bankupdateform', ['company_id' => Session::get('company_id'), 'edit_id' => $id]);
    }

}
