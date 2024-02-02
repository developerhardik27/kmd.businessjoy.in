<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\sendmail;
use App\Models\company;
use App\Models\company_detail;
use App\Models\user_permission;
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

    public function companydetailspdf($id)
    {

        $companydetails =  DB::table('company_details')
            ->join('country', 'company_details.country_id', '=', 'country.id')
            ->join('state', 'company_details.state_id', '=', 'state.id')
            ->join('city', 'company_details.city_id', '=', 'city.id')
            ->select('company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.address', 'company_details.gst_no', 'company_details.pincode', 'company_details.img', 'country.country_name', 'state.state_name', 'city.city_name')
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

        $userId = $request->input('user_id');

        if ($userId == 1) {

            $company = DB::table('company')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->join('country', 'company_details.country_id', '=', 'country.id')
                ->join('state', 'company_details.state_id', '=', 'state.id')
                ->join('city', 'company_details.city_id', '=', 'city.id')
                ->select('company.id', 'company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.address', 'company_details.gst_no', 'country.country_name', 'state.state_name', 'city.city_name', 'company.created_by', 'company.updated_by', 'company.created_at', 'company.updated_at', 'company.is_active', 'company.is_deleted')
                ->where('company.is_deleted', 0)->where('company.is_active', 1)
                ->get();
        } else {
            $company = DB::table('company')
                ->join('company_details', 'company.company_details_id', '=', 'company_details.id')
                ->join('country', 'company_details.country_id', '=', 'country.id')
                ->join('state', 'company_details.state_id', '=', 'state.id')
                ->join('city', 'company_details.city_id', '=', 'city.id')
                ->select('company.id', 'company_details.name', 'company_details.email', 'company_details.contact_no', 'company_details.address', 'company_details.gst_no', 'country.country_name', 'state.state_name', 'city.city_name', 'company.created_by', 'company.updated_by', 'company.created_at', 'company.updated_at', 'company.is_active', 'company.is_deleted')
                ->where('company.is_deleted', 0)->where('company.is_active', 1)->where('company.id', $userId)
                ->get();
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
            'created_by' => 'required|numeric',
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
   

            // Set the dynamic database name (sanitize and format if necessary)
            $dbName = 'db_' . $request->name . '_' . Str::lower(Str::random(3));

            // Create the dynamic database

            DB::connection(config('database.dynamic_connection'))->statement('CREATE DATABASE ' . $dbName);



            // Switch to the new database connection
            config(['database.connections.' . $dbName => [
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
            ]]);
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
                $table->longText('notes', 255)->nullable();
                $table->float('total');
                $table->float('gst');
                $table->float('grand_total');
                $table->integer('currency_id');
                $table->string('payment_type', 30);
                $table->string('status', 30)->default('pending');
                $table->bigInteger('account_id');
                $table->float('template_version')->default(1);
                $table->integer('company_id');
                $table->integer('company_details_id');
                $table->longText('show_col')->nullable();
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
                $table->double('amount');
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
                $table->double('price_per_unit');
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
                $table->mediumText('assigned_to', 255)->nullable();
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
                $table->mediumText('assigned_to', 255)->nullable();
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
            // --------------------------------- add company code start
            if ($request->hasFile('img') && $request->file('img') != '') {
                $image = $request->file('img');
                $imageName = $request->name . time() . '.' . $image->getClientOriginalExtension();
                if (!file_exists('uploads/')) {
                    mkdir('uploads/', 0755, true);
                }
                // Save the image to the uploads directory
                if ($image->move('uploads/', $imageName)) {

                    $company_details = DB::table('company_details')->insertGetId([
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
                        'created_by' => $request->created_by
                    ]);

                    if ($company_details) {
                        $company_details_id = $company_details;
                        $company = DB::table('company')->insertGetId([
                            'company_detail_id' => $company_details_id,
                            'dbname' => $dbName,
                            'created_by' => $request->created_by,
                        ]);

                        if ($company) {
                            $password = Str::random(8);
                            $hashpassword =  Hash::make($password);
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
                                'created_by' => $request->created_by,
                            ]);
                            if ($user) {
                                $userid = $user;
                                $rp = [
                                    "invoicemodule" => [
                                        "invoice" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                        "company" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                        "bank" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                        "user" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                        "customer" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                        "product" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                        "purchase" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"]
                                    ],
                                    "leadmodule" => [
                                        "lead" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"]
                                    ],
                                    "customersupportmodule" => [
                                        "customersupport" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"]
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

                if ($company_details) {
                    $company_details_id = $company_details;
                    $company = DB::table('company')->insertGetId([
                        'company_details_id' => $company_details_id,
                        'dbname' => $dbName,
                        'created_by' => $request->created_by,
                    ]);

                    if ($company) {
                        $password = Str::random(8);
                        $hashpassword =  Hash::make($password);
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
                            'created_by' => $request->created_by,
                        ]);
                        if ($user) {
                            $userid = $user;
                            $rp = [
                                "invoicemodule" => [
                                    "invoice" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                    "company" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                    "bank" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                    "user" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                    "customer" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                    "product" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"],
                                    "purchase" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"]
                                ],
                                "leadmodule" => [
                                    "lead" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"]
                                ],
                                "customersupportmodule" => [
                                    "customersupport" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0"]
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
            'updated_by' => 'numeric',
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


            if ($request->hasFile('img') && $request->hasFile('img') != '') {
                $image = $request->file('img');
                $imageName = $request->name . time() . '.' . $image->getClientOriginalExtension();

                // $company = company::join('company_details','company.company_details_id','=','company_details.id')
                //            ->select('company_details.img')->where('company.id',$id)
                //            ->get();
                // if ($company) {
                //     $imagePath = public_path('uploads/' . $company[0]->img); // old img remove
                //     if (is_file($imagePath)) {
                //         unlink($imagePath);
                //     }
                // }

                if ($image->move('uploads/', $imageName)) {
                    $company_details = DB::table('company_details')->insertGetId([
                        'name' => $request->name,
                        'email' => $request->email,
                        'contact_no' => $request->contact_number,
                        'address' => $request->address,
                        'country_id' => $request->country,
                        'state_id' => $request->state,
                        'city_id' => $request->city,
                        'pincode' => $request->pincode,
                        'gst_no' => $request->gst_number,
                        'img' => $imageName
                    ]);
                    if ($company_details) {
                        $company_details_id = $company_details;
                        $company = company::find($id);
                        if ($company) {

                            $companyupdate =  $company->update([
                                'company_details_id' => $company_details_id,
                                'updated_by' => $request->updated_by,

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
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'image not succesfully upload'
                    ]);
                }
            } else {
                $company = company::join('company_details', 'company.company_details_id', '=', 'company_details.id')
                    ->select('company_details.img')->where('company.id', $id)
                    ->get();
                $company_details = DB::table('company_details')->insertGetId([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_number,
                    'address' => $request->address,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                    'pincode' => $request->pincode,
                    'gst_no' => $request->gst_number,
                    'img' => $company[0]->img
                ]);
                if ($company_details) {
                    $company_details_id = $company_details;
                    $company = company::find($id);
                    if ($company) {

                        $companyupdate =  $company->update([
                            'company_details_id' => $company_details_id,
                            'updated_by' => $request->updated_by,

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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = company::find($id);

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
