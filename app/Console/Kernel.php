<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Run the auto mark late command every hour
        $schedule->command('forms:mark-late')->hourly();
        
        // Your existing queue worker
        $schedule->command('queue:work --stop-when-empty')
                ->everyMinute()
                ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}