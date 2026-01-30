<?php

namespace App\Http\Controllers\v4_3_2\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class partyController extends Controller
{
    public $version, $partyModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->partyModel = 'App\\Models\\' . $this->version . "\\party";
        } else {
            $this->partyModel = 'App\\Models\\v4_3_2\\party';
        }
    }

    public function partyindex(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        return view($this->version . '.admin.party.party', ["search" => $search]);
    }
    public function partycreate()
    {
        return view($this->version . '.admin.party.partyform', ['company_id' => Session::get('company_id')]);
    }
    public function partyedit($id)
    {
        return view($this->version . '.admin.party.partyupdateform', ['edit_id' => $id]);
    }
    public function gradeindex(Request $request)
    {
        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }
        return view($this->version . '.admin.grade.grade', ["search" => $search]);
    }
    public function gradecreate()
    {
        return view($this->version . '.admin.grade.gradeform', ['company_id' => Session::get('company_id')]);
    }
    public function gradeedit($id)
    {
        return view($this->version . '.admin.grade.gradeupdateform', ['edit_id' => $id]);
    }
}
