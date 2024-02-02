<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class dbscriptController extends Controller
{
    public function dbscript()
    {   
        $common_db_structure_query = 'ALTER TABLE `invoices` CHANGE `hidden_col` `show_col` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL' ;
        $companies = Company::select('dbname')->where('is_deleted', 0)->get();

        foreach ($companies as $company) {
            $dbname = $company->dbname;

            config(['database.connections.dynamic_connection.database' => $dbname]);

            // Establish connection to the dynamic database
            DB::purge('dynamic_connection');
            DB::reconnect('dynamic_connection');

            // Execute the SQL statement
            DB::connection('dynamic_connection')->statement($common_db_structure_query);

            echo $dbname . " : Changes succesfully  <br/>";
        }

        // Revert back to the default database connection
        DB::setDefaultConnection('mysql');
    }
}
