<?php

namespace App\Http\Controllers\v3_0_0\api;


use App\Mail\sendmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\company;
use Illuminate\Support\Str;

class userController extends commonController
{

    public $db, $companyId, $userId, $rp, $masterdbname, $user_permissionModel;
    public function __construct(Request $request)
    {

        if (isset($request->company_id)) {
            $dbname = company::find($request->company_id);
        } else {
            $dbname = company::find(1);
        }

        $this->db = $dbname->dbname;

        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');

        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->user_permissionModel = $this->getmodel('user_permission');

    }

    // return username and company name
    public function username(Request $request)
    {

        $user = DB::table('users')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->select('users.firstname', 'users.lastname', 'users.img', 'company_details.name')
            ->where('users.id', $this->userId)
            ->get();
        if ($user->isEmpty()) {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
        return $this->successresponse(200, 'user', $user);
    }

    // user details for profile
    public function userprofile(Request $request)
    {

        $users = DB::table('users')
            ->join('country', 'users.country_id', '=', 'country.id')
            ->join('state', 'users.state_id', '=', 'state.id')
            ->join('city', 'users.city_id', '=', 'city.id')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name', 'users.img', 'users.created_by', 'users.default_page')
            ->where('users.is_active', '1')->where('users.is_deleted', '0')->where('users.id', $request->id)
            ->get();


        if ($users->isEmpty()) {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($users[0]->created_by != $this->userId && $users[0]->id != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }


        return $this->successresponse(200, 'user', $users);
    }

    // user list who has customer support module permission
    public function customersupportuser()
    {
        $usersres = DB::table('users')
            ->join('country', 'users.country_id', '=', 'country.id')
            ->join('state', 'users.state_id', '=', 'state.id')
            ->join('city', 'users.city_id', '=', 'city.id')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->join('user_role', 'users.role', '=', 'user_role.id')
            ->join($this->db . '.user_permissions', 'users.id', '=', $this->db . '.user_permissions.user_id')
            ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.password', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name as company_name', 'user_role.role as user_role', 'users.img', 'users.created_by', 'users.updated_by', 'users.is_active')
            ->where('users.is_deleted', 0)
            ->whereJsonContains('rp->customersupportmodule->customersupport->show', "1")
            ->whereJsonContains('rp->customersupportmodule->customersupport->add', "1");

        if ($this->companyId != 1) {
            $users = $usersres->where('users.company_id', $this->companyId);
        }

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            $usersres->where('users.created_by', $this->userId)->orWhere('users.id', $this->userId);
        }

        $users = $usersres->get();


        if ($users->isEmpty()) {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
        return $this->successresponse(200, 'user', $users);
    }

    // user list who has lead module permission
    public function leaduser()
    {
        $usersres = DB::table('users')
            ->join('country', 'users.country_id', '=', 'country.id')
            ->join('state', 'users.state_id', '=', 'state.id')
            ->join('city', 'users.city_id', '=', 'city.id')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->join('user_role', 'users.role', '=', 'user_role.id')
            ->join($this->db . '.user_permissions', 'users.id', '=', $this->db . '.user_permissions.user_id')
            ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.password', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name as company_name', 'user_role.role as user_role', 'users.img', 'users.created_by', 'users.updated_by', 'users.is_active')
            ->where('users.is_deleted', 0)
            ->whereJsonContains('rp->leadmodule->lead->add', "1")
            ->whereJsonContains('rp->leadmodule->lead->show', "1");

        if ($this->companyId != 1) {
            $users = $usersres->where('users.company_id', $this->companyId);
        }

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            $usersres->where('users.created_by', $this->userId)->orWhere('users.id', $this->userId);
        }

        $users = $usersres->get();

        if ($users->isEmpty()) {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
        return $this->successresponse(200, 'user', $users);
    }

    // user list who has invoice module permission
    public function invoiceuser()
    {
        $usersres = DB::table('users')
            ->select('users.id', 'users.firstname', 'users.lastname')
            ->where('users.is_deleted', 0);

        if ($this->companyId != 1) {
            $users = $usersres->where('users.company_id', $this->companyId)
                ->whereJsonContains('rp->invoicemodule->invoice->add', "1")
                ->whereJsonContains('rp->invoicemodule->invoice->show', "1")
                ->join($this->db . '.user_permissions', 'users.id', '=', $this->db . '.user_permissions.user_id');
        }

        $users = $usersres->get();

        if ($users->isEmpty()) {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
        return $this->successresponse(200, 'user', $users);
    }

    // user list who has techsupport module permission
    public function techsupportuser()
    {

        $usersres = DB::table('users')
            ->join('country', 'users.country_id', '=', 'country.id')
            ->join('state', 'users.state_id', '=', 'state.id')
            ->join('city', 'users.city_id', '=', 'city.id')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->join('user_role', 'users.role', '=', 'user_role.id')
            ->join($this->db . '.user_permissions', 'users.id', '=', $this->db . '.user_permissions.user_id')
            ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.password', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name as company_name', 'user_role.role as user_role', 'users.img', 'users.created_by', 'users.updated_by', 'users.is_active')
            ->where('users.is_deleted', 0)
            ->whereJsonContains('rp->adminmodule->techsupport->show', "1")
            ->whereJsonContains('rp->adminmodule->techsupport->add', "1")
            ->whereJsonContains('rp->adminmodule->techsupport->view', "1")
            ->whereJsonContains('rp->adminmodule->techsupport->edit', "1");

        if ($this->companyId != 1) {
            $users = $usersres->where('users.company_id', $this->companyId);
        }

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            $usersres->where('users.created_by', $this->userId)->orWhere('users.id', $this->userId);
        }

        $users = $usersres->get();

        if ($users->isEmpty()) {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
        return $this->successresponse(200, 'user', $users);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $usersres = DB::table('users')
            ->join('country', 'users.country_id', '=', 'country.id')
            ->join('state', 'users.state_id', '=', 'state.id')
            ->join('city', 'users.city_id', '=', 'city.id')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->join('user_role', 'users.role', '=', 'user_role.id')
            ->leftJoin('users as creator', 'users.created_by', '=', 'creator.id')
            ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.password', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name as company_name', 'user_role.role as user_role', 'users.img', 'users.created_by', 'creator.firstname as creator_firstname', 'creator.lastname as creator_lastname', DB::raw("DATE_FORMAT(users.created_at, '%d-%M-%Y %h:%i %p')as created_at_formatted"), 'users.updated_by', 'users.is_active')
            ->where('users.is_deleted', 0);

        if ($this->companyId != 1) {
            $users = $usersres->where('users.company_id', $this->companyId);
        }

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            $usersres->where('users.created_by', $this->userId)->orWhere('users.id', $this->userId);
        }

        $users = $usersres->get();

        if ($users->isEmpty()) {
            return $this->successresponse(404, 'user', 'No Records Found');
        }

        if ($this->rp['adminmodule']['user']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        return $this->successresponse(200, 'user', $users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $company = company::find($this->companyId);
        $user = User::where('company_id', '=', $company->id)->where('is_deleted', 0)->get();

        $companymaxuser = $company->max_users;

        // check company max user limit
        if ($user->count() >= $companymaxuser) {
            return $this->successresponse(500, 'message', 'You are reached your limits to create user');
        }

        // validate incoming data request
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'password' => 'nullable|string|max:70',
            'contact_number' => 'required|numeric|digits:10',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'updated_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp['adminmodule']['user']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            // check email already exist or not
            $checkuseremail = User::where('email', $request->email)->where('is_deleted', 0)->get();

            if ($checkuseremail->isNotEmpty()) {
                return $this->successresponse(500, 'message', 'This email id already exists , Please enter other email id');
            }


            if ($this->rp['adminmodule']['userpermission']['add'] == 1) {

                //   check user permissions

                $invoice_show = (($this->rp['invoicemodule']['invoice']['show'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) || $this->userId == 1) ? $request->showinvoicemenu : "0";
                $invoice_add = (($this->rp['invoicemodule']['invoice']['add'] == 1) || $this->userId == 1) ? $request->addinvoice : "0";
                $invoice_view = (($this->rp['invoicemodule']['invoice']['view'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) || $this->userId == 1) ? $request->viewinvoice : "0";
                $invoice_edit = (($this->rp['invoicemodule']['invoice']['edit'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) || $this->userId == 1) ? $request->editinvoice : "0";
                $invoice_delete = (($this->rp['invoicemodule']['invoice']['delete'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) || $this->userId == 1) ? $request->deleteinvoice : "0";
                $invoice_alldata = (($this->rp['invoicemodule']['invoice']['alldata'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) || $this->userId == 1) ? $request->alldatainvoice : "0";

                $mngcol_show = (($this->rp['invoicemodule']['mngcol']['show'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) || $this->userId == 1) ? $request->showmngcolmenu : "0";
                $mngcol_add = (($this->rp['invoicemodule']['mngcol']['add'] == 1) || $this->userId == 1) ? $request->addmngcol : "0";
                $mngcol_view = (($this->rp['invoicemodule']['mngcol']['view'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) || $this->userId == 1) ? $request->viewmngcol : "0";
                $mngcol_edit = (($this->rp['invoicemodule']['mngcol']['edit'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) || $this->userId == 1) ? $request->editmngcol : "0";
                $mngcol_delete = (($this->rp['invoicemodule']['mngcol']['delete'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) || $this->userId == 1) ? $request->deletemngcol : "0";
                $mngcol_alldata = (($this->rp['invoicemodule']['mngcol']['alldata'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) || $this->userId == 1) ? $request->alldatamngcol : "0";

                $formula_show = (($this->rp['invoicemodule']['formula']['show'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) || $this->userId == 1) ? $request->showformulamenu : "0";
                $formula_add = (($this->rp['invoicemodule']['formula']['add'] == 1) || $this->userId == 1) ? $request->addformula : "0";
                $formula_view = (($this->rp['invoicemodule']['formula']['view'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) || $this->userId == 1) ? $request->viewformula : "0";
                $formula_edit = (($this->rp['invoicemodule']['formula']['edit'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) || $this->userId == 1) ? $request->editformula : "0";
                $formula_delete = (($this->rp['invoicemodule']['formula']['delete'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) || $this->userId == 1) ? $request->deleteformula : "0";
                $formula_alldata = (($this->rp['invoicemodule']['formula']['alldata'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) || $this->userId == 1) ? $request->alldataformula : "0";

                $invoicesetting_show = (($this->rp['invoicemodule']['invoicesetting']['show'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) || $this->userId == 1) ? $request->showinvoicesettingmenu : "0";
                $invoicesetting_add = (($this->rp['invoicemodule']['invoicesetting']['add'] == 1) || $this->userId == 1) ? $request->addinvoicesetting : "0";
                $invoicesetting_view = (($this->rp['invoicemodule']['invoicesetting']['view'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) || $this->userId == 1) ? $request->viewinvoicesetting : "0";
                $invoicesetting_edit = (($this->rp['invoicemodule']['invoicesetting']['edit'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) || $this->userId == 1) ? $request->editinvoicesetting : "0";
                $invoicesetting_delete = (($this->rp['invoicemodule']['invoicesetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) || $this->userId == 1) ? $request->deleteinvoicesetting : "0";
                $invoicesetting_alldata = (($this->rp['invoicemodule']['invoicesetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) || $this->userId == 1) ? $request->alldatainvoicesetting : "0";

                $invoicenumbersetting_show = (($this->rp['invoicemodule']['invoicenumbersetting']['show'] == 1 && $this->rp['invoicemodule']['invoicenumbersetting']['add'] == 1) || $this->userId == 1) ? $request->showinvoicenumbersettingmenu : "0";
                $invoicenumbersetting_add = (($this->rp['invoicemodule']['invoicenumbersetting']['add'] == 1) || $this->userId == 1) ? $request->addinvoicenumbersetting : "0";
                $invoicenumbersetting_view = (($this->rp['invoicemodule']['invoicenumbersetting']['view'] == 1 && $this->rp['invoicemodule']['invoicenumbersetting']['add'] == 1) || $this->userId == 1) ? $request->viewinvoicenumbersetting : "0";
                $invoicenumbersetting_edit = (($this->rp['invoicemodule']['invoicenumbersetting']['edit'] == 1 && $this->rp['invoicemodule']['invoicenumbersetting']['add'] == 1) || $this->userId == 1) ? $request->editinvoicenumbersetting : "0";
                $invoicenumbersetting_delete = (($this->rp['invoicemodule']['invoicenumbersetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicenumbersetting']['add'] == 1) || $this->userId == 1) ? $request->deleteinvoicenumbersetting : "0";
                $invoicenumbersetting_alldata = (($this->rp['invoicemodule']['invoicenumbersetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicenumbersetting']['add'] == 1) || $this->userId == 1) ? $request->alldatainvoicenumbersetting : "0";

                $invoicetandcsetting_show = (($this->rp['invoicemodule']['invoicetandcsetting']['show'] == 1 && $this->rp['invoicemodule']['invoicetandcsetting']['add'] == 1) || $this->userId == 1) ? $request->showinvoicetandcsettingmenu : "0";
                $invoicetandcsetting_add = (($this->rp['invoicemodule']['invoicetandcsetting']['add'] == 1) || $this->userId == 1) ? $request->addinvoicetandcsetting : "0";
                $invoicetandcsetting_view = (($this->rp['invoicemodule']['invoicetandcsetting']['view'] == 1 && $this->rp['invoicemodule']['invoicetandcsetting']['add'] == 1) || $this->userId == 1) ? $request->viewinvoicetandcsetting : "0";
                $invoicetandcsetting_edit = (($this->rp['invoicemodule']['invoicetandcsetting']['edit'] == 1 && $this->rp['invoicemodule']['invoicetandcsetting']['add'] == 1) || $this->userId == 1) ? $request->editinvoicetandcsetting : "0";
                $invoicetandcsetting_delete = (($this->rp['invoicemodule']['invoicetandcsetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicetandcsetting']['add'] == 1) || $this->userId == 1) ? $request->deleteinvoicetandcsetting : "0";
                $invoicetandcsetting_alldata = (($this->rp['invoicemodule']['invoicetandcsetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicetandcsetting']['add'] == 1) || $this->userId == 1) ? $request->alldatainvoicetandcsetting : "0";

                $invoicestandardsetting_show = (($this->rp['invoicemodule']['invoicestandardsetting']['show'] == 1 && $this->rp['invoicemodule']['invoicestandardsetting']['add'] == 1) || $this->userId == 1) ? $request->showinvoicestandardsettingmenu : "0";
                $invoicestandardsetting_add = (($this->rp['invoicemodule']['invoicestandardsetting']['add'] == 1) || $this->userId == 1) ? $request->addinvoicestandardsetting : "0";
                $invoicestandardsetting_view = (($this->rp['invoicemodule']['invoicestandardsetting']['view'] == 1 && $this->rp['invoicemodule']['invoicestandardsetting']['add'] == 1) || $this->userId == 1) ? $request->viewinvoicestandardsetting : "0";
                $invoicestandardsetting_edit = (($this->rp['invoicemodule']['invoicestandardsetting']['edit'] == 1 && $this->rp['invoicemodule']['invoicestandardsetting']['add'] == 1) || $this->userId == 1) ? $request->editinvoicestandardsetting : "0";
                $invoicestandardsetting_delete = (($this->rp['invoicemodule']['invoicestandardsetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicestandardsetting']['add'] == 1) || $this->userId == 1) ? $request->deleteinvoicestandardsetting : "0";
                $invoicestandardsetting_alldata = (($this->rp['invoicemodule']['invoicestandardsetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicestandardsetting']['add'] == 1) || $this->userId == 1) ? $request->alldatainvoicestandardsetting : "0";

                $invoicegstsetting_show = (($this->rp['invoicemodule']['invoicegstsetting']['show'] == 1 && $this->rp['invoicemodule']['invoicegstsetting']['add'] == 1) || $this->userId == 1) ? $request->showinvoicegstsettingmenu : "0";
                $invoicegstsetting_add = (($this->rp['invoicemodule']['invoicegstsetting']['add'] == 1) || $this->userId == 1) ? $request->addinvoicegstsetting : "0";
                $invoicegstsetting_view = (($this->rp['invoicemodule']['invoicegstsetting']['view'] == 1 && $this->rp['invoicemodule']['invoicegstsetting']['add'] == 1) || $this->userId == 1) ? $request->viewinvoicegstsetting : "0";
                $invoicegstsetting_edit = (($this->rp['invoicemodule']['invoicegstsetting']['edit'] == 1 && $this->rp['invoicemodule']['invoicegstsetting']['add'] == 1) || $this->userId == 1) ? $request->editinvoicegstsetting : "0";
                $invoicegstsetting_delete = (($this->rp['invoicemodule']['invoicegstsetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicegstsetting']['add'] == 1) || $this->userId == 1) ? $request->deleteinvoicegstsetting : "0";
                $invoicegstsetting_alldata = (($this->rp['invoicemodule']['invoicegstsetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicegstsetting']['add'] == 1) || $this->userId == 1) ? $request->alldatainvoicegstsetting : "0";

                $invoicecustomeridsetting_show = (($this->rp['invoicemodule']['invoicecustomeridsetting']['show'] == 1 && $this->rp['invoicemodule']['invoicecustomeridsetting']['add'] == 1) || $this->userId == 1) ? $request->showinvoicecustomeridsettingmenu : "0";
                $invoicecustomeridsetting_add = (($this->rp['invoicemodule']['invoicecustomeridsetting']['add'] == 1) || $this->userId == 1) ? $request->addinvoicecustomeridsetting : "0";
                $invoicecustomeridsetting_view = (($this->rp['invoicemodule']['invoicecustomeridsetting']['view'] == 1 && $this->rp['invoicemodule']['invoicecustomeridsetting']['add'] == 1) || $this->userId == 1) ? $request->viewinvoicecustomeridsetting : "0";
                $invoicecustomeridsetting_edit = (($this->rp['invoicemodule']['invoicecustomeridsetting']['edit'] == 1 && $this->rp['invoicemodule']['invoicecustomeridsetting']['add'] == 1) || $this->userId == 1) ? $request->editinvoicecustomeridsetting : "0";
                $invoicecustomeridsetting_delete = (($this->rp['invoicemodule']['invoicecustomeridsetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicecustomeridsetting']['add'] == 1) || $this->userId == 1) ? $request->deleteinvoicecustomeridsetting : "0";
                $invoicecustomeridsetting_alldata = (($this->rp['invoicemodule']['invoicecustomeridsetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicecustomeridsetting']['add'] == 1) || $this->userId == 1) ? $request->alldatainvoicecustomeridsetting : "0";

                $bank_show = (($this->rp['invoicemodule']['bank']['show'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) || $this->userId == 1) ? $request->showbankmenu : "0";
                $bank_add = (($this->rp['invoicemodule']['bank']['add'] == 1) || $this->userId == 1) ? $request->addbank : "0";
                $bank_view = (($this->rp['invoicemodule']['bank']['view'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) || $this->userId == 1) ? $request->viewbank : "0";
                $bank_edit = (($this->rp['invoicemodule']['bank']['edit'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) || $this->userId == 1) ? $request->editbank : "0";
                $bank_delete = (($this->rp['invoicemodule']['bank']['delete'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) || $this->userId == 1) ? $request->deletebank : "0";
                $bank_alldata = (($this->rp['invoicemodule']['bank']['alldata'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) || $this->userId == 1) ? $request->alldatabank : "0";

                $customer_show = (($this->rp['invoicemodule']['customer']['show'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) || $this->userId == 1) ? $request->showcustomermenu : "0";
                $customer_add = (($this->rp['invoicemodule']['customer']['add'] == 1) || $this->userId == 1) ? $request->addcustomer : "0";
                $customer_view = (($this->rp['invoicemodule']['customer']['view'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) || $this->userId == 1) ? $request->viewcustomer : "0";
                $customer_edit = (($this->rp['invoicemodule']['customer']['edit'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) || $this->userId == 1) ? $request->editcustomer : "0";
                $customer_delete = (($this->rp['invoicemodule']['customer']['delete'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) || $this->userId == 1) ? $request->deletecustomer : "0";
                $customer_alldata = (($this->rp['invoicemodule']['customer']['alldata'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) || $this->userId == 1) ? $request->alldatacustomer : "0";

                $lead_show = (($this->rp['leadmodule']['lead']['show'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) || $this->userId == 1) ? $request->showleadmenu : "0";
                $lead_add = (($this->rp['leadmodule']['lead']['add'] == 1) || $this->userId == 1) ? $request->addlead : "0";
                $lead_view = (($this->rp['leadmodule']['lead']['view'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) || $this->userId == 1) ? $request->viewlead : "0";
                $lead_edit = (($this->rp['leadmodule']['lead']['edit'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) || $this->userId == 1) ? $request->editlead : "0";
                $lead_delete = (($this->rp['leadmodule']['lead']['delete'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) || $this->userId == 1) ? $request->deletelead : "0";
                $lead_alldata = (($this->rp['leadmodule']['lead']['alldata'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) || $this->userId == 1) ? $request->alldatalead : "0";

                $customersupport_show = (($this->rp['customersupportmodule']['customersupport']['show'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) || $this->userId == 1) ? $request->showcustomersupportmenu : "0";
                $customersupport_add = (($this->rp['customersupportmodule']['customersupport']['add'] == 1) || $this->userId == 1) ? $request->addcustomersupport : "0";
                $customersupport_view = (($this->rp['customersupportmodule']['customersupport']['view'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) || $this->userId == 1) ? $request->viewcustomersupport : "0";
                $customersupport_edit = (($this->rp['customersupportmodule']['customersupport']['edit'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) || $this->userId == 1) ? $request->editcustomersupport : "0";
                $customersupport_delete = (($this->rp['customersupportmodule']['customersupport']['delete'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) || $this->userId == 1) ? $request->deletecustomersupport : "0";
                $customersupport_alldata = (($this->rp['customersupportmodule']['customersupport']['alldata'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) || $this->userId == 1) ? $request->alldatacustomersupport : "0";

                $product_show = (($this->rp['inventorymodule']['product']['show'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) || $this->userId == 1) ? $request->showproductmenu : "0";
                $product_add = (($this->rp['inventorymodule']['product']['add'] == 1) || $this->userId == 1) ? $request->addproduct : "0";
                $product_view = (($this->rp['inventorymodule']['product']['view'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) || $this->userId == 1) ? $request->viewproduct : "0";
                $product_edit = (($this->rp['inventorymodule']['product']['edit'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) || $this->userId == 1) ? $request->editproduct : "0";
                $product_delete = (($this->rp['inventorymodule']['product']['delete'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) || $this->userId == 1) ? $request->deleteproduct : "0";
                $product_alldata = (($this->rp['inventorymodule']['product']['alldata'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) || $this->userId == 1) ? $request->alldataproduct : "0";

                $productcategory_show = (($this->rp['inventorymodule']['productcategory']['show'] == 1 && $this->rp['inventorymodule']['productcategory']['add'] == 1) || $this->userId == 1) ? $request->showproductcategorymenu : "0";
                $productcategory_add = (($this->rp['inventorymodule']['productcategory']['add'] == 1) || $this->userId == 1) ? $request->addproductcategory : "0";
                $productcategory_view = (($this->rp['inventorymodule']['productcategory']['view'] == 1 && $this->rp['inventorymodule']['productcategory']['add'] == 1) || $this->userId == 1) ? $request->viewproductcategory : "0";
                $productcategory_edit = (($this->rp['inventorymodule']['productcategory']['edit'] == 1 && $this->rp['inventorymodule']['productcategory']['add'] == 1) || $this->userId == 1) ? $request->editproductcategory : "0";
                $productcategory_delete = (($this->rp['inventorymodule']['productcategory']['delete'] == 1 && $this->rp['inventorymodule']['productcategory']['add'] == 1) || $this->userId == 1) ? $request->deleteproductcategory : "0";
                $productcategory_alldata = (($this->rp['inventorymodule']['productcategory']['alldata'] == 1 && $this->rp['inventorymodule']['productcategory']['add'] == 1) || $this->userId == 1) ? $request->alldataproductcategory : "0";

                $productcolumnmapping_show = (($this->rp['inventorymodule']['productcolumnmapping']['show'] == 1 && $this->rp['inventorymodule']['productcolumnmapping']['add'] == 1) || $this->userId == 1) ? $request->showproductcolumnmappingmenu : "0";
                $productcolumnmapping_add = (($this->rp['inventorymodule']['productcolumnmapping']['add'] == 1) || $this->userId == 1) ? $request->addproductcolumnmapping : "0";
                $productcolumnmapping_view = (($this->rp['inventorymodule']['productcolumnmapping']['view'] == 1 && $this->rp['inventorymodule']['productcolumnmapping']['add'] == 1) || $this->userId == 1) ? $request->viewproductcolumnmapping : "0";
                $productcolumnmapping_edit = (($this->rp['inventorymodule']['productcolumnmapping']['edit'] == 1 && $this->rp['inventorymodule']['productcolumnmapping']['add'] == 1) || $this->userId == 1) ? $request->editproductcolumnmapping : "0";
                $productcolumnmapping_delete = (($this->rp['inventorymodule']['productcolumnmapping']['delete'] == 1 && $this->rp['inventorymodule']['productcolumnmapping']['add'] == 1) || $this->userId == 1) ? $request->deleteproductcolumnmapping : "0";
                $productcolumnmapping_alldata = (($this->rp['inventorymodule']['productcolumnmapping']['alldata'] == 1 && $this->rp['inventorymodule']['productcolumnmapping']['add'] == 1) || $this->userId == 1) ? $request->alldataproductcolumnmapping : "0";

                $purchase_show = (($this->rp['inventorymodule']['purchase']['show'] == 1 && $this->rp['inventorymodule']['purchase']['add'] == 1) || $this->userId == 1) ? $request->showpurchasemenu : "0";
                $purchase_add = (($this->rp['inventorymodule']['purchase']['add'] == 1) || $this->userId == 1) ? $request->addpurchase : "0";
                $purchase_view = (($this->rp['inventorymodule']['purchase']['view'] == 1 && $this->rp['inventorymodule']['purchase']['add'] == 1) || $this->userId == 1) ? $request->viewpurchase : "0";
                $purchase_edit = (($this->rp['inventorymodule']['purchase']['edit'] == 1 && $this->rp['inventorymodule']['purchase']['add'] == 1) || $this->userId == 1) ? $request->editpurchase : "0";
                $purchase_delete = (($this->rp['inventorymodule']['purchase']['delete'] == 1 && $this->rp['inventorymodule']['purchase']['add'] == 1) || $this->userId == 1) ? $request->deletepurchase : "0";
                $purchase_alldata = (($this->rp['inventorymodule']['purchase']['alldata'] == 1 && $this->rp['inventorymodule']['purchase']['add'] == 1) || $this->userId == 1) ? $request->alldatapurchase : "0";

                $inventory_show = (($this->rp['inventorymodule']['inventory']['show'] == 1 && $this->rp['inventorymodule']['inventory']['add'] == 1) || $this->userId == 1) ? $request->showinventorymenu : "0";
                $inventory_add = (($this->rp['inventorymodule']['inventory']['add'] == 1) || $this->userId == 1) ? $request->addinventory : "0";
                $inventory_view = (($this->rp['inventorymodule']['inventory']['view'] == 1 && $this->rp['inventorymodule']['inventory']['add'] == 1) || $this->userId == 1) ? $request->viewinventory : "0";
                $inventory_edit = (($this->rp['inventorymodule']['inventory']['edit'] == 1 && $this->rp['inventorymodule']['inventory']['add'] == 1) || $this->userId == 1) ? $request->editinventory : "0";
                $inventory_delete = (($this->rp['inventorymodule']['inventory']['delete'] == 1 && $this->rp['inventorymodule']['inventory']['add'] == 1) || $this->userId == 1) ? $request->deleteinventory : "0";
                $inventory_alldata = (($this->rp['inventorymodule']['inventory']['alldata'] == 1 && $this->rp['inventorymodule']['inventory']['add'] == 1) || $this->userId == 1) ? $request->alldatainventory : "0";

                $supplier_show = (($this->rp['inventorymodule']['supplier']['show'] == 1 && $this->rp['inventorymodule']['supplier']['add'] == 1) || $this->userId == 1) ? $request->showsuppliermenu : "0";
                $supplier_add = (($this->rp['inventorymodule']['supplier']['add'] == 1) || $this->userId == 1) ? $request->addsupplier : "0";
                $supplier_view = (($this->rp['inventorymodule']['supplier']['view'] == 1 && $this->rp['inventorymodule']['supplier']['add'] == 1) || $this->userId == 1) ? $request->viewsupplier : "0";
                $supplier_edit = (($this->rp['inventorymodule']['supplier']['edit'] == 1 && $this->rp['inventorymodule']['supplier']['add'] == 1) || $this->userId == 1) ? $request->editsupplier : "0";
                $supplier_delete = (($this->rp['inventorymodule']['supplier']['delete'] == 1 && $this->rp['inventorymodule']['supplier']['add'] == 1) || $this->userId == 1) ? $request->deletesupplier : "0";
                $supplier_alldata = (($this->rp['inventorymodule']['supplier']['alldata'] == 1 && $this->rp['inventorymodule']['supplier']['add'] == 1) || $this->userId == 1) ? $request->alldatasupplier : "0";

                $reminder_show = (($this->rp['remindermodule']['reminder']['show'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) || $this->userId == 1) ? $request->showremindermenu : "0";
                $reminder_add = (($this->rp['remindermodule']['reminder']['add'] == 1) || $this->userId == 1) ? $request->addreminder : "0";
                $reminder_view = (($this->rp['remindermodule']['reminder']['view'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) || $this->userId == 1) ? $request->viewreminder : "0";
                $reminder_edit = (($this->rp['remindermodule']['reminder']['edit'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) || $this->userId == 1) ? $request->editreminder : "0";
                $reminder_delete = (($this->rp['remindermodule']['reminder']['delete'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) || $this->userId == 1) ? $request->deletereminder : "0";
                $reminder_alldata = (($this->rp['remindermodule']['reminder']['alldata'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) || $this->userId == 1) ? $request->alldatareminder : "0";

                $remindercustomer_show = (($this->rp['remindermodule']['remindercustomer']['show'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) || $this->userId == 1) ? $request->showremindercustomermenu : "0";
                $remindercustomer_add = (($this->rp['remindermodule']['remindercustomer']['add'] == 1) || $this->userId == 1) ? $request->addremindercustomer : "0";
                $remindercustomer_view = (($this->rp['remindermodule']['remindercustomer']['view'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) || $this->userId == 1) ? $request->viewremindercustomer : "0";
                $remindercustomer_edit = (($this->rp['remindermodule']['remindercustomer']['edit'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) || $this->userId == 1) ? $request->editremindercustomer : "0";
                $remindercustomer_delete = (($this->rp['remindermodule']['remindercustomer']['delete'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) || $this->userId == 1) ? $request->deleteremindercustomer : "0";
                $remindercustomer_alldata = (($this->rp['remindermodule']['remindercustomer']['alldata'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) || $this->userId == 1) ? $request->alldataremindercustomer : "0";

                $company_show = (($this->rp['adminmodule']['company']['show'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) || $this->userId == 1) ? $request->showcompanymenu : "0";
                $company_add = (($this->rp['adminmodule']['company']['add'] == 1) || $this->userId == 1) ? $request->addcompany : "0";
                $company_view = (($this->rp['adminmodule']['company']['view'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) || $this->userId == 1) ? $request->viewcompany : "0";
                $company_edit = (($this->rp['adminmodule']['company']['edit'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) || $this->userId == 1) ? $request->editcompany : "0";
                $company_delete = (($this->rp['adminmodule']['company']['delete'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) || $this->userId == 1) ? $request->deletecompany : "0";
                $company_alldata = (($this->rp['adminmodule']['company']['alldata'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) || $this->userId == 1) ? $request->alldatacompany : "0";
                $company_maxuser = (($this->rp['adminmodule']['company']['max'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) || $this->userId == 1) ? $request->maxuser : "0";

                $user_show = (($this->rp['adminmodule']['user']['show'] == 1 && $this->rp['adminmodule']['user']['add'] == 1) || $this->userId == 1) ? $request->showusermenu : "0";
                $user_add = (($this->rp['adminmodule']['user']['add'] == 1) || $this->userId == 1) ? $request->adduser : "0";
                $user_view = (($this->rp['adminmodule']['user']['view'] == 1 && $this->rp['adminmodule']['user']['add'] == 1) || $this->userId == 1) ? $request->viewuser : "0";
                $user_edit = (($this->rp['adminmodule']['user']['edit'] == 1 && $this->rp['adminmodule']['user']['add'] == 1) || $this->userId == 1) ? $request->edituser : "0";
                $user_delete = (($this->rp['adminmodule']['user']['delete'] && $this->rp['adminmodule']['user']['add'] == 1) || $this->userId == 1) ? $request->deleteuser : "0";
                $user_alldata = (($this->rp['adminmodule']['user']['alldata'] && $this->rp['adminmodule']['user']['add'] == 1) || $this->userId == 1) ? $request->alldatauser : "0";

                $userpermission_show = (($this->rp['adminmodule']['userpermission']['show'] == 1 && $this->rp['adminmodule']['userpermission']['add'] == 1) || $this->userId == 1) ? $request->showuserpermissionmenu : "0";
                $userpermission_add = (($this->rp['adminmodule']['userpermission']['add'] == 1) || $this->userId == 1) ? $request->adduserpermission : "0";
                $userpermission_view = (($this->rp['adminmodule']['userpermission']['view'] == 1 && $this->rp['adminmodule']['userpermission']['add'] == 1) || $this->userId == 1) ? $request->viewuserpermission : "0";
                $userpermission_edit = (($this->rp['adminmodule']['userpermission']['edit'] == 1 && $this->rp['adminmodule']['userpermission']['add'] == 1) || $this->userId == 1) ? $request->edituserpermission : "0";
                $userpermission_delete = (($this->rp['adminmodule']['userpermission']['delete'] && $this->rp['adminmodule']['userpermission']['add'] == 1) || $this->userId == 1) ? $request->deleteuserpermission : "0";
                $userpermission_alldata = (($this->rp['adminmodule']['userpermission']['alldata'] && $this->rp['adminmodule']['userpermission']['add'] == 1) || $this->userId == 1) ? $request->alldatauserpermission : "0";

                $techsupport_show = (($this->rp['adminmodule']['techsupport']['show'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) || $this->userId == 1) ? $request->showtechsupportmenu : "0";
                $techsupport_add = (($this->rp['adminmodule']['techsupport']['add'] == 1) || $this->userId == 1) ? $request->addtechsupport : "0";
                $techsupport_view = (($this->rp['adminmodule']['techsupport']['view'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) || $this->userId == 1) ? $request->viewtechsupport : "0";
                $techsupport_edit = (($this->rp['adminmodule']['techsupport']['edit'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) || $this->userId == 1) ? $request->edittechsupport : "0";
                $techsupport_delete = (($this->rp['adminmodule']['techsupport']['delete'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) || $this->userId == 1) ? $request->deletetechsupport : "0";
                $techsupport_alldata = (($this->rp['adminmodule']['techsupport']['alldata'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) || $this->userId == 1) ? $request->alldatatechsupport : "0";

                $report_show = (($this->rp['reportmodule']['report']['show'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->showreportmenu : "0";
                $report_add = (($this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->addreport : "0";
                $report_view = (($this->rp['reportmodule']['report']['view'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->viewreport : "0";
                $report_edit = (($this->rp['reportmodule']['report']['edit'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->editreport : "0";
                $report_delete = (($this->rp['reportmodule']['report']['delete'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->deletereport : "0";
                $report_alldata = (($this->rp['reportmodule']['report']['add'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->assignedto : "0";
                $report_log = (($this->rp['reportmodule']['report']['log'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->logreport : "0";

                $blog_show = (($this->rp['blogmodule']['blog']['show'] == 1 && $this->rp['blogmodule']['blog']['add'] == 1) || $this->userId == 1) ? $request->showblogmenu : "0";
                $blog_add = (($this->rp['blogmodule']['blog']['add'] == 1) || $this->userId == 1) ? $request->addblog : "0";
                $blog_view = (($this->rp['blogmodule']['blog']['view'] == 1 && $this->rp['blogmodule']['blog']['add'] == 1) || $this->userId == 1) ? $request->viewblog : "0";
                $blog_edit = (($this->rp['blogmodule']['blog']['edit'] == 1 && $this->rp['blogmodule']['blog']['add'] == 1) || $this->userId == 1) ? $request->editblog : "0";
                $blog_delete = (($this->rp['blogmodule']['blog']['delete'] == 1 && $this->rp['blogmodule']['blog']['add'] == 1) || $this->userId == 1) ? $request->deleteblog : "0";
                $blog_alldata = (($this->rp['blogmodule']['blog']['add'] == 1 && $this->rp['blogmodule']['blog']['add'] == 1) || $this->userId == 1) ? $request->alldatablog : "0";

                $quotation_show = (($this->rp['quotationmodule']['quotation']['show'] == 1 && $this->rp['quotationmodule']['quotation']['add'] == 1) || $this->userId == 1) ? $request->showquotationmenu : "0";
                $quotation_add = (($this->rp['quotationmodule']['quotation']['add'] == 1) || $this->userId == 1) ? $request->addquotation : "0";
                $quotation_view = (($this->rp['quotationmodule']['quotation']['view'] == 1 && $this->rp['quotationmodule']['quotation']['add'] == 1) || $this->userId == 1) ? $request->viewquotation : "0";
                $quotation_edit = (($this->rp['quotationmodule']['quotation']['edit'] == 1 && $this->rp['quotationmodule']['quotation']['add'] == 1) || $this->userId == 1) ? $request->editquotation : "0";
                $quotation_delete = (($this->rp['quotationmodule']['quotation']['delete'] == 1 && $this->rp['quotationmodule']['quotation']['add'] == 1) || $this->userId == 1) ? $request->deletequotation : "0";
                $quotation_alldata = (($this->rp['quotationmodule']['quotation']['alldata'] == 1 && $this->rp['quotationmodule']['quotation']['add'] == 1) || $this->userId == 1) ? $request->alldataquotation : "0";

                $quotationmngcol_show = (($this->rp['quotationmodule']['quotationmngcol']['show'] == 1 && $this->rp['quotationmodule']['quotationmngcol']['add'] == 1) || $this->userId == 1) ? $request->showquotationmngcolmenu : "0";
                $quotationmngcol_add = (($this->rp['quotationmodule']['quotationmngcol']['add'] == 1) || $this->userId == 1) ? $request->addquotationmngcol : "0";
                $quotationmngcol_view = (($this->rp['quotationmodule']['quotationmngcol']['view'] == 1 && $this->rp['quotationmodule']['quotationmngcol']['add'] == 1) || $this->userId == 1) ? $request->viewquotationmngcol : "0";
                $quotationmngcol_edit = (($this->rp['quotationmodule']['quotationmngcol']['edit'] == 1 && $this->rp['quotationmodule']['quotationmngcol']['add'] == 1) || $this->userId == 1) ? $request->editquotationmngcol : "0";
                $quotationmngcol_delete = (($this->rp['quotationmodule']['quotationmngcol']['delete'] == 1 && $this->rp['quotationmodule']['quotationmngcol']['add'] == 1) || $this->userId == 1) ? $request->deletequotationmngcol : "0";
                $quotationmngcol_alldata = (($this->rp['quotationmodule']['quotationmngcol']['alldata'] == 1 && $this->rp['quotationmodule']['quotationmngcol']['add'] == 1) || $this->userId == 1) ? $request->alldataquotationmngcol : "0";

                $quotationformula_show = (($this->rp['quotationmodule']['quotationformula']['show'] == 1 && $this->rp['quotationmodule']['quotationformula']['add'] == 1) || $this->userId == 1) ? $request->showquotationformulamenu : "0";
                $quotationformula_add = (($this->rp['quotationmodule']['quotationformula']['add'] == 1) || $this->userId == 1) ? $request->addquotationformula : "0";
                $quotationformula_view = (($this->rp['quotationmodule']['quotationformula']['view'] == 1 && $this->rp['quotationmodule']['quotationformula']['add'] == 1) || $this->userId == 1) ? $request->viewquotationformula : "0";
                $quotationformula_edit = (($this->rp['quotationmodule']['quotationformula']['edit'] == 1 && $this->rp['quotationmodule']['quotationformula']['add'] == 1) || $this->userId == 1) ? $request->editquotationformula : "0";
                $quotationformula_delete = (($this->rp['quotationmodule']['quotationformula']['delete'] == 1 && $this->rp['quotationmodule']['quotationformula']['add'] == 1) || $this->userId == 1) ? $request->deletequotationformula : "0";
                $quotationformula_alldata = (($this->rp['quotationmodule']['quotationformula']['alldata'] == 1 && $this->rp['quotationmodule']['quotationformula']['add'] == 1) || $this->userId == 1) ? $request->alldataquotationformula : "0";

                $quotationsetting_show = (($this->rp['quotationmodule']['quotationsetting']['show'] == 1 && $this->rp['quotationmodule']['quotationsetting']['add'] == 1) || $this->userId == 1) ? $request->showquotationsettingmenu : "0";
                $quotationsetting_add = (($this->rp['quotationmodule']['quotationsetting']['add'] == 1) || $this->userId == 1) ? $request->addquotationsetting : "0";
                $quotationsetting_view = (($this->rp['quotationmodule']['quotationsetting']['view'] == 1 && $this->rp['quotationmodule']['quotationsetting']['add'] == 1) || $this->userId == 1) ? $request->viewquotationsetting : "0";
                $quotationsetting_edit = (($this->rp['quotationmodule']['quotationsetting']['edit'] == 1 && $this->rp['quotationmodule']['quotationsetting']['add'] == 1) || $this->userId == 1) ? $request->editquotationsetting : "0";
                $quotationsetting_delete = (($this->rp['quotationmodule']['quotationsetting']['delete'] == 1 && $this->rp['quotationmodule']['quotationsetting']['add'] == 1) || $this->userId == 1) ? $request->deletequotationsetting : "0";
                $quotationsetting_alldata = (($this->rp['quotationmodule']['quotationsetting']['alldata'] == 1 && $this->rp['quotationmodule']['quotationsetting']['add'] == 1) || $this->userId == 1) ? $request->alldataquotationsetting : "0";

                $quotationnumbersetting_show = (($this->rp['quotationmodule']['quotationnumbersetting']['show'] == 1 && $this->rp['quotationmodule']['quotationnumbersetting']['add'] == 1) || $this->userId == 1) ? $request->showquotationnumbersettingmenu : "0";
                $quotationnumbersetting_add = (($this->rp['quotationmodule']['quotationnumbersetting']['add'] == 1) || $this->userId == 1) ? $request->addquotationnumbersetting : "0";
                $quotationnumbersetting_view = (($this->rp['quotationmodule']['quotationnumbersetting']['view'] == 1 && $this->rp['quotationmodule']['quotationnumbersetting']['add'] == 1) || $this->userId == 1) ? $request->viewquotationnumbersetting : "0";
                $quotationnumbersetting_edit = (($this->rp['quotationmodule']['quotationnumbersetting']['edit'] == 1 && $this->rp['quotationmodule']['quotationnumbersetting']['add'] == 1) || $this->userId == 1) ? $request->editquotationnumbersetting : "0";
                $quotationnumbersetting_delete = (($this->rp['quotationmodule']['quotationnumbersetting']['delete'] == 1 && $this->rp['quotationmodule']['quotationnumbersetting']['add'] == 1) || $this->userId == 1) ? $request->deletequotationnumbersetting : "0";
                $quotationnumbersetting_alldata = (($this->rp['quotationmodule']['quotationnumbersetting']['alldata'] == 1 && $this->rp['quotationmodule']['quotationnumbersetting']['add'] == 1) || $this->userId == 1) ? $request->alldataquotationnumbersetting : "0";

                $quotationtandcsetting_show = (($this->rp['quotationmodule']['quotationtandcsetting']['show'] == 1 && $this->rp['quotationmodule']['quotationtandcsetting']['add'] == 1) || $this->userId == 1) ? $request->showquotationtandcsettingmenu : "0";
                $quotationtandcsetting_add = (($this->rp['quotationmodule']['quotationtandcsetting']['add'] == 1) || $this->userId == 1) ? $request->addquotationtandcsetting : "0";
                $quotationtandcsetting_view = (($this->rp['quotationmodule']['quotationtandcsetting']['view'] == 1 && $this->rp['quotationmodule']['quotationtandcsetting']['add'] == 1) || $this->userId == 1) ? $request->viewquotationtandcsetting : "0";
                $quotationtandcsetting_edit = (($this->rp['quotationmodule']['quotationtandcsetting']['edit'] == 1 && $this->rp['quotationmodule']['quotationtandcsetting']['add'] == 1) || $this->userId == 1) ? $request->editquotationtandcsetting : "0";
                $quotationtandcsetting_delete = (($this->rp['quotationmodule']['quotationtandcsetting']['delete'] == 1 && $this->rp['quotationmodule']['quotationtandcsetting']['add'] == 1) || $this->userId == 1) ? $request->deletequotationtandcsetting : "0";
                $quotationtandcsetting_alldata = (($this->rp['quotationmodule']['quotationtandcsetting']['alldata'] == 1 && $this->rp['quotationmodule']['quotationtandcsetting']['add'] == 1) || $this->userId == 1) ? $request->alldataquotationtandcsetting : "0";

                $quotationstandardsetting_show = (($this->rp['quotationmodule']['quotationstandardsetting']['show'] == 1 && $this->rp['quotationmodule']['quotationstandardsetting']['add'] == 1) || $this->userId == 1) ? $request->showquotationstandardsettingmenu : "0";
                $quotationstandardsetting_add = (($this->rp['quotationmodule']['quotationstandardsetting']['add'] == 1) || $this->userId == 1) ? $request->addquotationstandardsetting : "0";
                $quotationstandardsetting_view = (($this->rp['quotationmodule']['quotationstandardsetting']['view'] == 1 && $this->rp['quotationmodule']['quotationstandardsetting']['add'] == 1) || $this->userId == 1) ? $request->viewquotationstandardsetting : "0";
                $quotationstandardsetting_edit = (($this->rp['quotationmodule']['quotationstandardsetting']['edit'] == 1 && $this->rp['quotationmodule']['quotationstandardsetting']['add'] == 1) || $this->userId == 1) ? $request->editquotationstandardsetting : "0";
                $quotationstandardsetting_delete = (($this->rp['quotationmodule']['quotationstandardsetting']['delete'] == 1 && $this->rp['quotationmodule']['quotationstandardsetting']['add'] == 1) || $this->userId == 1) ? $request->deletequotationstandardsetting : "0";
                $quotationstandardsetting_alldata = (($this->rp['quotationmodule']['quotationstandardsetting']['alldata'] == 1 && $this->rp['quotationmodule']['quotationstandardsetting']['add'] == 1) || $this->userId == 1) ? $request->alldataquotationstandardsetting : "0";

                $quotationgstsetting_show = (($this->rp['quotationmodule']['quotationgstsetting']['show'] == 1 && $this->rp['quotationmodule']['quotationgstsetting']['add'] == 1) || $this->userId == 1) ? $request->showquotationgstsettingmenu : "0";
                $quotationgstsetting_add = (($this->rp['quotationmodule']['quotationgstsetting']['add'] == 1) || $this->userId == 1) ? $request->addquotationgstsetting : "0";
                $quotationgstsetting_view = (($this->rp['quotationmodule']['quotationgstsetting']['view'] == 1 && $this->rp['quotationmodule']['quotationgstsetting']['add'] == 1) || $this->userId == 1) ? $request->viewquotationgstsetting : "0";
                $quotationgstsetting_edit = (($this->rp['quotationmodule']['quotationgstsetting']['edit'] == 1 && $this->rp['quotationmodule']['quotationgstsetting']['add'] == 1) || $this->userId == 1) ? $request->editquotationgstsetting : "0";
                $quotationgstsetting_delete = (($this->rp['quotationmodule']['quotationgstsetting']['delete'] == 1 && $this->rp['quotationmodule']['quotationgstsetting']['add'] == 1) || $this->userId == 1) ? $request->deletequotationgstsetting : "0";
                $quotationgstsetting_alldata = (($this->rp['quotationmodule']['quotationgstsetting']['alldata'] == 1 && $this->rp['quotationmodule']['quotationgstsetting']['add'] == 1) || $this->userId == 1) ? $request->alldataquotationgstsetting : "0";

                $quotationcustomer_show = (($this->rp['quotationmodule']['quotationcustomer']['show'] == 1 && $this->rp['quotationmodule']['quotationcustomer']['add'] == 1) || $this->userId == 1) ? $request->showquotationcustomermenu : "0";
                $quotationcustomer_add = (($this->rp['quotationmodule']['quotationcustomer']['add'] == 1) || $this->userId == 1) ? $request->addquotationcustomer : "0";
                $quotationcustomer_view = (($this->rp['quotationmodule']['quotationcustomer']['view'] == 1 && $this->rp['quotationmodule']['quotationcustomer']['add'] == 1) || $this->userId == 1) ? $request->viewquotationcustomer : "0";
                $quotationcustomer_edit = (($this->rp['quotationmodule']['quotationcustomer']['edit'] == 1 && $this->rp['quotationmodule']['quotationcustomer']['add'] == 1) || $this->userId == 1) ? $request->editquotationcustomer : "0";
                $quotationcustomer_delete = (($this->rp['quotationmodule']['quotationcustomer']['delete'] == 1 && $this->rp['quotationmodule']['quotationcustomer']['add'] == 1) || $this->userId == 1) ? $request->deletequotationcustomer : "0";
                $quotationcustomer_alldata = (($this->rp['quotationmodule']['quotationcustomer']['alldata'] == 1 && $this->rp['quotationmodule']['quotationcustomer']['add'] == 1) || $this->userId == 1) ? $request->alldataquotationcustomer : "0";


                $rp = [
                    "invoicemodule" => [
                        "invoice" => ["show" => $invoice_show, "add" => $invoice_add, "view" => $invoice_view, "edit" => $invoice_edit, "delete" => $invoice_delete, "alldata" => $invoice_alldata],
                        "mngcol" => ["show" => $mngcol_show, "add" => $mngcol_add, "view" => $mngcol_view, "edit" => $mngcol_edit, "delete" => $mngcol_delete, "alldata" => $mngcol_alldata],
                        "formula" => ["show" => $formula_show, "add" => $formula_add, "view" => $formula_view, "edit" => $formula_edit, "delete" => $formula_delete, "alldata" => $formula_alldata],
                        "invoicesetting" => ["show" => $invoicesetting_show, "add" => $invoicesetting_add, "view" => $invoicesetting_view, "edit" => $invoicesetting_edit, "delete" => $invoicesetting_delete, "alldata" => $invoicesetting_alldata],
                        "bank" => ["show" => $bank_show, "add" => $bank_add, "view" => $bank_view, "edit" => $bank_edit, "delete" => $bank_delete, "alldata" => $bank_alldata],
                        "customer" => ["show" => $customer_show, "add" => $customer_add, "view" => $customer_view, "edit" => $customer_edit, "delete" => $customer_delete, "alldata" => $customer_alldata],
                        "invoicenumbersetting" => ["show" => $invoicenumbersetting_show, "add" => $invoicenumbersetting_add, "view" => $invoicenumbersetting_view, "edit" => $invoicenumbersetting_edit, "delete" => $invoicenumbersetting_delete, "alldata" => $invoicenumbersetting_alldata],
                        "invoicetandcsetting" => ["show" => $invoicetandcsetting_show, "add" => $invoicetandcsetting_add, "view" => $invoicetandcsetting_view, "edit" => $invoicetandcsetting_edit, "delete" => $invoicetandcsetting_delete, "alldata" => $invoicetandcsetting_alldata],
                        "invoicestandardsetting" => ["show" => $invoicestandardsetting_show, "add" => $invoicestandardsetting_add, "view" => $invoicestandardsetting_view, "edit" => $invoicestandardsetting_edit, "delete" => $invoicestandardsetting_delete, "alldata" => $invoicestandardsetting_alldata],
                        "invoicegstsetting" => ["show" => $invoicegstsetting_show, "add" => $invoicegstsetting_add, "view" => $invoicegstsetting_view, "edit" => $invoicegstsetting_edit, "delete" => $invoicegstsetting_delete, "alldata" => $invoicegstsetting_alldata],
                        "invoicecustomeridsetting" => ["show" => $invoicecustomeridsetting_show, "add" => $invoicecustomeridsetting_add, "view" => $invoicecustomeridsetting_view, "edit" => $invoicecustomeridsetting_edit, "delete" => $invoicecustomeridsetting_delete, "alldata" => $invoicecustomeridsetting_alldata],
                    ],
                    "leadmodule" => [
                        "lead" => ["show" => $lead_show, "add" => $lead_add, "view" => $lead_view, "edit" => $lead_edit, "delete" => $lead_delete, "alldata" => $lead_alldata]
                    ],
                    "customersupportmodule" => [
                        "customersupport" => ["show" => $customersupport_show, "add" => $customersupport_add, "view" => $customersupport_view, "edit" => $customersupport_edit, "delete" => $customersupport_delete, "alldata" => $customersupport_alldata]
                    ],
                    "adminmodule" => [
                        "company" => ["show" => $company_show, "add" => $company_add, "view" => $company_view, "edit" => $company_edit, "delete" => $company_delete, "alldata" => $company_alldata, "max" => $request->maxuser],
                        "user" => ["show" => $user_show, "add" => $user_add, "view" => $user_view, "edit" => $user_edit, "delete" => $user_delete, "alldata" => $user_alldata],
                        "techsupport" => ["show" => $techsupport_show, "add" => $techsupport_add, "view" => $techsupport_view, "edit" => $techsupport_edit, "delete" => $techsupport_delete, "alldata" => $techsupport_alldata],
                        "userpermission" => ["show" => $userpermission_show, "add" => $userpermission_add, "view" => $userpermission_view, "edit" => $userpermission_edit, "delete" => $userpermission_delete, "alldata" => $userpermission_alldata]
                    ],
                    "inventorymodule" => [
                        "product" => ["show" => $product_show, "add" => $product_add, "view" => $product_view, "edit" => $product_edit, "delete" => $product_delete, "alldata" => $product_alldata],
                        "productcategory" => ["show" => $productcategory_show, "add" => $productcategory_add, "view" => $productcategory_view, "edit" => $productcategory_edit, "delete" => $productcategory_delete, "alldata" => $productcategory_alldata],
                        "productcolumnmapping" => ["show" => $productcolumnmapping_show, "add" => $productcolumnmapping_add, "view" => $productcolumnmapping_view, "edit" => $productcolumnmapping_edit, "delete" => $productcolumnmapping_delete, "alldata" => $productcolumnmapping_alldata],
                        "purchase" => ["show" => $purchase_show, "add" => $purchase_add, "view" => $purchase_view, "edit" => $purchase_edit, "delete" => $purchase_delete, "alldata" => $purchase_alldata],
                        "inventory" => ["show" => $inventory_show, "add" => $inventory_add, "view" => $inventory_view, "edit" => $inventory_edit, "delete" => $inventory_delete, "alldata" => $inventory_alldata],
                        "supplier" => ["show" => $supplier_show, "add" => $supplier_add, "view" => $supplier_view, "edit" => $supplier_edit, "delete" => $supplier_delete, "alldata" => $supplier_alldata],
                    ],
                    "accountmodule" => [
                    ],
                    "remindermodule" => [
                        "reminder" => ["show" => $reminder_show, "add" => $reminder_add, "view" => $reminder_view, "edit" => $reminder_edit, "delete" => $reminder_delete, "alldata" => $reminder_alldata],
                        "remindercustomer" => ["show" => $remindercustomer_show, "add" => $remindercustomer_add, "view" => $remindercustomer_view, "edit" => $remindercustomer_edit, "delete" => $remindercustomer_delete, "alldata" => $remindercustomer_alldata]
                    ],
                    "reportmodule" => [
                        "report" => ["show" => $report_show, "add" => $report_add, "view" => $report_view, "edit" => $report_edit, "delete" => $report_delete, "alldata" => $request->assignedto, "log" => $request->logreport]
                    ],
                    "blogmodule" => [
                        "blog" => ["show" => $blog_show, "add" => $blog_add, "view" => $blog_view, "edit" => $blog_edit, "delete" => $blog_delete, "alldata" => $blog_alldata]
                    ],
                    "quotationmodule" => [
                        "quotation" => ["show" => $quotation_show, "add" => $quotation_add, "view" => $quotation_view, "edit" => $quotation_edit, "delete" => $quotation_delete, "alldata" => $quotation_alldata],
                        "quotationmngcol" => ["show" => $quotationmngcol_show, "add" => $quotationmngcol_add, "view" => $quotationmngcol_view, "edit" => $quotationmngcol_edit, "delete" => $quotationmngcol_delete, "alldata" => $quotationmngcol_alldata],
                        "quotationformula" => ["show" => $quotationformula_show, "add" => $quotationformula_add, "view" => $quotationformula_view, "edit" => $quotationformula_edit, "delete" => $quotationformula_delete, "alldata" => $quotationformula_alldata],
                        "quotationsetting" => ["show" => $quotationsetting_show, "add" => $quotationsetting_add, "view" => $quotationsetting_view, "edit" => $quotationsetting_edit, "delete" => $quotationsetting_delete, "alldata" => $quotationsetting_alldata],
                        "quotationnumbersetting" => ["show" => $quotationnumbersetting_show, "add" => $quotationnumbersetting_add, "view" => $quotationnumbersetting_view, "edit" => $quotationnumbersetting_edit, "delete" => $quotationnumbersetting_delete, "alldata" => $quotationnumbersetting_alldata],
                        "quotationtandcsetting" => ["show" => $quotationtandcsetting_show, "add" => $quotationtandcsetting_add, "view" => $quotationtandcsetting_view, "edit" => $quotationtandcsetting_edit, "delete" => $quotationtandcsetting_delete, "alldata" => $quotationtandcsetting_alldata],
                        "quotationstandardsetting" => ["show" => $quotationstandardsetting_show, "add" => $quotationstandardsetting_add, "view" => $quotationstandardsetting_view, "edit" => $quotationstandardsetting_edit, "delete" => $quotationstandardsetting_delete, "alldata" => $quotationstandardsetting_alldata],
                        "quotationgstsetting" => ["show" => $quotationgstsetting_show, "add" => $quotationgstsetting_add, "view" => $quotationgstsetting_view, "edit" => $quotationgstsetting_edit, "delete" => $quotationgstsetting_delete, "alldata" => $quotationgstsetting_alldata],
                        "quotationcustomer" => ["show" => $quotationcustomer_show, "add" => $quotationcustomer_add, "view" => $quotationcustomer_view, "edit" => $quotationcustomer_edit, "delete" => $quotationcustomer_delete, "alldata" => $quotationcustomer_alldata],
                    ],
                ];
                $rpjson = json_encode($rp);
            } else {
                $rpjson = json_encode($this->rp);
            }

            $passwordtoken = str::random(40); // generate password token for set new password
            $userdata = [];
            if ($request->hasFile('img') && $request->hasFile('img') != '') {
                $image = $request->file('img');
                $imageName = $request->firstname . time() . '.' . $image->getClientOriginalExtension();

                if (!file_exists('uploads/')) {
                    mkdir('uploads/', 0755, true);
                }
                // Save the image to the uploads directory
                if ($image->move('uploads/', $imageName)) {
                    $userdata['img'] = $imageName;
                }

            }

            $user = array_merge($userdata, [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'role' => 3,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'contact_no' => $request->contact_number,
                'country_id' => $request->country,
                'state_id' => $request->state,
                'city_id' => $request->city,
                'pincode' => $request->pincode,
                'pass_token' => $passwordtoken,
                'company_id' => $this->companyId,
                'created_by' => $this->userId
            ]);

            $users = User::insertGetId($user); // insert user data

            if ($users) {
                $userrp = $this->user_permissionModel::create([
                    'user_id' => $users,
                    'rp' => $rpjson,
                    'created_by' => $this->userId
                ]);
                $name = $request->firstname . ' ' . $request->lastname;
                Mail::to($request->email)->bcc('parthdeveloper9@gmail.com')->send(new sendmail($passwordtoken, $name, $request->email));
                return $this->successresponse(200, 'message', 'user succesfully created');
            } else {
                return $this->successresponse(500, 'message', 'user not succesfully create');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $user = User::find($id);

        if (!$user) {
            return $this->successresponse(500, 'message', "No Such user Found!");
        }


        if (($this->rp['adminmodule']['user']['alldata'] != 1) || ($user->company_id != $this->companyId)) {
            if ($user->created_by != $this->userId && $user->id != $this->userId && $this->userId != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $userdata['user'] = $user;

        $dbname = company::find($user->company_id);

        $userpermission = DB::table($dbname->dbname . '.user_permissions')->select('user_permissions.rp')
            ->where('user_id', $id)->get();

        if ($userpermission->isNotEmpty()) {
            $userdata['userpermission'] = $userpermission[0]->rp;
        }


        return $this->successresponse(200, 'user', $userdata);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $users = User::find($id);
        if (!$users) {
            return $this->successresponse(404, 'message', "No Such user Found!");
        }
        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($users->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($this->rp['adminmodule']['user']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        return $this->successresponse(200, 'user', $users);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'password' => 'nullable|string|max:70',
            'contact_number' => 'required|numeric|digits:10',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'created_by',
            'user_id' => 'required|numeric',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $checkuseremail = User::where('email', $request->email)
                ->where('is_deleted', 0)
                ->whereNot('id', $id)
                ->exists();

            if ($checkuseremail) {
                return $this->successresponse(500, 'message', 'This email id already exists , Please enter other email id');
            }

            $user = User::find($id);

            if (!$user) {
                return $this->successresponse(404, 'message', 'No such user found!');
            }

            if (($this->rp['adminmodule']['user']['alldata'] != 1) || ($user->company_id != $this->companyId)) {
                if ($user->created_by != $this->userId && $user->id != $this->userId && $this->userId != 1) {
                    if ($this->rp['adminmodule']['user']['edit'] != 1) {
                        return $this->successresponse(500, 'message', 'You are Unauthorized');
                    }
                }
            }



            $dbname = company::find($user->company_id);
            config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

            // Establish connection to the dynamic database
            DB::purge('dynamic_connection');
            DB::reconnect('dynamic_connection');

            if ($this->rp['adminmodule']['userpermission']['edit'] == 1) {
                //   check user permissions
                $invoice_show = (($this->rp['invoicemodule']['invoice']['show'] == 1 && $this->rp['invoicemodule']['invoice']['edit'] == 1) || $this->userId == 1) ? $request->showinvoicemenu : $this->rp['invoicemodule']['invoice']['show'];
                $invoice_add = (($this->rp['invoicemodule']['invoice']['add'] == 1 && $this->rp['invoicemodule']['invoice']['edit'] == 1) || $this->userId == 1) ? $request->addinvoice : $this->rp['invoicemodule']['invoice']['add'];
                $invoice_view = (($this->rp['invoicemodule']['invoice']['view'] == 1 && $this->rp['invoicemodule']['invoice']['edit'] == 1) || $this->userId == 1) ? $request->viewinvoice : $this->rp['invoicemodule']['invoice']['view'];
                $invoice_edit = (($this->rp['invoicemodule']['invoice']['edit'] == 1) || $this->userId == 1) ? $request->editinvoice : $this->rp['invoicemodule']['invoice']['edit'];
                $invoice_delete = (($this->rp['invoicemodule']['invoice']['delete'] == 1 && $this->rp['invoicemodule']['invoice']['edit'] == 1) || $this->userId == 1) ? $request->deleteinvoice : $this->rp['invoicemodule']['invoice']['delete'];
                $invoice_alldata = (($this->rp['invoicemodule']['invoice']['alldata'] == 1 && $this->rp['invoicemodule']['invoice']['edit'] == 1) || $this->userId == 1) ? $request->alldatainvoice : $this->rp['invoicemodule']['invoice']['alldata'];

                $mngcol_show = (($this->rp['invoicemodule']['mngcol']['show'] == 1 && $this->rp['invoicemodule']['mngcol']['edit'] == 1) || $this->userId == 1) ? $request->showmngcolmenu : $this->rp['invoicemodule']['mngcol']['show'];
                $mngcol_add = (($this->rp['invoicemodule']['mngcol']['add'] == 1 && $this->rp['invoicemodule']['mngcol']['edit'] == 1) || $this->userId == 1) ? $request->addmngcol : $this->rp['invoicemodule']['mngcol']['add'];
                $mngcol_view = (($this->rp['invoicemodule']['mngcol']['view'] == 1 && $this->rp['invoicemodule']['mngcol']['edit'] == 1) || $this->userId == 1) ? $request->viewmngcol : $this->rp['invoicemodule']['mngcol']['view'];
                $mngcol_edit = (($this->rp['invoicemodule']['mngcol']['edit'] == 1) || $this->userId == 1) ? $request->editmngcol : $this->rp['invoicemodule']['mngcol']['edit'];
                $mngcol_delete = (($this->rp['invoicemodule']['mngcol']['delete'] == 1 && $this->rp['invoicemodule']['mngcol']['edit'] == 1) || $this->userId == 1) ? $request->deletemngcol : $this->rp['invoicemodule']['mngcol']['delete'];
                $mngcol_alldata = (($this->rp['invoicemodule']['mngcol']['alldata'] == 1 && $this->rp['invoicemodule']['mngcol']['edit'] == 1) || $this->userId == 1) ? $request->alldatamngcol : $this->rp['invoicemodule']['mngcol']['alldata'];

                $formula_show = (($this->rp['invoicemodule']['formula']['show'] == 1 && $this->rp['invoicemodule']['formula']['edit'] == 1) || $this->userId == 1) ? $request->showformulamenu : $this->rp['invoicemodule']['formula']['show'];
                $formula_add = (($this->rp['invoicemodule']['formula']['add'] == 1 && $this->rp['invoicemodule']['formula']['edit'] == 1) || $this->userId == 1) ? $request->addformula : $this->rp['invoicemodule']['formula']['add'];
                $formula_view = (($this->rp['invoicemodule']['formula']['view'] == 1 && $this->rp['invoicemodule']['formula']['edit'] == 1) || $this->userId == 1) ? $request->viewformula : $this->rp['invoicemodule']['formula']['view'];
                $formula_edit = (($this->rp['invoicemodule']['formula']['edit'] == 1) || $this->userId == 1) ? $request->editformula : $this->rp['invoicemodule']['formula']['edit'];
                $formula_delete = (($this->rp['invoicemodule']['formula']['delete'] == 1 && $this->rp['invoicemodule']['formula']['edit'] == 1) || $this->userId == 1) ? $request->deleteformula : $this->rp['invoicemodule']['formula']['delete'];
                $formula_alldata = (($this->rp['invoicemodule']['formula']['alldata'] == 1 && $this->rp['invoicemodule']['formula']['edit'] == 1) || $this->userId == 1) ? $request->alldataformula : $this->rp['invoicemodule']['formula']['alldata'];

                $invoicesetting_show = (($this->rp['invoicemodule']['invoicesetting']['show'] == 1 && $this->rp['invoicemodule']['invoicesetting']['edit'] == 1) || $this->userId == 1) ? $request->showinvoicesettingmenu : $this->rp['invoicemodule']['invoicesetting']['show'];
                $invoicesetting_add = (($this->rp['invoicemodule']['invoicesetting']['add'] == 1 && $this->rp['invoicemodule']['invoicesetting']['edit'] == 1) || $this->userId == 1) ? $request->addinvoicesetting : $this->rp['invoicemodule']['invoicesetting']['add'];
                $invoicesetting_view = (($this->rp['invoicemodule']['invoicesetting']['view'] == 1 && $this->rp['invoicemodule']['invoicesetting']['edit'] == 1) || $this->userId == 1) ? $request->viewinvoicesetting : $this->rp['invoicemodule']['invoicesetting']['view'];
                $invoicesetting_edit = (($this->rp['invoicemodule']['invoicesetting']['edit'] == 1) || $this->userId == 1) ? $request->editinvoicesetting : $this->rp['invoicemodule']['invoicesetting']['edit'];
                $invoicesetting_delete = (($this->rp['invoicemodule']['invoicesetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicesetting']['edit'] == 1) || $this->userId == 1) ? $request->deleteinvoicesetting : $this->rp['invoicemodule']['invoicesetting']['delete'];
                $invoicesetting_alldata = (($this->rp['invoicemodule']['invoicesetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicesetting']['edit'] == 1) || $this->userId == 1) ? $request->alldatainvoicesetting : $this->rp['invoicemodule']['invoicesetting']['alldata'];

                $invoicenumbersetting_show = (($this->rp['invoicemodule']['invoicenumbersetting']['show'] == 1 && $this->rp['invoicemodule']['invoicenumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->showinvoicenumbersettingmenu : $this->rp['invoicemodule']['invoicenumbersetting']['show'];
                $invoicenumbersetting_add = (($this->rp['invoicemodule']['invoicenumbersetting']['add'] == 1 && $this->rp['invoicemodule']['invoicenumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->addinvoicenumbersetting : $this->rp['invoicemodule']['invoicenumbersetting']['add'];
                $invoicenumbersetting_view = (($this->rp['invoicemodule']['invoicenumbersetting']['view'] == 1 && $this->rp['invoicemodule']['invoicenumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->viewinvoicenumbersetting : $this->rp['invoicemodule']['invoicenumbersetting']['view'];
                $invoicenumbersetting_edit = (($this->rp['invoicemodule']['invoicenumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->editinvoicenumbersetting : $this->rp['invoicemodule']['invoicenumbersetting']['edit'];
                $invoicenumbersetting_delete = (($this->rp['invoicemodule']['invoicenumbersetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicenumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->deleteinvoicenumbersetting : $this->rp['invoicemodule']['invoicenumbersetting']['delete'];
                $invoicenumbersetting_alldata = (($this->rp['invoicemodule']['invoicenumbersetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicenumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->alldatainvoicenumbersetting : $this->rp['invoicemodule']['invoicenumbersetting']['alldata'];

                $invoicetandcsetting_show = (($this->rp['invoicemodule']['invoicetandcsetting']['show'] == 1 && $this->rp['invoicemodule']['invoicetandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->showinvoicetandcsettingmenu : $this->rp['invoicemodule']['invoicetandcsetting']['show'];
                $invoicetandcsetting_add = (($this->rp['invoicemodule']['invoicetandcsetting']['add'] == 1 && $this->rp['invoicemodule']['invoicetandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->addinvoicetandcsetting : $this->rp['invoicemodule']['invoicetandcsetting']['add'];
                $invoicetandcsetting_view = (($this->rp['invoicemodule']['invoicetandcsetting']['view'] == 1 && $this->rp['invoicemodule']['invoicetandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->viewinvoicetandcsetting : $this->rp['invoicemodule']['invoicetandcsetting']['view'];
                $invoicetandcsetting_edit = (($this->rp['invoicemodule']['invoicetandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->editinvoicetandcsetting : $this->rp['invoicemodule']['invoicetandcsetting']['edit'];
                $invoicetandcsetting_delete = (($this->rp['invoicemodule']['invoicetandcsetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicetandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->deleteinvoicetandcsetting : $this->rp['invoicemodule']['invoicetandcsetting']['delete'];
                $invoicetandcsetting_alldata = (($this->rp['invoicemodule']['invoicetandcsetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicetandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->alldatainvoicetandcsetting : $this->rp['invoicemodule']['invoicetandcsetting']['alldata'];

                $invoicestandardsetting_show = (($this->rp['invoicemodule']['invoicestandardsetting']['show'] == 1 && $this->rp['invoicemodule']['invoicestandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->showinvoicestandardsettingmenu : $this->rp['invoicemodule']['invoicestandardsetting']['show'];
                $invoicestandardsetting_add = (($this->rp['invoicemodule']['invoicestandardsetting']['add'] == 1 && $this->rp['invoicemodule']['invoicestandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->addinvoicestandardsetting : $this->rp['invoicemodule']['invoicestandardsetting']['add'];
                $invoicestandardsetting_view = (($this->rp['invoicemodule']['invoicestandardsetting']['view'] == 1 && $this->rp['invoicemodule']['invoicestandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->viewinvoicestandardsetting : $this->rp['invoicemodule']['invoicestandardsetting']['view'];
                $invoicestandardsetting_edit = (($this->rp['invoicemodule']['invoicestandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->editinvoicestandardsetting : $this->rp['invoicemodule']['invoicestandardsetting']['edit'];
                $invoicestandardsetting_delete = (($this->rp['invoicemodule']['invoicestandardsetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicestandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->deleteinvoicestandardsetting : $this->rp['invoicemodule']['invoicestandardsetting']['delete'];
                $invoicestandardsetting_alldata = (($this->rp['invoicemodule']['invoicestandardsetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicestandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->alldatainvoicestandardsetting : $this->rp['invoicemodule']['invoicestandardsetting']['alldata'];

                $invoicegstsetting_show = (($this->rp['invoicemodule']['invoicegstsetting']['show'] == 1 && $this->rp['invoicemodule']['invoicegstsetting']['edit'] == 1) || $this->userId == 1) ? $request->showinvoicegstsettingmenu : $this->rp['invoicemodule']['invoicegstsetting']['show'];
                $invoicegstsetting_add = (($this->rp['invoicemodule']['invoicegstsetting']['add'] == 1 && $this->rp['invoicemodule']['invoicegstsetting']['edit'] == 1) || $this->userId == 1) ? $request->addinvoicegstsetting : $this->rp['invoicemodule']['invoicegstsetting']['add'];
                $invoicegstsetting_view = (($this->rp['invoicemodule']['invoicegstsetting']['view'] == 1 && $this->rp['invoicemodule']['invoicegstsetting']['edit'] == 1) || $this->userId == 1) ? $request->viewinvoicegstsetting : $this->rp['invoicemodule']['invoicegstsetting']['view'];
                $invoicegstsetting_edit = (($this->rp['invoicemodule']['invoicegstsetting']['edit'] == 1) || $this->userId == 1) ? $request->editinvoicegstsetting : $this->rp['invoicemodule']['invoicegstsetting']['edit'];
                $invoicegstsetting_delete = (($this->rp['invoicemodule']['invoicegstsetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicegstsetting']['edit'] == 1) || $this->userId == 1) ? $request->deleteinvoicegstsetting : $this->rp['invoicemodule']['invoicegstsetting']['delete'];
                $invoicegstsetting_alldata = (($this->rp['invoicemodule']['invoicegstsetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicegstsetting']['edit'] == 1) || $this->userId == 1) ? $request->alldatainvoicegstsetting : $this->rp['invoicemodule']['invoicegstsetting']['alldata'];

                $invoicecustomeridsetting_show = (($this->rp['invoicemodule']['invoicecustomeridsetting']['show'] == 1 && $this->rp['invoicemodule']['invoicecustomeridsetting']['edit'] == 1) || $this->userId == 1) ? $request->showinvoicecustomeridsettingmenu : $this->rp['invoicemodule']['invoicecustomeridsetting']['show'];
                $invoicecustomeridsetting_add = (($this->rp['invoicemodule']['invoicecustomeridsetting']['add'] == 1 && $this->rp['invoicemodule']['invoicecustomeridsetting']['edit'] == 1) || $this->userId == 1) ? $request->addinvoicecustomeridsetting : $this->rp['invoicemodule']['invoicecustomeridsetting']['add'];
                $invoicecustomeridsetting_view = (($this->rp['invoicemodule']['invoicecustomeridsetting']['view'] == 1 && $this->rp['invoicemodule']['invoicecustomeridsetting']['edit'] == 1) || $this->userId == 1) ? $request->viewinvoicecustomeridsetting : $this->rp['invoicemodule']['invoicecustomeridsetting']['view'];
                $invoicecustomeridsetting_edit = (($this->rp['invoicemodule']['invoicecustomeridsetting']['edit'] == 1) || $this->userId == 1) ? $request->editinvoicecustomeridsetting : $this->rp['invoicemodule']['invoicecustomeridsetting']['edit'];
                $invoicecustomeridsetting_delete = (($this->rp['invoicemodule']['invoicecustomeridsetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicecustomeridsetting']['edit'] == 1) || $this->userId == 1) ? $request->deleteinvoicecustomeridsetting : $this->rp['invoicemodule']['invoicecustomeridsetting']['delete'];
                $invoicecustomeridsetting_alldata = (($this->rp['invoicemodule']['invoicecustomeridsetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicecustomeridsetting']['edit'] == 1) || $this->userId == 1) ? $request->alldatainvoicecustomeridsetting : $this->rp['invoicemodule']['invoicecustomeridsetting']['alldata'];

                $bank_show = (($this->rp['invoicemodule']['bank']['show'] == 1 && $this->rp['invoicemodule']['bank']['edit'] == 1) || $this->userId == 1) ? $request->showbankmenu : $this->rp['invoicemodule']['bank']['show'];
                $bank_add = (($this->rp['invoicemodule']['bank']['add'] == 1 && $this->rp['invoicemodule']['bank']['edit'] == 1) || $this->userId == 1) ? $request->addbank : $this->rp['invoicemodule']['bank']['add'];
                $bank_view = (($this->rp['invoicemodule']['bank']['view'] == 1 && $this->rp['invoicemodule']['bank']['edit'] == 1) || $this->userId == 1) ? $request->viewbank : $this->rp['invoicemodule']['bank']['view'];
                $bank_edit = (($this->rp['invoicemodule']['bank']['edit'] == 1) || $this->userId == 1) ? $request->editbank : $this->rp['invoicemodule']['bank']['edit'];
                $bank_delete = (($this->rp['invoicemodule']['bank']['delete'] == 1 && $this->rp['invoicemodule']['bank']['edit'] == 1) || $this->userId == 1) ? $request->deletebank : $this->rp['invoicemodule']['bank']['delete'];
                $bank_alldata = (($this->rp['invoicemodule']['bank']['alldata'] == 1 && $this->rp['invoicemodule']['bank']['edit'] == 1) || $this->userId == 1) ? $request->alldatabank : $this->rp['invoicemodule']['bank']['alldata'];

                $customer_show = (($this->rp['invoicemodule']['customer']['show'] == 1 && $this->rp['invoicemodule']['customer']['edit'] == 1) || $this->userId == 1) ? $request->showcustomermenu : $this->rp['invoicemodule']['customer']['show'];
                $customer_add = (($this->rp['invoicemodule']['customer']['add'] == 1 && $this->rp['invoicemodule']['customer']['edit'] == 1) || $this->userId == 1) ? $request->addcustomer : $this->rp['invoicemodule']['customer']['add'];
                $customer_view = (($this->rp['invoicemodule']['customer']['view'] == 1 && $this->rp['invoicemodule']['customer']['edit'] == 1) || $this->userId == 1) ? $request->viewcustomer : $this->rp['invoicemodule']['customer']['view'];
                $customer_edit = (($this->rp['invoicemodule']['customer']['edit'] == 1) || $this->userId == 1) ? $request->editcustomer : $this->rp['invoicemodule']['customer']['edit'];
                $customer_delete = (($this->rp['invoicemodule']['customer']['delete'] == 1 && $this->rp['invoicemodule']['customer']['edit'] == 1) || $this->userId == 1) ? $request->deletecustomer : $this->rp['invoicemodule']['customer']['delete'];
                $customer_alldata = (($this->rp['invoicemodule']['customer']['alldata'] == 1 && $this->rp['invoicemodule']['customer']['edit'] == 1) || $this->userId == 1) ? $request->alldatacustomer : $this->rp['invoicemodule']['customer']['alldata'];

                $lead_show = (($this->rp['leadmodule']['lead']['show'] == 1 && $this->rp['leadmodule']['lead']['edit'] == 1) || $this->userId == 1) ? $request->showleadmenu : $this->rp['leadmodule']['lead']['show'];
                $lead_add = (($this->rp['leadmodule']['lead']['add'] == 1 && $this->rp['leadmodule']['lead']['edit'] == 1) || $this->userId == 1) ? $request->addlead : $this->rp['leadmodule']['lead']['add'];
                $lead_view = (($this->rp['leadmodule']['lead']['view'] == 1 && $this->rp['leadmodule']['lead']['edit'] == 1) || $this->userId == 1) ? $request->viewlead : $this->rp['leadmodule']['lead']['view'];
                $lead_edit = (($this->rp['leadmodule']['lead']['edit'] == 1) || $this->userId == 1) ? $request->editlead : $this->rp['leadmodule']['lead']['edit'];
                $lead_delete = (($this->rp['leadmodule']['lead']['delete'] == 1 && $this->rp['leadmodule']['lead']['edit'] == 1) || $this->userId == 1) ? $request->deletelead : $this->rp['leadmodule']['lead']['delete'];
                $lead_alldata = (($this->rp['leadmodule']['lead']['alldata'] == 1 && $this->rp['leadmodule']['lead']['edit'] == 1) || $this->userId == 1) ? $request->alldatalead : $this->rp['leadmodule']['lead']['alldata'];

                $customersupport_show = (($this->rp['customersupportmodule']['customersupport']['show'] == 1 && $this->rp['customersupportmodule']['customersupport']['edit'] == 1) || $this->userId == 1) ? $request->showcustomersupportmenu : $this->rp['customersupportmodule']['customersupport']['show'];
                $customersupport_add = (($this->rp['customersupportmodule']['customersupport']['add'] == 1 && $this->rp['customersupportmodule']['customersupport']['edit'] == 1) || $this->userId == 1) ? $request->addcustomersupport : $this->rp['customersupportmodule']['customersupport']['add'];
                $customersupport_view = (($this->rp['customersupportmodule']['customersupport']['view'] == 1 && $this->rp['customersupportmodule']['customersupport']['edit'] == 1) || $this->userId == 1) ? $request->viewcustomersupport : $this->rp['customersupportmodule']['customersupport']['view'];
                $customersupport_edit = (($this->rp['customersupportmodule']['customersupport']['edit'] == 1) || $this->userId == 1) ? $request->editcustomersupport : $this->rp['customersupportmodule']['customersupport']['edit'];
                $customersupport_delete = (($this->rp['customersupportmodule']['customersupport']['delete'] == 1 && $this->rp['customersupportmodule']['customersupport']['edit'] == 1) || $this->userId == 1) ? $request->deletecustomersupport : $this->rp['customersupportmodule']['customersupport']['delete'];
                $customersupport_alldata = (($this->rp['customersupportmodule']['customersupport']['alldata'] == 1 && $this->rp['customersupportmodule']['customersupport']['edit'] == 1) || $this->userId == 1) ? $request->alldatacustomersupport : $this->rp['customersupportmodule']['customersupport']['alldata'];

                $product_show = (($this->rp['inventorymodule']['product']['show'] == 1 && $this->rp['inventorymodule']['product']['edit'] == 1) || $this->userId == 1) ? $request->showproductmenu : $this->rp['inventorymodule']['product']['show'];
                $product_add = (($this->rp['inventorymodule']['product']['add'] == 1 && $this->rp['inventorymodule']['product']['edit'] == 1) || $this->userId == 1) ? $request->addproduct : $this->rp['inventorymodule']['product']['add'];
                $product_view = (($this->rp['inventorymodule']['product']['view'] == 1 && $this->rp['inventorymodule']['product']['edit'] == 1) || $this->userId == 1) ? $request->viewproduct : $this->rp['inventorymodule']['product']['view'];
                $product_edit = (($this->rp['inventorymodule']['product']['edit'] == 1) || $this->userId == 1) ? $request->editproduct : $this->rp['inventorymodule']['product']['edit'];
                $product_delete = (($this->rp['inventorymodule']['product']['delete'] == 1 && $this->rp['inventorymodule']['product']['edit'] == 1) || $this->userId == 1) ? $request->deleteproduct : $this->rp['inventorymodule']['product']['delete'];
                $product_alldata = (($this->rp['inventorymodule']['product']['alldata'] == 1 && $this->rp['inventorymodule']['product']['edit'] == 1) || $this->userId == 1) ? $request->alldataproduct : $this->rp['inventorymodule']['product']['alldata'];

                $productcategory_show = (($this->rp['inventorymodule']['productcategory']['show'] == 1 && $this->rp['inventorymodule']['productcategory']['edit'] == 1) || $this->userId == 1) ? $request->showproductcategorymenu : $this->rp['inventorymodule']['productcategory']['show'];
                $productcategory_add = (($this->rp['inventorymodule']['productcategory']['add'] == 1 && $this->rp['inventorymodule']['productcategory']['edit'] == 1) || $this->userId == 1) ? $request->addproductcategory : $this->rp['inventorymodule']['productcategory']['add'];
                $productcategory_view = (($this->rp['inventorymodule']['productcategory']['view'] == 1 && $this->rp['inventorymodule']['productcategory']['edit'] == 1) || $this->userId == 1) ? $request->viewproductcategory : $this->rp['inventorymodule']['productcategory']['view'];
                $productcategory_edit = (($this->rp['inventorymodule']['productcategory']['edit'] == 1) || $this->userId == 1) ? $request->editproductcategory : $this->rp['inventorymodule']['productcategory']['edit'];
                $productcategory_delete = (($this->rp['inventorymodule']['productcategory']['delete'] == 1 && $this->rp['inventorymodule']['productcategory']['edit'] == 1) || $this->userId == 1) ? $request->deleteproductcategory : $this->rp['inventorymodule']['productcategory']['delete'];
                $productcategory_alldata = (($this->rp['inventorymodule']['productcategory']['alldata'] == 1 && $this->rp['inventorymodule']['productcategory']['edit'] == 1) || $this->userId == 1) ? $request->alldataproductcategory : $this->rp['inventorymodule']['productcategory']['alldata'];

                $productcolumnmapping_show = (($this->rp['inventorymodule']['productcolumnmapping']['show'] == 1 && $this->rp['inventorymodule']['productcolumnmapping']['edit'] == 1) || $this->userId == 1) ? $request->showproductcolumnmappingmenu : $this->rp['inventorymodule']['productcolumnmapping']['show'];
                $productcolumnmapping_add = (($this->rp['inventorymodule']['productcolumnmapping']['add'] == 1 && $this->rp['inventorymodule']['productcolumnmapping']['edit'] == 1) || $this->userId == 1) ? $request->addproductcolumnmapping : $this->rp['inventorymodule']['productcolumnmapping']['add'];
                $productcolumnmapping_view = (($this->rp['inventorymodule']['productcolumnmapping']['view'] == 1 && $this->rp['inventorymodule']['productcolumnmapping']['edit'] == 1) || $this->userId == 1) ? $request->viewproductcolumnmapping : $this->rp['inventorymodule']['productcolumnmapping']['view'];
                $productcolumnmapping_edit = (($this->rp['inventorymodule']['productcolumnmapping']['edit'] == 1) || $this->userId == 1) ? $request->editproductcolumnmapping : $this->rp['inventorymodule']['productcolumnmapping']['edit'];
                $productcolumnmapping_delete = (($this->rp['inventorymodule']['productcolumnmapping']['delete'] == 1 && $this->rp['inventorymodule']['productcolumnmapping']['edit'] == 1) || $this->userId == 1) ? $request->deleteproductcolumnmapping : $this->rp['inventorymodule']['productcolumnmapping']['delete'];
                $productcolumnmapping_alldata = (($this->rp['inventorymodule']['productcolumnmapping']['alldata'] == 1 && $this->rp['inventorymodule']['productcolumnmapping']['edit'] == 1) || $this->userId == 1) ? $request->alldataproductcolumnmapping : $this->rp['inventorymodule']['productcolumnmapping']['alldata'];

                $purchase_show = (($this->rp['inventorymodule']['purchase']['show'] == 1 && $this->rp['inventorymodule']['purchase']['edit'] == 1) || $this->userId == 1) ? $request->showpurchasemenu : $this->rp['inventorymodule']['purchase']['show'];
                $purchase_add = (($this->rp['inventorymodule']['purchase']['add'] == 1 && $this->rp['inventorymodule']['purchase']['edit'] == 1) || $this->userId == 1) ? $request->addpurchase : $this->rp['inventorymodule']['purchase']['add'];
                $purchase_view = (($this->rp['inventorymodule']['purchase']['view'] == 1 && $this->rp['inventorymodule']['purchase']['edit'] == 1) || $this->userId == 1) ? $request->viewpurchase : $this->rp['inventorymodule']['purchase']['view'];
                $purchase_edit = (($this->rp['inventorymodule']['purchase']['edit'] == 1) || $this->userId == 1) ? $request->editpurchase : $this->rp['inventorymodule']['purchase']['edit'];
                $purchase_delete = (($this->rp['inventorymodule']['purchase']['delete'] == 1 && $this->rp['inventorymodule']['purchase']['edit'] == 1) || $this->userId == 1) ? $request->deletepurchase : $this->rp['inventorymodule']['purchase']['delete'];
                $purchase_alldata = (($this->rp['inventorymodule']['purchase']['alldata'] == 1 && $this->rp['inventorymodule']['purchase']['edit'] == 1) || $this->userId == 1) ? $request->alldatapurchase : $this->rp['inventorymodule']['purchase']['alldata'];

                $inventory_show = (($this->rp['inventorymodule']['inventory']['show'] == 1 && $this->rp['inventorymodule']['inventory']['edit'] == 1) || $this->userId == 1) ? $request->showinventorymenu : $this->rp['inventorymodule']['inventory']['show'];
                $inventory_add = (($this->rp['inventorymodule']['inventory']['add'] == 1 && $this->rp['inventorymodule']['inventory']['edit'] == 1) || $this->userId == 1) ? $request->addinventory : $this->rp['inventorymodule']['inventory']['add'];
                $inventory_view = (($this->rp['inventorymodule']['inventory']['view'] == 1 && $this->rp['inventorymodule']['inventory']['edit'] == 1) || $this->userId == 1) ? $request->viewinventory : $this->rp['inventorymodule']['inventory']['view'];
                $inventory_edit = (($this->rp['inventorymodule']['inventory']['edit'] == 1) || $this->userId == 1) ? $request->editinventory : $this->rp['inventorymodule']['inventory']['edit'];
                $inventory_delete = (($this->rp['inventorymodule']['inventory']['delete'] == 1 && $this->rp['inventorymodule']['inventory']['edit'] == 1) || $this->userId == 1) ? $request->deleteinventory : $this->rp['inventorymodule']['inventory']['delete'];
                $inventory_alldata = (($this->rp['inventorymodule']['inventory']['alldata'] == 1 && $this->rp['inventorymodule']['inventory']['edit'] == 1) || $this->userId == 1) ? $request->alldatainventory : $this->rp['inventorymodule']['inventory']['alldata'];

                $supplier_show = (($this->rp['inventorymodule']['supplier']['show'] == 1 && $this->rp['inventorymodule']['supplier']['edit'] == 1) || $this->userId == 1) ? $request->showsuppliermenu : $this->rp['inventorymodule']['supplier']['show'];
                $supplier_add = (($this->rp['inventorymodule']['supplier']['add'] == 1 && $this->rp['inventorymodule']['supplier']['edit'] == 1) || $this->userId == 1) ? $request->addsupplier : $this->rp['inventorymodule']['supplier']['add'];
                $supplier_view = (($this->rp['inventorymodule']['supplier']['view'] == 1 && $this->rp['inventorymodule']['supplier']['edit'] == 1) || $this->userId == 1) ? $request->viewsupplier : $this->rp['inventorymodule']['supplier']['view'];
                $supplier_edit = (($this->rp['inventorymodule']['supplier']['edit'] == 1) || $this->userId == 1) ? $request->editsupplier : $this->rp['inventorymodule']['supplier']['edit'];
                $supplier_delete = (($this->rp['inventorymodule']['supplier']['delete'] == 1 && $this->rp['inventorymodule']['supplier']['edit'] == 1) || $this->userId == 1) ? $request->deletesupplier : $this->rp['inventorymodule']['supplier']['delete'];
                $supplier_alldata = (($this->rp['inventorymodule']['supplier']['alldata'] == 1 && $this->rp['inventorymodule']['supplier']['edit'] == 1) || $this->userId == 1) ? $request->alldatasupplier : $this->rp['inventorymodule']['supplier']['alldata'];

                $reminder_show = (($this->rp['remindermodule']['reminder']['show'] == 1 && $this->rp['remindermodule']['reminder']['edit'] == 1) || $this->userId == 1) ? $request->showremindermenu : $this->rp['remindermodule']['reminder']['show'];
                $reminder_add = (($this->rp['remindermodule']['reminder']['add'] == 1 && $this->rp['remindermodule']['reminder']['edit'] == 1) || $this->userId == 1) ? $request->addreminder : $this->rp['remindermodule']['reminder']['add'];
                $reminder_view = (($this->rp['remindermodule']['reminder']['view'] == 1 && $this->rp['remindermodule']['reminder']['edit'] == 1) || $this->userId == 1) ? $request->viewreminder : $this->rp['remindermodule']['reminder']['view'];
                $reminder_edit = (($this->rp['remindermodule']['reminder']['edit'] == 1) || $this->userId == 1) ? $request->editreminder : $this->rp['remindermodule']['reminder']['edit'];
                $reminder_delete = (($this->rp['remindermodule']['reminder']['delete'] == 1 && $this->rp['remindermodule']['reminder']['edit'] == 1) || $this->userId == 1) ? $request->deletereminder : $this->rp['remindermodule']['reminder']['delete'];
                $reminder_alldata = (($this->rp['remindermodule']['reminder']['alldata'] == 1 && $this->rp['remindermodule']['reminder']['edit'] == 1) || $this->userId == 1) ? $request->alldatareminder : $this->rp['remindermodule']['reminder']['alldata'];

                $remindercustomer_show = (($this->rp['remindermodule']['remindercustomer']['show'] == 1 && $this->rp['remindermodule']['remindercustomer']['edit'] == 1) || $this->userId == 1) ? $request->showremindercustomermenu : "0";
                $remindercustomer_add = (($this->rp['remindermodule']['remindercustomer']['add'] == 1 && $this->rp['remindermodule']['remindercustomer']['edit'] == 1) || $this->userId == 1) ? $request->addremindercustomer : "0";
                $remindercustomer_view = (($this->rp['remindermodule']['remindercustomer']['view'] == 1 && $this->rp['remindermodule']['remindercustomer']['edit'] == 1) || $this->userId == 1) ? $request->viewremindercustomer : "0";
                $remindercustomer_edit = (($this->rp['remindermodule']['remindercustomer']['edit'] == 1) || $this->userId == 1) ? $request->editremindercustomer : "0";
                $remindercustomer_delete = (($this->rp['remindermodule']['remindercustomer']['delete'] == 1 && $this->rp['remindermodule']['remindercustomer']['edit'] == 1) || $this->userId == 1) ? $request->deleteremindercustomer : "0";
                $remindercustomer_alldata = (($this->rp['remindermodule']['remindercustomer']['alldata'] == 1 && $this->rp['remindermodule']['remindercustomer']['edit'] == 1) || $this->userId == 1) ? $request->alldataremindercustomer : "0";

                $company_show = (($this->rp['adminmodule']['company']['show'] == 1 && $this->rp['adminmodule']['company']['edit'] == 1) || $this->userId == 1) ? $request->showcompanymenu : "0";
                $company_add = (($this->rp['adminmodule']['company']['add'] == 1 && $this->rp['adminmodule']['company']['edit'] == 1) || $this->userId == 1) ? $request->addcompany : "0";
                $company_view = (($this->rp['adminmodule']['company']['view'] == 1 && $this->rp['adminmodule']['company']['edit'] == 1) || $this->userId == 1) ? $request->viewcompany : "0";
                $company_edit = (($this->rp['adminmodule']['company']['edit'] == 1) || $this->userId == 1) ? $request->editcompany : "0";
                $company_delete = (($this->rp['adminmodule']['company']['delete'] == 1 && $this->rp['adminmodule']['company']['edit'] == 1) || $this->userId == 1) ? $request->deletecompany : "0";
                $company_alldata = (($this->rp['adminmodule']['company']['alldata'] == 1 && $this->rp['adminmodule']['company']['edit'] == 1) || $this->userId == 1) ? $request->alldatacompany : "0";
                $company_maxuser = (($this->rp['adminmodule']['company']['max'] == 1 && $this->rp['adminmodule']['company']['edit'] == 1) || $this->userId == 1) ? $request->maxuser : "0";

                $user_show = (($this->rp['adminmodule']['user']['show'] == 1 && $this->rp['adminmodule']['user']['edit'] == 1) || $this->userId == 1) ? $request->showusermenu : "0";
                $user_add = (($this->rp['adminmodule']['user']['add'] == 1 && $this->rp['adminmodule']['user']['edit'] == 1) || $this->userId == 1) ? $request->adduser : "0";
                $user_view = (($this->rp['adminmodule']['user']['view'] == 1 && $this->rp['adminmodule']['user']['edit'] == 1) || $this->userId == 1) ? $request->viewuser : "0";
                $user_edit = (($this->rp['adminmodule']['user']['edit'] == 1) || $this->userId == 1) ? $request->edituser : "0";
                $user_delete = (($this->rp['adminmodule']['user']['delete'] && $this->rp['adminmodule']['user']['edit'] == 1) || $this->userId == 1) ? $request->deleteuser : "0";
                $user_alldata = (($this->rp['adminmodule']['user']['alldata'] && $this->rp['adminmodule']['user']['edit'] == 1) || $this->userId == 1) ? $request->alldatauser : "0";

                $userpermission_show = (($this->rp['adminmodule']['userpermission']['show'] == 1 && $this->rp['adminmodule']['userpermission']['edit'] == 1) || $this->userId == 1) ? $request->showuserpermissionmenu : "0";
                $userpermission_add = (($this->rp['adminmodule']['userpermission']['add'] == 1 && $this->rp['adminmodule']['userpermission']['edit'] == 1) || $this->userId == 1) ? $request->adduserpermission : "0";
                $userpermission_view = (($this->rp['adminmodule']['userpermission']['view'] == 1 && $this->rp['adminmodule']['userpermission']['edit'] == 1) || $this->userId == 1) ? $request->viewuserpermission : "0";
                $userpermission_edit = (($this->rp['adminmodule']['userpermission']['edit'] == 1) || $this->userId == 1) ? $request->edituserpermission : "0";
                $userpermission_delete = (($this->rp['adminmodule']['userpermission']['delete'] && $this->rp['adminmodule']['userpermission']['edit'] == 1) || $this->userId == 1) ? $request->deleteuserpermission : "0";
                $userpermission_alldata = (($this->rp['adminmodule']['userpermission']['alldata'] && $this->rp['adminmodule']['userpermission']['edit'] == 1) || $this->userId == 1) ? $request->alldatauserpermission : "0";

                $techsupport_show = (($this->rp['adminmodule']['techsupport']['show'] == 1 && $this->rp['adminmodule']['techsupport']['edit'] == 1) || $this->userId == 1) ? $request->showtechsupportmenu : "0";
                $techsupport_add = (($this->rp['adminmodule']['techsupport']['add'] == 1 && $this->rp['adminmodule']['techsupport']['edit'] == 1) || $this->userId == 1) ? $request->addtechsupport : "0";
                $techsupport_view = (($this->rp['adminmodule']['techsupport']['view'] == 1 && $this->rp['adminmodule']['techsupport']['edit'] == 1) || $this->userId == 1) ? $request->viewtechsupport : "0";
                $techsupport_edit = (($this->rp['adminmodule']['techsupport']['edit'] == 1) || $this->userId == 1) ? $request->edittechsupport : "0";
                $techsupport_delete = (($this->rp['adminmodule']['techsupport']['delete'] == 1 && $this->rp['adminmodule']['techsupport']['edit'] == 1) || $this->userId == 1) ? $request->deletetechsupport : "0";
                $techsupport_alldata = (($this->rp['adminmodule']['techsupport']['alldata'] == 1 && $this->rp['adminmodule']['techsupport']['edit'] == 1) || $this->userId == 1) ? $request->alldatatechsupport : "0";

                $report_show = (($this->rp['reportmodule']['report']['show'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->showreportmenu : "0";
                $report_add = (($this->rp['reportmodule']['report']['add'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->addreport : "0";
                $report_view = (($this->rp['reportmodule']['report']['view'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->viewreport : "0";
                $report_edit = (($this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->editreport : "0";
                $report_delete = (($this->rp['reportmodule']['report']['delete'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->deletereport : "0";
                $report_alldata = (($this->rp['reportmodule']['report']['add'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->assignedto : "0";
                $report_log = (($this->rp['reportmodule']['report']['log'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->logreport : "0";


                $quotation_show = (($this->rp['quotationmodule']['quotation']['show'] == 1 && $this->rp['quotationmodule']['quotation']['edit'] == 1) || $this->userId == 1) ? $request->showquotationmenu : $this->rp['quotationmodule']['quotation']['show'];
                $quotation_add = (($this->rp['quotationmodule']['quotation']['add'] == 1 && $this->rp['quotationmodule']['quotation']['edit'] == 1) || $this->userId == 1) ? $request->addquotation : $this->rp['quotationmodule']['quotation']['add'];
                $quotation_view = (($this->rp['quotationmodule']['quotation']['view'] == 1 && $this->rp['quotationmodule']['quotation']['edit'] == 1) || $this->userId == 1) ? $request->viewquotation : $this->rp['quotationmodule']['quotation']['view'];
                $quotation_edit = (($this->rp['quotationmodule']['quotation']['edit'] == 1) || $this->userId == 1) ? $request->editquotation : $this->rp['quotationmodule']['quotation']['edit'];
                $quotation_delete = (($this->rp['quotationmodule']['quotation']['delete'] == 1 && $this->rp['quotationmodule']['quotation']['edit'] == 1) || $this->userId == 1) ? $request->deletequotation : $this->rp['quotationmodule']['quotation']['delete'];
                $quotation_alldata = (($this->rp['quotationmodule']['quotation']['alldata'] == 1 && $this->rp['quotationmodule']['quotation']['edit'] == 1) || $this->userId == 1) ? $request->alldataquotation : $this->rp['quotationmodule']['quotation']['alldata'];

                $quotationmngcol_show = (($this->rp['quotationmodule']['quotationmngcol']['show'] == 1 && $this->rp['quotationmodule']['quotationmngcol']['edit'] == 1) || $this->userId == 1) ? $request->showquotationmngcolmenu : $this->rp['quotationmodule']['quotationmngcol']['show'];
                $quotationmngcol_add = (($this->rp['quotationmodule']['quotationmngcol']['add'] == 1 && $this->rp['quotationmodule']['quotationmngcol']['edit'] == 1) || $this->userId == 1) ? $request->addquotationmngcol : $this->rp['quotationmodule']['quotationmngcol']['add'];
                $quotationmngcol_view = (($this->rp['quotationmodule']['quotationmngcol']['view'] == 1 && $this->rp['quotationmodule']['quotationmngcol']['edit'] == 1) || $this->userId == 1) ? $request->viewquotationmngcol : $this->rp['quotationmodule']['quotationmngcol']['view'];
                $quotationmngcol_edit = (($this->rp['quotationmodule']['quotationmngcol']['edit'] == 1) || $this->userId == 1) ? $request->editquotationmngcol : $this->rp['quotationmodule']['quotationmngcol']['edit'];
                $quotationmngcol_delete = (($this->rp['quotationmodule']['quotationmngcol']['delete'] == 1 && $this->rp['quotationmodule']['quotationmngcol']['edit'] == 1) || $this->userId == 1) ? $request->deletequotationmngcol : $this->rp['quotationmodule']['quotationmngcol']['delete'];
                $quotationmngcol_alldata = (($this->rp['quotationmodule']['quotationmngcol']['alldata'] == 1 && $this->rp['quotationmodule']['quotationmngcol']['edit'] == 1) || $this->userId == 1) ? $request->alldataquotationmngcol : $this->rp['quotationmodule']['quotationmngcol']['alldata'];

                $quotationformula_show = (($this->rp['quotationmodule']['quotationformula']['show'] == 1 && $this->rp['quotationmodule']['quotationformula']['edit'] == 1) || $this->userId == 1) ? $request->showquotationformulamenu : $this->rp['quotationmodule']['quotationformula']['show'];
                $quotationformula_add = (($this->rp['quotationmodule']['quotationformula']['add'] == 1 && $this->rp['quotationmodule']['quotationformula']['edit'] == 1) || $this->userId == 1) ? $request->addquotationformula : $this->rp['quotationmodule']['quotationformula']['add'];
                $quotationformula_view = (($this->rp['quotationmodule']['quotationformula']['view'] == 1 && $this->rp['quotationmodule']['quotationformula']['edit'] == 1) || $this->userId == 1) ? $request->viewquotationformula : $this->rp['quotationmodule']['quotationformula']['view'];
                $quotationformula_edit = (($this->rp['quotationmodule']['quotationformula']['edit'] == 1) || $this->userId == 1) ? $request->editquotationformula : $this->rp['quotationmodule']['quotationformula']['edit'];
                $quotationformula_delete = (($this->rp['quotationmodule']['quotationformula']['delete'] == 1 && $this->rp['quotationmodule']['quotationformula']['edit'] == 1) || $this->userId == 1) ? $request->deletequotationformula : $this->rp['quotationmodule']['quotationformula']['delete'];
                $quotationformula_alldata = (($this->rp['quotationmodule']['quotationformula']['alldata'] == 1 && $this->rp['quotationmodule']['quotationformula']['edit'] == 1) || $this->userId == 1) ? $request->alldataquotationformula : $this->rp['quotationmodule']['quotationformula']['alldata'];

                $quotationsetting_show = (($this->rp['quotationmodule']['quotationsetting']['show'] == 1 && $this->rp['quotationmodule']['quotationsetting']['edit'] == 1) || $this->userId == 1) ? $request->showquotationsettingmenu : $this->rp['quotationmodule']['quotationsetting']['show'];
                $quotationsetting_add = (($this->rp['quotationmodule']['quotationsetting']['add'] == 1 && $this->rp['quotationmodule']['quotationsetting']['edit'] == 1) || $this->userId == 1) ? $request->addquotationsetting : $this->rp['quotationmodule']['quotationsetting']['add'];
                $quotationsetting_view = (($this->rp['quotationmodule']['quotationsetting']['view'] == 1 && $this->rp['quotationmodule']['quotationsetting']['edit'] == 1) || $this->userId == 1) ? $request->viewquotationsetting : $this->rp['quotationmodule']['quotationsetting']['view'];
                $quotationsetting_edit = (($this->rp['quotationmodule']['quotationsetting']['edit'] == 1) || $this->userId == 1) ? $request->editquotationsetting : $this->rp['quotationmodule']['quotationsetting']['edit'];
                $quotationsetting_delete = (($this->rp['quotationmodule']['quotationsetting']['delete'] == 1 && $this->rp['quotationmodule']['quotationsetting']['edit'] == 1) || $this->userId == 1) ? $request->deletequotationsetting : $this->rp['quotationmodule']['quotationsetting']['delete'];
                $quotationsetting_alldata = (($this->rp['quotationmodule']['quotationsetting']['alldata'] == 1 && $this->rp['quotationmodule']['quotationsetting']['edit'] == 1) || $this->userId == 1) ? $request->alldataquotationsetting : $this->rp['quotationmodule']['quotationsetting']['alldata'];

                $quotationnumbersetting_show = (($this->rp['quotationmodule']['quotationnumbersetting']['show'] == 1 && $this->rp['quotationmodule']['quotationnumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->showquotationnumbersettingmenu : $this->rp['quotationmodule']['quotationnumbersetting']['show'];
                $quotationnumbersetting_add = (($this->rp['quotationmodule']['quotationnumbersetting']['add'] == 1 && $this->rp['quotationmodule']['quotationnumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->addquotationnumbersetting : $this->rp['quotationmodule']['quotationnumbersetting']['add'];
                $quotationnumbersetting_view = (($this->rp['quotationmodule']['quotationnumbersetting']['view'] == 1 && $this->rp['quotationmodule']['quotationnumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->viewquotationnumbersetting : $this->rp['quotationmodule']['quotationnumbersetting']['view'];
                $quotationnumbersetting_edit = (($this->rp['quotationmodule']['quotationnumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->editquotationnumbersetting : $this->rp['quotationmodule']['quotationnumbersetting']['edit'];
                $quotationnumbersetting_delete = (($this->rp['quotationmodule']['quotationnumbersetting']['delete'] == 1 && $this->rp['quotationmodule']['quotationnumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->deletequotationnumbersetting : $this->rp['quotationmodule']['quotationnumbersetting']['delete'];
                $quotationnumbersetting_alldata = (($this->rp['quotationmodule']['quotationnumbersetting']['alldata'] == 1 && $this->rp['quotationmodule']['quotationnumbersetting']['edit'] == 1) || $this->userId == 1) ? $request->alldataquotationnumbersetting : $this->rp['quotationmodule']['quotationnumbersetting']['alldata'];

                $quotationtandcsetting_show = (($this->rp['quotationmodule']['quotationtandcsetting']['show'] == 1 && $this->rp['quotationmodule']['quotationtandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->showquotationtandcsettingmenu : $this->rp['quotationmodule']['quotationtandcsetting']['show'];
                $quotationtandcsetting_add = (($this->rp['quotationmodule']['quotationtandcsetting']['add'] == 1 && $this->rp['quotationmodule']['quotationtandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->addquotationtandcsetting : $this->rp['quotationmodule']['quotationtandcsetting']['add'];
                $quotationtandcsetting_view = (($this->rp['quotationmodule']['quotationtandcsetting']['view'] == 1 && $this->rp['quotationmodule']['quotationtandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->viewquotationtandcsetting : $this->rp['quotationmodule']['quotationtandcsetting']['view'];
                $quotationtandcsetting_edit = (($this->rp['quotationmodule']['quotationtandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->editquotationtandcsetting : $this->rp['quotationmodule']['quotationtandcsetting']['edit'];
                $quotationtandcsetting_delete = (($this->rp['quotationmodule']['quotationtandcsetting']['delete'] == 1 && $this->rp['quotationmodule']['quotationtandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->deletequotationtandcsetting : $this->rp['quotationmodule']['quotationtandcsetting']['delete'];
                $quotationtandcsetting_alldata = (($this->rp['quotationmodule']['quotationtandcsetting']['alldata'] == 1 && $this->rp['quotationmodule']['quotationtandcsetting']['edit'] == 1) || $this->userId == 1) ? $request->alldataquotationtandcsetting : $this->rp['quotationmodule']['quotationtandcsetting']['alldata'];

                $quotationstandardsetting_show = (($this->rp['quotationmodule']['quotationstandardsetting']['show'] == 1 && $this->rp['quotationmodule']['quotationstandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->showquotationstandardsettingmenu : $this->rp['quotationmodule']['quotationstandardsetting']['show'];
                $quotationstandardsetting_add = (($this->rp['quotationmodule']['quotationstandardsetting']['add'] == 1 && $this->rp['quotationmodule']['quotationstandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->addquotationstandardsetting : $this->rp['quotationmodule']['quotationstandardsetting']['add'];
                $quotationstandardsetting_view = (($this->rp['quotationmodule']['quotationstandardsetting']['view'] == 1 && $this->rp['quotationmodule']['quotationstandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->viewquotationstandardsetting : $this->rp['quotationmodule']['quotationstandardsetting']['view'];
                $quotationstandardsetting_edit = (($this->rp['quotationmodule']['quotationstandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->editquotationstandardsetting : $this->rp['quotationmodule']['quotationstandardsetting']['edit'];
                $quotationstandardsetting_delete = (($this->rp['quotationmodule']['quotationstandardsetting']['delete'] == 1 && $this->rp['quotationmodule']['quotationstandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->deletequotationstandardsetting : $this->rp['quotationmodule']['quotationstandardsetting']['delete'];
                $quotationstandardsetting_alldata = (($this->rp['quotationmodule']['quotationstandardsetting']['alldata'] == 1 && $this->rp['quotationmodule']['quotationstandardsetting']['edit'] == 1) || $this->userId == 1) ? $request->alldataquotationstandardsetting : $this->rp['quotationmodule']['quotationstandardsetting']['alldata'];

                $quotationgstsetting_show = (($this->rp['quotationmodule']['quotationgstsetting']['show'] == 1 && $this->rp['quotationmodule']['quotationgstsetting']['edit'] == 1) || $this->userId == 1) ? $request->showquotationgstsettingmenu : $this->rp['quotationmodule']['quotationgstsetting']['show'];
                $quotationgstsetting_add = (($this->rp['quotationmodule']['quotationgstsetting']['add'] == 1 && $this->rp['quotationmodule']['quotationgstsetting']['edit'] == 1) || $this->userId == 1) ? $request->addquotationgstsetting : $this->rp['quotationmodule']['quotationgstsetting']['add'];
                $quotationgstsetting_view = (($this->rp['quotationmodule']['quotationgstsetting']['view'] == 1 && $this->rp['quotationmodule']['quotationgstsetting']['edit'] == 1) || $this->userId == 1) ? $request->viewquotationgstsetting : $this->rp['quotationmodule']['quotationgstsetting']['view'];
                $quotationgstsetting_edit = (($this->rp['quotationmodule']['quotationgstsetting']['edit'] == 1) || $this->userId == 1) ? $request->editquotationgstsetting : $this->rp['quotationmodule']['quotationgstsetting']['edit'];
                $quotationgstsetting_delete = (($this->rp['quotationmodule']['quotationgstsetting']['delete'] == 1 && $this->rp['quotationmodule']['quotationgstsetting']['edit'] == 1) || $this->userId == 1) ? $request->deletequotationgstsetting : $this->rp['quotationmodule']['quotationgstsetting']['delete'];
                $quotationgstsetting_alldata = (($this->rp['quotationmodule']['quotationgstsetting']['alldata'] == 1 && $this->rp['quotationmodule']['quotationgstsetting']['edit'] == 1) || $this->userId == 1) ? $request->alldataquotationgstsetting : $this->rp['quotationmodule']['quotationgstsetting']['alldata'];

                $quotationcustomer_show = (($this->rp['quotationmodule']['quotationcustomer']['show'] == 1 && $this->rp['quotationmodule']['quotationcustomer']['edit'] == 1) || $this->userId == 1) ? $request->showquotationcustomermenu : $this->rp['quotationmodule']['quotationcustomer']['show'];
                $quotationcustomer_add = (($this->rp['quotationmodule']['quotationcustomer']['add'] == 1 && $this->rp['quotationmodule']['quotationcustomer']['edit'] == 1) || $this->userId == 1) ? $request->addquotationcustomer : $this->rp['quotationmodule']['quotationcustomer']['add'];
                $quotationcustomer_view = (($this->rp['quotationmodule']['quotationcustomer']['view'] == 1 && $this->rp['quotationmodule']['quotationcustomer']['edit'] == 1) || $this->userId == 1) ? $request->viewquotationcustomer : $this->rp['quotationmodule']['quotationcustomer']['view'];
                $quotationcustomer_edit = (($this->rp['quotationmodule']['quotationcustomer']['edit'] == 1) || $this->userId == 1) ? $request->editquotationcustomer : $this->rp['quotationmodule']['quotationcustomer']['edit'];
                $quotationcustomer_delete = (($this->rp['quotationmodule']['quotationcustomer']['delete'] == 1 && $this->rp['quotationmodule']['quotationcustomer']['edit'] == 1) || $this->userId == 1) ? $request->deletequotationcustomer : $this->rp['quotationmodule']['quotationcustomer']['delete'];
                $quotationcustomer_alldata = (($this->rp['quotationmodule']['quotationcustomer']['alldata'] == 1 && $this->rp['quotationmodule']['quotationcustomer']['edit'] == 1) || $this->userId == 1) ? $request->alldataquotationcustomer : $this->rp['quotationmodule']['quotationgstsetting']['alldata'];


                $rp = [
                    "invoicemodule" => [
                        "invoice" => ["show" => $invoice_show, "add" => $invoice_add, "view" => $invoice_view, "edit" => $invoice_edit, "delete" => $invoice_delete, "alldata" => $invoice_alldata],
                        "mngcol" => ["show" => $mngcol_show, "add" => $mngcol_add, "view" => $mngcol_view, "edit" => $mngcol_edit, "delete" => $mngcol_delete, "alldata" => $mngcol_alldata],
                        "formula" => ["show" => $formula_show, "add" => $formula_add, "view" => $formula_view, "edit" => $formula_edit, "delete" => $formula_delete, "alldata" => $formula_alldata],
                        "invoicesetting" => ["show" => $invoicesetting_show, "add" => $invoicesetting_add, "view" => $invoicesetting_view, "edit" => $invoicesetting_edit, "delete" => $invoicesetting_delete, "alldata" => $invoicesetting_alldata],
                        "bank" => ["show" => $bank_show, "add" => $bank_add, "view" => $bank_view, "edit" => $bank_edit, "delete" => $bank_delete, "alldata" => $bank_alldata],
                        "customer" => ["show" => $customer_show, "add" => $customer_add, "view" => $customer_view, "edit" => $customer_edit, "delete" => $customer_delete, "alldata" => $customer_alldata],
                        "invoicenumbersetting" => ["show" => $invoicenumbersetting_show, "add" => $invoicenumbersetting_add, "view" => $invoicenumbersetting_view, "edit" => $invoicenumbersetting_edit, "delete" => $invoicenumbersetting_delete, "alldata" => $invoicenumbersetting_alldata],
                        "invoicetandcsetting" => ["show" => $invoicetandcsetting_show, "add" => $invoicetandcsetting_add, "view" => $invoicetandcsetting_view, "edit" => $invoicetandcsetting_edit, "delete" => $invoicetandcsetting_delete, "alldata" => $invoicetandcsetting_alldata],
                        "invoicestandardsetting" => ["show" => $invoicestandardsetting_show, "add" => $invoicestandardsetting_add, "view" => $invoicestandardsetting_view, "edit" => $invoicestandardsetting_edit, "delete" => $invoicestandardsetting_delete, "alldata" => $invoicestandardsetting_alldata],
                        "invoicegstsetting" => ["show" => $invoicegstsetting_show, "add" => $invoicegstsetting_add, "view" => $invoicegstsetting_view, "edit" => $invoicegstsetting_edit, "delete" => $invoicegstsetting_delete, "alldata" => $invoicegstsetting_alldata],
                        "invoicecustomeridsetting" => ["show" => $invoicecustomeridsetting_show, "add" => $invoicecustomeridsetting_add, "view" => $invoicecustomeridsetting_view, "edit" => $invoicecustomeridsetting_edit, "delete" => $invoicecustomeridsetting_delete, "alldata" => $invoicecustomeridsetting_alldata],
                    ],
                    "leadmodule" => [
                        "lead" => ["show" => $lead_show, "add" => $lead_add, "view" => $lead_view, "edit" => $lead_edit, "delete" => $lead_delete, "alldata" => $lead_alldata]
                    ],
                    "customersupportmodule" => [
                        "customersupport" => ["show" => $customersupport_show, "add" => $customersupport_add, "view" => $customersupport_view, "edit" => $customersupport_edit, "delete" => $customersupport_delete, "alldata" => $customersupport_alldata]
                    ],
                    "adminmodule" => [
                        "company" => ["show" => $company_show, "add" => $company_add, "view" => $company_view, "edit" => $company_edit, "delete" => $company_delete, "alldata" => $company_alldata, "max" => $request->maxuser],
                        "user" => ["show" => $user_show, "add" => $user_add, "view" => $user_view, "edit" => $user_edit, "delete" => $user_delete, "alldata" => $user_alldata],
                        "techsupport" => ["show" => $techsupport_show, "add" => $techsupport_add, "view" => $techsupport_view, "edit" => $techsupport_edit, "delete" => $techsupport_delete, "alldata" => $techsupport_alldata],
                        "userpermission" => ["show" => $userpermission_show, "add" => $userpermission_add, "view" => $userpermission_view, "edit" => $userpermission_edit, "delete" => $userpermission_delete, "alldata" => $userpermission_alldata]
                    ],
                    "inventorymodule" => [
                        "product" => ["show" => $product_show, "add" => $product_add, "view" => $product_view, "edit" => $product_edit, "delete" => $product_delete, "alldata" => $product_alldata],
                        "productcategory" => ["show" => $productcategory_show, "add" => $productcategory_add, "view" => $productcategory_view, "edit" => $productcategory_edit, "delete" => $productcategory_delete, "alldata" => $productcategory_alldata],
                        "productcolumnmapping" => ["show" => $productcolumnmapping_show, "add" => $productcolumnmapping_add, "view" => $productcolumnmapping_view, "edit" => $productcolumnmapping_edit, "delete" => $productcolumnmapping_delete, "alldata" => $productcolumnmapping_alldata],
                        "purchase" => ["show" => $purchase_show, "add" => $purchase_add, "view" => $purchase_view, "edit" => $purchase_edit, "delete" => $purchase_delete, "alldata" => $purchase_alldata],
                        "inventory" => ["show" => $inventory_show, "add" => $inventory_add, "view" => $inventory_view, "edit" => $inventory_edit, "delete" => $inventory_delete, "alldata" => $inventory_alldata],
                        "supplier" => ["show" => $supplier_show, "add" => $supplier_add, "view" => $supplier_view, "edit" => $supplier_edit, "delete" => $supplier_delete, "alldata" => $supplier_alldata]
                    ],
                    "accountmodule" => [
                    ],
                    "remindermodule" => [
                        "reminder" => ["show" => $reminder_show, "add" => $reminder_add, "view" => $reminder_view, "edit" => $reminder_edit, "delete" => $reminder_delete, "alldata" => $reminder_alldata],
                        "remindercustomer" => ["show" => $remindercustomer_show, "add" => $remindercustomer_add, "view" => $remindercustomer_view, "edit" => $remindercustomer_edit, "delete" => $remindercustomer_delete, "alldata" => $remindercustomer_alldata]
                    ],
                    "reportmodule" => [
                        "report" => ["show" => $report_show, "add" => $report_add, "view" => $report_view, "edit" => $report_edit, "delete" => $report_delete, "alldata" => $request->assignedto, "log" => $request->logreport]
                    ],
                    "blogmodule" => [
                        "blog" => ["show" => $request->showblogmenu, "add" => $request->addblog, "view" => $request->viewblog, "edit" => $request->editblog, "delete" => $request->deleteblog, "alldata" => $request->alldatablog]
                    ],
                    "quotationmodule" => [
                        "quotation" => ["show" => $quotation_show, "add" => $quotation_add, "view" => $quotation_view, "edit" => $quotation_edit, "delete" => $quotation_delete, "alldata" => $quotation_alldata],
                        "quotationmngcol" => ["show" => $quotationmngcol_show, "add" => $quotationmngcol_add, "view" => $quotationmngcol_view, "edit" => $quotationmngcol_edit, "delete" => $quotationmngcol_delete, "alldata" => $quotationmngcol_alldata],
                        "quotationformula" => ["show" => $quotationformula_show, "add" => $quotationformula_add, "view" => $quotationformula_view, "edit" => $quotationformula_edit, "delete" => $quotationformula_delete, "alldata" => $quotationformula_alldata],
                        "quotationsetting" => ["show" => $quotationsetting_show, "add" => $quotationsetting_add, "view" => $quotationsetting_view, "edit" => $quotationsetting_edit, "delete" => $quotationsetting_delete, "alldata" => $quotationsetting_alldata],
                        "quotationnumbersetting" => ["show" => $quotationnumbersetting_show, "add" => $quotationnumbersetting_add, "view" => $quotationnumbersetting_view, "edit" => $quotationnumbersetting_edit, "delete" => $quotationnumbersetting_delete, "alldata" => $quotationnumbersetting_alldata],
                        "quotationtandcsetting" => ["show" => $quotationtandcsetting_show, "add" => $quotationtandcsetting_add, "view" => $quotationtandcsetting_view, "edit" => $quotationtandcsetting_edit, "delete" => $quotationtandcsetting_delete, "alldata" => $quotationtandcsetting_alldata],
                        "quotationstandardsetting" => ["show" => $quotationstandardsetting_show, "add" => $quotationstandardsetting_add, "view" => $quotationstandardsetting_view, "edit" => $quotationstandardsetting_edit, "delete" => $quotationstandardsetting_delete, "alldata" => $quotationstandardsetting_alldata],
                        "quotationgstsetting" => ["show" => $quotationgstsetting_show, "add" => $quotationgstsetting_add, "view" => $quotationgstsetting_view, "edit" => $quotationgstsetting_edit, "delete" => $quotationgstsetting_delete, "alldata" => $quotationgstsetting_alldata],
                        "quotationcustomer" => ["show" => $quotationcustomer_show, "add" => $quotationcustomer_add, "view" => $quotationcustomer_view, "edit" => $quotationcustomer_edit, "delete" => $quotationcustomer_delete, "alldata" => $quotationcustomer_alldata],
                    ],
                ];

                $rpjson = json_encode($rp);
            }

            $userupdatedata = [];
            if ($request->hasFile('img') && $request->hasFile('img') != '') {
                $image = $request->file('img');
                $imageName = $request->firstname . time() . '.' . $image->getClientOriginalExtension();

                if ($image->move('uploads/', $imageName)) {
                    $imagePath = 'uploads/' . $user->img;
                    if (is_file($imagePath)) {
                        unlink($imagePath);  // old img remove
                    }
                    $userupdatedata['img'] = $imageName;
                }
            }


            $userupdatedata = array_merge($userupdatedata, [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'contact_no' => $request->contact_number,
                'country_id' => $request->country,
                'state_id' => $request->state,
                'city_id' => $request->city,
                'pincode' => $request->pincode,
                'updated_by' => $this->userId,
                'updated_at' => date('Y-m-d')
            ]);

            if ($request->password != '') {
                $userupdatedata['password'] = Hash::make($request->password);
            }
            $user = $user->update($userupdatedata); //update user data
            if ($user) {
                if ($request->editrole == 1) {
                    return $this->successresponse(200, 'message', 'user succesfully updated');
                } else {

                    if ($this->rp['adminmodule']['userpermission']['edit'] == 1) {
                        $searchuserrp = $this->user_permissionModel::where('user_id', $id)->first();
                        if ($searchuserrp) {
                            $rpupdate = $searchuserrp->update([
                                "rp" => $rpjson,
                                'updated_by' => $this->userId
                            ]);

                            if ($rpupdate) {
                                return $this->successresponse(200, 'message', 'user succesfully updated');
                            } else {
                                return $this->successresponse(404, 'message', 'user role & permissions not succesfully updated!');
                            }
                        } else {
                            return $this->successresponse(404, 'message', 'No Such roles & permissions  Found!');
                        }
                    }
                    return $this->successresponse(200, 'message', 'user succesfully updated');
                }
            } else {
                return $this->successresponse(404, 'message', 'user not succesfully updated!');
            }

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $users = User::find($id);
        if (!$users) { 
            return $this->successresponse(404, 'message', 'No Such user Found!');
        }
        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($users->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($this->rp['adminmodule']['user']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $users->update([
            'is_deleted' => 1

        ]);
        return $this->successresponse(200, 'message', 'user succesfully deleted');
    }

    // status update
    public function statusupdate(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) { 
            return $this->successresponse(404, 'message', 'No Such user Found!');
        }
        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($user->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($this->rp['adminmodule']['user']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $user->update([
            'is_active' => $request->status
        ]);
        return $this->successresponse(200, 'message', 'user status succesfully updated');
    }

    public function changepassword(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        }

        $user = User::find($id);

        if (!$user) {
            return $this->successresponse(404, 'message', 'User not found');
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorresponse(422, ["current_password" => ['Current password does not match']]);
        }

        if ($request->new_password !== $request->new_password_confirmation) {
            return $this->errorresponse(422, ["new_password_confirmation" => ['New password and confirm password does not match']]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        return $this->successresponse(200, 'message', 'Password changed successfully');
    }

    // set default page 
    public function setdefaultpage(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) { 
            return $this->successresponse(404, 'message', 'No Such user Record Found!');
        }
        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($user->id != $this->userId && $user->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $user->update([
            'default_module' => $request->default_module,
            'default_page' => $request->default_page,
        ]);
        return $this->successresponse(200, 'message', 'Homepage succesfully updated');
    }

}
