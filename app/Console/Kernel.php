<?php

namespace App\Console;

use App\Console\Commands\SendServicePDF;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Rinvex\Statistics\Jobs\CleanStatisticsRequests;
use Rinvex\Statistics\Jobs\CrunchStatistics;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SendServicePDF::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('SendTrainingInfo')->everyFiveMinutes();
        $schedule->command('SendServicePDF')->cron('0 7 * * 1');

        if(env("IS_DEMO", false))
        {
            $schedule->command('demo:createDemoClient')->daily()->at('03:00');
        }
        else
        {
            //backups not in DEMO mode
            $schedule->command('backup:clean')->daily()->at('01:00');
            $schedule->command('backup:run --only-db')->daily()->at('02:00');
            //backup of application. As this should be the stable release on Github, this is normally not necessary
            //$schedule->command('backup:run')->monthlyOn(1, '02:30');
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
        $this->load(__DIR__.'/Commands');
    }
}
