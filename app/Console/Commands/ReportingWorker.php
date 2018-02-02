<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReportingWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:reporting-worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Work on aged and realtime report(s).';

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
        $this->info("Setting up queue drivers.");
        $this->info("Reporting Queue Priority...");
        $this->warn("[Queue] (1) REPORT PROCESSING -- To handle all reporting processing methods. This is usally aged reporting.");
        $this->call("queue:work",[
            "--queue" => "report-processing"
        ]);
    }
}
