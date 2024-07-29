<?php

namespace App\Http\Controllers\v1_1_0\api;

use App\Http\Controllers\Controller;
use App\Models\company;
use Illuminate\Support\Facades\DB;

class commonController extends Controller
{
    
    public function dbname(string $id = null){
        
        $dbname = company::find($id);
       
        if($dbname == null){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 500,
                'message' => 'Database Not Found'
            ]);
            die();
        }else{ 
   
            config(['database.connections.dynamic_connection.database' => $dbname->dbname]);

            // Establish connection to the dynamic database
            DB::purge('dynamic_connection');
            DB::reconnect('dynamic_connection');
            
            return true;
        }

    }

    public function getmodel($model)
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (isset($_SESSION['folder_name'])) {
            $version = $_SESSION['folder_name'];
            return 'App\\Models\\' . $version . "\\" . $model ;
        } else {
            return 'App\\Models\\v1_1_0\\' . $model;
        }
    }


    
    public function returnresponse(){
        return response()->json(['error' => 'Unauthorized'], 401);
    }



    public function successresponse($status , $statuskey , $message , $extrakey = null , $extrakeyvalue = null,$code = 200){
        
        $response = [
            'status' => $status,
            $statuskey => $message
        ];
        if(isset($extrakey) && isset($extrakeyvalue)){
          $response[$extrakey] = $extrakeyvalue ;
        }

        return response()->json($response,$code);
    }
    public function errorresponse($status , $errorsdata , $code = 422){
        $response = [
            'status' => $status,
            'errors' => $errorsdata
        ];

        return response()->json($response,$code);
    }

}
