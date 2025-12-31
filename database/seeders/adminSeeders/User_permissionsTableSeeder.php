<?php

namespace Database\Seeders\adminSeeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\support\Facades\DB;
class User_permissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rp = [
            "invoicemodule" => [
                "invoice" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                "mngcol" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                "formula" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                "invoicesetting" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                "bank" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                "customer" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
            ],
            "leadmodule" => [
                "lead" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"]
            ],
            "customersupportmodule" => [
                "customersupport" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"]
            ],
            "adminmodule" => [
                "company" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"],
                "user" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0", "max" => "0"]
            ],
            "inventorymodule" => [
                "product" => ["show" => "0", "add" => "0", "view" => "0", "edit" => "0", "delete" => "0", "alldata" => "0"]
            ],
           'accountmodule' => []
        ];
        $rpjson = json_encode($rp);
         
        DB::table('user_permissions')->insert([
            'user_id' => '1',
            'rp' =>  $rpjson,
        ]);
    }
}
