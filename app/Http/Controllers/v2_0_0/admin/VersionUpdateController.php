<?php

namespace App\Http\Controllers\v2_0_0\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VersionUpdateController extends Controller
{

    public $version;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
        session_start();
        if(isset($_SESSION['folder_name'])){
            $this->version =  $_SESSION['folder_name'];
        }
    }
    public function versioncontrol(){
        if(session('admin_role') == 1){
            return view($this->version.'.admin.versionupdate');
        }
        abort(404);
    }
}
