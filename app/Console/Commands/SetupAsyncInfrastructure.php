<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupAsyncInfrastructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:setup-async';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds out the queues for the lead capturing tasks.';

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
        $this->info("Setting up async queue drivers.");
        $this->info("Queue Priority...");
        $this->warn("[Queue] (1) LEAD PROCESSING -- To handle lead captures.");
        $this->warn("[Queue] (2) PLATFORM PROCESSING -- To handle all internal processing methods.");
        $this->call("queue:work",[
            "--queue" => "lead-processing,platform-processing"
        ]);
    }
}
