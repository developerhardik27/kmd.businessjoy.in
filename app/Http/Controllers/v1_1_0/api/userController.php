<?php

namespace App\Http\Controllers\v1_1_0\api;


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

    public function username(Request $request)
    {

        $user = DB::table('users')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->select('users.firstname', 'users.lastname', 'users.img', 'company_details.name')->where('users.id', $this->userId)->get();
        if ($user->count() > 0) {
            return $this->successresponse(200, 'user', $user);
        } else {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
    }

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


        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($users[0]->created_by != $this->userId && $users[0]->id != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
       

        if ($users->count() > 0) {
            return $this->successresponse(200, 'user', $users);
        } else {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
    }

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
            ->where($this->db . '.user_permissions.rp', 'LIKE', '%"customersupport":{"show":"1","add":"1"%');

        if ($this->companyId != 1) {
            $users = $usersres->where('users.company_id', $this->companyId);
        }

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            $usersres->where('users.created_by', $this->userId)->orWhere('users.id', $this->userId);
        }

        $users = $usersres->get();


        if ($users->count() > 0) {
            return $this->successresponse(200, 'user', $users);
        } else {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
    }

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
            ->where($this->db . '.user_permissions.rp', 'LIKE', '%"lead":{"show":"1","add":"1"%');

        if ($this->companyId != 1) {
            $users = $usersres->where('users.company_id', $this->companyId);
        }

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            $usersres->where('users.created_by', $this->userId)->orWhere('users.id', $this->userId);
        }

        $users = $usersres->get();

        if ($users->count() > 0) {
            return $this->successresponse(200, 'user', $users);
        } else {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
    }

    public function invoiceuser()
    {
        $usersres = DB::table('users')
            ->select('users.id', 'users.firstname', 'users.lastname')
            ->where('users.is_deleted', 0);

        if ($this->companyId != 1) {
            $users = $usersres->where('users.company_id', $this->companyId)
                ->where($this->db . '.user_permissions.rp', 'LIKE', '%"invoice":{"show":"1","add":"1"%')
                ->join($this->db . '.user_permissions', 'users.id', '=', $this->db . '.user_permissions.user_id');
        }

        $users = $usersres->get();

        if ($users->count() > 0) {
            return $this->successresponse(200, 'user', $users);
        } else {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
    }

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
            ->where($this->db . '.user_permissions.rp', 'LIKE', '%"techsupport":{"show":"1","add":"1","view":"1","edit":"1"%');

        if ($this->companyId != 1) {
            $users = $usersres->where('users.company_id', $this->companyId);
        }

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            $usersres->where('users.created_by', $this->userId)->orWhere('users.id', $this->userId);
        }

        $users = $usersres->get();

        if ($users->count() > 0) {
            return $this->successresponse(200, 'user', $users);
        } else {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
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
            ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.password', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name as company_name', 'user_role.role as user_role', 'users.img', 'users.created_by', 'users.updated_by', 'users.is_active')
            ->where('users.is_deleted', 0);

        if ($this->companyId != 1) {
            $users = $usersres->where('users.company_id', $this->companyId);
        }

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            $usersres->where('users.created_by', $this->userId)->orWhere('users.id', $this->userId);
        }

        $users = $usersres->get();


        if ($this->rp['adminmodule']['user']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($users->count() > 0) {
            return $this->successresponse(200, 'user', $users);
        } else {
            return $this->successresponse(404, 'user', 'No Records Found');
        }
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
        $user = User::where('company_id', '=', $company->id)->where('is_deleted',0)->get();

        $companymaxuser = $company->max_users;


        if ($user->count() >= $companymaxuser) {
            return $this->successresponse(500, 'message', 'You are reached your limits to create user');
        }





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

            $checkuseremail = User::where('email', $request->email)->where('is_deleted', 0)->get();

            if (count($checkuseremail) > 0) {
                return $this->successresponse(500, 'message', 'This email id already exists , Please enter other email id');
            }

            if ($this->rp['adminmodule']['user']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            if ($this->rp['adminmodule']['userpermission']['add'] == 1) {

                //   check user permissions

                $invoice_show = ($this->rp['invoicemodule']['invoice']['show'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->showinvoicemenu : "0";
                $invoice_add = ($this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->addinvoice : "0";
                $invoice_view = ($this->rp['invoicemodule']['invoice']['view'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->viewinvoice : "0";
                $invoice_edit = ($this->rp['invoicemodule']['invoice']['edit'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->editinvoice : "0";
                $invoice_delete = ($this->rp['invoicemodule']['invoice']['delete'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->deleteinvoice : "0";
                $invoice_alldata = ($this->rp['invoicemodule']['invoice']['alldata'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->alldatainvoice : "0";

                $mngcol_show = ($this->rp['invoicemodule']['mngcol']['show'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->showmngcolmenu : "0";
                $mngcol_add = ($this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->addmngcol : "0";
                $mngcol_view = ($this->rp['invoicemodule']['mngcol']['view'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->viewmngcol : "0";
                $mngcol_edit = ($this->rp['invoicemodule']['mngcol']['edit'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->editmngcol : "0";
                $mngcol_delete = ($this->rp['invoicemodule']['mngcol']['delete'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->deletemngcol : "0";
                $mngcol_alldata = ($this->rp['invoicemodule']['mngcol']['alldata'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->alldatamngcol : "0";

                $formula_show = ($this->rp['invoicemodule']['formula']['show'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) ? $request->showformulamenu : "0";
                $formula_add = ($this->rp['invoicemodule']['formula']['add'] == 1) ? $request->addformula : "0";
                $formula_view = ($this->rp['invoicemodule']['formula']['view'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) ? $request->viewformula : "0";
                $formula_edit = ($this->rp['invoicemodule']['formula']['edit'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) ? $request->editformula : "0";
                $formula_delete = ($this->rp['invoicemodule']['formula']['delete'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) ? $request->deleteformula : "0";
                $formula_alldata = ($this->rp['invoicemodule']['formula']['alldata'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) ? $request->alldataformula : "0";

                $invoicesetting_show = ($this->rp['invoicemodule']['invoicesetting']['show'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->showinvoicesettingmenu : "0";
                $invoicesetting_add = ($this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->addinvoicesetting : "0";
                $invoicesetting_view = ($this->rp['invoicemodule']['invoicesetting']['view'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->viewinvoicesetting : "0";
                $invoicesetting_edit = ($this->rp['invoicemodule']['invoicesetting']['edit'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->editinvoicesetting : "0";
                $invoicesetting_delete = ($this->rp['invoicemodule']['invoicesetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->deleteinvoicesetting : "0";
                $invoicesetting_alldata = ($this->rp['invoicemodule']['invoicesetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->alldatainvoicesetting : "0";

                $bank_show = ($this->rp['invoicemodule']['bank']['show'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) ? $request->showbankmenu : "0";
                $bank_add = ($this->rp['invoicemodule']['bank']['add'] == 1) ? $request->addbank : "0";
                $bank_view = ($this->rp['invoicemodule']['bank']['view'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) ? $request->viewbank : "0";
                $bank_edit = ($this->rp['invoicemodule']['bank']['edit'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) ? $request->editbank : "0";
                $bank_delete = ($this->rp['invoicemodule']['bank']['delete'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) ? $request->deletebank : "0";
                $bank_alldata = ($this->rp['invoicemodule']['bank']['alldata'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) ? $request->alldatabank : "0";

                $customer_show = ($this->rp['invoicemodule']['customer']['show'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) ? $request->showcustomermenu : "0";
                $customer_add = ($this->rp['invoicemodule']['customer']['add'] == 1) ? $request->addcustomer : "0";
                $customer_view = ($this->rp['invoicemodule']['customer']['view'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) ? $request->viewcustomer : "0";
                $customer_edit = ($this->rp['invoicemodule']['customer']['edit'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) ? $request->editcustomer : "0";
                $customer_delete = ($this->rp['invoicemodule']['customer']['delete'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) ? $request->deletecustomer : "0";
                $customer_alldata = ($this->rp['invoicemodule']['customer']['alldata'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) ? $request->alldatacustomer : "0";

                $lead_show = ($this->rp['leadmodule']['lead']['show'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) ? $request->showleadmenu : "0";
                $lead_add = ($this->rp['leadmodule']['lead']['add'] == 1) ? $request->addlead : "0";
                $lead_view = ($this->rp['leadmodule']['lead']['view'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) ? $request->viewlead : "0";
                $lead_edit = ($this->rp['leadmodule']['lead']['edit'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) ? $request->editlead : "0";
                $lead_delete = ($this->rp['leadmodule']['lead']['delete'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) ? $request->deletelead : "0";
                $lead_alldata = ($this->rp['leadmodule']['lead']['alldata'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) ? $request->alldatalead : "0";

                $customersupport_show = ($this->rp['customersupportmodule']['customersupport']['show'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->showcustomersupportmenu : "0";
                $customersupport_add = ($this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->addcustomersupport : "0";
                $customersupport_view = ($this->rp['customersupportmodule']['customersupport']['view'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->viewcustomersupport : "0";
                $customersupport_edit = ($this->rp['customersupportmodule']['customersupport']['edit'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->editcustomersupport : "0";
                $customersupport_delete = ($this->rp['customersupportmodule']['customersupport']['delete'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->deletecustomersupport : "0";
                $customersupport_alldata = ($this->rp['customersupportmodule']['customersupport']['alldata'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->alldatacustomersupport : "0";

                $product_show = ($this->rp['inventorymodule']['product']['show'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) ? $request->showproductmenu : "0";
                $product_add = ($this->rp['inventorymodule']['product']['add'] == 1) ? $request->addproduct : "0";
                $product_view = ($this->rp['inventorymodule']['product']['view'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) ? $request->viewproduct : "0";
                $product_edit = ($this->rp['inventorymodule']['product']['edit'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) ? $request->editproduct : "0";
                $product_delete = ($this->rp['inventorymodule']['product']['delete'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) ? $request->deleteproduct : "0";
                $product_alldata = ($this->rp['inventorymodule']['product']['alldata'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) ? $request->alldataproduct : "0";

                $purchase_show = ($this->rp['accountmodule']['purchase']['show'] == 1 && $this->rp['accountmodule']['purchase']['add'] == 1) ? $request->showpurchasemenu : "0";
                $purchase_add = ($this->rp['accountmodule']['purchase']['add'] == 1) ? $request->addpurchase : "0";
                $purchase_view = ($this->rp['accountmodule']['purchase']['view'] == 1 && $this->rp['accountmodule']['purchase']['add'] == 1) ? $request->viewpurchase : "0";
                $purchase_edit = ($this->rp['accountmodule']['purchase']['edit'] == 1 && $this->rp['accountmodule']['purchase']['add'] == 1) ? $request->editpurchase : "0";
                $purchase_delete = ($this->rp['accountmodule']['purchase']['delete'] == 1 && $this->rp['accountmodule']['purchase']['add'] == 1) ? $request->deletepurchase : "0";
                $purchase_alldata = ($this->rp['accountmodule']['purchase']['alldata'] == 1 && $this->rp['accountmodule']['purchase']['add'] == 1) ? $request->alldatapurchase : "0";

                $reminder_show = ($this->rp['remindermodule']['reminder']['show'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) ? $request->showremindermenu : "0";
                $reminder_add = ($this->rp['remindermodule']['reminder']['add'] == 1) ? $request->addreminder : "0";
                $reminder_view = ($this->rp['remindermodule']['reminder']['view'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) ? $request->viewreminder : "0";
                $reminder_edit = ($this->rp['remindermodule']['reminder']['edit'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) ? $request->editreminder : "0";
                $reminder_delete = ($this->rp['remindermodule']['reminder']['delete'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) ? $request->deletereminder : "0";
                $reminder_alldata = ($this->rp['remindermodule']['reminder']['alldata'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) ? $request->alldatareminder : "0";

                $remindercustomer_show = ($this->rp['remindermodule']['remindercustomer']['show'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->showremindercustomermenu : "0";
                $remindercustomer_add = ($this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->addremindercustomer : "0";
                $remindercustomer_view = ($this->rp['remindermodule']['remindercustomer']['view'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->viewremindercustomer : "0";
                $remindercustomer_edit = ($this->rp['remindermodule']['remindercustomer']['edit'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->editremindercustomer : "0";
                $remindercustomer_delete = ($this->rp['remindermodule']['remindercustomer']['delete'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->deleteremindercustomer : "0";
                $remindercustomer_alldata = ($this->rp['remindermodule']['remindercustomer']['alldata'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->alldataremindercustomer : "0";

                $company_show = ($this->rp['adminmodule']['company']['show'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->showcompanymenu : "0";
                $company_add = ($this->rp['adminmodule']['company']['add'] == 1) ? $request->addcompany : "0";
                $company_view = ($this->rp['adminmodule']['company']['view'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->viewcompany : "0";
                $company_edit = ($this->rp['adminmodule']['company']['edit'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->editcompany : "0";
                $company_delete = ($this->rp['adminmodule']['company']['delete'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->deletecompany : "0";
                $company_alldata = ($this->rp['adminmodule']['company']['alldata'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->alldatacompany : "0";
                $company_maxuser = ($this->rp['adminmodule']['company']['max'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->maxuser : "0";

                $user_show = ($this->rp['adminmodule']['user']['show'] == 1 && $this->rp['adminmodule']['user']['add'] == 1) ? $request->showusermenu : "0";
                $user_add = ($this->rp['adminmodule']['user']['add'] == 1) ? $request->adduser : "0";
                $user_view = ($this->rp['adminmodule']['user']['view'] == 1 && $this->rp['adminmodule']['user']['add'] == 1) ? $request->viewuser : "0";
                $user_edit = ($this->rp['adminmodule']['user']['edit'] == 1 && $this->rp['adminmodule']['user']['add'] == 1) ? $request->edituser : "0";
                $user_delete = ($this->rp['adminmodule']['user']['delete'] && $this->rp['adminmodule']['user']['add'] == 1) == 1 ? $request->deleteuser : "0";
                $user_alldata = ($this->rp['adminmodule']['user']['alldata'] && $this->rp['adminmodule']['user']['add'] == 1) == 1 ? $request->alldatauser : "0";

                $userpermission_show = ($this->rp['adminmodule']['userpermission']['show'] == 1 && $this->rp['adminmodule']['userpermission']['add'] == 1) ? $request->showuserpermissionmenu : "0";
                $userpermission_add = ($this->rp['adminmodule']['userpermission']['add'] == 1) ? $request->adduserpermission : "0";
                $userpermission_view = ($this->rp['adminmodule']['userpermission']['view'] == 1 && $this->rp['adminmodule']['userpermission']['add'] == 1) ? $request->viewuserpermission : "0";
                $userpermission_edit = ($this->rp['adminmodule']['userpermission']['edit'] == 1 && $this->rp['adminmodule']['userpermission']['add'] == 1) ? $request->edituserpermission : "0";
                $userpermission_delete = ($this->rp['adminmodule']['userpermission']['delete'] && $this->rp['adminmodule']['userpermission']['add'] == 1) == 1 ? $request->deleteuserpermission : "0";
                $userpermission_alldata = ($this->rp['adminmodule']['userpermission']['alldata'] && $this->rp['adminmodule']['userpermission']['add'] == 1) == 1 ? $request->alldatauserpermission : "0";

                $techsupport_show = ($this->rp['adminmodule']['techsupport']['show'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->showtechsupportmenu : "0";
                $techsupport_add = ($this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->addtechsupport : "0";
                $techsupport_view = ($this->rp['adminmodule']['techsupport']['view'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->viewtechsupport : "0";
                $techsupport_edit = ($this->rp['adminmodule']['techsupport']['edit'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->edittechsupport : "0";
                $techsupport_delete = ($this->rp['adminmodule']['techsupport']['delete'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->deletetechsupport : "0";
                $techsupport_alldata = ($this->rp['adminmodule']['techsupport']['alldata'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->alldatatechsupport : "0";

                $report_show = ($this->rp['reportmodule']['report']['show'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->showreportmenu : "0";
                $report_add = ($this->rp['reportmodule']['report']['add'] == 1) ? $request->addreport : "0";
                $report_view = ($this->rp['reportmodule']['report']['view'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->viewreport : "0";
                $report_edit = ($this->rp['reportmodule']['report']['edit'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->editreport : "0";
                $report_delete = ($this->rp['reportmodule']['report']['delete'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->deletereport : "0";
                $report_alldata = ($this->rp['reportmodule']['report']['add'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->assignedto : "0";
                $report_log = ($this->rp['reportmodule']['report']['log'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->logreport : "0";

                $blog_show = ($this->rp['blogmodule']['blog']['show'] == 1 && $this->rp['blogmodule']['blog']['add'] == 1) ? $request->showblogmenu : "0";
                $blog_add = ($this->rp['blogmodule']['blog']['add'] == 1) ? $request->addblog : "0";
                $blog_view = ($this->rp['blogmodule']['blog']['view'] == 1 && $this->rp['blogmodule']['blog']['add'] == 1) ? $request->viewblog : "0";
                $blog_edit = ($this->rp['blogmodule']['blog']['edit'] == 1 && $this->rp['blogmodule']['blog']['add'] == 1) ? $request->editblog : "0";
                $blog_delete = ($this->rp['blogmodule']['blog']['delete'] == 1 && $this->rp['blogmodule']['blog']['add'] == 1) ? $request->deleteblog : "0";
                $blog_alldata = ($this->rp['blogmodule']['blog']['add'] == 1 && $this->rp['blogmodule']['blog']['add'] == 1) ? $request->alldatablog : "0";




                $rp = [
                    "invoicemodule" => [
                        "invoice" => ["show" => $invoice_show, "add" => $invoice_add, "view" => $invoice_view, "edit" => $invoice_edit, "delete" => $invoice_delete, "alldata" => $invoice_alldata],
                        "mngcol" => ["show" => $mngcol_show, "add" => $mngcol_add, "view" => $mngcol_view, "edit" => $mngcol_edit, "delete" => $mngcol_delete, "alldata" => $mngcol_alldata],
                        "formula" => ["show" => $formula_show, "add" => $formula_add, "view" => $formula_view, "edit" => $formula_edit, "delete" => $formula_delete, "alldata" => $formula_alldata],
                        "invoicesetting" => ["show" => $invoicesetting_show, "add" => $invoicesetting_add, "view" => $invoicesetting_view, "edit" => $invoicesetting_edit, "delete" => $invoicesetting_delete, "alldata" => $invoicesetting_alldata],
                        "bank" => ["show" => $bank_show, "add" => $bank_add, "view" => $bank_view, "edit" => $bank_edit, "delete" => $bank_delete, "alldata" => $bank_alldata],
                        "customer" => ["show" => $customer_show, "add" => $customer_add, "view" => $customer_view, "edit" => $customer_edit, "delete" => $customer_delete, "alldata" => $customer_alldata],
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
                        "product" => ["show" => $product_show, "add" => $product_add, "view" => $product_view, "edit" => $product_edit, "delete" => $product_delete, "alldata" => $product_alldata]
                    ],
                    "accountmodule" => [
                        "purchase" => ["show" => $purchase_show, "add" => $purchase_add, "view" => $purchase_view, "edit" => $purchase_edit, "delete" => $purchase_delete, "alldata" => $purchase_alldata]
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
                    ]
                ];
                $rpjson = json_encode($rp);
            }

            $passwordtoken = str::random(40);
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

            $users = User::insertgetId($user);

            if ($users) {

                if ($this->rp['adminmodule']['userpermission']['add'] == 1) {
                    $userrp = $this->user_permissionModel::create([
                        'user_id' => $users,
                        'rp' => $rpjson,
                        'created_by' => $this->userId
                    ]);
                }

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
        $dbname = company::find($user->company_id);

        $users = DB::table('users')
            ->join($dbname->dbname . '.user_permissions', 'users.id', '=', $dbname->dbname . '.user_permissions.user_id')
            ->select('users.*', 'user_permissions.rp')
            ->where('users.id', $id)->get();


        if (($this->rp['adminmodule']['user']['alldata'] != 1) || ($users[0]->company_id != $this->companyId)) {
            if ($users[0]->created_by != $this->userId && $users[0]->id != $this->userId && $this->userId != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        if ($users) {
            return $this->successresponse(200, 'user', $users);
        } else {
            return $this->successresponse(404, 'message', "No Such user Found!");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $users = User::find($id);
        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($users->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($this->rp['adminmodule']['user']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        if ($users) {
            return $this->successresponse(200, 'user', $users);
        } else {
            return $this->successresponse(404, 'message', "No Such user Found!");
        }
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

            $checkuseremail = User::where('email', $request->email)->where('is_deleted', 0)->get();

            if (count($checkuseremail) > 0 && $checkuseremail[0]->id != $id) {
                return $this->successresponse(500, 'message', 'This email id already exists , Please enter other email id');
            }

            $user = User::find($id);

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
                $invoice_show = ($this->rp['invoicemodule']['invoice']['show'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->showinvoicemenu : $this->rp['invoicemodule']['invoice']['show'];
                $invoice_add = ($this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->addinvoice : $this->rp['invoicemodule']['invoice']['add'];
                $invoice_view = ($this->rp['invoicemodule']['invoice']['view'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->viewinvoice : $this->rp['invoicemodule']['invoice']['view'];
                $invoice_edit = ($this->rp['invoicemodule']['invoice']['edit'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->editinvoice : $this->rp['invoicemodule']['invoice']['edit'];
                $invoice_delete = ($this->rp['invoicemodule']['invoice']['delete'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->deleteinvoice : $this->rp['invoicemodule']['invoice']['delete'];
                $invoice_alldata = ($this->rp['invoicemodule']['invoice']['alldata'] == 1 && $this->rp['invoicemodule']['invoice']['add'] == 1) ? $request->alldatainvoice : $this->rp['invoicemodule']['invoice']['alldata'];

                $mngcol_show = ($this->rp['invoicemodule']['mngcol']['show'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->showmngcolmenu : $this->rp['invoicemodule']['mngcol']['show'];
                $mngcol_add = ($this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->addmngcol : $this->rp['invoicemodule']['mngcol']['add'];
                $mngcol_view = ($this->rp['invoicemodule']['mngcol']['view'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->viewmngcol : $this->rp['invoicemodule']['mngcol']['view'];
                $mngcol_edit = ($this->rp['invoicemodule']['mngcol']['edit'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->editmngcol : $this->rp['invoicemodule']['mngcol']['edit'];
                $mngcol_delete = ($this->rp['invoicemodule']['mngcol']['delete'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->deletemngcol : $this->rp['invoicemodule']['mngcol']['delete'];
                $mngcol_alldata = ($this->rp['invoicemodule']['mngcol']['alldata'] == 1 && $this->rp['invoicemodule']['mngcol']['add'] == 1) ? $request->alldatamngcol : $this->rp['invoicemodule']['mngcol']['alldata'];

                $formula_show = ($this->rp['invoicemodule']['formula']['show'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) ? $request->showformulamenu : $this->rp['invoicemodule']['formula']['show'];
                $formula_add = ($this->rp['invoicemodule']['formula']['add'] == 1) ? $request->addformula : $this->rp['invoicemodule']['formula']['add'];
                $formula_view = ($this->rp['invoicemodule']['formula']['view'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) ? $request->viewformula : $this->rp['invoicemodule']['formula']['view'];
                $formula_edit = ($this->rp['invoicemodule']['formula']['edit'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) ? $request->editformula : $this->rp['invoicemodule']['formula']['edit'];
                $formula_delete = ($this->rp['invoicemodule']['formula']['delete'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) ? $request->deleteformula : $this->rp['invoicemodule']['formula']['delete'];
                $formula_alldata = ($this->rp['invoicemodule']['formula']['alldata'] == 1 && $this->rp['invoicemodule']['formula']['add'] == 1) ? $request->alldataformula : $this->rp['invoicemodule']['formula']['alldata'];

                $invoicesetting_show = ($this->rp['invoicemodule']['invoicesetting']['show'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->showinvoicesettingmenu : $this->rp['invoicemodule']['invoicesetting']['show'];
                $invoicesetting_add = ($this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->addinvoicesetting : $this->rp['invoicemodule']['invoicesetting']['add'];
                $invoicesetting_view = ($this->rp['invoicemodule']['invoicesetting']['view'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->viewinvoicesetting : $this->rp['invoicemodule']['invoicesetting']['view'];
                $invoicesetting_edit = ($this->rp['invoicemodule']['invoicesetting']['edit'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->editinvoicesetting : $this->rp['invoicemodule']['invoicesetting']['edit'];
                $invoicesetting_delete = ($this->rp['invoicemodule']['invoicesetting']['delete'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->deleteinvoicesetting : $this->rp['invoicemodule']['invoicesetting']['delete'];
                $invoicesetting_alldata = ($this->rp['invoicemodule']['invoicesetting']['alldata'] == 1 && $this->rp['invoicemodule']['invoicesetting']['add'] == 1) ? $request->alldatainvoicesetting : $this->rp['invoicemodule']['invoicesetting']['alldata'];

                $bank_show = ($this->rp['invoicemodule']['bank']['show'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) ? $request->showbankmenu : $this->rp['invoicemodule']['bank']['show'];
                $bank_add = ($this->rp['invoicemodule']['bank']['add'] == 1) ? $request->addbank : $this->rp['invoicemodule']['bank']['add'];
                $bank_view = ($this->rp['invoicemodule']['bank']['view'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) ? $request->viewbank : $this->rp['invoicemodule']['bank']['view'];
                $bank_edit = ($this->rp['invoicemodule']['bank']['edit'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) ? $request->editbank : $this->rp['invoicemodule']['bank']['edit'];
                $bank_delete = ($this->rp['invoicemodule']['bank']['delete'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) ? $request->deletebank : $this->rp['invoicemodule']['bank']['delete'];
                $bank_alldata = ($this->rp['invoicemodule']['bank']['alldata'] == 1 && $this->rp['invoicemodule']['bank']['add'] == 1) ? $request->alldatabank : $this->rp['invoicemodule']['bank']['alldata'];

                $customer_show = ($this->rp['invoicemodule']['customer']['show'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) ? $request->showcustomermenu : $this->rp['invoicemodule']['customer']['show'];
                $customer_add = ($this->rp['invoicemodule']['customer']['add'] == 1) ? $request->addcustomer : $this->rp['invoicemodule']['customer']['add'];
                $customer_view = ($this->rp['invoicemodule']['customer']['view'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) ? $request->viewcustomer : $this->rp['invoicemodule']['customer']['view'];
                $customer_edit = ($this->rp['invoicemodule']['customer']['edit'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) ? $request->editcustomer : $this->rp['invoicemodule']['customer']['edit'];
                $customer_delete = ($this->rp['invoicemodule']['customer']['delete'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) ? $request->deletecustomer : $this->rp['invoicemodule']['customer']['delete'];
                $customer_alldata = ($this->rp['invoicemodule']['customer']['alldata'] == 1 && $this->rp['invoicemodule']['customer']['add'] == 1) ? $request->alldatacustomer : $this->rp['invoicemodule']['customer']['alldata'];

                $lead_show = ($this->rp['leadmodule']['lead']['show'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) ? $request->showleadmenu : $this->rp['leadmodule']['lead']['show'];
                $lead_add = ($this->rp['leadmodule']['lead']['add'] == 1) ? $request->addlead : $this->rp['leadmodule']['lead']['add'];
                $lead_view = ($this->rp['leadmodule']['lead']['view'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) ? $request->viewlead : $this->rp['leadmodule']['lead']['view'];
                $lead_edit = ($this->rp['leadmodule']['lead']['edit'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) ? $request->editlead : $this->rp['leadmodule']['lead']['edit'];
                $lead_delete = ($this->rp['leadmodule']['lead']['delete'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) ? $request->deletelead : $this->rp['leadmodule']['lead']['delete'];
                $lead_alldata = ($this->rp['leadmodule']['lead']['alldata'] == 1 && $this->rp['leadmodule']['lead']['add'] == 1) ? $request->alldatalead : $this->rp['leadmodule']['lead']['alldata'];

                $customersupport_show = ($this->rp['customersupportmodule']['customersupport']['show'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->showcustomersupportmenu : $this->rp['customersupportmodule']['customersupport']['show'];
                $customersupport_add = ($this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->addcustomersupport : $this->rp['customersupportmodule']['customersupport']['add'];
                $customersupport_view = ($this->rp['customersupportmodule']['customersupport']['view'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->viewcustomersupport : $this->rp['customersupportmodule']['customersupport']['view'];
                $customersupport_edit = ($this->rp['customersupportmodule']['customersupport']['edit'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->editcustomersupport : $this->rp['customersupportmodule']['customersupport']['edit'];
                $customersupport_delete = ($this->rp['customersupportmodule']['customersupport']['delete'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->deletecustomersupport : $this->rp['customersupportmodule']['customersupport']['delete'];
                $customersupport_alldata = ($this->rp['customersupportmodule']['customersupport']['alldata'] == 1 && $this->rp['customersupportmodule']['customersupport']['add'] == 1) ? $request->alldatacustomersupport : $this->rp['customersupportmodule']['customersupport']['alldata'];

                $product_show = ($this->rp['inventorymodule']['product']['show'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) ? $request->showproductmenu : $this->rp['inventorymodule']['product']['show'];
                $product_add = ($this->rp['inventorymodule']['product']['add'] == 1) ? $request->addproduct : $this->rp['inventorymodule']['product']['add'];
                $product_view = ($this->rp['inventorymodule']['product']['view'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) ? $request->viewproduct : $this->rp['inventorymodule']['product']['view'];
                $product_edit = ($this->rp['inventorymodule']['product']['edit'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) ? $request->editproduct : $this->rp['inventorymodule']['product']['edit'];
                $product_delete = ($this->rp['inventorymodule']['product']['delete'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) ? $request->deleteproduct : $this->rp['inventorymodule']['product']['delete'];
                $product_alldata = ($this->rp['inventorymodule']['product']['alldata'] == 1 && $this->rp['inventorymodule']['product']['add'] == 1) ? $request->alldataproduct : $this->rp['inventorymodule']['product']['alldata'];

                $purchase_show = ($this->rp['accountmodule']['purchase']['show'] == 1 && $this->rp['accountmodule']['purchase']['add'] == 1) ? $request->showpurchasemenu : $this->rp['accountmodule']['purchase']['show'];
                $purchase_add = ($this->rp['accountmodule']['purchase']['add'] == 1) ? $request->addpurchase : $this->rp['accountmodule']['purchase']['add'];
                $purchase_view = ($this->rp['accountmodule']['purchase']['view'] == 1 && $this->rp['accountmodule']['purchase']['add'] == 1) ? $request->viewpurchase : $this->rp['accountmodule']['purchase']['view'];
                $purchase_edit = ($this->rp['accountmodule']['purchase']['edit'] == 1 && $this->rp['accountmodule']['purchase']['add'] == 1) ? $request->editpurchase : $this->rp['accountmodule']['purchase']['edit'];
                $purchase_delete = ($this->rp['accountmodule']['purchase']['delete'] == 1 && $this->rp['accountmodule']['purchase']['add'] == 1) ? $request->deletepurchase : $this->rp['accountmodule']['purchase']['delete'];
                $purchase_alldata = ($this->rp['accountmodule']['purchase']['alldata'] == 1 && $this->rp['accountmodule']['purchase']['add'] == 1) ? $request->alldatapurchase : $this->rp['accountmodule']['purchase']['alldata'];

                $reminder_show = ($this->rp['remindermodule']['reminder']['show'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) ? $request->showremindermenu : $this->rp['remindermodule']['reminder']['show'];
                $reminder_add = ($this->rp['remindermodule']['reminder']['add'] == 1) ? $request->addreminder : $this->rp['remindermodule']['reminder']['add'];
                $reminder_view = ($this->rp['remindermodule']['reminder']['view'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) ? $request->viewreminder : $this->rp['remindermodule']['reminder']['view'];
                $reminder_edit = ($this->rp['remindermodule']['reminder']['edit'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) ? $request->editreminder : $this->rp['remindermodule']['reminder']['edit'];
                $reminder_delete = ($this->rp['remindermodule']['reminder']['delete'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) ? $request->deletereminder : $this->rp['remindermodule']['reminder']['delete'];
                $reminder_alldata = ($this->rp['remindermodule']['reminder']['alldata'] == 1 && $this->rp['remindermodule']['reminder']['add'] == 1) ? $request->alldatareminder : $this->rp['remindermodule']['reminder']['alldata'];

                $remindercustomer_show = ($this->rp['remindermodule']['remindercustomer']['show'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->showremindercustomermenu : "0";
                $remindercustomer_add = ($this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->addremindercustomer : "0";
                $remindercustomer_view = ($this->rp['remindermodule']['remindercustomer']['view'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->viewremindercustomer : "0";
                $remindercustomer_edit = ($this->rp['remindermodule']['remindercustomer']['edit'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->editremindercustomer : "0";
                $remindercustomer_delete = ($this->rp['remindermodule']['remindercustomer']['delete'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->deleteremindercustomer : "0";
                $remindercustomer_alldata = ($this->rp['remindermodule']['remindercustomer']['alldata'] == 1 && $this->rp['remindermodule']['remindercustomer']['add'] == 1) ? $request->alldataremindercustomer : "0";

                $company_show = ($this->rp['adminmodule']['company']['show'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->showcompanymenu : "0";
                $company_add = ($this->rp['adminmodule']['company']['add'] == 1) ? $request->addcompany : "0";
                $company_view = ($this->rp['adminmodule']['company']['view'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->viewcompany : "0";
                $company_edit = ($this->rp['adminmodule']['company']['edit'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->editcompany : "0";
                $company_delete = ($this->rp['adminmodule']['company']['delete'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->deletecompany : "0";
                $company_alldata = ($this->rp['adminmodule']['company']['alldata'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->alldatacompany : "0";
                $company_maxuser = ($this->rp['adminmodule']['company']['max'] == 1 && $this->rp['adminmodule']['company']['add'] == 1) ? $request->maxuser : "0";

                $user_show = ($this->rp['adminmodule']['user']['show'] == 1 && $this->rp['adminmodule']['user']['add'] == 1) ? $request->showusermenu : "0";
                $user_add = ($this->rp['adminmodule']['user']['add'] == 1) ? $request->adduser : "0";
                $user_view = ($this->rp['adminmodule']['user']['view'] == 1 && $this->rp['adminmodule']['user']['add'] == 1) ? $request->viewuser : "0";
                $user_edit = ($this->rp['adminmodule']['user']['edit'] == 1 && $this->rp['adminmodule']['user']['add'] == 1) ? $request->edituser : "0";
                $user_delete = ($this->rp['adminmodule']['user']['delete'] && $this->rp['adminmodule']['user']['add'] == 1) == 1 ? $request->deleteuser : "0";
                $user_alldata = ($this->rp['adminmodule']['user']['alldata'] && $this->rp['adminmodule']['user']['add'] == 1) == 1 ? $request->alldatauser : "0";

                $userpermission_show = ($this->rp['adminmodule']['userpermission']['show'] == 1 && $this->rp['adminmodule']['userpermission']['add'] == 1) ? $request->showuserpermissionmenu : "0";
                $userpermission_add = ($this->rp['adminmodule']['userpermission']['add'] == 1) ? $request->adduserpermission : "0";
                $userpermission_view = ($this->rp['adminmodule']['userpermission']['view'] == 1 && $this->rp['adminmodule']['userpermission']['add'] == 1) ? $request->viewuserpermission : "0";
                $userpermission_edit = ($this->rp['adminmodule']['userpermission']['edit'] == 1 && $this->rp['adminmodule']['userpermission']['add'] == 1) ? $request->edituserpermission : "0";
                $userpermission_delete = ($this->rp['adminmodule']['userpermission']['delete'] && $this->rp['adminmodule']['userpermission']['add'] == 1) == 1 ? $request->deleteuserpermission : "0";
                $userpermission_alldata = ($this->rp['adminmodule']['userpermission']['alldata'] && $this->rp['adminmodule']['userpermission']['add'] == 1) == 1 ? $request->alldatauserpermission : "0";

                $techsupport_show = ($this->rp['adminmodule']['techsupport']['show'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->showtechsupportmenu : "0";
                $techsupport_add = ($this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->addtechsupport : "0";
                $techsupport_view = ($this->rp['adminmodule']['techsupport']['view'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->viewtechsupport : "0";
                $techsupport_edit = ($this->rp['adminmodule']['techsupport']['edit'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->edittechsupport : "0";
                $techsupport_delete = ($this->rp['adminmodule']['techsupport']['delete'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->deletetechsupport : "0";
                $techsupport_alldata = ($this->rp['adminmodule']['techsupport']['alldata'] == 1 && $this->rp['adminmodule']['techsupport']['add'] == 1) ? $request->alldatatechsupport : "0";

                $report_show = ($this->rp['reportmodule']['report']['show'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->showreportmenu : "0";
                $report_add = ($this->rp['reportmodule']['report']['add'] == 1) ? $request->addreport : "0";
                $report_view = ($this->rp['reportmodule']['report']['view'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->viewreport : "0";
                $report_edit = ($this->rp['reportmodule']['report']['edit'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->editreport : "0";
                $report_delete = ($this->rp['reportmodule']['report']['delete'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->deletereport : "0";
                $report_alldata = ($this->rp['reportmodule']['report']['add'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->assignedto : "0";
                $report_log = ($this->rp['reportmodule']['report']['log'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) ? $request->logreport : "0";

                $rp = [
                    "invoicemodule" => [
                        "invoice" => ["show" => $invoice_show, "add" => $invoice_add, "view" => $invoice_view, "edit" => $invoice_edit, "delete" => $invoice_delete, "alldata" => $invoice_alldata],
                        "mngcol" => ["show" => $mngcol_show, "add" => $mngcol_add, "view" => $mngcol_view, "edit" => $mngcol_edit, "delete" => $mngcol_delete, "alldata" => $mngcol_alldata],
                        "formula" => ["show" => $formula_show, "add" => $formula_add, "view" => $formula_view, "edit" => $formula_edit, "delete" => $formula_delete, "alldata" => $formula_alldata],
                        "invoicesetting" => ["show" => $invoicesetting_show, "add" => $invoicesetting_add, "view" => $invoicesetting_view, "edit" => $invoicesetting_edit, "delete" => $invoicesetting_delete, "alldata" => $invoicesetting_alldata],
                        "bank" => ["show" => $bank_show, "add" => $bank_add, "view" => $bank_view, "edit" => $bank_edit, "delete" => $bank_delete, "alldata" => $bank_alldata],
                        "customer" => ["show" => $customer_show, "add" => $customer_add, "view" => $customer_view, "edit" => $customer_edit, "delete" => $customer_delete, "alldata" => $customer_alldata],
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
                        "product" => ["show" => $product_show, "add" => $product_add, "view" => $product_view, "edit" => $product_edit, "delete" => $product_delete, "alldata" => $product_alldata]
                    ],
                    "accountmodule" => [
                        "purchase" => ["show" => $purchase_show, "add" => $purchase_add, "view" => $purchase_view, "edit" => $purchase_edit, "delete" => $purchase_delete, "alldata" => $purchase_alldata]
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
                    ]
                ];

                $rpjson = json_encode($rp);
            }
            $users = User::find($id);
            $userupdatedata = [];
            if ($request->hasFile('img') && $request->hasFile('img') != '') {
                $image = $request->file('img');
                $imageName = $request->firstname . time() . '.' . $image->getClientOriginalExtension();

                if ($image->move('uploads/', $imageName)) {
                    $imagePath = 'uploads/' . $users->img;
                    if (is_file($imagePath)) {
                        unlink($imagePath);  // old img remove
                    }
                    $userupdatedata['img'] = $imageName;
                }
            }

            if ($users) {
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
                $user = $users->update($userupdatedata);
                if ($user) {
                    if ($request->editrole == 1) {
                        return $this->successresponse(200, 'message', 'user succesfully updated');
                    } else {

                        if ($this->rp['adminmodule']['userpermission']['edit'] == 1) {
                            $searchuserrp = $this->user_permissionModel::where('user_id', $id)->first();
                            if ($searchuserrp) {
                                $rpupdate = $searchuserrp->update([
                                    "rp" => $rpjson
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
            } else {
                return $this->successresponse(404, 'message', 'No Such user Found!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $users = User::find($id);
        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($users->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($this->rp['adminmodule']['user']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        if ($users) {
            $users->update([
                'is_deleted' => 1

            ]);
            return $this->successresponse(200, 'message', 'user succesfully deleted');
        } else {
            return $this->successresponse(404, 'message', 'No Such user Found!');
        }
    }

    // status update
    public function statusupdate(Request $request, string $id)
    {
        $user = User::find($id);
        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($user->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($this->rp['adminmodule']['user']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        if ($user) {
            $user->update([
                'is_active' => $request->status
            ]);
            return $this->successresponse(200, 'message', 'user status succesfully updated');
        } else {
            return $this->successresponse(404, 'message', 'No Such user Found!');
        }
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
            return $this->errorresponse(422, ["current_password"=>['Current password does not match']]);
        }

        if ($request->new_password !== $request->new_password_confirmation) {
            return $this->errorresponse(422, ["new_password_confirmation"=>['New password and confirm password does not match']]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        return $this->successresponse(200, 'message', 'Password changed successfully');
    }


    public function setdefaultpage(Request $request, string $id)
    {
        $user = User::find($id);

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($user->id != $this->userId && $user->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        if ($user) {
            $user->update([
                'default_module' => $request->default_module,
                'default_page' => $request->default_page,
            ]);
            return $this->successresponse(200, 'message', 'Homepage succesfully updated');
        } else {
            return $this->successresponse(404, 'message', 'No Such user Record Found!');
        }
    }

}
