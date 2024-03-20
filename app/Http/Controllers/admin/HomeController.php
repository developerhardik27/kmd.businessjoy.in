<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{

    public $version;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        $this->version = $_SESSION['folder_name'];


    }

    public function index()
    {

        return view($this->version.'.admin.index');
    }



    public function logout(Request $request)
    {

        DB::table('users')
            ->where('id', session('user_id'))
            ->update(['api_token' => null]);

        $request->session()->forget([
            'admin_role',
            'user_id',
            'company_id',
            'name',
            'img',
            'api_token',
            'invoice',
            'menu',
            'lead',
            'customersupport',
            'user_permissions',
            'folder_name'
        ]);
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_destroy();
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login');
    }

    public function singlelogout(Request $request)
    {

        $request->session()->forget([
            'admin_role',
            'user_id',
            'company_id',
            'name',
            'img',
            'api_token',
            'invoice',
            'menu',
            'lead',
            'customersupport',
            'user_permissions',
            'folder_name'
        ]);
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
        session_destroy();
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login')->with('unauthorized','You are already logged in on a different device');
    }
}
