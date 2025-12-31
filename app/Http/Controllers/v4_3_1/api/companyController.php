<?php

namespace App\Http\Controllers\v4_3_1\api;

use App\Models\User;
use App\Mail\sendmail;
use App\Models\company;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\company_detail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class companyController extends commonController
{
    public $userId, $companyId, $rp, $user, $invoice_other_settingModel, $quotation_other_settingModel, $logistic_settingModel, $blog_settingModel, $user_permissionModel, $newdbname, $masterdbname, $lead_settingModel;
    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;

        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->user_permissionModel = $this->getmodel('user_permission');
        $this->invoice_other_settingModel = $this->getmodel('invoice_other_setting');
        $this->quotation_other_settingModel = $this->getmodel('quotation_other_setting');
        $this->logistic_settingModel = $this->getmodel('logistic_setting');
        $this->blog_settingModel = $this->getmodel('blog_setting');
        $this->lead_settingModel = $this->getmodel('lead_setting');
    }

    // for using pdf 
    public function companydetailspdf($id)
    {
        $companydetails = DB::table('company_details')
            ->join('country', 'company_details.country_id', '=', 'country.id')
            ->join('state', 'company_details.state_id', '=', 'state.id')
            ->join('city', 'company_details.city_id', '=', 'city.id')
            ->select('company_details.name', 'company_details.company_id', 'company_details.email', 'company_details.contact_no', 'company_details.alternative_number', 'company_details.god_names', 'company_details.house_no_building_name', 'company_details.road_name_area_colony', 'company_details.gst_no', 'company_details.pincode', 'company_details.img', 'company_details.pr_sign_img', 'company_details.transporter_id', 'country.country_name', 'state.state_name', 'city.city_name')
            ->where('company_details.id', $id)->get();

        if ($companydetails->isEmpty()) {
            return $this->successresponse(404, 'companydetails', 'No Records Found');
        }

        return $this->successresponse(200, 'companydetails', $companydetails);
    }

    // using in my profile company data
    public function companyprofile(Request $request)
    {
        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->join('country', 'company_details.country_id', '=', 'country.id')
            ->join('state', 'company_details.state_id', '=', 'state.id')
            ->join('city', 'company_details.city_id', '=', 'city.id')
            ->select('company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.alternative_number', 'company_details.house_no_building_name', 'company_details.road_name_area_colony', 'company_details.gst_no', 'company_details.pincode', 'company.max_users', 'company_details.website_url', 'company_details.god_names', 'company_details.img', 'company_details.pr_sign_img', 'company_details.transporter_id', 'country.country_name', 'state.state_name', 'city.city_name')
            ->where('company.id', $this->companyId)
            ->get();

        if ($company->isEmpty()) {
            return $this->successresponse(404, 'company', 'No Records Found');
        }

        $user = User::find($this->userId);
        if (!$user) {
            return $this->successresponse(404, 'company', 'No Records Found');
        }

        if (($this->rp['adminmodule']['company']['alldata'] != 1) && $this->companyId != $user->company_id) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        return $this->successresponse(200, 'company', $company);
    }

    /**
     * company list for company version update.
     */

    public function companylistforversioncontrol(Request $request)
    {
        if ($this->rp['adminmodule']['company']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->select('company.id', 'company.app_version', 'company_details.name', 'company_details.email', 'company_details.contact_no', 'is_deleted');

        if (!isset($request->is_deleted)) {
            $company->where('company.is_deleted', 0);
        }

        if ($this->companyId != 1) {
            $company->where('company.is_active', 1);
            if ($this->rp['adminmodule']['company']['alldata'] != 1) {
                $company->where('company.id', $this->companyId);
            } else {
                $company->where('company.created_by', $this->userId)->orWhere('company.id', $this->companyId);
            }
        }

        $company =  $company->get();


        if ($company->isEmpty()) {
            return $this->successresponse(404, 'company', 'No Records Found');
        }

        return $this->successresponse(200, 'company', $company);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->join('country', 'company_details.country_id', '=', 'country.id')
            ->join('state', 'company_details.state_id', '=', 'state.id')
            ->join('city', 'company_details.city_id', '=', 'city.id')
            ->join('users', 'company.created_by', '=', 'users.id')
            ->select(
                'company.id',
                'company_details.name',
                'company_details.email',
                DB::raw("CAST(company_details.contact_no AS CHAR) as contact_no"),
                DB::raw("CAST(company_details.alternative_number AS CHAR) as alternative_number"),
                'company_details.house_no_building_name',
                'company_details.road_name_area_colony',
                'company_details.gst_no',
                'company_details.pan_number',
                'company_details.img',
                'company_details.pr_sign_img',
                'company_details.transporter_id',
                'country.country_name',
                'state.state_name',
                'city.city_name',
                'company.created_by',
                'company.updated_by',
                DB::raw("DATE_FORMAT(company.created_at,'%d-%M-%Y %h:%i %p')as created_at_formatted"),
                'users.firstname as creator_firstname',
                'users.lastname as creator_lastname',
                'company.updated_at',
                'company.is_active',
                'company.is_deleted'
            )
            ->where('company.is_deleted', 0);

        if ($this->companyId != 1) {
            $company->where('company.is_active', 1);
            if ($this->rp['adminmodule']['company']['alldata'] != 1) {
                $company->where('company.id', $this->companyId);
            } else {
                $company->where('company.created_by', $this->userId)->orWhere('company.id', $this->companyId);
            }
        }

        $company = $company->get();

        if ($this->rp['adminmodule']['company']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        if ($company->isEmpty()) {
            return DataTables::of($company)
                ->with([
                    'status' => 404,
                ])->make(true);
        }

        return DataTables::of($company)
            ->with([
                'status' => 200,
            ])->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'nullable|string|max:50',
            'email_default_user' => 'required|string|max:50',
            'contact_number' => 'required|numeric|digits:10',
            'alternative_number' => 'nullable|numeric|digits:10',
            'house_no_building_name' => 'required|string|max:255',
            'road_name_area_colony' => 'required|string|max:255',
            'gst_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric',
            'maxuser' => 'nullable|numeric',
            'company_website_url' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'sign_img' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'transporter_id' => 'nullable|string|max:50',
            'user_id' => 'required|numeric',
            'updated_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted',
            'god_name'   => 'required|array|min:1',


        ]);

        if ($validator->fails()) {
            // return error response
            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp['adminmodule']['company']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            // check user email is not duplicate
            $checkuseremail = User::where('email', $request->email_default_user)->where('is_deleted', 0)->exists();

            if ($checkuseremail) {
                return $this->successresponse(500, 'message', 'This email id already exists , Please enter other email id');
            }


            $modifiedname = preg_replace('/[^a-zA-Z0-9_]+/', '_', $request->name);

            // If the cleaned name starts with a digit, prepend an underscore
            if (ctype_digit(substr($modifiedname, 0, 1))) {
                $modifiedname = '_' . $modifiedname;
            }

            // Set the dynamic database name (sanitize and format if necessary) 

            $appUrl = config('app.url');
            $baseUrl = basename($appUrl);

            if ($baseUrl === 'localhost:8000') {
                // If the host is localhost
                $this->newdbname = 'bj_local_' . $modifiedname . '_' . Str::lower(Str::random(3));
                if (app()->environment('testing')) {
                    $this->newdbname = "testing_company";
                }
            } elseif ($baseUrl === 'staging.businessjoy.in') {
                // If the host is staging.businessjoy.in
                $this->newdbname = 'staging_business_joy_' . $modifiedname . '_' . Str::lower(Str::random(3));
                if (app()->environment('testing')) {
                    $this->newdbname = "staging_business_joy_testing_company";
                }
            } else {
                // For any other host, provide a default
                $this->newdbname = 'business_joy_' . $modifiedname . '_' . Str::lower(Str::random(3));
                if (app()->environment('testing')) {
                    $this->newdbname = "business_joy_testing_company";
                }
            }

            // Create the dynamic database

            if (app()->environment('testing')) {
                // Drop the database if it exists
                DB::connection(config('database.dynamic_connection'))->statement('DROP DATABASE IF EXISTS `' . $this->newdbname . '`');
            }

            // this to create new data base 
            DB::connection(config('database.dynamic_connection'))->statement('CREATE DATABASE ' . $this->newdbname);

            // Switch to the new database connection
            config([
                'database.connections.' . $this->newdbname => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '3306'),
                    'database' => $this->newdbname,
                    'username' => env('DB_USERNAME', 'forge'),
                    'password' => env('DB_PASSWORD', ''),
                    'unix_socket' => env('DB_SOCKET', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                ]
            ]);

            // required migrations path for company table
            $paths = [
                'database/migrations/individualcompanydb',
                'database/migrations/v1_1_1',
                'database/migrations/v1_2_1',
                'database/migrations/v2_0_0',
                'database/migrations/v3_0_0',
                'database/migrations/v4_0_0',
                'database/migrations/v4_1_0',
                'database/migrations/v4_2_0',
                'database/migrations/v4_2_1/individual',
                'database/migrations/v4_2_2/individual',
                'database/migrations/v4_2_3/individual',
                'database/migrations/v4_3_0/individual',
                'database/migrations/v4_3_1/individual',
            ];

            // Run migrations only from the specified path
            foreach ($paths as $path) {
                Artisan::call('migrate', [
                    '--path' => $path,
                    '--database' => $this->newdbname,
                ]);
            }

            // switch to database 
            config(['database.connections.dynamic_connection.database' => $this->newdbname]);

            return $this->executeTransaction(function () use ($request) {
                // Establish connection to the dynamic database
                DB::purge('dynamic_connection');
                DB::reconnect('dynamic_connection');

                // --------------------------------- add company code start
                $godNames = $request->input('god_name');

                //dd($godNames);
                $company_details_data = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_number,

                    'house_no_building_name' => $request->house_no_building_name,
                    'road_name_area_colony' => $request->road_name_area_colony,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                    'pincode' => $request->pincode,
                    'gst_no' => $request->gst_number,
                    'pan_number' => $request->pan_number,
                    'website_url' => $request->company_website_url,
                    'transporter_id' => $request->transporter_id,
                    'god_names' => json_encode($godNames),
                    'alternative_number' => $request->alternative_number,
                ];

                // add company details this in main superadmin side db name is newbjdb
                $company_details = DB::table('company_details')->insertGetId($company_details_data); // insert company details

                if ($company_details) {
                    $company_details_id = $company_details;
                    $company = DB::table('company')->insertGetId([   // insert company record
                        'company_details_id' => $company_details_id,
                        'dbname' => $this->newdbname,
                        'app_version' => $this->companyVersion ?? $_SESSION['folder_name'],
                        'max_users' => $request->maxuser,
                        'created_by' => $this->userId,
                    ]);
                    // upload company image and signature image
                    $imageName = null;
                    $sign_imageName = null;
                    if (($request->hasFile('img') && $request->file('img') != null) || ($request->hasFile('sign_img') && $request->file('sign_img') != null)) {

                        $image = $request->file('img');
                        $sign_image = $request->file('sign_img');

                        $dirPath = public_path('uploads/') . $company . '/';

                        if (!file_exists($dirPath)) {
                            mkdir($dirPath, 0755, true);
                        }

                        $updateDetails = [];
                        $updateDetails['company_id'] = $company;

                        // Check if image file is uploaded
                        if ($image) {
                            $imageName = $request->name . time() . '.' . $image->getClientOriginalExtension();
                            $image->move($dirPath, $imageName); // upload image
                            $updateDetails['img'] = $company . '/' . $imageName;
                        }

                        // Check if signature image file is uploaded
                        if ($sign_image) {
                            $sign_imageName = $request->name . time() . 'sign.' . $sign_image->getClientOriginalExtension();
                            $sign_image->move($dirPath, $sign_imageName); // upload signature image
                            $updateDetails['img'] = $company . '/' . $sign_imageName;
                        }

                        DB::table('company_details')->where('id', $company_details_id)->update(
                            $updateDetails
                        );
                    }

                    // seeding default settings
                    $this->seedingDefaultSettings($this->userId);

                    if ($company) {

                        $company_id = $company;

                        $passwordtoken = str::random(40);
                        $user = DB::table('users')->insertGetId([  // create new default user
                            'firstname' => $request->name,
                            'email' => $request->email_default_user,
                            'role' => 2,
                            'contact_no' => $request->contact_number,
                            'country_id' => $request->country,
                            'state_id' => $request->state,
                            'city_id' => $request->city,
                            'pincode' => $request->pincode,
                            'pass_token' => $passwordtoken,
                            'company_id' => $company_id,
                            'created_by' => $this->userId,
                        ]);

                        if ($user) {
                            $userid = $user;
                            $rp = [
                                "invoicemodule" => [
                                    "invoicedashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "invoice" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "mngcol" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "formula" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "invoicesetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "invoiceformsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "bank" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "customer" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "invoicenumbersetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "invoicetandcsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "invoicestandardsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "invoicegstsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "invoicecustomeridsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "invoiceapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "tdsregister" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                                ],
                                "leadmodule" => [
                                    "leaddashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "lead" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "leadsettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "upcomingfollowup" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "analysis" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "leadownerperformance" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "recentactivity" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "calendar" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "leadapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "import" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "export" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                                ],
                                "customersupportmodule" => [
                                    "customersupportdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "customersupport" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "customersupportapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                                ],
                                "adminmodule" => [
                                    "admindashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "company" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null, "max" => null],
                                    "user" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "techsupport" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "userpermission" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "adminapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "loginhistory" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                                ],
                                "inventorymodule" => [
                                    "inventorydashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "product" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "purchase" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "productcategory" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "productcolumnmapping" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "inventory" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "supplier" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "inventoryapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                                ],
                                "remindermodule" => [
                                    "reminderdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "reminder" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "remindercustomer" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "reminderapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                                ],
                                "reportmodule" => [
                                    "reportdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "report" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null, "log" => null],
                                    "reportapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                                ],
                                "blogmodule" => [
                                    "blogdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "blog" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null,],
                                    "blogsettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null,],
                                    "blogapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                                ],
                                "quotationmodule" => [
                                    "quotationdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "quotation" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "quotationmngcol" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "quotationformula" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "quotationsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "quotationnumbersetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "quotationtandcsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "quotationstandardsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "quotationgstsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "quotationcustomer" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "quotationapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                                ],
                                'logisticmodule' => [
                                    "logisticdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "consignorcopy" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "logisticsettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "Lrnumbersettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "logisticformsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "consignmentnotenumbersettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "consignorcopytandcsettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "logisticothersettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "consignee" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "consignor" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "logisticapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "watermark" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "downloadcopysetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "transporterbilling" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "lrcolumnmapping" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],

                                ],
                                'developermodule' => [
                                    "developerdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "slowpage" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "errorlog" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "cronjob" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "techdoc" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "versiondoc" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "recentactivitydata" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "cleardata" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                    "automatetest" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                                ]
                            ];

                            $rpjson = json_encode($rp);

                            $userrp = $this->user_permissionModel::create([  // create user permission
                                'user_id' => $userid,
                                'rp' => $rpjson,
                                'created_by' => $this->userId
                            ]);

                            if ($userrp) {
                                Mail::to($request->email_default_user)->send(new sendmail($passwordtoken, $request->name, $request->email_default_user));
                                return $this->successresponse(200, 'message', 'Company succesfully added');
                            } else {
                                throw new \Exception("User permission creation failed!");
                            }
                        } else {
                            throw new \Exception("User creation failed!");
                        }
                    } else {
                        throw new \Exception("Company creation failed!");
                    }
                } else {
                    throw new \Exception("Company details creation failed!");
                }
            });
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->where('company.id', $id)
            ->get();

        if ($company->isEmpty()) {
            return $this->successresponse(404, 'message', "No Such company Found!");
        }


        return $this->successresponse(200, 'company', $company);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if ($this->rp['adminmodule']['company']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->where('company.id', $id)->first();

        if (!$company) {
            return $this->successresponse(404, 'message', "No Such company Found!");
        }

        if ($this->rp['adminmodule']['company']['alldata'] != 1) {
            if ($company->created_by != $this->userId && $company->id != $this->companyId) {
                return $this->successresponse(500, 'message', "You are Unauthorized!");
            }
        }


        return $this->successresponse(200, 'company', $company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($this->rp['adminmodule']['company']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        return $this->executeTransaction(function () use ($request, $id) {
            // validate incoming request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'email' => 'nullable|string|max:50',
                'contact_number' => 'required|numeric|digits:10',
                'alternative_number' => 'nullable|numeric|digits:10',
                'house_no_building_name' => 'required|string|max:255',
                'road_name_area_colony' => 'required|string|max:255',
                'gst_number' => 'nullable|string|max:50',
                'pan_number' => 'nullable|string|max:50',
                'country' => 'required|numeric',
                'state' => 'required|numeric',
                'city' => 'required|numeric',
                'pincode' => 'required|numeric',
                'maxuser' => 'nullable|numeric',
                'company_website_url' => 'nullable|string',
                'img' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
                'sign_img' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
                'transporter_id' => 'nullable|string|max:50',
                'user_id' => 'numeric',
                'updated_at',
                'is_active',
                'is_deleted',
                'god_name'   => 'required|array|min:1',
            ]);

            if ($validator->fails()) {
                // return error response if validator fails
                return $this->errorresponse(422, $validator->messages());
            } else {

                $company = company::join('company_details', 'company.company_details_id', '=', 'company_details.id')
                    ->select('company.id', 'company.created_by', 'company_details.img', 'company_details.pr_sign_img', 'company_details.watermark_img')->where('company.id', $id)
                    ->get();

                if (empty($company)) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }

                if ($this->rp['adminmodule']['company']['alldata'] != 1) {
                    if ($company[0]->created_by != $this->userId && $company[0]->id != $this->companyId) {
                        return $this->successresponse(500, 'message', "You are Unauthorized!");
                    }
                }

                $imageName = $company[0]->img;
                $sign_imageName = $company[0]->pr_sign_img;
                $watermark_imageName = $company[0]->watermark_img;

                if (($request->hasFile('img') && $request->file('img') != null) || ($request->hasFile('sign_img') && $request->file('sign_img') != null)) {

                    $image = $request->file('img');
                    $sign_image = $request->file('sign_img');

                    $dirPath = public_path('uploads/') . $id . '/';

                    // Check if image file is uploaded
                    if ($image) {
                        $imageName = $request->name . time() . '.' . $image->getClientOriginalExtension();
                        $image->move($dirPath, $imageName); // upload image
                        $imageName = $id . '/' . $imageName;
                    }

                    // Check if signature image file is uploaded
                    if ($sign_image) {
                        $sign_imageName = $request->name . time() . 'sign.' . $sign_image->getClientOriginalExtension();
                        $sign_image->move($dirPath, $sign_imageName); // upload signature image
                        $sign_imageName = $id . '/' . $sign_imageName;
                    }
                }
                $godNames = $request->input('god_name');
                $company_details_data = [
                    'company_id' => $id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_number,
                    'house_no_building_name' => $request->house_no_building_name,
                    'road_name_area_colony' => $request->road_name_area_colony,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                    'pincode' => $request->pincode,
                    'gst_no' => $request->gst_number,
                    'pan_number' => $request->pan_number,
                    'website_url' => $request->company_website_url,
                    'img' => $imageName,
                    'pr_sign_img' => $sign_imageName,
                    'watermark_img' => $watermark_imageName,
                    'transporter_id' => $request->transporter_id,
                    'god_names' => json_encode($godNames),
                    'alternative_number' => $request->alternative_number,
                ];

                //dd($company_details_data);

                $company_details = DB::table('company_details')->insertGetId($company_details_data); // insert company details (create new company details record  everytime on company update)
                if ($company_details) {
                    $company_details_id = $company_details;
                    $company = company::find($id);
                    if ($company) {
                        if (isset($request->maxuser)) {
                            $company->max_users = $request->maxuser;
                            $company->save();
                        }
                        $companyupdate = $company->update([  // update company details id into company record table
                            'company_details_id' => $company_details_id,
                            'updated_by' => $this->userId,
                        ]);

                        if ($companyupdate) {
                            return $this->successresponse(200, 'message', 'company succesfully updated');
                        } else {
                            $company_details = company_detail::find($company_details_id);  // delete newly created company details record if it will not created proper
                            $company_details->delete();
                            return $this->successresponse(200, 'message', 'company not succesfully updated');
                        }
                    } else {
                        return $this->successresponse(404, 'message', 'No Such company Found!');
                    }
                } else {
                    return $this->successresponse(500, 'message', 'Oops ! Something Went wrong');
                }
            }
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        return $this->executeTransaction(function () use ($id) {

            if ($this->rp['adminmodule']['company']['delete'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $company = company::find($id);

            if (!$company) {
                return $this->successresponse(404, 'message', 'No Such company Found!');
            }

            if ($this->rp['adminmodule']['company']['alldata'] != 1) {
                if ($company->created_by != $this->userId && $company->id != $this->companyId) {
                    return $this->successresponse(500, 'message', "You are Unauthorized!");
                }
            }

            $company->update([
                'is_deleted' => 1
            ]);


            $users = User::where('company_id', $id)->update([
                'is_deleted' => 1
            ]);


            return $this->successresponse(200, 'message', 'company succesfully deleted');
        });
    }

    // company active/deactive function
    public function statusupdate(Request $request, string $id)
    {
        if ($this->rp['adminmodule']['company']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        return $this->executeTransaction(function () use ($request, $id) {
            $company = company::find($id);

            if (!$company) {
                return $this->successresponse(404, 'message', 'No Such Company Found!');
            }

            if ($this->rp['adminmodule']['company']['alldata'] != 1) {
                if ($company->created_by != $this->userId && $company->id != $this->companyId) {
                    return $this->successresponse(500, 'message', "You are Unauthorized!");
                }
            }

            $company->update([
                'is_active' => $request->status
            ]);


            $users = User::where('company_id', $id)->update([
                'is_active' => $request->status
            ]);

            return $this->successresponse(200, 'message', 'Comapny status succesfully updated');
        });
    }


    // helper functions
    private function seedingDefaultSettings(int $userId)
    {
        $this->invoice_other_settingModel::create([  // default invoice other settings insert
            'overdue_day' => 45,
            'year_start' => date('Y-m-d', strtotime(date('Y') . '-04-01')),
            'sgst' => 9,
            'cgst' => 9,
            'gst' => 0,
            'customer_id' => 1,
            'current_customer_id' => 1,
            'created_by' => $userId,
        ]);

        $this->quotation_other_settingModel::create([  // default quotation other settings insert
            'overdue_day' => 30,
            'year_start' => date('Y-m-d', strtotime(date('Y') . '-04-01')),
            'sgst' => 9,
            'cgst' => 9,
            'gst' => 0,
            'customer_id' => 1,
            'current_customer_id' => 1,
            'created_by' => $userId,
        ]);

        $this->logistic_settingModel::create([  // default logistic other settings create
            'created_by' => $userId,
        ]);

        $this->blog_settingModel::create([  // default blog settings create
            'details_endpoint' => '',
            'img_allowed_filetype' => 'jpg,jpeg,png',
            'img_max_size' => '10',
            'img_width' => '600',
            'img_height' => '400',
            'thumbnail_img_width' => '400',
            'thumbnail_img_height' => '266',
            'validate_dimension' => '0',
        ]);

        $this->lead_settingModel::create([  // default lead settings create
            'country' => 0,
            'state' => 0,
            'city' => 0,
            'autofill_value' => 'As Per User',
            'country_default_value' => null,
            'state_default_value' => null,
            'city_default_value' => null,
        ]);
    }
}
