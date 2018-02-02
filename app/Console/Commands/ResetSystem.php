<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will reset the database, then, seed the database.';

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
        // Give the developer some context.
        $this->info("Resetting the platform information, database. (Functionality will remain.)");
        $this->warn("Resetting the database:");
        $this->call('migrate:refresh');
        $this->warn("Seeding the database:");
        $this->call('db:seed');
    }
}
