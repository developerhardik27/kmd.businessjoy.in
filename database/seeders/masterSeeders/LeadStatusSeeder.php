<?php

namespace Database\Seeders\masterSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LeadStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $data = [
            ['leadstatus_name' => 'Not Interested'],
            ['leadstatus_name' => 'Not Receiving'],
            ['leadstatus_name' => 'New Lead'],
            ['leadstatus_name' => 'Interested'],
            ['leadstatus_name' => 'Switch Off'],
            ['leadstatus_name' => 'Does Not Exist'],
            ['leadstatus_name' => 'Email Sent'],
            ['leadstatus_name' => 'Wrong Number'],
            ['leadstatus_name' => 'By Mistake'],
            ['leadstatus_name' => 'Positive'],
            ['leadstatus_name' => 'Busy'],
            ['leadstatus_name' => 'Call Back'],
        ];

        DB::table('leadstatus_name')->insert($data);
    }
}
