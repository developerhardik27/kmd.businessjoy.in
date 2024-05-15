<?php

namespace Database\Seeders\adminSeeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'firstname' => ' super',
            'lastname' => 'admin',
            'email' => 'superadmin@email.com',
            'password' => Hash::make('sa123'),
            'contact_no' => 9874634240,
            'country_id' => 1,
            'state_id' => 7,
            'city_id' => 323,
            'pincode' => 465738,
            'company_id' => 1,
            'created_by' => 1,
            'role'=>1,
        ]);
    }
}
