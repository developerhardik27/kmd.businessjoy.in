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
                "invoicedashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "invoice" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "mngcol" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "formula" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "invoicesetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "invoiceformsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "bank" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "customer" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "invoicenumbersetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "invoicetandcsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "invoicestandardsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "invoicegstsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "invoicecustomeridsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "invoiceapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "tdsregister" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
            ],
            "leadmodule" => [
                "leaddashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "lead" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "leadsettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "upcomingfollowup" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "analysis" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "leadownerperformance" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "recentactivity" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "calendar" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "leadapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "import" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "export" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
            ],
            "customersupportmodule" => [
                "customersupportdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "customersupport" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "customersupportapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
            ],
            "adminmodule" => [
                "admindashboard" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "company" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1, "max" => 1],
                "user" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "techsupport" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "userpermission" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "adminapi" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "loginhistory" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1]
            ],
            "inventorymodule" => [
                "inventorydashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "product" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "purchase" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "productcategory" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "productcolumnmapping" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "inventory" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "supplier" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "inventoryapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
            ],
            "remindermodule" => [
                "reminderdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "reminder" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "remindercustomer" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "reminderapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
            ],
            "reportmodule" => [
                "reportdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "report" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null, "log" => null],
                "reportapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
            ],
            "blogmodule" => [
                "blogdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "blog" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null,],
                "blogsettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null,],
                "blogapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
            ],
            "quotationmodule" => [
                "quotationdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "quotation" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "quotationmngcol" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "quotationformula" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "quotationsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "quotationnumbersetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "quotationtandcsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "quotationstandardsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "quotationgstsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "quotationcustomer" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "quotationapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null]
            ],
            'logisticmodule' => [
                "logisticdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "consignorcopy" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "logisticsettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "Lrnumbersettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "logisticformsetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "consignmentnotenumbersettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "consignorcopytandcsettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "logisticothersettings" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "consignee" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "consignor" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "logisticapi" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "watermark" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "downloadcopysetting" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "transporterbilling" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "lrcolumnmapping" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
            ],
            'developermodule' => [
                "developerdashboard" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "slowpage" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "errorlog" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "cronjob" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "techdoc" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "versiondoc" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "recentactivitydata" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "cleardata" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
                "automatetest" => ["show" => 1, "add" => 1, "view" => 1, "edit" => 1, "delete" => 1, "alldata" => 1],
            ],
            'hrmodule' => [
                "hrdashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "employees" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "companiesholidays" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "letters" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
            ],
            "teamodule" => [
                "teadashboard" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "companymaster" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "garden" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "party" => ["show" => null, "add => null", "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "order" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "grade" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "brokerpurchase" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
                "brokeragebill" => ["show" => null, "add" => null, "view" => null, "edit" => null, "delete" => null, "alldata" => null],
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
