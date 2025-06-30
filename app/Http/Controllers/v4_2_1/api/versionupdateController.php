<?php

namespace App\Http\Controllers\v4_2_1\api;

use Exception;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                                }else{
                                    $paths = [
                                        'database/migrations/v4_2_0',
                                    ];
                                }
                                break;
                            // Add more cases as needed
                        }

                        
                        if (!empty($paths)) {
                            // Run migrations only from the specified path and specific db
                            foreach ($paths as $path) {
                                try{
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
