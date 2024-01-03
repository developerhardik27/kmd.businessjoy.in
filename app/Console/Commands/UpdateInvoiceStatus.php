<?php

namespace App\Console\Commands;

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
        $fifteenDaysAgo = Carbon::now()->subDays(15);

        DB::table('invoices')
            ->where('status', 'pending')
            ->where('created_at', '<=', $fifteenDaysAgo)
            ->update(['status' => 'due']);

        $this->info('Invoice status updated successfully.');
    }
}
