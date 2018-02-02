<?php

namespace App\Console\Commands;

use App\Jobs\FireLeadSend;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class MaskLeadSend extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:leadsend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute a lead send via console.';

    /**
     * Create a new command instance.
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
        $this->info("Mask a lead send via Developer Console.");
        $this->warn("DO NOT DO IN PRODUCTION.");
        $campaignId = $this->ask("What campaign_id do you want to use?");
        $publisherId = $this->ask("What publisher_id do you want to use?");
        $leadId = $this->ask("What lead_id do you want to send?");
        dispatch(new FireLeadSend($campaignId,$publisherId,$leadId));
    }
}
