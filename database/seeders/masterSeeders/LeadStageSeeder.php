<?php

namespace Database\Seeders\masterSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LeadStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['leadstage_name' => 'New Lead'],
            ['leadstage_name' => 'Requirement Gathering'],
            ['leadstage_name' => 'Quotation'],
            ['leadstage_name' => 'In Followup'],
            ['leadstage_name' => 'Sale'],
            ['leadstage_name' => 'Cancelled'],
            ['leadstage_name' => 'Disqualified'],
            ['leadstage_name' => 'Future Lead'],
            ['leadstage_name' => 'Retargeting'], 
        ];

        DB::table('leadstage')->insert($data);
    }
}
