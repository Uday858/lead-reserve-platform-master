<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Log;

class DelayedLeadSendingToBacklog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job
     *
     * @return void
     */
    public function handle()
    {
        try {
            shell_exec("node " . getcwd() . "/queue_control/migration.js");
        } catch(\Exception $e) {
            Log::error("Queue Migration Was Not Completed!",[$e->getMessage()]);
        }
    }
}
