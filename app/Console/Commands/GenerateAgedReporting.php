<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Carbon\Carbon;
use \App\PlatformReport;
use \App\Jobs\BuildReport;

class GenerateAgedReporting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:generate-aged-reporting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new reporting metrics for older data.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $BackDays = $this->ask("How Many Days Back Do You Want To Go?");
        for($i = 1; $i <= $BackDays; $i++) {
            system("clear");
            // Index starts at 1 because of daily report.
            $date = Carbon::today()->subday($i);
            $this->info("Reports To Be Generated     - " . $BackDays);
            $this->info("Reports Currently Generated - " . $i);
            $this->info("____________________________________");
            $this->warn("Checking to see if report generated?");
            if(PlatformReport::whereTimestamp($date)->exists()) {
                $this->info("Report already exists...");
            } else {
                $this->warn("Running platform.daily report...");
                (new BuildReport('platform.daily',$date))->handle();
                $this->info("Built Report For " . $date);    
            }
        }
    }
}
