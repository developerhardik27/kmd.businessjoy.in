<?php

namespace Database\Seeders\masterSeeders;

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
                "invoice" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"],
                "mngcol" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"],
                "formula" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"],
                "invoicesetting" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"],
                "bank" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"],
                "customer" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"],
            ],
            "leadmodule" => [
                "lead" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"]
            ],
            "customersupportmodule" => [
                "customersupport" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"]
            ],
            "adminmodule" => [
                "company" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"],
                "user" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1", "max" => "1"],
                "techsupport" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"]
            ],
            "inventorymodule" => [
                "product" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"]
            ],
            "accountmodule" => [
                "purchase" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"]
            ],
            "remindermodule" => [
                "reminder" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"],
                "remindercustomer" => ["show" => "1", "add" => "1", "view" => "1", "edit" => "1", "delete" => "1", "alldata" => "1"]
            ]
        ];
        $rpjson = json_encode($rp);
         
        DB::table('user_permissions')->insert([
            'user_id' => '1',
            'rp' =>  $rpjson,
            'created_by' => 1
        ]);
    }
}
