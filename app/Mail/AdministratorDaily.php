<?php

namespace App\Mail;

use App\Publisher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Providers\ReportingMetricsProvider;
use \Carbon\Carbon;

class AdministratorDaily extends Mailable
{
    use Queueable, SerializesModels;

    public $publisher, $leadgenPerformance, $fromDate, $toDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        // From and To
        $this->fromDate = Carbon::yesterday()->startOfDay();
        $this->toDate = Carbon::today()->endOfDay();
        // Get the publisher leadgen performance.
        $this->leadgenPerformance = (new ReportingMetricsProvider())->getAgedLeadgenPerformance($this->fromDate,$this->toDate);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('notifications@leadreserve.com',"LeadReserve Notification")
                    ->subject($this->fromDate->toFormattedDateString() . " Report")
                    ->view('emails.admin_daily_report')
                    ->with([
                        'reportDate' => $this->fromDate,
                        'fromDate' => $this->fromDate,
                        'toDate' => $this->toDate
                    ]);
    }
}
