<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // // Schedule your jobs here
        // $schedule->job(new \App\Jobs\CheckExpiredWorksheetsJob)->everyFiveMinutes();
        // // // Schedule the command to run hourly
        // // $schedule->command('worksheets:check-expired')->hourly();

        // Schedule the command to run every minute
        $schedule->command('run:every-three-seconds')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}
