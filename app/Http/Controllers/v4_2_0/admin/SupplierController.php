<?php

namespace App\Http\Controllers\v4_2_0\admin;

use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class SupplierController extends Controller
{

    public $version, $supplierModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->supplierModel = 'App\\Models\\' . $this->version . "\\supplier";
        } else {
            $this->supplierModel = 'App\\Models\\v4_2_0\\supplier';
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view($this->version . '.admin.Suppliers.supplier');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view($this->version . '.admin.Suppliers.supplierform', ['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
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


        return view($this->version . '.admin.Suppliers.supplierupdateform', ['company_id' => Session::get('company_id'), 'user_id' => Session::get('user_id'), 'edit_id' => $id]);
    }

    
}
