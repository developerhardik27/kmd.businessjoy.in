<?php

namespace App\Console\Commands;

use App\Models\company;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class UpdateInvoiceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update invoice statuses after due days if still pending';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $companies = Company::select('dbname')->where('is_deleted', 0)->get();

        foreach ($companies as $company) {
            $dbname = $company->dbname;

            try {
                config(['database.connections.dynamic_connection.database' => $dbname]);

                // Reconnect to the dynamic database
                DB::purge('dynamic_connection');
                DB::reconnect('dynamic_connection');

                if (DB::connection('dynamic_connection')->getSchemaBuilder()->hasTable('invoices')) {
                    // Get today's date
                    $today = now()->toDateString();

                    // Update invoices that are overdue
                    $updated = DB::connection('dynamic_connection')->table('invoices')
                        ->where('status', 'pending')
                        ->whereRaw("DATE(created_at) + INTERVAL COALESCE(overdue_date, 0) DAY <= ?", [$today])
                        ->update(['status' => 'due']);

                    // Log the result
                    $this->info("Updated invoices for database: $dbname. Rows affected: $updated.");
                } else {
                    // Log if the table does not exist
                    $this->info("Table 'invoices' does not exist in database: $dbname.");
                }
            } catch (\Exception $e) {
                // Log the error
                $this->error("Error updating invoices for database: $dbname. Error: " . $e->getMessage());
            }
        }

        // Display a single success message after all databases are processed
        $this->info('Invoice status updated successfully for all databases.');




        // Revert back to the default database connection
        DB::setDefaultConnection('mysql');


    }
}
