<?php

namespace App\Http\Controllers\v4_2_3\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    public $version ;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name']; 
        }

    }

    public function index(){
        return view($this->version . '.admin.Report.report',['user_id' => Session::get('user_id'), 'company_id' => Session::get('company_id')]);
    }


}
