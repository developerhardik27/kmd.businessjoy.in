<?php

namespace Database\Seeders\adminSeeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class User_roleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_role')->insert([
            ['role' => 'Super Admin'],
            ['role' => 'Admin'],
        ]);
    }
}
