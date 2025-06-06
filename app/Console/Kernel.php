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
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('invoices:update-status')->dailyAt('06:00')
            ->sendOutputTo(storage_path('logs/scheduled_task_output.txt'))
            ->after(function () {
                // Path to the output file
                $outputFile = storage_path('logs/scheduled_task_output.txt');

                try {
                    // Send the email
                    $sendoutput = Mail::to('parthdeveloper9@gmail.com')->send(new ScheduledTaskOutputMail($outputFile));

                    if ($sendoutput) {
                        // Delete the file after sending email
                        if (File::exists($outputFile)) {
                            File::delete($outputFile);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Error sending email or deleting file: " . $e->getMessage());
                }
            });

        $schedule->command('delete:temp-records')->dailyAt('06:00')
            ->sendOutputTo(storage_path('logs/scheduled_task_output.txt'))
            ->after(function () {
                // Path to the output file
                $outputFile = storage_path('logs/scheduled_task_output.txt');

                try {
                    // Send the email
                    $sendoutput = Mail::to('parthdeveloper9@gmail.com')->send(new ScheduledTaskOutputMail($outputFile));

                    if ($sendoutput) {
                        // Delete the file after sending email
                        if (File::exists($outputFile)) {
                            File::delete($outputFile);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Error sending email or deleting file: " . $e->getMessage());
                }
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
