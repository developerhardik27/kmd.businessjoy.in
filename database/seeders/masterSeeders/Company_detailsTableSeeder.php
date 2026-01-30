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
            'house_no_building_name' => 'tesdfa',
            'road_name_area_colony' => 'tesdfa',
            'country_id' => 101,
            'state_id' => 4030,
            'city_id' => 131900,
            'pincode' => 465738,
            'gst_no' => 'test324235dsf',
            'god_names' => json_encode(["Jay somnath", "jay mataji", "Jay somnath"]),
            'alternative_number' => "7285008403",
        ]);
    }
}
