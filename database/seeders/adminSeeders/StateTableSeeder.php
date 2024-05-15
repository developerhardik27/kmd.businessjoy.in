<?php

namespace Database\Seeders\adminSeeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // ['country_id,state_name] ;
        $indian_states = [
            [1, ' Andhra Pradesh'],
            [1, ' Arunachal Pradesh'],
            [1, ' Assam'],
            [1, ' Bihar'],
            [1, ' Chhattisgarh'],
            [1, ' Goa'],
            [1, ' Gujarat'],
            [1, ' Haryana'],
            [1, ' Himachal Pradesh'],
            [1, ' Jharkhand'],
            [1, ' Karnataka'],
            [1, ' Kerala'],
            [1, ' Madhya Pradesh'],
            [1, ' Maharashtra'],
            [1, ' Manipur'],
            [1, ' Meghalaya'],
            [1, ' Mizoram'],
            [1, ' Nagaland'],
            [1, ' Odisha'],
            [1, ' Punjab'],
            [1, ' Rajasthan'],
            [1, ' Sikkim'],
            [1, ' Tamil Nadu'],
            [1, ' Tripura'],
            [1, ' Uttar Pradesh'],
            [1, ' Uttarakhand'],
            [1, ' West Bengal'],
            [1, ' Andaman and Nicobar Islands'],
            [1, ' Dadra and Nagar Haveli and Daman and Diu'],
            [1, ' Lakshadweep'],
            [1, ' Delhi'],
            [1, ' Puducherry'],
            [1, 'Jammu & Kashmir']
        ];



        // Insert states for India
        foreach ($indian_states as $state) {
            DB::table('state')->insert([
                'country_id' => $state[0],
                'state_name' => $state[1],
                'created_by' => 1,
            ]);
        }
    }
}
