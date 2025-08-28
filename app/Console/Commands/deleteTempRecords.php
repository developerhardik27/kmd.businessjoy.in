<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\task_schedule_list;

class deleteTempRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:temp-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete last day temporory records from db and disk';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companies = Company::select('dbname')->where('is_deleted', 0)->get();

        try {
            foreach ($companies as $company) {
                $dbname = $company->dbname;

                // Dynamically set the connection for each company
                config(['database.connections.dynamic_connection.database' => $dbname]);

                // Establish connection to the dynamic database
                DB::purge('dynamic_connection');
                DB::reconnect('dynamic_connection');

                if (DB::connection('dynamic_connection')->getSchemaBuilder()->hasTable('temp_images')) {
                    // Fetch all media names from the temp_images table
                    $records = DB::connection('dynamic_connection')->table('temp_images')
                        ->where('created_at', '<', Carbon::today())  // Optional filter: only records created before today
                        ->get(['media_name']);  // Fetch media_name column

                    // Iterate over each record to check for the corresponding file in the temp folder
                    foreach ($records as $record) {
                        $mediaName = $record->media_name;
                        $filePath = public_path("uploads/temp/{$mediaName}");

                        // Check if the file exists in the temp folder
                        if (File::exists($filePath)) {
                            // If the file exists, delete it
                            File::delete($filePath);
                            $this->info("Deleted file: $filePath from temp folder.");
                        } else {
                            $this->info("File not found: $filePath in temp folder.");
                        }
                    }

                    // Optionally, delete the records from the database (if required)
                    $updated = DB::connection('dynamic_connection')->table('temp_images')
                        ->where('created_at', '<', Carbon::today())  // Optional filter
                        ->delete();

                    $this->info("Deleted temp records for database: $dbname. Rows affected: $updated.");
                } else {
                    $this->info("Table 'temp_images' does not exist in database: $dbname.");
                }
            }

            task_schedule_list::where('name', $this->signature)
                ->update(['last_run_time' => now()]);
        } catch (\Exception $e) {
            // Catch any general errors and log them
            $this->info("An error occurred during the delete temp records: " . $e->getMessage());
        } finally {
            // Always revert back to the default database connection
            DB::setDefaultConnection('mysql');
        }

        // Final message to confirm completion
        $this->info('Temp records and corresponding files deleted successfully.');
    }
}
