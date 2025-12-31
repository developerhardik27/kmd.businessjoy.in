<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
            'database/migrations/masterdb',
            'database/migrations/newmasterdbtable',
            'database/migrations/v4_2_1/master',
            'database/migrations/v4_2_2/master',
            'database/migrations/v4_2_3/master',
            'database/migrations/v4_3_0/master',
            'database/migrations/v4_3_1/master',
        ];

        Artisan::call('optimize:clear');

        $migrationsuccesfully = true;  // Set this to true initially and check each migration

        foreach ($paths as $path) {
            try {
                Log::info("Running migration: {$path}");

                $exitCode = Artisan::call('migrate', [
                    '--path' => $path,
                ]);

                if ($exitCode !== 0) {
                    throw new \Exception("Migration failed with exit code {$exitCode} for {$path}");
                }

                Log::info("Migration completed successfully: {$path}");
            } catch (\Exception $e) {
                Log::error("Migration failed for {$path}: " . $e->getMessage());
                $migrationsuccesfully = false;  // If one migration fails, set this to false
            }
        }

        if ($migrationsuccesfully) {
            $this->info('Master data migration completed successfully.');
            $this->seedMasterData();
        } else {
            $this->error('Master data setup encountered errors during migration.');
        }
    }

    private function seedMasterData()
    {
        try {
            Artisan::call('db:seed', [
                '--force' => true
            ]);
            $this->info('Master data seeded successfully.');
        } catch (\Exception $e) {
            Log::error('Seeding master data failed: ' . $e->getMessage());
            $this->error('Seeding master data failed. Check logs for details.');
        }
    }
}
