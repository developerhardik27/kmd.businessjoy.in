<?php

namespace App\Http\Controllers\v4_3_0\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public $version, $leadModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->leadModel = 'App\\Models\\' . $this->version . "\\reminder";
        } else {
            $this->leadModel = 'App\\Models\\v4_3_0\\reminder';
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
        return view($this->version . '.admin.Reminder.reminder', ['search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id=null)
    {
        if(isset($id)){
            return view($this->version . '.admin.Reminder.newreminderform', ['edit_id' => $id]);
        }
        return view($this->version . '.admin.Reminder.reminderform');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view($this->version . '.admin.Reminder.reminderupdateform', ['edit_id' => $id]);
    }

}
