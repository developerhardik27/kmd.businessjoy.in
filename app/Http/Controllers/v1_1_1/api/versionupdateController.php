<?php

namespace App\Http\Controllers\v1_1_1\api;

use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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
    public function updatecompanyversion(Request $request)
    {

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


                if ($company->app_version != $request->version) {
                    $paths = [];
                    switch ($request->version) {
                        case 'v1_1_1':
                            $paths = [
                                'database/migrations/v1_1_1',
                            ];
                            break;
                        // Add more cases as needed
                    }
  
                    if (!empty($paths)) {
                        // Run migrations only from the specified path
                        foreach ($paths as $path) {
                            Artisan::call('migrate', [
                                '--path' => $path,
                                '--database' => $company->dbname,
                            ]);
                        }
                    } 

                    config(['database.connections.dynamic_connection.database' => $company->dbname]);

                    // Establish connection to the dynamic database
                    DB::purge('dynamic_connection');
                    DB::reconnect('dynamic_connection');

                    switch ($request->version) {
                        case 'v1_1_1':
                            $getgstsettings = DB::connection('dynamic_connection')->table('invoice_other_settings')->select('sgst', 'cgst', 'gst')->first();
                            $gstsettings = json_encode([
                                'sgst' => $getgstsettings->sgst,
                                'cgst' => $getgstsettings->cgst,
                                'gst' => $getgstsettings->gst,
                            ]);
                            DB::connection('dynamic_connection')->table('invoices')->where('is_deleted', 0)->update([
                                'gstsettings' => $gstsettings
                            ]);
                            break;

                    }

                    $company->app_version = $request->version;
                    $company->save();


                    return $this->successresponse(200, 'message', 'Company version succesfully updated');

                }
                 else {
                    return $this->successresponse(500, 'message', 'This company is already in latest version.');
                }
            } else {
                return $this->successresponse(404, 'message', 'No such company found!');
            }
        }

    }
}
