<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Publisher;
use App\Mail\PublisherDaily;
use App\Mail\AdministratorDaily;
use App\Providers\PlatformSettingProvider;

class SendDailyEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        // TODO: expand this.!
        Log::info("New Daily Email To Be Sent Out..");
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Send publisher emails.
        // DONT DO THIS --- $this->sendDailyPubEmail();
        // Send daily admin email.
        $this->sendDailyAdminEmail();
    }

    /**
    * Send daily admin emails.
    * @return void
    */
    private function sendDailyAdminEmail()
    {
        // Message
        $message = (new AdministratorDaily())->onQueue('platform-processing');
        // User Setting
        $settingProvider = (new PlatformSettingProvider());
        Mail::to($settingProvider->setting('admin_notification_email'),$settingProvider->setting('admin_notification_name'))
        ->send($message);
        Log::info("Sent Admin Email");
    }

    /**
    * Send daily pub emails.
    * @return void
    */
    private function sendDailyPubEmail()
    {
        foreach(Publisher::all() as $publisher) {
            $message = (new PublisherDaily($publisher->id))->onQueue('platform-processing');
            Mail::to($publisher->email,$publisher->name)->send($message);
            Log::info("Sent Daily Email To: " . $publisher->email);
        }
    }
}
