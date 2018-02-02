<?php

namespace App\Providers;

use App\Campaign;
use App\CampaignAttribute;
use App\Lead;
use App\LeadPoint;
use App\PlatformEvent;
use Illuminate\Support\Facades\DB;

class LeadMoldingProvider
{
    /**
     * @param $leadIdArray
     * @return array
     */
    public function getStatusesForLeads($leadIdArray)
    {
        // Generate a status array.
        $statuses = [
            "captured" => count($leadIdArray),
            "accepted" => 0,
            "rejected" => 0
        ];

        if(count($leadIdArray)>10000) {
            $leadStatusesToParse = $this->batchArray($leadIdArray,5000,function($array,&$resArray){
                if(!isset($resArray[0])&&!isset($resArray[1])) {
                    $resArray[0] = 0;
                    $resArray[1] = 0;
                }

                $resArray[0] += PlatformEvent::whereName("lead.accepted")->whereIn("json_value->lead_id",$array)->count();
                $resArray[1] += PlatformEvent::whereName("lead.rejected")->whereIn("json_value->lead_id",$array)->count();
            });
        } else {
            $leadStatusesToParse[0] = PlatformEvent::whereName("lead.accepted")->whereIn("json_value->lead_id",$leadIdArray)->count();
            $leadStatusesToParse[1] = PlatformEvent::whereName("lead.rejected")->whereIn("json_value->lead_id",$leadIdArray)->count();
        }

        $statuses["accepted"] = (isset($leadStatusesToParse[0])?$leadStatusesToParse[0]:0);
        $statuses["rejected"] = (isset($leadStatusesToParse[1])?$leadStatusesToParse[1]:0);

        // Not sent.
        $statuses["unsent"] = count($leadIdArray) - ($statuses["accepted"] + $statuses["rejected"]);

        return $statuses;
    }

    /**
     * Batch array
     * @param $array
     * @param $size
     * @param $callback
     * @return array
     */
    private function batchArray($array,$size,$callback) {
        // Create the mutable array.
        $resultArray = [];

        // Go through the current array, with a provided size for the array partition.
        foreach(array_chunk($array,$size) as $arrayPartition) {
            $callback($arrayPartition,$resultArray);
        }

        // Return the mutable array.
        return $resultArray;
    }

    /**
     * @param $leadId
     * @return string
     */
    public function getStatusForLead($leadId)
    {
        // Build out the platform event handler.
        $platformEventHandler = new PlatformEventHandlerServiceProvider();

        // Get the accepted and rejected counts.
        $isAccepted = $platformEventHandler->buildPlatformQuery("lead.accepted","lead_id",$leadId)->count();
        $isRejected = $platformEventHandler->buildPlatformQuery("lead.rejected","lead_id",$leadId)->count();

        // Simple return statement.
        return ($isAccepted==0&&$isRejected==0) ? "not-sent" : ($isAccepted>$isRejected) ? "accepted" : "rejected";
    }

    /**
     * Find status for lead.
     *
     * @param $leadId
     * @return string
     */
    public function generateStatusForLead($leadId)
    {
        $leadObject = Lead::whereId($leadId)->first();
        if (Campaign::whereId($leadObject->campaign_id)->exists()) {
            if (Campaign::whereId($leadObject->campaign_id)->first()->type->name == "linkout" || Campaign::whereId($leadObject->campaign_id)->first()->type->name == "CPA") {
                // Retrieve the lead conversion platform event.
                $leadConversion = PlatformEvent::whereName("lead.conversion")->where("json_value", "like", "%\"lead_id\": " . $leadId . "%")->first();

                // Make sure to accept or not-sent.
                return ($leadConversion != null) ? "accepted" : "not-sent";
            } else {
                // Retrieve the lead sent platform_event.
                $leadSentEvent = PlatformEvent::whereName("lead.sent")->where("json_value", "like", "%\"lead_id\": " . $leadId . "%")->first();

                if ($leadSentEvent == null) {
                    if (PlatformEvent::whereName("lead.sent.failed")->where("json_value", "like", "%\"lead_id\": " . $leadId . "%")->first() != null) {
                        return "rejected";
                    } else {
                        return "not-sent";
                    }
                } else {
                    // Decode the platform_event json_value.
                    $decodedJsonEventValue = json_decode($leadSentEvent->json_value, 1);

                    // Get the campaign success response.
                    $campaignSuccessResponseValue = CampaignAttribute::whereCampaignId($decodedJsonEventValue["campaign_id"])->whereName("success_response")->first()->data->value;

                    // Check the strpos to find if the response matches. (OK will never be in the same word as FAIL..etc..)
                    return (strpos($decodedJsonEventValue["response_data"]["contents"], $campaignSuccessResponseValue) === false) ? "rejected" : "accepted";
                }
            }
        } else {
            return "not-sent";
        }
    }

    /**
     * Genearte an array of leads via campaign and publisher relations.
     *
     * @param $campaignId
     * @param $publisherId
     * @return array
     */
    public function generateLeadsFromCampaignAndPublisherIds($campaignId, $publisherId)
    {
        // Retrieve the "lead list" for this campaign and publisher.
        $leadList = Lead::whereCampaignId($campaignId)->wherePublisherId($publisherId)->get();

        // Generate a blank array and start pushing formatted leads to it.
        $formattedLeadArray = [];
        foreach ($leadList as $preformattedLead) {
            $formattedLeadArray[] = $this->generateLeadArrayFromDB($preformattedLead->id);
        }

        // Send back the final array of *formatted leads.
        return $formattedLeadArray;
    }


    /**
     * Generate an array of items from the database.
     *
     * @param $leadObject
     * @param $leadId
     * @return array
     */
    public function generateLeadArrayFromDB($leadId = null)
    {
        // Retrieve the core lead object.
        $coreLeadObject = Lead::whereId($leadId)->first()->toArray();

        // Build an array with additional lead points.
        $additionalLeadPoints = LeadPoint::whereLeadId($leadId)->get()->toArray();
        $additionalLeadPointArray = [];
        foreach ($additionalLeadPoints as $addLeadPoint) {
            $additionalLeadPointArray[$addLeadPoint["key"]] = $addLeadPoint["value"];
        }

        // Return the *merged array with core lead object and addl. lead points.
        return array_merge($coreLeadObject, $additionalLeadPointArray);
    }
}
