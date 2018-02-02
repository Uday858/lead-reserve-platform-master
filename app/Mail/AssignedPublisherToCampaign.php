<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Providers\PaperworkProvider;

use App\Campaign;
use App\Publisher;
use Carbon\Carbon;

class AssignedPublisherToCampaign extends Mailable
{
    use Queueable, SerializesModels;

    public $campaign, $publisher, $ioPDF, $tcPDF, $postingInstruction,$payout,$leadCap;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($campaignId,$publisherId,$postingInstruction,$payout,$leadCap)
    {
        $this->campaign = Campaign::whereId($campaignId)->first();
        $this->publisher = Publisher::whereId($publisherId)->first();
        $this->ioPDF = (new PaperworkProvider())->generateIOForPublisher($campaignId,$publisherId);
        $this->tcPDF = (new PaperworkProvider())->generateTermsAndConditionsForPublisher($campaignId,$publisherId);
        $this->postingInstruction = $postingInstruction;
        $this->payout = $payout;
        $this->leadCap = $leadCap;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('notifications@leadreserve.com',"LeadReserve Notification")
                    ->subject($this->campaign->name . " Materials & Setup")
                    ->view('emails.new_campaign_for_publisher')
                    ->with([
                        'reportDate' => Carbon::today()->toFormattedDateString(),
                        'postingInstructionLink' => $this->postingInstruction,
                        'leadcap' => $this->leadCap,
                        'payout' => $this->payout
                    ])
                    ->attachData($this->ioPDF->output(), 'IO__ReserveTech__'.$this->campaign->name.'__'.$this->publisher->name.'.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->attachData($this->tcPDF->output(), 'TermsAndConditions__ReserveTech__'.$this->campaign->name.'__'.$this->publisher->name.'.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
