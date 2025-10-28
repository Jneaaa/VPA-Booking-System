<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\AdminApprovalController;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {        
        // Run every minute to check for forms that should be marked as ongoing
        $schedule->call(function () {
            app()->make(AdminApprovalController::class)->autoMarkOngoingForms();
        })->everyMinute();

        // Run every 5 minutes to check for late forms
        $schedule->call(function () {
            app()->make(AdminApprovalController::class)->autoMarkLateForms();
        })->everyFiveMinutes();

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
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}