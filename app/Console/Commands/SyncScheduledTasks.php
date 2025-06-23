<?php

namespace App\Console\Commands;

use ReflectionClass;
use Cron\CronExpression;
use Illuminate\Console\Command;
use App\Models\task_schedule_list;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class SyncScheduledTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:scheduled-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage task scheduling list';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schedule = app(Schedule::class);
        $events = collect($schedule->events());

        $activeNames = [];

        // Get all registered Artisan commands
        $consoleKernel = app(ConsoleKernel::class);
        $commands = $consoleKernel->all();

        foreach ($events as $event) {

            $commandStr = $event->command;

            // Look for the word 'artisan' with or without quotes
            $artisanPos = strpos($commandStr, 'artisan');

            if ($artisanPos !== false) {
                // Extract the string starting just after 'artisan'
                $afterArtisan = substr($commandStr, $artisanPos + strlen('artisan'));

                // Trim any leading quotes, whitespace, etc.
                $signatureString = ltrim($afterArtisan, " \t\n\r\0\x0B\"'");

                // Extract just the command name (before any args or options)
                $baseCommand = explode(' ', $signatureString)[0];
            } else {
                continue; // skip if 'artisan' is not found
            }

            $cronExpression = $event->expression;
            $cron = CronExpression::factory($cronExpression);
            $nextRun = $cron->getNextRunDate();
            $formattedSchedule = $nextRun->format('Y-m-d H:i');

            $name = $signatureString;
            $description = null;

            if (isset($commands[$baseCommand])) {
                $commandObj = $commands[$baseCommand];

                try {
                    if (method_exists($commandObj, 'getDescription')) {
                        $description = $commandObj->getDescription();
                    } else {
                        // fallback: reflection to get protected $description property
                        $ref = new ReflectionClass($commandObj);
                        if ($ref->hasProperty('description')) {
                            $descProp = $ref->getProperty('description');
                            $descProp->setAccessible(true);
                            $description = $descProp->getValue($commandObj);
                        }
                    }
                } catch (\Exception $e) {
                    // Optionally log error
                    $description = null;
                }
            }

            $activeNames[] = $name;

            task_schedule_list::updateOrCreate(
                ['name' => $name],
                [
                    'command' => $commandStr,
                    'description' => $description,
                    'schedule' => $formattedSchedule,
                    'is_active' => 1,
                    'is_deleted' => 0,
                ]
            );
        }

        // Mark commands not scheduled anymore as inactive
        task_schedule_list::whereNotIn('name', $activeNames)->update(['is_active' => 0]);

        $this->info('✅ Cron jobs synced successfully into the scheduled_tasks table.');
    }

}
