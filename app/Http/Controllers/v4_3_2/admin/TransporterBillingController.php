<?php

namespace App\Http\Controllers\v4_3_2\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransporterBillingController extends Controller
{
    public $version, $transporterbillingModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->transporterbillingModel = 'App\\Models\\' . $this->version . "\\transporter_billing";
        } else {
            $this->transporterbillingModel = 'App\\Models\\v4_3_1\\transporter_billing';
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view($this->version . '.admin.TransporterBilling.transporterbilling');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.TransporterBilling.transporterbillingform');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.TransporterBilling.transporterbillingupdateform', ['edit_id' => $id]);
    }
}
