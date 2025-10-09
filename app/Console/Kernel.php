<?php

namespace App\Console;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Mail\ScheduledTaskOutputMail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // ...
        \App\Console\Commands\UpdateInvoiceStatus::class,
    ];

    protected function getDailyCronLogPath()
    {
        return storage_path('logs/' . 'cron_logs_'.now()->format('Y-m-d') . '.log');
    }

    protected function ensureCronLogDirectoryExists()
    {
        $directory = storage_path('logs/');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $logPath = $this->getDailyCronLogPath();

        // Ensure log directory exists
        $this->ensureCronLogDirectoryExists();

        // 1. invoices:update-status
        $schedule->command('invoices:update-status')
            ->dailyAt('06:00')
            ->before(function () use ($logPath) {
                File::append($logPath, "===== [invoices:update-status] Started at " . now() . " =====\n");
            })
            ->appendOutputTo($logPath)
            ->after(function () use ($logPath) {
                File::append($logPath, "===== [invoices:update-status] Ended at " . now() . " =====\n\n");
            });

        // 2. delete:temp-records
        $schedule->command('delete:temp-records')
            ->dailyAt('06:00')
            ->before(function () use ($logPath) {
                File::append($logPath, "===== [delete:temp-records] Started at " . now() . " =====\n");
            })
            ->appendOutputTo($logPath)
            ->after(function () use ($logPath) {
                File::append($logPath, "===== [delete:temp-records] Ended at " . now() . " =====\n\n");
            });

        // 3. delete:temp-records
        $schedule->command('amazon:refresh-tokens')
            ->hourly()
            ->before(function () use ($logPath) {
                File::append($logPath, "===== [amazon:refresh-tokens] Started at " . now() . " =====\n");
            })
            ->appendOutputTo($logPath)
            ->after(function () use ($logPath) {
                File::append($logPath, "===== [amazon:refresh-tokens] Ended at " . now() . " =====\n\n");
            });

        // 4. sync:scheduled-tasks
        $schedule->command('sync:scheduled-tasks')
            ->dailyAt('06:00')
            ->before(function () use ($logPath) {
                File::append($logPath, "===== [sync:scheduled-tasks] Started at " . now() . " =====\n");
            })
            ->appendOutputTo($logPath)
            ->after(function () use ($logPath) {
                File::append($logPath, "===== [sync:scheduled-tasks] Ended at " . now() . " =====\n\n");
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
