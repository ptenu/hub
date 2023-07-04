<?php

namespace App\Console;

use App\Jobs\CreateCharges;
use App\Jobs\PersistAllStatuses;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Tasks running less than once a day
        $schedule->job(new CreateCharges)->dailyAt('01:00');
        $schedule->job(new PersistAllStatuses)->dailyAt('08:00');

        // Tasks which run multiple times per day
        // ...
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
