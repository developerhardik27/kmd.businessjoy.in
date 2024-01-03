<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            'city_id' => 339,
            'pincode' => 453490,
            'company_id' => 1,
            'created_by' => 1,
            'role'=>1,

        ]);

       
    }
}
