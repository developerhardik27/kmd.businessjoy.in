<?php

namespace App\Http\Controllers\v4_1_0\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryController extends Controller
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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view($this->version . '.admin.Inventory.inventory');
    }
}
