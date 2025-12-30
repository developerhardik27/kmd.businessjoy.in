<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\masterSeeders\StateTableSeeder;
use Database\Seeders\masterSeeders\CityTableSeeder;
use Database\Seeders\masterSeeders\UsersTableSeeder;
use Database\Seeders\masterSeeders\CountryTableSeeder;
use Database\Seeders\masterSeeders\CurrencyTableSeeder;
use Database\Seeders\masterSeeders\CompanyTableSeeder;
use Database\Seeders\masterSeeders\User_roleTableSeeder;
use Database\Seeders\masterSeeders\User_permissionsTableSeeder;
use Database\Seeders\masterSeeders\Company_detailsTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
        UsersTableSeeder::class,
        User_roleTableSeeder::class,
        User_permissionsTableSeeder::class,
        StateTableSeeder::class,
        CurrencyTableSeeder::class,
        CountryTableSeeder::class,
        CompanyTableSeeder::class,
        Company_detailsTableSeeder::class,
        CityTableSeeder::class,
        ]);
    }
}
