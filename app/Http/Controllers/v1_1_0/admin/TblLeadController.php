<?php

namespace App\Http\Controllers\v1_1_0\admin;

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
            $this->leadModel = 'App\\Models\\v1_1_0\\tbllead';
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
        return view($this->version . '.admin.lead', ['search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->version . '.admin.leadform');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.leadupdateform', ['edit_id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
