<?php

namespace App\Http\Controllers\v4_3_2\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class brokeragebillController extends Controller
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
        return view($this->version . '.admin.brokeragebill.brokeragebill', ["search" => $search]);
    }
    public function create(Request $request)
    {
        return view($this->version . '.admin.brokeragebill.brokeragebillform', [
            'company_id' => Session::get('company_id'),
            'edit_id' => $request->id
        ]);
    }   
}
