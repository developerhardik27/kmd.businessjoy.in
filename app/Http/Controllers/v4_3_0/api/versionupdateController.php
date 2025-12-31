<?php

namespace App\Http\Controllers\v4_3_0\api;

use App\Models\tech_support;
use App\Models\User;
use Exception;
use App\Models\company;
use Illuminate\Http\Request;
use App\Models\company_detail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class versionupdateController extends commonController
{
    public $companyId, $userId;
    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
    }

    /**
     * Summary of updatecompanyversion
     * upgrade company version
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */

    public function updatecompanyversion(Request $request)
    {

        return $this->executeTransaction(function () use ($request) {
            $validator = Validator::make($request->all(), [
                'company' => 'required',
                'version' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->errorresponse(422, $validator->messages());
            } else {

                $company = company::find($request->company);


                if ($company) {
                    config([
                        'database.connections.' . $company->dbname => [
                            'driver' => 'mysql',
                            'host' => env('DB_HOST', '127.0.0.1'),
                            'port' => env('DB_PORT', '3306'),
                            'database' => $company->dbname,
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


                    if ($company->app_version) {
                        $paths = [];
                        switch ($request->version) {
                            case 'v1_1_1':
                                $paths = [
                                    'database/migrations/v1_1_1',
                                ];
                                if ($request->company == 1) {
                                    $paths = [
                                        'database/migrations/newmasterdbtable',
                                    ];
                                }
                                break;
                            case 'v1_2_1':
                                $paths = [
                                    'database/migrations/v1_2_1',
                                ];
                                if ($request->company == 1) {
                                    $paths = [
                                        'database/migrations/newmasterdbtable',
                                    ];
                                }
                                break;
                            case 'v2_0_0':
                                if ($request->company != 1) {
                                    $paths = [
                                        'database/migrations/v2_0_0',
                                    ];
                                }
                                break;
                            case 'v3_0_0':
                                if ($request->company != 1) {
                                    $paths = [
                                        'database/migrations/v3_0_0',
                                    ];
                                } else {
                                    $paths = [
                                        'database/migrations/newmasterdbtable',
                                    ];
                                }
                                break;
                            case 'v4_0_0':
                                if ($request->company != 1) {
                                    $paths = [
                                        'database/migrations/v4_0_0',
                                    ];
                                } else {
                                    $paths = [
                                        'database/migrations/newmasterdbtable',
                                    ];
                                }
                                break;
                            case 'v4_1_0':
                                if ($request->company != 1) {
                                    $paths = [
                                        'database/migrations/v4_1_0',
                                    ];
                                }
                                break;
                            case 'v4_2_0':
                                if ($request->company == 1) {
                                    $paths = [
                                        'database/migrations/newmasterdbtable',
                                    ];
                                } else {
                                    $paths = [
                                        'database/migrations/v4_2_0',
                                    ];
                                }
                                break;
                            case 'v4_2_1':
                                if ($request->company == 1) {
                                    $paths = [
                                        'database/migrations/v4_2_1/master',
                                    ];
                                } else {
                                    $paths = [
                                        'database/migrations/v4_2_1/individual',
                                    ];
                                }
                                break;
                            case 'v4_2_2':
                                if ($request->company == 1) {
                                    $paths = [
                                        'database/migrations/v4_2_2/master',
                                    ];
                                } else {
                                    $paths = [
                                        'database/migrations/v4_2_2/individual',
                                    ];
                                }
                                break;
                            case 'v4_2_3':
                                if ($request->company != 1) {
                                    $paths = [
                                        'database/migrations/v4_2_3/individual',
                                    ];
                                }
                                break;
                            case 'v4_3_0':
                                if ($request->company != 1) {
                                    $paths = [
                                        'database/migrations/v4_3_0/individual',
                                    ];
                                } else {
                                    $paths = [
                                        'database/migrations/v4_3_0/master',
                                    ];
                                }
                                break;
                            case 'v4_3_1':
                                if ($request->company != 1) {
                                    $paths = [
                                        'database/migrations/v4_3_1/individual',
                                    ];
                                } else {
                                    $paths = [
                                        'database/migrations/v4_3_1/master',
                                    ];
                                }
                                break;

                                // Add more cases as needed
                        }


                        if (!empty($paths)) {
                            // Run migrations only from the specified path and specific db
                            foreach ($paths as $path) {
                                try {
                                    Artisan::call('migrate', [
                                        '--path' => $path,
                                        '--database' => $company->dbname,
                                    ]);
                                } catch (Exception $e) {
                                    Log::error($e);
                                }
                            }
                        }



                        config(['database.connections.dynamic_connection.database' => $company->dbname]);

                        // Establish connection to the dynamic database
                        DB::purge('dynamic_connection');
                        DB::reconnect('dynamic_connection');

                        switch ($request->version) {
                            case 'v1_1_1':
                                try {
                                    if (Schema::connection('dynamic_connection')->hasTable('invoice_other_settings') && Schema::hasTable('invoices')) {
                                        $getGstSettings = DB::connection('dynamic_connection')
                                            ->table('invoice_other_settings')
                                            ->select('sgst', 'cgst', 'gst')
                                            ->first();

                                        if ($getGstSettings) {
                                            // Encode GST settings as JSON
                                            $gstSettingsJson = json_encode([
                                                'sgst' => $getGstSettings->sgst,
                                                'cgst' => $getGstSettings->cgst,
                                                'gst' => $getGstSettings->gst,
                                            ]);

                                            // Update 'invoices' table where 'gstsettings' is NULL or empty
                                            DB::connection('dynamic_connection')
                                                ->table('invoices')
                                                ->where('is_deleted', 0)
                                                ->whereIn('gstsettings', [null, ''])
                                                ->update(['gstsettings' => $gstSettingsJson]);
                                        }
                                    }
                                } catch (Exception $e) {
                                    Log::error($e);
                                }
                                break;
                            case 'v1_2_1':
                                $rp = DB::connection('dynamic_connection')->table('user_permissions')->get();
                                if ($rp) {
                                    foreach ($rp as $userrp) {
                                        $jsonrp = json_decode($userrp->rp, true);
                                        $newrp = [
                                            "invoicenumbersetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                            "invoicetandcsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                            "invoicestandardsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                            "invoicegstsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                            "invoicecustomeridsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                        ];

                                        if (!isset($jsonrp['invoicemodule']['invoicenumbersetting'])) {

                                            // Update the 'invoicemodule' section with new permissions
                                            $jsonrp['invoicemodule'] = array_merge($jsonrp['invoicemodule'], $newrp);

                                            // Encode updated permissions back to JSON
                                            $updatedRpJson = json_encode($jsonrp);
                                            // Update the database
                                            DB::connection('dynamic_connection')->table('user_permissions')
                                                ->where('user_id', $userrp->user_id)
                                                ->update(['rp' => $updatedRpJson]);
                                        }
                                    }
                                }
                                break;
                            case 'v2_0_0':
                                $rp = DB::connection('dynamic_connection')->table('user_permissions')->get();
                                if ($rp) {
                                    foreach ($rp as $userrp) {
                                        $jsonrp = json_decode($userrp->rp, true);
                                        $newrp = [
                                            'quotationmodule' => [
                                                "quotation" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "quotationmngcol" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "quotationformula" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "quotationsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "quotationnumbersetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "quotationtandcsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "quotationstandardsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "quotationgstsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "quotationcustomer" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                            ]
                                        ];

                                        if (!isset($jsonrp['quotationmodule'])) {

                                            // add the 'quotation module' section with new permissions
                                            $jsonrp = array_merge($jsonrp, $newrp);

                                            // Encode updated permissions back to JSON
                                            $updatedRpJson = json_encode($jsonrp);
                                            // Update the database
                                            DB::connection('dynamic_connection')->table('user_permissions')
                                                ->where('user_id', $userrp->user_id)
                                                ->update(['rp' => $updatedRpJson]);
                                        }
                                    }
                                }

                                if (Schema::connection('dynamic_connection')->hasTable('quotation_other_settings')) {
                                    $existingData = DB::connection('dynamic_connection')
                                        ->table('quotation_other_settings')
                                        ->count();

                                    if ($existingData == 0) {
                                        $setDefaultSettings = DB::connection('dynamic_connection')
                                            ->table('quotation_other_settings')
                                            ->insert([
                                                'overdue_day' => 30,
                                                'year_start' => date('Y-m-d', strtotime(date('Y') . '-04-01')),
                                                'sgst' => 9,
                                                'cgst' => 9,
                                                'gst' => 0,
                                                'customer_id' => 1,
                                                'current_customer_id' => 1,
                                                'created_by' => $this->userId,
                                            ]);
                                    }
                                }

                                break;
                            case 'v3_0_0':
                                $rp = DB::connection('dynamic_connection')->table('user_permissions')->get();
                                if ($rp) {
                                    foreach ($rp as $userrp) {
                                        $jsonrp = json_decode($userrp->rp, true);

                                        if (isset($jsonrp['accountmodule']['purchase'])) {

                                            unset($jsonrp['accountmodule']['purchase']);

                                            // Add or update the 'purchase' section in the 'inventorymodule'
                                            $jsonrp['inventorymodule']['purchase'] = ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null];
                                            $jsonrp['inventorymodule']['productcategory'] = ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null];
                                            $jsonrp['inventorymodule']['productcolumnmapping'] = ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null];
                                            $jsonrp['inventorymodule']['inventory'] = ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null];
                                            $jsonrp['inventorymodule']['supplier'] = ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null];

                                            // Encode updated permissions back to JSON
                                            $updatedRpJson = json_encode($jsonrp);
                                            // Update the database
                                            DB::connection('dynamic_connection')->table('user_permissions')
                                                ->where('user_id', $userrp->user_id)
                                                ->update(['rp' => $updatedRpJson]);
                                        }
                                    }
                                }
                                break;
                            case 'v4_0_0':
                                $rp = DB::connection('dynamic_connection')->table('user_permissions')->get();
                                if ($rp) {
                                    foreach ($rp as $userrp) {
                                        $jsonrp = json_decode($userrp->rp, true);
                                        $newrp = [
                                            'logisticmodule' => [
                                                "consignorcopy" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "logisticsettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "consignmentnotenumbersettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "consignorcopytandcsettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "consignee" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "consignor" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                            ]
                                        ];

                                        if (!isset($jsonrp['logisticmodule'])) {

                                            // add the 'logistic module' section with new permissions
                                            $jsonrp = array_merge($jsonrp, $newrp);

                                            // Encode updated permissions back to JSON
                                            $updatedRpJson = json_encode($jsonrp);
                                            // Update the database
                                            DB::connection('dynamic_connection')->table('user_permissions')
                                                ->where('user_id', $userrp->user_id)
                                                ->update(['rp' => $updatedRpJson]);
                                        }
                                    }
                                }

                                // add default setting for logistic module
                                if (Schema::connection('dynamic_connection')->hasTable('logistic_settings')) {
                                    $existingData = DB::connection('dynamic_connection')
                                        ->table('logistic_settings')
                                        ->count();

                                    if ($existingData == 0) {
                                        $setDefaultSettings = DB::connection('dynamic_connection')
                                            ->table('logistic_settings')
                                            ->insert([
                                                'created_by' => $this->userId,
                                            ]);
                                    }
                                }

                                break;
                            case 'v4_1_0':
                                $rp = DB::connection('dynamic_connection')->table('user_permissions')->get();
                                if ($rp) {
                                    foreach ($rp as $userrp) {
                                        $jsonrp = json_decode($userrp->rp, true);

                                        if (!isset($jsonrp['invoicemodule']['invoicedashboard'])) {
                                            $jsonrp['invoicemodule']['invoicedashboard'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['leadmodule']['leaddashboard'])) {
                                            $jsonrp['leadmodule']['leaddashboard'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                            $jsonrp['leadmodule']['upcomingfollowup'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                            $jsonrp['leadmodule']['analysis'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                            $jsonrp['leadmodule']['leadownerperformance'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                            $jsonrp['leadmodule']['recentactivity'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                            $jsonrp['leadmodule']['calendar'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['customersupportmodule']['customersupportdashboard'])) {
                                            $jsonrp['customersupportmodule']['customersupportdashboard'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['adminmodule']['admindashboard'])) {
                                            $jsonrp['adminmodule']['admindashboard'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['inventorymodule']['inventorydashboard'])) {
                                            $jsonrp['inventorymodule']['inventorydashboard'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['remindermodule']['reminderdashboard'])) {
                                            $jsonrp['remindermodule']['reminderdashboard'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['reportmodule']['reportdashboard'])) {
                                            $jsonrp['reportmodule']['reportdashboard'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['blogmodule']['blogdashboard'])) {
                                            $jsonrp['blogmodule']['blogdashboard'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['quotationmodule']['quotationdashboard'])) {
                                            $jsonrp['quotationmodule']['quotationdashboard'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['logisticmodule']['logisticdashboard'])) {
                                            $jsonrp['logisticmodule']['logisticdashboard'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        // Encode updated permissions back to JSON
                                        $updatedRpJson = json_encode($jsonrp);
                                        // Update the database
                                        DB::connection('dynamic_connection')->table('user_permissions')
                                            ->where('user_id', $userrp->user_id)
                                            ->update(['rp' => $updatedRpJson]);
                                    }
                                }

                                break;

                            case 'v4_2_0':
                                $rp = DB::connection('dynamic_connection')->table('user_permissions')->get();
                                if ($rp) {
                                    foreach ($rp as $userrp) {
                                        $jsonrp = json_decode($userrp->rp, true);
                                        $newrp = [
                                            'developermodule' => [
                                                "slowpage" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "errorlog" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "cronjob" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "techdoc" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                                "versiondoc" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                                            ]
                                        ];

                                        if (!isset($jsonrp['developermodule'])) {
                                            // add the 'developer module' section with new permissions
                                            $jsonrp = array_merge($jsonrp, $newrp);
                                        }

                                        if (!isset($jsonrp['logisticmodule']['logisticothersettings'])) {
                                            $jsonrp['logisticmodule']['logisticothersettings'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        if (!isset($jsonrp['invoicemodule']['invoiceapi'])) {
                                            $jsonrp['invoicemodule']['invoiceapi'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['leadmodule']['leadapi'])) {
                                            $jsonrp['leadmodule']['leadapi'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['customersupportmodule']['customersupportapi'])) {
                                            $jsonrp['customersupportmodule']['customersupportapi'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['adminmodule']['adminapi'])) {
                                            $jsonrp['adminmodule']['adminapi'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['inventorymodule']['inventoryapi'])) {
                                            $jsonrp['inventorymodule']['inventoryapi'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['remindermodule']['reminderapi'])) {
                                            $jsonrp['remindermodule']['reminderapi'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['reportmodule']['reportapi'])) {
                                            $jsonrp['reportmodule']['reportapi'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['blogmodule']['blogapi'])) {
                                            $jsonrp['blogmodule']['blogapi'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['quotationmodule']['quotationapi'])) {
                                            $jsonrp['quotationmodule']['quotationapi'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['logisticmodule']['logisticapi'])) {
                                            $jsonrp['logisticmodule']['logisticapi'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }


                                        // Encode updated permissions back to JSON
                                        $updatedRpJson = json_encode($jsonrp);
                                        // Update the database
                                        DB::connection('dynamic_connection')->table('user_permissions')
                                            ->where('user_id', $userrp->user_id)
                                            ->update(['rp' => $updatedRpJson]);
                                    }
                                }

                                break;

                            case 'v4_2_1':
                                $rp = DB::connection('dynamic_connection')->table('user_permissions')->get();
                                if ($rp) {
                                    foreach ($rp as $userrp) {
                                        $jsonrp = json_decode($userrp->rp, true);

                                        if (!isset($jsonrp['logisticmodule']['watermark'])) {
                                            $jsonrp['logisticmodule']['watermark'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        if (!isset($jsonrp['developermodule']['recentactivitydata'])) {
                                            $jsonrp['developermodule']['recentactivitydata'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        // Encode updated permissions back to JSON
                                        $updatedRpJson = json_encode($jsonrp);
                                        // Update the database
                                        DB::connection('dynamic_connection')->table('user_permissions')
                                            ->where('user_id', $userrp->user_id)
                                            ->update(['rp' => $updatedRpJson]);
                                    }
                                }

                                break;

                            case 'v4_2_2':

                                if ($company->id != 1) {
                                    $blogsettings = DB::connection('dynamic_connection')->table('blog_settings')
                                        ->count();

                                    if ($blogsettings == 0) {
                                        DB::connection('dynamic_connection')->table('blog_settings')
                                            ->insert([
                                                'details_endpoint' => '',
                                                'img_allowed_filetype' => 'jpg,jpeg,png',
                                                'img_max_size' => '10',
                                                'img_width' => '600',
                                                'img_height' => '400',
                                                'thumbnail_img_width' => '400',
                                                'thumbnail_img_height' => '266',
                                                'validate_dimension' => '0',
                                                'created_at' => now()
                                            ]);
                                    }
                                }

                                $directoryPath = public_path('uploads/') . $company->id;

                                if (!file_exists($directoryPath)) {
                                    mkdir($directoryPath, 0755, true);
                                    mkdir($directoryPath . '/blog', 0755, true);
                                    mkdir($directoryPath . '/product', 0755, true);
                                    mkdir($directoryPath . '/lead/attachments/', 0755, true);
                                    mkdir($directoryPath . '/lead/callhistory/', 0755, true);
                                }

                                // products img move
                                if ($company->id != 1) {
                                    $products = DB::connection('dynamic_connection')->table('products')->where('is_deleted', 0)->get();

                                    if (!$products->isEmpty()) {

                                        foreach ($products as $product) {
                                            $productImgs = $product->product_media;

                                            if (!empty($productImgs)) {
                                                $newImgName = [];
                                                $productImgs = explode(',', $productImgs);

                                                foreach ($productImgs as $productImg) {

                                                    $checkProductImg = public_path('uploads/products/' . $productImg);

                                                    if (File::exists($checkProductImg)) {
                                                        // Generate a new unique name for the productimg
                                                        $extension = File::extension($productImg);
                                                        $productImgNewName = uniqid() . '-' . time() . '.' . $extension;

                                                        $dirPath = public_path('uploads/' . $company->id . '/product/' . $product->id . '/');

                                                        if (!File::exists($dirPath)) {
                                                            File::makeDirectory($dirPath, 0755, true);
                                                        }

                                                        $destinationPath = $dirPath . $productImgNewName;

                                                        // Move file
                                                        File::move($checkProductImg, $destinationPath);

                                                        // Save the relative path to the DB
                                                        $newImgName[] = $company->id . '/product/' . $product->id . '/' . $productImgNewName;
                                                    }
                                                }

                                                $newImgString = implode(',', $newImgName);

                                                DB::connection('dynamic_connection')->table('products')->where('id', $product->id)->update([
                                                    'product_media' => $newImgString
                                                ]);
                                            }
                                        }
                                    }
                                }

                                // blog img move
                                if ($company->id != 1) {
                                    $blogs = DB::connection('dynamic_connection')->table('blogs')->where('is_deleted', 0)->get();

                                    if (!$blogs->isEmpty()) {
                                        foreach ($blogs as $blog) {
                                            $blogOldImg = $blog->img;

                                            if (!empty($blogOldImg)) {
                                                // Prepare paths
                                                $blogImagePath = public_path('blog/' . ltrim($blogOldImg, '/'));
                                                $thumbnailPath = public_path('blog/thumbnail/' . ltrim($blogOldImg, '/')); // ✅ Fixed missing slash

                                                // Generate new names
                                                $extension = pathinfo($blogOldImg, PATHINFO_EXTENSION);
                                                $newFileName = uniqid() . '-' . time() . '.' . $extension;
                                                $newThumbFileName = uniqid() . '-' . time() . '.' . $extension;

                                                $dateFolder = date('dmY');
                                                $baseDir = public_path("uploads/{$company->id}/blog/{$dateFolder}/");

                                                // Move main blog image
                                                if (File::exists($blogImagePath)) {
                                                    if (!File::exists($baseDir)) {
                                                        File::makeDirectory($baseDir, 0755, true);
                                                    }

                                                    $destinationImagePath = $baseDir . $newFileName;
                                                    File::move($blogImagePath, $destinationImagePath);

                                                    $blog->img = "{$company->id}/blog/{$dateFolder}/{$newFileName}";
                                                }

                                                // Move thumbnail image
                                                $thumbDir = $baseDir . 'thumbnail/';
                                                if (File::exists($thumbnailPath)) {
                                                    if (!File::exists($thumbDir)) {
                                                        File::makeDirectory($thumbDir, 0755, true);
                                                    }

                                                    $destinationThumbPath = $thumbDir . $newThumbFileName;
                                                    File::move($thumbnailPath, $destinationThumbPath);

                                                    $blog->thumbnail_img = "{$company->id}/blog/{$dateFolder}/thumbnail/{$newThumbFileName}";
                                                }

                                                // Save updated blog
                                                DB::connection('dynamic_connection')->table('blogs')->where('id', $blog->id)->update([
                                                    'img' => $blog->img,
                                                    'thumbnail_img' => $blog->thumbnail_img,
                                                ]);
                                            }
                                        }
                                    }
                                }

                                $company_details = company_detail::find($company->company_details_id);

                                if ($company_details) {
                                    $fields = ['img', 'pr_sign_img', 'watermark_img'];

                                    foreach ($fields as $field) {
                                        $value = $company_details->$field;
                                        if (!empty($value)) {
                                            $image = public_path('uploads/' . $company_details->$field);

                                            if (File::exists($image)) {
                                                // Generate a new unique name for the image
                                                $extension = File::extension($company_details->$field);
                                                $newName = uniqid() . '-' . time() . '.' . $extension;

                                                $dirPath = public_path('uploads/' . $company->id . '/');

                                                $destinationPath = $dirPath . $newName;

                                                // Move file
                                                File::move($image, $destinationPath);

                                                // Save the relative path to the DB
                                                $company_details->$field = $company->id . '/' . $newName;
                                            }
                                        }
                                    }

                                    // Save updated record
                                    $company_details->save();
                                }

                                // update user permissions and change img/attachments paths
                                $rp = DB::connection('dynamic_connection')->table('user_permissions')->get();
                                if ($rp) {

                                    foreach ($rp as $userrp) {

                                        if (!file_exists($directoryPath . '/user_' . $userrp->user_id)) { // create user folder for photos
                                            mkdir($directoryPath . '/user_' . $userrp->user_id, 0755, true);
                                            mkdir($directoryPath . '/techsupport/user_' . $userrp->user_id, 0755, true);
                                        }

                                        $jsonrp = json_decode($userrp->rp, true);

                                        if (!isset($jsonrp['developermodule']['developerdashboard'])) {
                                            $jsonrp['developermodule']['developerdashboard'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        if (!isset($jsonrp['adminmodule']['loginhistory'])) {
                                            $jsonrp['adminmodule']['loginhistory'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        if (!isset($jsonrp['blogmodule']['blogsettings'])) {
                                            $jsonrp['blogmodule']['blogsettings'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        // Encode updated permissions back to JSON
                                        $updatedRpJson = json_encode($jsonrp);
                                        // Update the database
                                        DB::connection('dynamic_connection')->table('user_permissions')
                                            ->where('user_id', $userrp->user_id)
                                            ->update(['rp' => $updatedRpJson]);


                                        // move img/files to new path and update path in db
                                        $user = User::find($userrp->user_id);

                                        if ($user) {
                                            $oldimg = $user->img;
                                            if (!empty($oldimg)) {
                                                $userimage = public_path('uploads/' . $user->img);

                                                if (File::exists($userimage)) {
                                                    // Generate a new unique name for the userimage
                                                    $extension = File::extension($user->img);
                                                    $userImgNewName = uniqid() . '-' . time() . '.' . $extension;

                                                    $dirPath = public_path('uploads/' . $company->id . '/user_' . $user->id . '/');

                                                    $destinationPath = $dirPath . $userImgNewName;

                                                    // Move file
                                                    File::move($userimage, $destinationPath);

                                                    // Save the relative path to the DB
                                                    $user->img = $company->id . '/user_' . $user->id . '/' . $userImgNewName;
                                                    $user->save();
                                                }
                                            }
                                        }

                                        //techsupport attachment move 
                                        $techSupports = tech_support::where('is_deleted', 0)->get();

                                        if (!$techSupports->isEmpty()) {
                                            foreach ($techSupports as $techSupport) {

                                                if (!in_array($techSupport->attachment, ['[]', ''])) {
                                                    $newImgName = [];
                                                    $attachments = json_decode($techSupport->attachment, true);

                                                    foreach ($attachments as $attachment) {
                                                        $checkAttachment = public_path('uploads/files/' . $attachment);

                                                        if (File::exists($checkAttachment)) {
                                                            // Generate a new unique name for the userimage
                                                            $extension = File::extension($attachment);
                                                            $newAttachmentName = uniqid() . '-' . time() . '.' . $extension;

                                                            $dirPath = public_path('uploads/' . $company->id . '/techsupport/' . $techSupport->id . '/');

                                                            if (!File::exists($dirPath)) {
                                                                File::makeDirectory($dirPath, 0755, true);
                                                            }

                                                            $destinationPath = $dirPath . $newAttachmentName;

                                                            // Move file
                                                            File::move($checkAttachment, $destinationPath);

                                                            // Save the relative path to the DB
                                                            $newImgName[] = $company->id . '/techsupport/' . $techSupport->id . '/' . $newAttachmentName;
                                                        }
                                                    }

                                                    if (!empty($newImgName)) {
                                                        $newImgString = json_encode($newImgName);

                                                        tech_support::where('id', $techSupport->id)->update([
                                                            'attachment' => $newImgString
                                                        ]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                break;

                            case 'v4_2_3':
                                $rp = DB::connection('dynamic_connection')->table('user_permissions')->get();
                                if ($rp) {
                                    foreach ($rp as $userrp) {
                                        $jsonrp = json_decode($userrp->rp, true);

                                        if (!isset($jsonrp['leadmodule']['import'])) {
                                            $jsonrp['leadmodule']['import'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        if (!isset($jsonrp['leadmodule']['export'])) {
                                            $jsonrp['leadmodule']['export'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        // Encode updated permissions back to JSON
                                        $updatedRpJson = json_encode($jsonrp);
                                        // Update the database
                                        DB::connection('dynamic_connection')->table('user_permissions')
                                            ->where('user_id', $userrp->user_id)
                                            ->update(['rp' => $updatedRpJson]);
                                    }
                                }
                                break;
                            case 'v4_3_0':
                                $rp = DB::connection('dynamic_connection')->table('user_permissions')->get();
                                if ($rp) {
                                    foreach ($rp as $userrp) {
                                        $jsonrp = json_decode($userrp->rp, true);

                                        if (!isset($jsonrp['invoicemodule']['tdsregister'])) {
                                            $jsonrp['invoicemodule']['tdsregister'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        if (!isset($jsonrp['leadmodule']['leadsettings'])) {
                                            $jsonrp['leadmodule']['leadsettings'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        if (!isset($jsonrp['logisticmodule']['transporterbilling'])) {
                                            $jsonrp['logisticmodule']['transporterbilling'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        if (!isset($jsonrp['logisticmodule']['downloadcopysetting'])) {
                                            $jsonrp['logisticmodule']['downloadcopysetting'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        if (!isset($jsonrp['developermodule']['cleardata'])) {
                                            $jsonrp['developermodule']['cleardata'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }

                                        // Encode updated permissions back to JSON
                                        $updatedRpJson = json_encode($jsonrp);
                                        // Update the database
                                        DB::connection('dynamic_connection')->table('user_permissions')
                                            ->where('user_id', $userrp->user_id)
                                            ->update(['rp' => $updatedRpJson]);
                                    }
                                }

                                if ($company->id != 1) {
                                    $leadsettings = DB::connection('dynamic_connection')->table('lead_settings')
                                        ->count();

                                    if ($leadsettings == 0) {
                                        DB::connection('dynamic_connection')->table('lead_settings')
                                            ->insert([
                                                'country' => 0,
                                                'state' => 0,
                                                'city' => 0,
                                                'autofill_value' => 'As Per User',
                                                'country_default_value' => null,
                                                'state_default_value' => null,
                                                'city_default_value' => null,
                                                'created_at' => now()
                                            ]);
                                    }
                                }
                                break;
                            case 'v4_3_1':
                                $rp = DB::connection('dynamic_connection')->table('user_permissions')->get();
                                if ($rp) {
                                    // update user permissions
                                    foreach ($rp as $userrp) {
                                        $jsonrp = json_decode($userrp->rp, true);
                                        // Encode updated permissions back to JSON
                                        if (!isset($jsonrp['logisticmodule']['lrcolumnmapping'])) {
                                            $jsonrp['logisticmodule']['lrcolumnmapping'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if(!isset($jsonrp['developermodule']['automatetest'])) {
                                            $jsonrp['developermodule']['automatetest'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (!isset($jsonrp['invoicemodule']['invoiceformsetting'])) {
                                            $jsonrp['invoicemodule']['invoiceformsetting'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if (isset($jsonrp['invoicemodule']['formsetting'])) {
                                            unset($jsonrp['invoicemodule']['formsetting']);
                                        }
                                        if (!isset($jsonrp['logisticmodule']['logisticformsetting'])) {
                                            $jsonrp['logisticmodule']['logisticformsetting'] = ["show" => 0, "add" => 0, "view" => 0, "edit" => 0, "delete" => 0, "alldata" => 0];
                                        }
                                        if(isset($jsonrp['logisticmodule']['formsetting'])) {
                                            unset($jsonrp['logisticmodule']['formsetting']);
                                        }
                                        $updatedRpJson = json_encode($jsonrp);
                                        // Update the database
                                        DB::connection('dynamic_connection')->table('user_permissions')
                                            ->where('user_id', $userrp->user_id)
                                            ->update(['rp' => $updatedRpJson]);
                                    }
                                }
                                if ($company->id != 1) {
                                    // get consignee from consignee table
                                    $consignees = DB::connection('dynamic_connection')->table('consignees')->get();

                                    // move consignee from consignee table to customers table and delete from consignee table
                                    if ($consignees->isNotEmpty()) {
                                        foreach ($consignees as $consignee) {
                                            $customer =  DB::connection('dynamic_connection')->table('customers')->insertGetId([
                                                'firstname' => $consignee->firstname,
                                                'lastname' => $consignee->lastname,
                                                'company_name' => $consignee->company_name,
                                                'email' => $consignee->email,
                                                'contact_no' => $consignee->contact_no,
                                                'house_no_building_name' => $consignee->house_no_building_name,
                                                'road_name_area_colony' => $consignee->road_name_area_colony,
                                                'country_id' => $consignee->country_id,
                                                'state_id' => $consignee->state_id,
                                                'city_id' => $consignee->city_id,
                                                'pincode' => $consignee->pincode,
                                                'gst_no' => $consignee->gst_no,
                                                'pan_number' => $consignee->pan_number,
                                                'customer_type' => 'consignee',
                                                'company_id' => $company->id,
                                                'created_by' => $consignee->created_by,
                                                'updated_by' => $consignee->updated_by,
                                                'created_at' => $consignee->created_at,
                                                'updated_at' => $consignee->updated_at,
                                                'is_active' => $consignee->is_active,
                                                'is_deleted' => $consignee->is_deleted,
                                            ]);

                                            DB::connection('dynamic_connection')->table('consignor_copy')
                                                ->where('consignee_id', $consignee->id)->update([
                                                    'consignee_id' => $customer
                                                ]);
                                        }
                                        DB::connection('dynamic_connection')->table('consignees')
                                            ->delete();
                                    }

                                    // get consignors from consignors table
                                    $consignors = DB::connection('dynamic_connection')->table('consignors')->get();

                                    // move consignors from consignors table to customers table and delete from consignors table
                                    if ($consignors->isNotEmpty()) {
                                        foreach ($consignors as $consignor) {
                                            $customer =  DB::connection('dynamic_connection')->table('customers')->insertGetId([
                                                'firstname' => $consignor->firstname,
                                                'lastname' => $consignor->lastname,
                                                'company_name' => $consignor->company_name,
                                                'email' => $consignor->email,
                                                'contact_no' => $consignor->contact_no,
                                                'house_no_building_name' => $consignor->house_no_building_name,
                                                'road_name_area_colony' => $consignor->road_name_area_colony,
                                                'country_id' => $consignor->country_id,
                                                'state_id' => $consignor->state_id,
                                                'city_id' => $consignor->city_id,
                                                'pincode' => $consignor->pincode,
                                                'gst_no' => $consignor->gst_no,
                                                'pan_number' => $consignor->pan_number,
                                                'customer_type' => 'consignor',
                                                'company_id' => $company->id,
                                                'created_by' => $consignor->created_by,
                                                'updated_by' => $consignor->updated_by,
                                                'created_at' => $consignor->created_at,
                                                'updated_at' => $consignor->updated_at,
                                                'is_active' => $consignor->is_active,
                                                'is_deleted' => $consignor->is_deleted,
                                            ]);

                                            DB::connection('dynamic_connection')->table('consignor_copy')
                                                ->where('consignor_id', $consignor->id)->update([
                                                    'consignor_id' => $customer
                                                ]);
                                        }
                                        DB::connection('dynamic_connection')->table('consignors')
                                            ->delete();
                                    }
                                }
                                break;
                        }

                        $company->app_version = $request->version;
                        $company->save();

                        return $this->successresponse(200, 'message', 'Company version succesfully updated');
                    } else {
                        return $this->successresponse(500, 'message', 'This company is already in latest version.');
                    }
                } else {
                    return $this->successresponse(404, 'message', 'No such company found!');
                }
            }
        });
    }
}
