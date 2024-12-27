<?php

namespace App\Http\Controllers\v2_0_0\api;

use App\Mail\sendmail;
use App\Models\company;
use App\Models\User;
use App\Models\company_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class companyController extends commonController
{
    public $userId, $companyId, $rp, $user, $invoice_other_settingModel, $user_permissionModel;
    public function __construct(Request $request)
    {

        if ($request->company_id) {
            $this->companyId = $request->company_id;
        } else {
            $this->companyId = session()->get('company_id');
        }
        if ($request->user_id) {
            $this->userId = $request->user_id;
        } else {
            $this->userId = session()->get('user_id');
        }

        $this->user = User::find($this->userId);
        $dbname = company::find($this->user->company_id);
        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->user_permissionModel = $this->getmodel('user_permission');
        $this->invoice_other_settingModel = $this->getmodel('invoice_other_setting');
    }


    // for using pdf 
    public function companydetailspdf($id)
    {

        $companydetails = DB::table('company_details')
            ->join('country', 'company_details.country_id', '=', 'country.id')
            ->join('state', 'company_details.state_id', '=', 'state.id')
            ->join('city', 'company_details.city_id', '=', 'city.id')
            ->select('company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.house_no_building_name', 'company_details.road_name_area_colony', 'company_details.gst_no', 'company_details.pincode', 'company_details.img', 'company_details.pr_sign_img', 'country.country_name', 'state.state_name', 'city.city_name')
            ->where('company_details.id', $id)->get();

        if ($companydetails->count() > 0) {
            return $this->successresponse(200, 'companydetails', $companydetails);
        } else {
            return $this->successresponse(404, 'companydetails', 'No Records Found');
        }
    }

    // using in my profile company data
    public function companyprofile(Request $request)
    {
        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->join('country', 'company_details.country_id', '=', 'country.id')
            ->join('state', 'company_details.state_id', '=', 'state.id')
            ->join('city', 'company_details.city_id', '=', 'city.id')
            ->select('company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.house_no_building_name', 'company_details.road_name_area_colony', 'company_details.gst_no', 'company_details.pincode', 'company.max_users', 'company_details.img', 'company_details.pr_sign_img', 'country.country_name', 'state.state_name', 'city.city_name')
            ->where('company.id', $this->companyId)
            ->get();


        $user = User::find($this->userId);
        if (($this->rp['adminmodule']['company']['alldata'] != 1) && $this->companyId != $user->company_id) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }


        if ($company->count() > 0) {
            return $this->successresponse(200, 'company', $company);
        } else {
            return $this->successresponse(404, 'company', 'No Records Found');
        }
    }

    /**
     * company list for company version update.
     */

    public function companylistforversioncontrol(Request $request)
    {
        if ($this->companyId == 1) {
            $company = DB::table('company')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->select('company.id', 'company.app_version', 'company_details.name', 'company_details.email', 'company_details.contact_no')
                ->where('company.is_deleted', 0)
                ->get();
        }

        if ($this->rp['adminmodule']['company']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($company->count() > 0) {
            return $this->successresponse(200, 'company', $company);
        } else {
            return $this->successresponse(404, 'company', 'No Records Found');
        }
    }


    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {


        if ($this->companyId == 1) {
            $company = DB::table('company')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->join('country', 'company_details.country_id', '=', 'country.id')
                ->join('state', 'company_details.state_id', '=', 'state.id')
                ->join('city', 'company_details.city_id', '=', 'city.id')
                ->join('users', 'company.created_by', '=', 'users.id')
                ->select('company.id', 'company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.house_no_building_name', 'company_details.road_name_area_colony', 'company_details.gst_no', 'country.country_name', 'state.state_name', 'city.city_name', 'company.created_by', 'company.updated_by', DB::raw("DATE_FORMAT(company.created_at,'%d-%M-%Y %h:%i %p')as created_at_formatted"), 'users.firstname as creator_firstname', 'users.lastname as creator_lastname', 'company.updated_at', 'company.is_active', 'company.is_deleted')
                ->where('company.is_deleted', 0)
                ->get();
        } else {
            $companyres = DB::table('company')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->join('country', 'company_details.country_id', '=', 'country.id')
                ->join('state', 'company_details.state_id', '=', 'state.id')
                ->join('city', 'company_details.city_id', '=', 'city.id')
                ->join('users', 'company.created_by', '=', 'users.id')
                ->select('company.id', 'company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.house_no_building_name', 'company_details.road_name_area_colony', 'company_details.gst_no', 'country.country_name', 'state.state_name', 'city.city_name', 'company.created_by', 'company.updated_by', DB::raw("DATE_FORMAT(company.created_at,'%d-%M-%Y %h:%i %p')as created_at_formatted"), 'users.firstname as creator_firstname', 'users.lastname as creator_lastname', 'company.updated_at', 'company.is_active', 'company.is_deleted')
                ->where('company.is_deleted', 0)->where('company.is_active', 1);

            if ($this->rp['adminmodule']['company']['alldata'] != 1) {
                $companyres->where('company.id', $this->companyId);
            } else {
                $companyres->where('company.created_by', $this->userId)->orWhere('company.id', $this->companyId);
            }
            $company = $companyres->get();
        }

        if ($this->rp['adminmodule']['company']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($company->count() > 0) {
            return $this->successresponse(200, 'company', $company);
        } else {
            return $this->successresponse(404, 'company', 'No Records Found');
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
        // validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'nullable|string|max:50',
            'email_default_user' => 'required|string|max:50',
            'contact_number' => 'required|numeric|digits:10',
            'house_no_building_name' => 'required|string|max:255',
            'road_name_area_colony' => 'required|string|max:255',
            'gst_number' => 'nullable|string|max:50',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric',
            'maxuser' => 'nullable|numeric',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'sign_img' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'user_id' => 'required|numeric',
            'updated_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            // return error response
            return $this->errorresponse(422, $validator->messages());
        } else {

            if ($this->rp['adminmodule']['company']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            // check user email is not duplicate
            $checkuseremail = User::where('email', $request->email_default_user)->where('is_deleted', 0)->get();

            if (count($checkuseremail) > 0) {
                return $this->successresponse(500, 'message', 'This email id already exists , Please enter other email id');
            }


            $modifiedname = preg_replace('/[^a-zA-Z0-9_]+/', '_', $request->name);

            // If the cleaned name starts with a digit, prepend an underscore
            if (ctype_digit(substr($modifiedname, 0, 1))) {
                $modifiedname = '_' . $modifiedname;
            }

            // Set the dynamic database name (sanitize and format if necessary) 

            $host = $_SERVER['HTTP_HOST'];

            if ($host === 'localhost:8000') {
                // If the host is localhost
                $dbName = 'bj_local_' . $modifiedname . '_' . Str::lower(Str::random(3));
            } elseif ($host === 'staging.businessjoy.in') {
                // If the host is staging.businessjoy.in
                $dbName = 'staging_business_joy_' . $modifiedname . '_' . Str::lower(Str::random(3));
            } else {
                // For any other host, provide a default
                $dbName = 'business_joy_' . $modifiedname . '_' . Str::lower(Str::random(3));
            }

            // Create the dynamic database

            DB::connection(config('database.dynamic_connection'))->statement('CREATE DATABASE ' . $dbName);

            // Switch to the new database connection
            config([
                'database.connections.' . $dbName => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '3306'),
                    'database' => $dbName,
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

            // required migrations path
            $paths = [
                'database/migrations/individualcompanydb',
                'database/migrations/v1_1_1',
                'database/migrations/v1_2_1',
                'database/migrations/v2_0_0',
            ];

            // Run migrations only from the specified path
            foreach ($paths as $path) {
                Artisan::call('migrate', [
                    '--path' => $path,
                    '--database' => $dbName,
                ]);
            }

            config(['database.connections.dynamic_connection.database' => $dbName]);

            // Establish connection to the dynamic database
            DB::purge('dynamic_connection');
            DB::reconnect('dynamic_connection');

            // --------------------------------- add company code start

            $imageName = null;
            $sign_imageName = null;
            if (($request->hasFile('img') && $request->file('img') != null) || ($request->hasFile('sign_img') && $request->file('sign_img') != null)) {

                $image = $request->file('img');
                $sign_image = $request->file('sign_img');

                // Check if image file is uploaded
                if ($image) {
                    $imageName = $request->name . time() . '.' . $image->getClientOriginalExtension();
                    $image->move('uploads/', $imageName); // upload image
                }

                // Check if signature image file is uploaded
                if ($sign_image) {
                    $sign_imageName = $request->name . time() . 'sign.' . $sign_image->getClientOriginalExtension();
                    $sign_image->move('uploads/', $sign_imageName); // upload signature image
                }
            }

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
            ];

            // Check if $imageName is set, if yes, create 'img' field
            if (isset($imageName)) {
                $company_details_data['img'] = $imageName;
            }

            // Check if $sign_imageName is set, if yes, create 'pr_sign_img' field
            if (isset($sign_imageName)) {
                $company_details_data['pr_sign_img'] = $sign_imageName;
            }

            $company_details = DB::table('company_details')->insertGetId($company_details_data); // insert company details

            if ($company_details) {
                $company_details_id = $company_details;
                $company = DB::table('company')->insertGetId([   // insert company record
                    'company_details_id' => $company_details_id,
                    'dbname' => $dbName,
                    'app_version' => $_SESSION['folder_name'],
                    'max_users' => $request->maxuser,
                    'created_by' => $this->userId,
                ]);

                $this->invoice_other_settingModel::create([  // default other settings insert
                    'overdue_day' => 45,
                    'year_start' => date('Y-m-d', strtotime('2024-04-01')),
                    'sgst' => 9,
                    'cgst' => 9,
                    'gst' => 0,
                    'customer_id' => 1,
                    'current_customer_id' => 1,
                    'created_by' => $this->userId,
                ]);

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
                                "invoice" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "mngcol" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "formula" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "invoicesetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "bank" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "customer" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "invoicenumbersetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "invoicetandcsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "invoicestandardsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "invoicegstsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "invoicecustomeridsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                            ],
                            "leadmodule" => [
                                "lead" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                            ],
                            "customersupportmodule" => [
                                "customersupport" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                            ],
                            "adminmodule" => [
                                "company" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null, "max" => null],
                                "user" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "techsupport" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "userpermission" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                            ],
                            "inventorymodule" => [
                                "product" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                            ],
                            "accountmodule" => [
                                "purchase" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
                            ],
                            "remindermodule" => [
                                "reminder" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "remindercustomer" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                            ],
                            "reportmodule" => [
                                "report" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null, "log" => null]
                            ],
                            "blogmodule" => [
                                "blog" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null,]
                            ],
                            "quotationmodule" => [
                                "quotation" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "quotationmngcol" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "quotationformula" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "quotationsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "quotationnumbersetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "quotationtandcsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "quotationstandardsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "quotationgstsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                "quotationcustomer" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                            ],
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
                            return $this->successresponse(500, 'message', 'User Permission Not succesfully added');
                        }
                    } else {
                        return $this->successresponse(500, 'message', 'User Not succesfully added');
                    }
                } else {
                    $id = $company; // companyid
                    $record = company::find($id);
                    $companydetails = company_detail::find($company_details_id);
                    // Check if the record exists
                    if ($record && $companydetails) {
                        // Delete the record
                        $record->delete();
                        $companydetails->delete();
                    }
                    return $this->successresponse(500, 'message', 'Oops ! Something Went wrong');
                }
            } else {
                return $this->successresponse(500, 'message', 'Oops ! Company details not succesfully create');
            }
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
        if ($company) {
            return $this->successresponse(200, 'company', $company);
        } else {
            return $this->successresponse(404, 'message', "No Such company Found!");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->where('company.id', $id)->get();
        if ($this->rp['adminmodule']['company']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($company) {
            return $this->successresponse(200, 'company', $company);
        } else {
            return $this->successresponse(404, 'message', "No Such company Found!");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'nullable|string|max:50',
            'contact_number' => 'required|numeric|digits:10',
            'house_no_building_name' => 'required|string|max:255',
            'road_name_area_colony' => 'required|string|max:255',
            'gst_number' => 'nullable|string|max:50',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric',
            'maxuser' => 'nullable|numeric',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'sign_img' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'user_id' => 'numeric',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            // return error response if validator fails
            return $this->errorresponse(422, $validator->messages());
        } else {
            if ($this->rp['adminmodule']['company']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $company = company::join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->select('company_details.img', 'company_details.pr_sign_img')->where('company.id', $id)
                ->get();

            $imageName = $company[0]->img;
            $sign_imageName = $company[0]->pr_sign_img;

            if (($request->hasFile('img') && $request->file('img') != null) || ($request->hasFile('sign_img') && $request->file('sign_img') != null)) {

                $image = $request->file('img');
                $sign_image = $request->file('sign_img');

                if ($image) {
                    $imageName = $request->name . time() . '.' . $image->getClientOriginalExtension();
                    $image->move('uploads/', $imageName);
                }

                // Check if signature image file is uploaded
                if ($sign_image) {
                    $sign_imageName = $request->name . time() . 'sign.' . $sign_image->getClientOriginalExtension();
                    $sign_image->move('uploads/', $sign_imageName);
                }

            }

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
                'img' => $imageName,
                'pr_sign_img' => $sign_imageName
            ];

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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = company::find($id);

        if ($this->rp['adminmodule']['company']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        if ($company) {
            $company->update([
                'is_deleted' => 1
            ]);
        } else {
            return $this->successresponse(404, 'message', 'No Such company Found!');
        }
        $users = User::where('company_id', $id)->get();
        if ($users) {
            $users->each(function ($user) {
                $user->update([
                    'is_deleted' => 1
                ]);
            });
            return $this->successresponse(200, 'message', 'company succesfully deleted');
        }
    }

    // for join tables company to another table
    public function joincompany()
    {
        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->select('company.id', 'company_details.name')->where('company.is_deleted', 0)->where('company.is_active', 1)
            ->get();

        if ($company->count() > 0) {
            return $this->successresponse(200, 'company', $company);
        } else {
            return $this->successresponse(404, 'company', 'No Records Found');
        }
    }

    // company active/deactive function
    public function statusupdate(Request $request, string $id)
    {

        $company = company::find($id);

        if ($company) {
            $company->update([
                'is_active' => $request->status
            ]);
        } else {
            return $this->successresponse(404, 'message', 'No Such Company Found!');
        }

        $users = User::where('company_id', $id)->get();

        if ($this->rp['adminmodule']['company']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        if ($users) {
            $users->each(function ($user) use ($request) {
                $user->update([
                    'is_active' => $request->status
                ]);
            });
            return $this->successresponse(200, 'message', 'Comapny status succesfully updated');
        }
    }
}