<?php

namespace App\Providers;

use App\ResourceAccessCode;
use \Carbon\Carbon;

class PublisherResourceProvider
{
    /**
        Get posting instructions link.
    */
    public function getPostingInstructionLink($campaignId,$publisherId)
    {
        $postingSpecURL = "";
        if(ResourceAccessCode::whereResourcePath("posting.".$campaignId.".".$publisherId)->exists()) {
            $postingSpecURL = route('resources.access',['accessCode' => md5(ResourceAccessCode::whereResourcePath("posting.".$campaignId.".".$publisherId)->first()->access_secret)]);
        } else {
            $resourceAccessCode = ResourceAccessCode::create([
               'is_active' => 1,
                'resource_name' => "Posting Instructions",
                'resource_path' => "posting.".$campaignId.".".$publisherId,
                'access_secret' => 'pub'.$publisherId.".".Carbon::now()->toAtomString()
            ]);
            $postingSpecURL = route('resources.access',["accessCode" => md5($resourceAccessCode->access_secret)]);
        }
        return $postingSpecURL;
    }
}