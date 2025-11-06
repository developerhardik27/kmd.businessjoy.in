<?php

namespace App\Http\Controllers\v4_3_0\admin;

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
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
        }
    }
    public function versioncontrol()
    {
        if(session('admin_role') == 1){

            $versionFiles = config('app.version_files') ?? [];

            return view($this->version . '.admin.versionupdate', compact('versionFiles'));
        }

        abort(404);
    }
}
