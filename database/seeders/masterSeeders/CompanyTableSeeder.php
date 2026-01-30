<?php

namespace Database\Seeders\masterSeeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        DB::table('company')->insert([
            'company_details_id' => '1',
            'dbname' => config('database.connections.mysql.database'),
            'app_version' => config('app.latestversion', 'v4_3_2'),
            'max_users' => 5,
            'created_by' => 1,
        ]);
    }
}
