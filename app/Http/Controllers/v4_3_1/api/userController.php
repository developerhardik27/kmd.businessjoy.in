<?php

namespace App\Http\Controllers\v4_3_1\api;


use App\Models\User;
use App\Mail\sendmail;
use App\Models\company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\user_activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class userController extends commonController
{

    public $db, $companyId, $userId, $rp, $masterdbname, $user_permissionModel, $role_permissionModel;

    // modules
    private $modulesConfig = [
        "invoicemodule" => [
            "invoicedashboard" => ["show", "add", "view", "edit", "delete", "alldata"],
            "invoice" => ["show", "add", "view", "edit", "delete", "alldata"],
            "mngcol" => ["show", "add", "view", "edit", "delete", "alldata"],
            "formula" => ["show", "add", "view", "edit", "delete", "alldata"],
            "invoicesetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "invoiceformsetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "bank" => ["show", "add", "view", "edit", "delete", "alldata"],
            "customer" => ["show", "add", "view", "edit", "delete", "alldata"],
            "invoicenumbersetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "invoicetandcsetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "invoicestandardsetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "invoicegstsetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "invoicecustomeridsetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "invoiceapi" => ["show", "add", "view", "edit", "delete", "alldata"],
            "tdsregister" => ["show", "add", "view", "edit", "delete", "alldata"],
        ],
        "leadmodule" => [
            "leaddashboard" => ["show", "add", "view", "edit", "delete", "alldata"],
            "lead" => ["show", "add", "view", "edit", "delete", "alldata"],
            "leadsettings" => ["show", "add", "view", "edit", "delete", "alldata"],
            "upcomingfollowup" => ["show", "add", "view", "edit", "delete", "alldata"],
            "analysis" => ["show", "add", "view", "edit", "delete", "alldata"],
            "leadownerperformance" => ["show", "add", "view", "edit", "delete", "alldata"],
            "recentactivity" => ["show", "add", "view", "edit", "delete", "alldata"],
            "calendar" => ["show", "add", "view", "edit", "delete", "alldata"],
            "leadapi" => ["show", "add", "view", "edit", "delete", "alldata"],
            "import" => ["show", "add", "view", "edit", "delete", "alldata"],
            "export" => ["show", "add", "view", "edit", "delete", "alldata"]
        ],
        "customersupportmodule" => [
            "customersupportdashboard" => ["show", "add", "view", "edit", "delete", "alldata"],
            "customersupport" => ["show", "add", "view", "edit", "delete", "alldata"],
            "customersupportapi" => ["show", "add", "view", "edit", "delete", "alldata"]
        ],
        "adminmodule" => [
            "admindashboard" => ["show", "add", "view", "edit", "delete", "alldata"],
            "company" => ["show", "add", "view", "edit", "delete", "alldata", "max"],
            "user" => ["show", "add", "view", "edit", "delete", "alldata"],
            "techsupport" => ["show", "add", "view", "edit", "delete", "alldata"],
            "userpermission" => ["show", "add", "view", "edit", "delete", "alldata"],
            "adminapi" => ["show", "add", "view", "edit", "delete", "alldata"],
            "loginhistory" => ["show", "add", "view", "edit", "delete", "alldata"]
        ],
        "inventorymodule" => [
            "inventorydashboard" => ["show", "add", "view", "edit", "delete", "alldata"],
            "product" => ["show", "add", "view", "edit", "delete", "alldata"],
            "productcategory" => ["show", "add", "view", "edit", "delete", "alldata"],
            "productcolumnmapping" => ["show", "add", "view", "edit", "delete", "alldata"],
            "purchase" => ["show", "add", "view", "edit", "delete", "alldata"],
            "inventory" => ["show", "add", "view", "edit", "delete", "alldata"],
            "supplier" => ["show", "add", "view", "edit", "delete", "alldata"],
            "inventoryapi" => ["show", "add", "view", "edit", "delete", "alldata"]
        ],
        // "accountmodule" => [
        // ],
        "remindermodule" => [
            "reminderdashboard" => ["show", "add", "view", "edit", "delete", "alldata"],
            "reminder" => ["show", "add", "view", "edit", "delete", "alldata"],
            "remindercustomer" => ["show", "add", "view", "edit", "delete", "alldata"],
            "reminderapi" => ["show", "add", "view", "edit", "delete", "alldata"]
        ],
        // "reportmodule" => [
        //     "reportdashboard" => ["show", "add", "view", "edit", "delete", "alldata"],
        //     "report" => ["show", "add", "view", "edit", "delete", "alldata", "log"],
        //     "reportapi" => ["show", "add", "view", "edit", "delete", "alldata"]
        // ],
        "blogmodule" => [
            "blogdashboard" => ["show", "add", "view", "edit", "delete", "alldata"],
            "blog" => ["show", "add", "view", "edit", "delete", "alldata"],
            "blogsettings" => ["show", "add", "view", "edit", "delete", "alldata"],
            "blogapi" => ["show", "add", "view", "edit", "delete", "alldata"]
        ],
        "quotationmodule" => [
            "quotationdashboard" => ["show", "add", "view", "edit", "delete", "alldata"],
            "quotation" => ["show", "add", "view", "edit", "delete", "alldata"],
            "quotationmngcol" => ["show", "add", "view", "edit", "delete", "alldata"],
            "quotationformula" => ["show", "add", "view", "edit", "delete", "alldata"],
            "quotationsetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "quotationnumbersetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "quotationtandcsetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "quotationstandardsetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "quotationgstsetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "quotationcustomer" => ["show", "add", "view", "edit", "delete", "alldata"],
            "quotationapi" => ["show", "add", "view", "edit", "delete", "alldata"]
        ],
        "logisticmodule" => [
            "logisticdashboard" => ["show", "add", "view", "edit", "delete", "alldata"],
            "consignorcopy" => ["show", "add", "view", "edit", "delete", "alldata"],
            "logisticsettings" => ["show", "add", "view", "edit", "delete", "alldata"],
            "lrcolumnmapping" => ["show", "add", "view", "edit", "delete", "alldata"],
            "logisticformsetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "consignmentnotenumbersettings" => ["show", "add", "view", "edit", "delete", "alldata"],
            "consignorcopytandcsettings" => ["show", "add", "view", "edit", "delete", "alldata"],
            "logisticothersettings" => ["show", "add", "view", "edit", "delete", "alldata"],
            "consignee" => ["show", "add", "view", "edit", "delete", "alldata"],
            "consignor" => ["show", "add", "view", "edit", "delete", "alldata"],
            "logisticapi" => ["show", "add", "view", "edit", "delete", "alldata"],
            "watermark" => ["show", "add", "view", "edit", "delete", "alldata"],
            "downloadcopysetting" => ["show", "add", "view", "edit", "delete", "alldata"],
            "transporterbilling" => ["show", "add", "view", "edit", "delete", "alldata"]
        ],
        "developermodule" => [
            "developerdashboard" => ["show", "add", "view", "edit", "delete", "alldata"],
            "automatetest" => ["show", "add", "view", "edit", "delete", "alldata"],
            "slowpage" => ["show", "add", "view", "edit", "delete", "alldata"],
            "errorlog" => ["show", "add", "view", "edit", "delete", "alldata"],
            "cronjob" => ["show", "add", "view", "edit", "delete", "alldata"],
            "techdoc" => ["show", "add", "view", "edit", "delete", "alldata"],
            "versiondoc" => ["show", "add", "view", "edit", "delete", "alldata"],
            "recentactivitydata" => ["show", "add", "view", "edit", "delete", "alldata"],
            "cleardata" => ["show", "add", "view", "edit", "delete", "alldata"]
        ],

    ];

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

        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        if (empty($permissions)) {
            $this->customerrorresponse();
        }
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->user_permissionModel = $this->getmodel('user_permission');
        $this->role_permissionModel = $this->getmodel('role_permission');
    }

    /**
     * Show the login history
     */
    public function loginhistory(Request $request)
    {
        $loginhistory = user_activity::orderBy('created_at', 'desc');

        if ($this->userId == 1 && isset($request->request_id)) {
            $loginhistory->where('user_id', $request->request_id);
        } else {
            $loginhistory->where('user_id', $this->userId);
        }

        $loginhistory = $loginhistory
            ->select(
                'username',
                'ip',
                'status',
                'country',
                'device',
                'browser',
                'via',
                'message',
                DB::raw("DATE_FORMAT(created_at, '%d-%M-%Y %h:%i %p')as created_at_formatted")
            )->get();

        if ($loginhistory->isEmpty()) {
            return DataTables::of($loginhistory)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }

        return DataTables::of($loginhistory)
            ->with([
                'status' => 200,
            ])
            ->make(true);
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
    public function invoiceuser(Request $request)
    {
        $targetUserId = $request->target_user_id ?? $this->userId; // target users company id / logged in user company id
        $targetCompanyId = $this->companyId;
        if ($this->companyId == 1 && $targetUserId != 1) {
            $targetCompanyId = $this->getcompanyidbyuserid($targetUserId);
        }

        $users = DB::table('users')
            ->select('users.id', 'users.firstname', 'users.lastname')
            ->where('users.is_deleted', 0)
            ->where('users.is_active', 1)
            ->where('users.company_id', $targetCompanyId)
            // commented this condition. give all company related users
            // ->whereJsonContains('rp->invoicemodule->invoice->add', "1")
            // ->whereJsonContains('rp->invoicemodule->invoice->show', "1")
            // ->join($this->db . '.user_permissions', 'users.id', '=', $this->db . '.user_permissions.user_id')
            ->get();

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
     * helper function - get user's company id by user id
     */
    public function getcompanyidbyuserid(int $userId)
    {
        $companyId = User::where('id', $userId)->value('company_id');
        return $companyId;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->rp['adminmodule']['user']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

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

        return $this->successresponse(200, 'user', $users);
    }

    /**
     * Display a listing of the DataTable.
     */
    public function userdatatable(Request $request)
    {
        if ($this->rp['adminmodule']['user']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $usersres = DB::table('users')
            ->join('country', 'users.country_id', '=', 'country.id')
            ->join('state', 'users.state_id', '=', 'state.id')
            ->join('city', 'users.city_id', '=', 'city.id')
            ->join('company', 'users.company_id', '=', 'company.id')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->join('user_role', 'users.role', '=', 'user_role.id')
            ->leftJoin('users as creator', 'users.created_by', '=', 'creator.id')
            ->select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.contact_no', 'country.country_name', 'state.state_name', 'city.city_name', 'users.pincode', 'company_details.name as company_name', 'user_role.role as user_role', 'users.img', 'users.created_by', 'creator.firstname as creator_firstname', 'creator.lastname as creator_lastname', DB::raw("DATE_FORMAT(users.created_at, '%d-%M-%Y %h:%i %p')as created_at_formatted"), 'users.updated_by', 'users.is_active')
            ->where('users.is_deleted', 0);

        if ($this->companyId != 1) {
            $users = $usersres->where('users.company_id', $this->companyId);
        }

        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            $usersres->where('users.created_by', $this->userId)->orWhere('users.id', $this->userId);
        }

        $users = $usersres->get();

        if ($users->isEmpty()) {
            return DataTables::of($users)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                ])
                ->make(true);
        }

        return DataTables::of($users)
            ->with([
                'status' => 200,
            ])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->executeTransaction(function () use ($request) {

            if ($this->rp['adminmodule']['user']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

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
                'user_role_permission' => 'nullable|numeric',
                'created_by',
                'created_at',
                'updated_at',
                'is_active',
                'is_deleted'
            ]);

            if ($validator->fails()) {
                return $this->errorresponse(422, $validator->messages());
            } else {
                // check email already exist or not
                $checkuseremail = User::where('email', $request->email)->where('is_deleted', 0)->get();

                if ($checkuseremail->isNotEmpty()) {
                    return $this->successresponse(500, 'message', 'This email id already exists , Please enter other email id');
                }

                if ($this->rp['adminmodule']['userpermission']['add'] == 1) {
                    $rpjson = $this->adduserpermission($request);
                } else {
                    $rpjson = json_encode($this->rp);
                }

                $passwordtoken = str::random(40); // generate password token for set new password

                $user = [
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
                    'role_permissions' => $request->user_role_permission,
                    'pass_token' => $passwordtoken,
                    'company_id' => $this->companyId,
                    'created_by' => $this->userId
                ];

                $users = User::create($user); // insert user data

                if ($users) {

                    if ($request->hasFile('img') && $request->hasFile('img') != '') {
                        $image = $request->file('img');
                        $imageName = $request->firstname . time() . '.' . $image->getClientOriginalExtension();

                        $dirPath = public_path('uploads/') . $this->companyId . '/user_' . $users->id;

                        if (!file_exists($dirPath)) {
                            mkdir($dirPath, 0755, true);
                        }

                        // Save the image to the uploads directory
                        if ($image->move($dirPath, $imageName)) {
                            $users->img = $this->companyId . '/user_' . $users->id . '/' . $imageName;
                            $users->save();
                        }
                    }

                    $userrp = $this->user_permissionModel::create([
                        'user_id' => $users->id,
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
        });
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
        if ($this->rp['adminmodule']['user']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $users = User::find($id);
        if (!$users) {
            return $this->successresponse(404, 'message', "No Such user Found!");
        }
        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($users->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        return $this->successresponse(200, 'user', $users);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return $this->executeTransaction(function () use ($request, $id) {
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
                'user_role_permission' => 'nullable|numeric',
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
                    $rpjson = $this->edituserpermission($request);
                }

                $userupdatedata = [];

                if ($request->hasFile('img') && $request->hasFile('img') != '') {
                    $image = $request->file('img');
                    $imageName = $request->firstname . time() . '.' . $image->getClientOriginalExtension();

                    $dirPath = public_path('uploads/') . $this->companyId . '/user_' . $id;

                    if ($image->move($dirPath, $imageName)) {
                        $imagePath = public_path('uploads/') . $user->img;
                        if (is_file($imagePath)) {
                            unlink($imagePath);  // old img remove
                        }
                        $userupdatedata['img'] = $this->companyId . '/user_' . $id . '/' . $imageName;
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
                    'role_permissions' => $request->user_role_permission,
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

                                if($searchuserrp->rp !== $rpjson){
                                    $user = User::find($id);
                                    $user->api_token = null;
                                    $user->super_api_token = null;
                                    $user->save(); 
                                }

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
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($this->rp['adminmodule']['user']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $users = User::find($id);
        if (!$users) {
            return $this->successresponse(404, 'message', 'No Such user Found!');
        }
        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($users->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $users->update([
            'is_deleted' => 1
        ]);
        return $this->successresponse(200, 'message', 'user succesfully deleted');
    }

    /**
     * Summary of statusupdate
     * active/deactive 
     * deactive user will not able to login
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function statusupdate(Request $request, string $id)
    {
        if ($this->rp['adminmodule']['user']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $user = User::find($id);
        if (!$user) {
            return $this->successresponse(404, 'message', 'No Such user Found!');
        }
        if ($this->rp['adminmodule']['user']['alldata'] != 1) {
            if ($user->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }
        $user->update([
            'is_active' => $request->status
        ]);
        return $this->successresponse(200, 'message', 'user status succesfully updated');
    }

    /**
     * Summary of changepassword
     * change password
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
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


    //USER ROLE AND PERMISSIONS METHODS 

    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function userrolepermissiondattable(Request $request)
    {
        if ($this->rp['adminmodule']['userpermission']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $userrolepermission = $this->role_permissionModel::where('is_deleted', 0);

        if ($this->rp['adminmodule']['userpermission']['alldata'] != 1) {
            $userrolepermission->where('created_by', $this->userId);
        }

        $totalcount = $userrolepermission->get()->count(); // count total record

        $userrolepermission = $userrolepermission->get();

        if ($userrolepermission->isEmpty()) {
            return DataTables::of($userrolepermission)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($userrolepermission)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
            ])
            ->make(true);
    }

    public function storeuserrolepermission(Request $request)
    {
        if ($this->rp['adminmodule']['userpermission']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        // validate incoming data request
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {
            $checkRoleExist = $this->role_permissionModel::where('role_name', $request->role_name)->where('is_deleted', 0)->exists();

            if ($checkRoleExist) {
                return $this->successresponse(500, 'message', 'User role already exists');
            }


            $rpjson = $this->adduserpermission($request);

            $userrp = $this->role_permissionModel::create([
                'role_name' => $request->role_name,
                'role_permissions' => $rpjson,
                'created_by' => $this->userId
            ]);

            return $this->successresponse(200, 'message', 'User role permission succesfully created');
        }
    }

    /**
     * Display the specified resource.
     */
    public function edituserrolepermission(string $id)
    {
        if ($this->rp['adminmodule']['userpermission']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $userrolepermission = $this->role_permissionModel::find($id);

        if (!$userrolepermission) {
            return $this->successresponse(500, 'message', "No User Role Found!");
        }

        return $this->successresponse(200, 'userrolepermission', $userrolepermission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateuserrolepermission(Request $request, string $id)
    {
        if ($this->rp['adminmodule']['userpermission']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:50',
            'user_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $checkuserrole = $this->role_permissionModel::where('role_name', $request->role_name)
                ->where('is_deleted', 0)
                ->whereNot('id', $id)
                ->exists();

            if ($checkuserrole) {
                return $this->successresponse(500, 'message', 'This role already exists , Please enter other role name');
            }


            $userrolepermission = $this->role_permissionModel::find($id);

            if (!$userrolepermission) {
                return $this->successresponse(404, 'message', 'No such record found!');
            }

            $rpjson = $this->edituserpermission($request);

            $user = $userrolepermission->update([
                'role_name' => $request->role_name,
                'role_permissions' => $rpjson,
                'updated_by' => $this->userId
            ]);

            return $this->successresponse(200, 'message', 'User role permission successfully updated.');
        }
    }

    /**
     * Summary of statusupdate
     * active/deactive 
     * deactive user will not able to login
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function userrolepermissionstatusupdate(Request $request, string $id)
    {

        if ($this->rp['adminmodule']['userpermission']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $userrolepermission = $this->role_permissionModel::find($id);

        if (!$userrolepermission) {
            return $this->successresponse(404, 'message', 'No such record found!');
        }

        if ($this->rp['adminmodule']['userpermission']['alldata'] != 1) {
            if ($userrolepermission->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $userrolepermission->update([
            'is_active' => $request->status
        ]);

        return $this->successresponse(200, 'message', 'User role status succesfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function userrolepermissiondestroy(string $id)
    {
        if ($this->rp['adminmodule']['userpermission']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $userrolepermission = $this->role_permissionModel::find($id);

        if (!$userrolepermission) {
            return $this->successresponse(404, 'message', 'No such record found!');
        }

        if ($this->rp['adminmodule']['userpermission']['alldata'] != 1) {
            if ($userrolepermission->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $userrolepermission->update([
            'is_deleted' => 1
        ]);

        return $this->successresponse(200, 'message', 'User role succesfully deleted');
    }


    /**
     * Display a listing of the resource.
     */
    public function userrolepermissionindex(Request $request)
    {

        if ($this->rp['adminmodule']['userpermission']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $userrolepermission = $this->role_permissionModel::where('is_active', 1)
            ->where('is_deleted', 0)
            ->get();

        if ($userrolepermission->isEmpty()) {
            return $this->successresponse(404, 'user', 'No Records Found');
        }

        return $this->successresponse(200, 'userrolepermission', $userrolepermission);
    }



    /**
     * Summary of adduserpermission
     * @param mixed $request
     * @return bool|string
     */
    public function adduserpermission($request)
    {

        $reportdashboard_show = (($this->rp['reportmodule']['reportdashboard']['show'] == 1 && $this->rp['reportmodule']['reportdashboard']['add'] == 1) || $this->userId == 1) ? $request->showreportdashboardmenu : null;
        $reportdashboard_add = (($this->rp['reportmodule']['reportdashboard']['add'] == 1) || $this->userId == 1) ? $request->addreportdashboard : null;
        $reportdashboard_view = (($this->rp['reportmodule']['reportdashboard']['view'] == 1 && $this->rp['reportmodule']['reportdashboard']['add'] == 1) || $this->userId == 1) ? $request->viewreportdashboard : null;
        $reportdashboard_edit = (($this->rp['reportmodule']['reportdashboard']['edit'] == 1 && $this->rp['reportmodule']['reportdashboard']['add'] == 1) || $this->userId == 1) ? $request->editreportdashboard : null;
        $reportdashboard_delete = (($this->rp['reportmodule']['reportdashboard']['delete'] == 1 && $this->rp['reportmodule']['reportdashboard']['add'] == 1) || $this->userId == 1) ? $request->deletereportdashboard : null;
        $reportdashboard_alldata = (($this->rp['reportmodule']['reportdashboard']['add'] == 1 && $this->rp['reportmodule']['reportdashboard']['add'] == 1) || $this->userId == 1) ? $request->alldatareportdashboard : null;

        $report_show = (($this->rp['reportmodule']['report']['show'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->showreportmenu : null;
        $report_add = (($this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->addreport : null;
        $report_view = (($this->rp['reportmodule']['report']['view'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->viewreport : null;
        $report_edit = (($this->rp['reportmodule']['report']['edit'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->editreport : null;
        $report_delete = (($this->rp['reportmodule']['report']['delete'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->deletereport : null;
        $report_alldata = (($this->rp['reportmodule']['report']['add'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->assignedto : null;
        $report_log = (($this->rp['reportmodule']['report']['log'] == 1 && $this->rp['reportmodule']['report']['add'] == 1) || $this->userId == 1) ? $request->logreport : null;

        $reportapi_show = (($this->rp['reportmodule']['reportapi']['show'] == 1 && $this->rp['reportmodule']['reportapi']['add'] == 1) || $this->userId == 1) ? $request->showreportapimenu : null;
        $reportapi_add = (($this->rp['reportmodule']['reportapi']['add'] == 1) || $this->userId == 1) ? $request->addreportapi : null;
        $reportapi_view = (($this->rp['reportmodule']['reportapi']['view'] == 1 && $this->rp['reportmodule']['reportapi']['add'] == 1) || $this->userId == 1) ? $request->viewreportapi : null;
        $reportapi_edit = (($this->rp['reportmodule']['reportapi']['edit'] == 1 && $this->rp['reportmodule']['reportapi']['add'] == 1) || $this->userId == 1) ? $request->editreportapi : null;
        $reportapi_delete = (($this->rp['reportmodule']['reportapi']['delete'] == 1 && $this->rp['reportmodule']['reportapi']['add'] == 1) || $this->userId == 1) ? $request->deletereportapi : null;
        $reportapi_alldata = (($this->rp['reportmodule']['reportapi']['alldata'] == 1 && $this->rp['reportmodule']['reportapi']['add'] == 1) || $this->userId == 1) ? $request->alldatareportapi : null;

        $rp = [
            "accountmodule" => [],
            "reportmodule" => [
                "reportdashboard" => ["show" => $reportdashboard_show, "add" => $reportdashboard_add, "view" => $reportdashboard_view, "edit" => $reportdashboard_edit, "delete" => $reportdashboard_delete, "alldata" => $reportdashboard_alldata],
                "report" => ["show" => $report_show, "add" => $report_add, "view" => $report_view, "edit" => $report_edit, "delete" => $report_delete, "alldata" => $report_alldata, "log" => $report_log],
                "reportapi" => ["show" => $reportapi_show, "add" => $reportapi_add, "view" => $reportapi_view, "edit" => $reportapi_edit, "delete" => $reportapi_delete, "alldata" => $reportapi_alldata]
            ]

        ];

        $result = [];

        foreach ($this->modulesConfig as $module => $submodules) {
            foreach ($submodules as $submodule => $actions) {
                foreach ($actions as $action) {
                    $value = $this->getPermissionValue($module, $submodule, $action, $request, 'add');
                    $result[$module][$submodule][$action] = $value;
                }
            }
        }

        $rp = array_merge($result, $rp);

        return json_encode($rp);
    }



    public function edituserpermission($request)
    {
        $reportdashboard_show = (($this->rp['reportmodule']['reportdashboard']['show'] == 1 && $this->rp['reportmodule']['reportdashboard']['edit'] == 1) || $this->userId == 1) ? $request->showreportdashboardmenu : $this->rp['reportmodule']['reportdashboard']['show'];
        $reportdashboard_add = (($this->rp['reportmodule']['reportdashboard']['add'] == 1 && $this->rp['reportmodule']['reportdashboard']['edit'] == 1) || $this->userId == 1) ? $request->addreportdashboard : $this->rp['reportmodule']['reportdashboard']['add'];
        $reportdashboard_view = (($this->rp['reportmodule']['reportdashboard']['view'] == 1 && $this->rp['reportmodule']['reportdashboard']['edit'] == 1) || $this->userId == 1) ? $request->viewreportdashboard : $this->rp['reportmodule']['reportdashboard']['view'];
        $reportdashboard_edit = (($this->rp['reportmodule']['reportdashboard']['edit'] == 1) || $this->userId == 1) ? $request->editreportdashboard : $this->rp['reportmodule']['reportdashboard']['edit'];
        $reportdashboard_delete = (($this->rp['reportmodule']['reportdashboard']['delete'] == 1 && $this->rp['reportmodule']['reportdashboard']['edit'] == 1) || $this->userId == 1) ? $request->deletereportdashboard : $this->rp['reportmodule']['reportdashboard']['delete'];
        $reportdashboard_alldata = (($this->rp['reportmodule']['reportdashboard']['alldata'] == 1 && $this->rp['reportmodule']['reportdashboard']['edit'] == 1) || $this->userId == 1) ? $request->alldatareportdashboard : $this->rp['reportmodule']['reportdashboard']['alldata'];

        $report_show = (($this->rp['reportmodule']['report']['show'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->showreportmenu : null;
        $report_add = (($this->rp['reportmodule']['report']['add'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->addreport : null;
        $report_view = (($this->rp['reportmodule']['report']['view'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->viewreport : null;
        $report_edit = (($this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->editreport : null;
        $report_delete = (($this->rp['reportmodule']['report']['delete'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->deletereport : null;
        $report_alldata = (($this->rp['reportmodule']['report']['add'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->assignedto : null;
        $report_log = (($this->rp['reportmodule']['report']['log'] == 1 && $this->rp['reportmodule']['report']['edit'] == 1) || $this->userId == 1) ? $request->logreport : null;

        $reportapi_show = (($this->rp['reportmodule']['reportapi']['show'] == 1 && $this->rp['reportmodule']['reportapi']['edit'] == 1) || $this->userId == 1) ? $request->showreportapimenu : null;
        $reportapi_add = (($this->rp['reportmodule']['reportapi']['add'] == 1 && $this->rp['reportmodule']['reportapi']['edit'] == 1) || $this->userId == 1) ? $request->addreportapi : null;
        $reportapi_view = (($this->rp['reportmodule']['reportapi']['view'] == 1 && $this->rp['reportmodule']['reportapi']['edit'] == 1) || $this->userId == 1) ? $request->viewreportapi : null;
        $reportapi_edit = (($this->rp['reportmodule']['reportapi']['edit'] == 1) || $this->userId == 1) ? $request->editreportapi : null;
        $reportapi_delete = (($this->rp['reportmodule']['reportapi']['delete'] == 1 && $this->rp['reportmodule']['reportapi']['edit'] == 1) || $this->userId == 1) ? $request->deletereportapi : null;
        $reportapi_alldata = (($this->rp['reportmodule']['reportapi']['alldata'] == 1 && $this->rp['reportmodule']['reportapi']['edit'] == 1) || $this->userId == 1) ? $request->alldatareportapi : null;

        $rp = [
            "accountmodule" => [],
            "reportmodule" => [
                "reportdashboard" => ["show" => $reportdashboard_show, "add" => $reportdashboard_add, "view" => $reportdashboard_view, "edit" => $reportdashboard_edit, "delete" => $reportdashboard_delete, "alldata" => $reportdashboard_alldata],
                "report" => ["show" => $report_show, "add" => $report_add, "view" => $report_view, "edit" => $report_edit, "delete" => $report_delete, "alldata" => $report_alldata, "log" => $report_log],
                "reportapi" => ["show" => $reportapi_show, "add" => $reportapi_add, "view" => $reportapi_view, "edit" => $reportapi_edit, "delete" => $reportapi_delete, "alldata" => $reportapi_alldata]
            ],
        ];

        $result = [];

        foreach ($this->modulesConfig as $module => $submodules) {
            foreach ($submodules as $submodule => $actions) {
                foreach ($actions as $action) {
                    $value = $this->getPermissionValue($module, $submodule, $action, $request, 'edit');
                    $result[$module][$submodule][$action] = $value;
                }
            }
        }

        $rp = array_merge($result, $rp);

        return json_encode($rp);
    }


    private function getPermissionValue($module, $submodule, $action, $request, $type)
    {
        $isAdmin = $this->userId == 1;
        $hasAccess = isset($this->rp[$module][$submodule][$action]) && $this->rp[$module][$submodule][$action] == 1;
        $hasAdd = isset($this->rp[$module][$submodule][$type]) && $this->rp[$module][$submodule][$type] == 1;

        // Require both current action and $type(add/edit) permission for most actions
        $allowed = ($hasAccess && $hasAdd) || $isAdmin || $action == $type;

        $submodule = $action == 'show' ? $submodule . 'menu' : $submodule;

        $requestKey = $action == 'max' ? 'maxuser' : $action . $submodule;

        return $allowed ? ($request->$requestKey ?? null) : null;
    }
}
