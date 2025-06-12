<?php

namespace App\Http\Controllers\v4_1_0\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TblLeadController extends Controller
{

    public $version, $leadModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->leadModel = 'App\\Models\\' . $this->version . "\\tbllead";
        } else {
            $this->leadModel = 'App\\Models\\v4_1_0\\tbllead';
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        return view($this->version . '.admin.Lead.lead', ['search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.Lead.leadform');
    }
   
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.Lead.leadupdateform', ['edit_id' => $id]);
    } 

      /**
     * Display a listing of the resource.
     */
    public function upcomingfollowup(Request $request)
    {
        return view($this->version . '.admin.Lead.upcomingfollowup');
    }

    public function analysis(Request $request)
    {
        return view($this->version . '.admin.Lead.analysis');
    }

    public function leadownerperformance(Request $request)
    {
        return view($this->version . '.admin.Lead.leadownerperformance');
    }

    public function recentactivity(Request $request)
    {
        return view($this->version . '.admin.Lead.recentactivity');
    }
    public function calendar(Request $request)
    {
        return view($this->version . '.admin.Lead.calendar');
    }

    
}
