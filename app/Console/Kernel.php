<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Jobs\BuildReport;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ResetSystem::class,
        Commands\MaskLeadSend::class,
        Commands\SetupAsyncInfrastructure::class,
        Commands\GenerateAgedReporting::class,
        Commands\ReportingWorker::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function(){
            (new \App\Jobs\CacheDispenser\CampaignData())->generate();(new \App\Jobs\CacheDispenser\CampaignPublisherData())->generate();
        })->hourly();
        $schedule->call(function () {
            (new \App\Jobs\BuildReport("platform",Carbon::now()))->handle();
        })->everyFiveMinutes();
        $schedule->call(function () {
            // generate report
            (new \App\Jobs\BuildReport("platform.daily",Carbon::yesterday()))->handle();
            // generate technical report (items processed in queue that day, current queue, migration status for lead send, etc...technical load..)
            // generate lead sending
            (new \App\Jobs\DelayedLeadSendingToBacklog())->handle();
            //(new \App\Jobs\SendDailyEmails())->handle();
        })->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
