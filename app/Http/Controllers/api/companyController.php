<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\sendmail;
use App\Models\company;
use App\Models\company_detail;
use App\Models\user_permission;
use App\Models\invoice_other_setting;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class companyController extends Controller
{
    public $userId, $companyId, $rp;
    public function __construct(Request $request)
    {

        if (session()->get('company_id')) {
            $this->companyId = session()->get('company_id');
        } else {
            $this->companyId = $request->company_id;
        }
        if (session()->get('user_id')) {
            $this->userId = session()->get('user_id');
        } else {
            $this->userId = $request->user_id;
        }

        $dbname = company::find($this->companyId);
        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        $this->rp = json_decode($permissions[0]['rp'], true);
    }



    public function companydetailspdf($id)
    {

        $companydetails = DB::table('company_details')
            ->join('country', 'company_details.country_id', '=', 'country.id')
            ->join('state', 'company_details.state_id', '=', 'state.id')
            ->join('city', 'company_details.city_id', '=', 'city.id')
            ->select('company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.address', 'company_details.gst_no', 'company_details.pincode', 'company_details.img','company_details.pr_sign_img', 'country.country_name', 'state.state_name', 'city.city_name')
            ->where('company_details.id', $id)->get();

        if ($companydetails->count() > 0) {
            return response()->json([
                'status' => 200,
                'companydetails' => $companydetails
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'companydetails' => 'No Records Found'
            ], 404);
        }
    }

    public function companyprofile(Request $request)
    {

        $companyId = $request->input('company_id');

        $company = DB::table('company')
            ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
            ->join('country', 'company_details.country_id', '=', 'country.id')
            ->join('state', 'company_details.state_id', '=', 'state.id')
            ->join('city', 'company_details.city_id', '=', 'city.id')
            ->select('company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.address', 'company_details.gst_no', 'company_details.pincode', 'company_details.img', 'country.country_name', 'state.state_name', 'city.city_name')
            ->where('company.id', $companyId)
            ->get();

        if ($this->rp['invoicemodule']['company']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized!'
            ]);
        }


        if ($company->count() > 0) {
            return response()->json([
                'status' => 200,
                'company' => $company
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'company' => 'No Records Found'
            ], 404);
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
                ->select('company.id', 'company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.address', 'company_details.gst_no', 'country.country_name', 'state.state_name', 'city.city_name', 'company.created_by', 'company.updated_by', 'company.created_at', 'company.updated_at', 'company.is_active', 'company.is_deleted')
                ->where('company.is_deleted', 0)->where('company.is_active', 1)
                ->get();
        } else {
            $companyres = DB::table('company')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->join('country', 'company_details.country_id', '=', 'country.id')
                ->join('state', 'company_details.state_id', '=', 'state.id')
                ->join('city', 'company_details.city_id', '=', 'city.id')
                ->select('company.id', 'company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.address', 'company_details.gst_no', 'country.country_name', 'state.state_name', 'city.city_name', 'company.created_by', 'company.updated_by', 'company.created_at', 'company.updated_at', 'company.is_active', 'company.is_deleted')
                ->where('company.is_deleted', 0)->where('company.is_active', 1);

            if ($this->rp['invoicemodule']['company']['alldata'] != 1) {
                $companyres->where('company.id', $this->companyId);
            } else {
                $companyres->where('company.created_by', $this->userId)->orWhere('company.id', $this->companyId);
            }
            $company = $companyres->get();
        }

        if ($this->rp['invoicemodule']['company']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized!'
            ]);
        }

        if ($company->count() > 0) {
            return response()->json([
                'status' => 200,
                'company' => $company
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'company' => 'No Records Found'
            ], 404);
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


        $validator = Validator::make($request->all(), [

            'name' => 'required|string|max:50',
            'email' => 'required|string|max:50',
            'contact_number' => 'required|numeric|digits:10',
            'address' => 'required|string|max:255',
            'gst_number' => 'required|string|max:50',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric|digits:6',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sign_img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'user_id' => 'required|numeric',
            'updated_by',
            'created_at',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            if ($this->rp['invoicemodule']['company']['add'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized!'
                ]);
            }

            // Set the dynamic database name (sanitize and format if necessary)
            $dbName = 'bj_' . $request->name . '_' . Str::lower(Str::random(3));

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
            // *** invoice module table start ***

            // bank details table 
            Schema::connection($dbName)->create('bank_details', function (Blueprint $table) {
                $table->id();
                $table->string('holder_name', 50);
                $table->string('account_no', 50);
                $table->string('swift_code', 20)->nullable();
                $table->string('ifsc_code', 20);
                $table->string('branch_name', 50);
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
            });


            // customers table 
            Schema::connection($dbName)->create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('firstname', 50);
                $table->string('lastname', 50);
                $table->string('company_name', 50);
                $table->string('email', 50);
                $table->string('contact_no', 15);
                $table->string('address', 255);
                $table->integer('country_id');
                $table->integer('state_id');
                $table->integer('city_id');
                $table->integer('pincode');
                $table->string('gst_no', 50)->nullable();
                $table->integer('company_id');
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
            });


            // invoice table 
            Schema::connection($dbName)->create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('inv_no', 30);
                $table->date('inv_date')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->integer('customer_id');
                $table->longText('notes')->nullable();
                $table->float('total', 10, 2);
                $table->float('gst');
                $table->float('grand_total', 10, 2);
                $table->integer('currency_id');
                $table->string('payment_type', 30);
                $table->string('status', 30)->default('pending');
                $table->bigInteger('account_id');
                $table->float('template_version')->default(1);
                $table->integer('company_id');
                $table->integer('company_details_id');
                $table->longText('show_col')->nullable();
                $table->integer('overdue_date')->nullable();
                $table->integer('t_and_c_id')->nullable();
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
            });

            // invoice_other_settings table
            Schema::connection($dbName)->create('invoice_other_settins', function (Blueprint $table) {
                $table->id();
                $table->integer('overdue_day')->nullable();
                $table->date('year_start')->nullable()->default('2024-04-01');
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
            });

            // invoice_terms_and_conditions table
            Schema::connection($dbName)->create('invoice_terms_and_conditions', function (Blueprint $table) {
                $table->id();
                $table->longText('t_anc_c')->nullable();
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
            });

            // tbl invoice column table 
            Schema::connection($dbName)->create('tbl_invoice_columns', function (Blueprint $table) {
                $table->id();
                $table->string('column_name', 50);
                $table->string('column_type', 50);
                $table->integer('column_order')->nullable();
                $table->tinyInteger('is_hide')->default(0);
                $table->integer('company_id');
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
            });

            // tbl invoice formula table
            Schema::connection($dbName)->create('tbl_invoice_formulas', function (Blueprint $table) {
                $table->id();
                $table->string('first_column', 50);
                $table->char('operation', 1);
                $table->string('second_column', 50);
                $table->string('output_column', 50);
                $table->integer('formula_order')->nullable();
                $table->integer('company_id');
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
            });

            // purchases table 
            Schema::connection($dbName)->create('purchases', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50);
                $table->mediumText('description');
                $table->double('amount', 10, 2);
                $table->string('amount_type', 20);
                $table->date('date');
                $table->string('img', 100)->nullable();
                $table->integer('company_id');
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
            });

            // payment details table
            Schema::connection($dbName)->create('payment_details', function (Blueprint $table) {
                $table->id();
                $table->integer('inv_id');
                $table->string('transaction_id', 50)->nullable();
                $table->date('datetime')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->string('paid_by', 30);
                $table->string('paid_type', 30);
                $table->double('amount', 10, 2);
                $table->double('paid_amount', 10, 2);
                $table->double('pending_amount', 10, 2);
                $table->integer('part_payment')->default(0);
                $table->date('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->date('updated_at')->nullable();
            });

            // products table 
            Schema::connection($dbName)->create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50);
                $table->longText('description');
                $table->string('product_code', 50);
                $table->string('unit', 10);
                $table->double('price_per_unit', 10, 2);
                $table->integer('company_id');
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
            });

            // manage column (for invoice details) dynamic table
            Schema::connection($dbName)->create('mng_col', function (Blueprint $table) {
                $table->id(); // Auto-incrementing primary key
                $table->integer('invoice_id');
                $table->integer('amount');
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
            });

            // user permission table
            Schema::connection($dbName)->create('user_permissions', function (Blueprint $table) {
                $table->id(); // Auto-incrementing primary key
                $table->integer('user_id');
                $table->longText('rp');
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
            });

            // *** invoice module table end ***
            // -----------------------------------
            // *** lead module table start ***

            // tbl lead table 

            Schema::connection($dbName)->create('tbllead', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->nullable();
                $table->string('email', 50)->nullable();
                $table->string('contact_no', 13)->nullable();
                $table->string('title', 100)->nullable();
                $table->string('budget', 100)->nullable();
                $table->string('company', 50)->nullable();
                $table->string('audience_type', 50)->nullable();
                $table->string('customer_type', 50)->nullable();
                $table->string('status', 30)->default('New Lead');
                $table->date('last_follow_up')->nullable();
                $table->date('next_follow_up')->nullable();
                $table->integer('number_of_follow_up')->default(0);
                $table->longText('notes')->nullable();
                $table->string('lead_stage', 30)->default('New Lead');
                $table->mediumText('assigned_to')->nullable();
                $table->integer('assigned_by')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
                $table->string('source', 100)->nullable();
                $table->string('ip', 100)->nullable();
                $table->text('country')->nullable();
                $table->text('msg_from_lead')->nullable();
                $table->integer('attempt_lead')->nullable();
            });

            // tbl lead history table
            Schema::connection($dbName)->create('tblleadhistory', function (Blueprint $table) {
                $table->id();
                $table->dateTime('call_date');
                $table->longText('history_notes')->nullable();
                $table->string('call_status', 20)->nullable();
                $table->tinyInteger('is_active')->default(1);
                $table->tinyInteger('is_deleted')->default(0);
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->integer('leadid');
                $table->integer('companyid')->nullable();
            });

            // *** lead module table end ***
            // ---------------------------------
            // *** customer support module table start ***
            // customer support table 

            Schema::connection($dbName)->create('customer_support', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->nullable();
                $table->string('email', 50)->nullable();
                $table->string('contact_no', 13)->nullable();
                $table->string('title', 100)->nullable();
                $table->string('budget', 100)->nullable();
                $table->string('audience_type', 50)->nullable();
                $table->string('customer_type', 50)->nullable();
                $table->string('status', 30)->default('Open');
                $table->date('last_call')->nullable();
                $table->date('next_call')->nullable();
                $table->integer('number_of_call')->default(0);
                $table->longText('notes')->nullable();
                $table->string('ticket', 50)->nullable();
                $table->mediumText('assigned_to')->nullable();
                $table->integer('assigned_by')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
                $table->string('source', 100)->nullable();
                $table->string('ip', 100)->nullable();
                $table->text('country')->nullable();
            });

            // customer suppport history table 

            Schema::connection($dbName)->create('customersupporthistory', function (Blueprint $table) {
                $table->id();
                $table->dateTime('call_date');
                $table->longText('history_notes')->nullable();
                $table->string('call_status', 20)->nullable();
                $table->tinyInteger('is_active')->default(1);
                $table->tinyInteger('is_deleted')->default(0);
                $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->integer('csid');
                $table->integer('companyid')->nullable();
            });

            // *** customer support module table end ***


            config(['database.connections.dynamic_connection.database' => $dbName]);

            // Establish connection to the dynamic database
            DB::purge('dynamic_connection');
            DB::reconnect('dynamic_connection');

            // --------------------------------- add company code start
            if (($request->hasFile('img') && $request->file('img') != null) || ($request->hasFile('sign_img') && $request->file('sign_img') != null)) {
                $image = $request->file('img');
                $sign_image = $request->file('sign_img');

                // Check if image file is uploaded
                if ($image) {
                    $imageName = $request->name . time() . '.' . $image->getClientOriginalExtension();
                    $imagemove = $image->move('uploads/', $imageName);
                }

                // Check if signature image file is uploaded
                if ($sign_image) {
                    $sign_imageName = $request->name . time() . '.' . $sign_image->getClientOriginalExtension();
                    $sign_imagemove = $sign_image->move('uploads/', $sign_imageName);
                }
                 
                $company_details = null ;
                // Check if either of the file moves were successful
                if (isset($imagemove) || isset($sign_imagemove)) {
                    $company_details_data = [
                        'name' => $request->name,
                        'email' => $request->email,
                        'contact_no' => $request->contact_number,
                        'address' => $request->address,
                        'country_id' => $request->country,
                        'state_id' => $request->state,
                        'city_id' => $request->city,
                        'pincode' => $request->pincode,
                        'gst_no' => $request->gst_number,
                    ];

                    // Check if $imageName is set, if yes, update 'img' field
                    if (isset($imageName)) {
                        $company_details_data['img'] = $imageName;
                    }

                    // Check if $sign_imageName is set, if yes, update 'pr_sign_img' field
                    if (isset($sign_imageName)) {
                        $company_details_data['pr_sign_img'] = $sign_imageName;
                    }

                    $company_details = DB::table('company_details')->insertGetId($company_details_data);

                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'image not succesfully upload'
                    ]);
                }
            } else {
                $company_details = DB::table('company_details')->insertGetId([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_number,
                    'address' => $request->address,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                    'pincode' => $request->pincode,
                    'gst_no' => $request->gst_number
                ]);
            }
            if ($company_details) {
                $company_details_id = $company_details;
                $company = DB::table('company')->insertGetId([
                    'company_details_id' => $company_details_id,
                    'dbname' => $dbName,
                    'created_by' => $this->userId,
                ]);
                 
                invoice_other_setting::create([
                    
                ]);
        
                if ($company) {
                    $password = Str::random(8);
                    $hashpassword = Hash::make($password);
                    $company_id = $company;

                    $user = DB::table('users')->insertGetId([
                        'firstname' => $request->name,
                        'email' => $request->email,
                        'password' => $hashpassword,
                        'role' => 2,
                        'contact_no' => $request->contact_number,
                        'country_id' => $request->country,
                        'state_id' => $request->state,
                        'city_id' => $request->city,
                        'pincode' => $request->pincode,
                        'company_id' => $company_id,
                        'created_by' => $this->userId,
                    ]);
                    if ($user) {
                        $userid = $user;
                        $rp = [
                            "invoicemodule" => [
                                "invoice" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                                "mngcol" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                                "formula" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                                "invoicesetting" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                                "company" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                                "bank" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                                "user" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                                "customer" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                                "product" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                                "purchase" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"]
                            ],
                            "leadmodule" => [
                                "lead" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"]
                            ],
                            "customersupportmodule" => [
                                "customersupport" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"]
                            ]
                        ];

                        $rpjson = json_encode($rp);

                        $userrp = user_permission::create([
                            'user_id' => $userid,
                            'rp' => $rpjson
                        ]);

                        if ($userrp) {
                            Mail::to($request->email)->cc('parthdeveloper9@gmail.com')->send(new sendmail($request->email, $password));
                            return response()->json([
                                'status' => 200,
                                'message' => 'Company succesfully added'
                            ], 200);
                        } else {
                            return response()->json([
                                'status' => 500,
                                'message' => 'User Permission Not succesfully added'
                            ], 500);
                        }
                    } else {
                        return response()->json([
                            'status' => 500,
                            'message' => 'User Not succesfully added'
                        ], 500);
                    }
                } else {
                    $id = $company;
                    $record = company::find($id);
                    $companydetails = company_detail::find($company_details_id);
                    // Check if the record exists
                    if ($record && $companydetails) {
                        // Delete the record
                        $record->delete();
                        $companydetails->delete();
                    }
                    return response()->json([
                        'status' => 500,
                        'message' => 'Oops ! Something Went wrong'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Oops ! Something Went wrong'
                ], 500);
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

        if ($this->rp['invoicemodule']['company']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($company) {
            return response()->json([
                'status' => 200,
                'company' => $company
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'company' => $company,
                'message' => "No Such company Found!"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $company = DB::table('company')->join('company_details', 'company.company_details_id', '=', 'company_details.id')->where('company.id', $id)->get();
        if ($this->rp['invoicemodule']['company']['edit'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }

        if ($company) {
            return response()->json([
                'status' => 200,
                'company' => $company
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'company' => $company,
                'message' => "No Such company Found!"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|string|max:50',
            'contact_number' => 'required|numeric|digits:10',
            'address' => 'required|string|max:255',
            'gst_number' => 'required|string|max:50',
            'country' => 'required|numeric',
            'state' => 'required|numeric',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric|digits:6',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sign_img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'user_id' => 'numeric',
            'updated_at',
            'is_active',
            'is_deleted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            if ($this->rp['invoicemodule']['company']['edit'] != 1) {
                return response()->json([
                    'status' => 500,
                    'message' => 'You are Unauthorized'
                ]);
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
                    $sign_imageName = $request->name . time() . '.' . $sign_image->getClientOriginalExtension();
                    $sign_image->move('uploads/', $sign_imageName);
                }

            }

            $company_details_data = [
                'name' => $request->name,
                'email' => $request->email,
                'contact_no' => $request->contact_number,
                'address' => $request->address,
                'country_id' => $request->country,
                'state_id' => $request->state,
                'city_id' => $request->city,
                'pincode' => $request->pincode,
                'gst_no' => $request->gst_number,
                'img' => $imageName,
                'pr_sign_img' => $sign_imageName
            ];

            $company_details = DB::table('company_details')->insertGetId($company_details_data);
            if ($company_details) {
                $company_details_id = $company_details;
                $company = company::find($id);
                if ($company) {
                    $companyupdate = $company->update([
                        'company_details_id' => $company_details_id,
                        'updated_by' => $this->userId,
                    ]);

                    if ($companyupdate) {
                        return response()->json([
                            'status' => 200,
                            'message' => 'company succesfully updated'
                        ]);
                    } else {
                        $company_details = company_detail::find($company_details_id);
                        $company_details->delete();
                        return response()->json([
                            'status' => 200,
                            'message' => 'company not succesfully updated'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'No Such company Found!'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Oops ! Something Went wrong'
                ], 500);
            }

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = company::find($id);

        if ($this->rp['invoicemodule']['company']['delete'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized'
            ]);
        }
        if ($company) {
            $company->update([
                'is_deleted' => 1
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'company succesfully deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Such company Found!'
            ], 404);
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
            return response()->json([
                'status' => 200,
                'company' => $company
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'company' => 'No Records Found'
            ], 404);
        }
    }
}
