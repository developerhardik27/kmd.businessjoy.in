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
    protected $description = 'Update invoice statuses after 15 days if still pending';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        // $fifteenDaysAgo = Carbon::now()->subDays(15);
        // $companies = company::select('dbname')->where('is_deleted', 0)->get();
        // foreach ($companies as $company) {
        //     $dbname = $company->dbname;

        //     config(['database.connections.dynamic_connection.database' => $dbname]);

        //     // Establish connection to the dynamic database
        //     DB::purge('dynamic_connection');
        //     DB::reconnect('dynamic_connection');

        //     // Execute the SQL statement
        //     DB::connection('dynamic_connection')->table('invoices')
        //         ->where('status', 'pending')
        //         ->where('created_at', '<=', $fifteenDaysAgo)
        //         ->update(['status' => 'due']);

        // }
        // $this->info('Invoice status updated successfully.');
        // // Revert back to the default database connection
        // DB::setDefaultConnection('mysql');

        $companies = Company::select('dbname')->where('is_deleted', 0)->get();
        foreach ($companies as $company) {
            $dbname = $company->dbname;


            try {
                config(['database.connections.dynamic_connection.database' => $dbname]);

                // Establish connection to the dynamic database
                DB::purge('dynamic_connection');
                DB::reconnect('dynamic_connection');

                if (DB::connection('dynamic_connection')->getSchemaBuilder()->hasTable('invoices')) {
                    // Execute the SQL statement
                    $updated = DB::connection('dynamic_connection')->table('invoices')
                        ->where('status', 'pending')
                        ->whereRaw('DATE_ADD(created_at, INTERVAL overdue_date DAY) <= CURDATE()')
                        ->update(['status' => 'due']);

                    // Log the result
                    \Log::info("Updated invoices for database: $dbname. Rows affected: $updated.");
                } else {
                    // Log if the table does not exist
                    \Log::warning("Table 'invoices' does not exist in database: $dbname.");
                }
            } catch (\Exception $e) {
                // Log the error
                \Log::error("Error updating invoices for database: $dbname. Error: " . $e->getMessage());
            }

        }

        $this->info('Invoice status updated successfully.');

        // Revert back to the default database connection
        DB::setDefaultConnection('mysql');


    }
}
