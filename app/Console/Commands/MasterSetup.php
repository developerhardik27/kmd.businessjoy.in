<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class MasterSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:master-setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set up master data in the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $paths = [
            base_path('database/migrations/masterdb'),
            base_path('database/migrations/newmasterdbtable'),
            base_path('database/migrations/v4_2_1/master'),
            base_path('database/migrations/v4_2_2/master'),
            base_path('database/migrations/v4_2_3/master'),
            base_path('database/migrations/v4_3_0/master'),
            base_path('database/migrations/v4_3_1/master'),
        ];
       
        foreach ($paths as $path) {
            try {
                Artisan::call('migrate', [
                    '--path' => $path,

                ]);
            } catch (Exception $e) {
                Log::error($e);
            }
        }
        Artisan::call('db:seed', [
            '--force' => true
        ]);
    }
}
