<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class commonController extends Controller
{
    public function dbname(string $id){
        
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
}
