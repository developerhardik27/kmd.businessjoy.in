<?php

namespace App\Http\Controllers\v4_3_2\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

use function Symfony\Component\String\s;

class HrController extends Controller
{
    public $version, $employeeModel;
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $this->version = $_SESSION['folder_name'];
            $this->employeeModel = 'App\\Models\\' . $this->version . "\\employee";
        } else {
            $this->employeeModel = 'App\\Models\\v4_3_2\\employee';
        }
    }
    public function index(Request $request)
    {

        if (isset($request->search)) {
            $search = $request->search;
        } else {
            $search = '';
        }

        return view($this->version . '.admin.Employee.employee', ["search" => $search]);
    }
    public function create()
    {
        return view($this->version . '.admin.Employee.employeeform', ['company_id' => Session::get('company_id')]);
    }
    public function edit($id)
    {
        return view($this->version . '.admin.Employee.employeeupdateform', compact('id'));
    }
    public function companiesholidays()
    {
        return view($this->version . '.admin.Employee.companiesholidays');
    }
    public function letter()
    {
        return view($this->version . '.admin.Employee.letter');
    }
    public function editletter($id)
    {
        return view($this->version . '.admin.Employee.letterupdateform',['edit_id'=>$id]);
    }
}
