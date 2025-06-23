<?php

namespace App\Http\Controllers\v4_2_0\admin;

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
            $this->leadModel = 'App\\Models\\v4_2_0\\tbllead';
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
     * create new lead
     */
    public function create()
    {
        return view($this->version . '.admin.Lead.leadform');
    }

    /**
     * Show the form for editing the specified resource.
     * edit lead
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.Lead.leadupdateform', ['edit_id' => $id]);
    }

    /**
     * Display a listing of the resource.
     * lead upcoming follow up
     */
    public function upcomingfollowup()
    {
        return view($this->version . '.admin.Lead.upcomingfollowup');
    }

    /**
     * Summary of analysis
     * lead analysis page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function analysis()
    {
        return view($this->version . '.admin.Lead.analysis');
    }

    /**
     * Summary of leadownerperformance
     * lead owner performance view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function leadownerperformance()
    {
        return view($this->version . '.admin.Lead.leadownerperformance');
    }

    /**
     * Summary of recentactivity
     * lead recent activity 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function recentactivity()
    {
        return view($this->version . '.admin.Lead.recentactivity');
    }

    /**
     * Summary of calendar
     * next follow up and call history in calendar view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function calendar()
    {
        return view($this->version . '.admin.Lead.calendar');
    }

    /**
     * Display a listing of the resource.
     * return leadapi table view
     */
    public function leadapi()
    {
        return view($this->version . '.admin.otherapi',['module' => 'lead']);
    }


}
