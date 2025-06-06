<?php

namespace Database\Seeders;

use App\Models\user_permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {  

        DB::table('company_details')->insert([
            'name' => 'oceanmnc',
            'email' => 'superadmin@email.com',
            'contact_no' => 9874634240,
            'address' => ' tesdfa',
            'country_id' => 1,
            'state_id' => 4,
            'city_id' => 333,
            'pincode' => 465738,
            'gst_no' => 'test324235dsf',
            'tablename' => 'mng_col_1',
            'dbname' => 'invoicedb'
        ]);
       
        DB::table('company')->insert([
            'company_details_id'=>1,
            'created_by' => 1,
        ]);

        DB::table('users')->insert([
            'firstname' => ' super',
            'lastname' => 'admin',
            'email' => 'superadmin@email.com',
            'password' => Hash::make('sa123'),
            'contact_no' => 9874634240,
            'country_id' => 1,
            'state_id' => 4,
            'city_id' => 333,
            'pincode' => 465738,
            'company_id' => 1,
            'created_by' => 1,
            'role'=>1,
        ]);
         
        $rp = [
            "invoicemodule" => [
                "invoice" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1 , "alldata" => 1],
                "mngcol" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1 , "alldata" => 1],
                "formula" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1 , "alldata" => 1],
                "invoicesetting" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1 , "alldata" => 1],
                "company" => ["show" => 1, "add" => 1, "view" => 1, "edit" =>1, "delete" => 1 , "alldata" => 1],
                "bank" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1 , "alldata" => 1],
                "user" => ["show" => 1, "add" => 1, "view" =>1, "edit" => 1, "delete" =>1 , "alldata" => 1],
                "customer" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1 , "alldata" => 1],
                "product" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1 , "alldata" => 1],
                "purchase" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1 , "alldata" => 1]
            ],
            "leadmodule" => [
                "lead" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1 , "alldata" => 1]
            ],
            "customersupportmodule" => [
                "customersupport" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1 , "alldata" => 1]
            ]
        ];
        $rpjson = json_encode($rp);
         \App\Models\v1_0_0\user_permission::create([
            'user_id' => 1,
            'rp' => $rpjson
        ]); 

        $tableName = 'mng_col';

        // Create the table if it doesn't exist
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id(); // Auto-incrementing primary key
                $table->integer('invoice_id');
                $table->integer('amount');
                $table->integer('created_by');
                $table->integer('updated_by')->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->nullable();
                $table->integer('is_active')->default(1);
                $table->integer('is_deleted')->default(0);
            });
        }
    }
    
}
