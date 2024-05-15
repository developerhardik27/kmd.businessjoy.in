<?php

namespace Database\Seeders\masterSeeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Company_detailsTableSeeder extends Seeder
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
            'state_id' => 7,
            'city_id' => 333,
            'pincode' => 465738,
            'gst_no' => 'test324235dsf'
        ]);
    }
}
