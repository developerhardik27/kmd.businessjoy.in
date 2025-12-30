<?php

namespace App\Http\Controllers\v4_3_1\api;

use Throwable;
use App\Models\company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class commonController extends Controller
{
    public $companyVersion;

    public function dbname($id = null)
    {
        if ($id == null) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 500,
                'message' => 'Database Not Found'
            ]);
            die();
        }
        $dbname = company::find($id);

        $this->companyVersion = $dbname->app_version;
        config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

        // Establish connection to the dynamic database
        DB::purge('dynamic_connection');
        DB::reconnect('dynamic_connection');

        return true;
    }

    public function customerrorresponse()
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 403,
            'message' => 'Forbidden!'
        ]);
        die();
    }

    public function getmodel($model)
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $version = $_SESSION['folder_name'];
            return 'App\\Models\\' . $version . "\\" . $model;
        } else {
            return 'App\\Models\\v4_3_1\\' . $model;
        }
    }



    public function returnresponse()
    {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function successresponse($status, $statuskey, $message, $extrakey = null, $extrakeyvalue = null, $code = 200)
    {

        $response = [
            'status' => $status,
            $statuskey => $message
        ];
        if (isset($extrakey) && isset($extrakeyvalue)) {
            $response[$extrakey] = $extrakeyvalue;
        }

        return response()->json($response, $code);
    }

    public function errorresponse($status, $errorsdata, $code = 422)
    {
        if ($status == 422) {
            $response = [
                'status' => $status,
                'errors' => $errorsdata
            ];
            return response()->json($response, $code);
        }

        $response = [
            'status' => $status,
            "message" => $errorsdata
        ];

        return response()->json($response, 200);

    }


    protected function executeTransaction(callable $callback)
    {

        DB::beginTransaction(); // Start transaction on default connection
        DB::connection('dynamic_connection')->beginTransaction(); // Start transaction on dynamic_connection

        try {
            $result = $callback();

            DB::commit(); // Commit default connection
            DB::connection('dynamic_connection')->commit(); // Commit dynamic connection

            return $result;
        } catch (Throwable $e) {
            DB::rollBack(); // Rollback default connection
            DB::connection('dynamic_connection')->rollBack(); // Rollback dynamic connection

            Log::error("Transaction Rolled Back: " . $e->__toString());

            return $this->errorResponse(500, $e->getMessage());
        }
    }


}
