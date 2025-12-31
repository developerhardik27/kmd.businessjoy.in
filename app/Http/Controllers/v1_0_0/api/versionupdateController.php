<?php

namespace App\Http\Controllers\v1_0_0\api;

use Exception;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
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


                if ($company->app_version) {

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
    }
}
