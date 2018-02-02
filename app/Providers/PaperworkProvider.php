<?php

namespace App\Providers;

use App\Campaign;
use App\Publisher;
use Barryvdh\DomPDF\Facade as PDF;

class PaperworkProvider
{
    public function generateIOForPublisher($campaignId,$publisherId)
    {
        $pdf = PDF::loadView('paperwork.insertion_order_safe',[
            'campaign' => Campaign::whereId($campaignId)->first(),
            'publisher' => Publisher::whereId($publisherId)->first(),
        ]);
        return $pdf;
    }
    public function generateTermsAndConditionsForPublisher($campaignId,$publisherId)
    {
        $pdf = PDF::loadView('paperwork.terms_and_conditions_safe',[
            'campaign' => Campaign::whereId($campaignId)->first(),
            'publisher' => Publisher::whereId($publisherId)->first(),
        ]);
        return $pdf;
    }
}