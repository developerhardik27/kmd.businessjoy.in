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
        $common_db_structure_query = "";
        $companies = Company::select('dbname')->where('is_deleted', 0)->get();
        foreach ($companies as $company) {
            $dbname = $company->dbname;

            config(['database.connections.dynamic_connection.database' => $dbname]);

            try {
                // Establish connection to the dynamic database
                DB::purge('dynamic_connection');
                DB::reconnect('dynamic_connection');
    
                // Execute the SQL statement
                DB::connection('dynamic_connection')->statement($common_db_structure_query);
    
                echo $dbname . " : Changes successfully <br/>";
            } catch (\Exception $e) {
                // Handle the case where the table or column does not exist
                echo $dbname . " : Skipped (Table or column does not exist)<br/>";
            }
        }

        // Revert back to the default database connection
        DB::setDefaultConnection('mysql');
    }
}


// query executed log ***********************

// ALTER TABLE customers drop column address -  18-07-2024 17:57
// result :-
// newbjdb : Skipped (Table or column does not exist)
// bj_Shree_Vinayak_Battery_Zone_k9r : Changes successfully
// bj_Dell_nmn : Changes successfully
// bj_siddhi_qkk : Changes successfully
// bj_samsung_zk0 : Changes successfully
// business_joy_falcon_p1m : Changes successfully


// ALTER TABLE `customers` ADD `house_no_building_name` VARCHAR(255) NULL AFTER `contact_no`, ADD `road_name_area_colony` VARCHAR(255) NULL AFTER `house_no_building_name` -  18-07-2024 18:01
// result :-
// newbjdb : Skipped (Table or column does not exist)
// bj_Shree_Vinayak_Battery_Zone_k9r : Changes successfully
// bj_Dell_nmn : Changes successfully
// bj_siddhi_qkk : Changes successfully
// bj_samsung_zk0 : Changes successfully
// business_joy_falcon_p1m : Changes successfully

// ALTER TABLE company_details drop column address -  19-07-2024 10:50
// result :-
// newbjdb : Changes successfully
// bj_Shree_Vinayak_Battery_Zone_k9r : Skipped (Table or column does not exist)
// bj_Dell_nmn : Skipped (Table or column does not exist)
// bj_siddhi_qkk : Skipped (Table or column does not exist)
// bj_samsung_zk0 : Skipped (Table or column does not exist)
// business_joy_falcon_p1m : Skipped (Table or column does not exist)

// ALTER TABLE `company_details` ADD `house_no_building_name` VARCHAR(255) NULL AFTER `contact_no`, ADD `road_name_area_colony` VARCHAR(255) NULL AFTER `house_no_building_name` -  19-07-2024 10:52
// result :-
// newbjdb : Changes successfully
// bj_Shree_Vinayak_Battery_Zone_k9r : Skipped (Table or column does not exist)
// bj_Dell_nmn : Skipped (Table or column does not exist)
// bj_siddhi_qkk : Skipped (Table or column does not exist)
// bj_samsung_zk0 : Skipped (Table or column does not exist)
// business_joy_falcon_p1m : Skipped (Table or column does not exist)