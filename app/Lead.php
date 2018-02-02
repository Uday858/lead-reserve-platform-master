<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * App\Lead
 *
 * @property int $id
 * @property int $campaign_id
 * @property int $publisher_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email_address
 * @property string $ip_address
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Lead whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Lead whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Lead whereEmailAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Lead whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Lead whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Lead whereIpAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Lead whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Lead wherePublisherId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Lead whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Lead extends Model
{
    protected $guarded = ["created_at", "updated_at"];

    /**
     * Get full format of the lead.
     * @return array
     */
    public function retrieveFullFormat()
    {
        return array_merge(
            $this->getAttributes(),
            LeadPoint::whereLeadId($this->id)->get(["key", "value"])->mapWithKeys(function ($item) {
                return [$item["key"] => $item["value"]];
            })->all()
        );
    }

    /**
     * @param $campaignId
     * @param $leadArray null
     * @return array
     */
    public function getTranslationValueToCertificateProvider($campaignId, $leadArray = null)
    {
        // Pull full lead format.
        if($leadArray == null) {
            $leadArray = $this->retrieveFullFormat();
        }

        // Build out the certificate array.
        // TODO: This one's for (Trusted Form)
        $certificateArray = [
            "first_name" => "",
            "last_name" => "",
            "email_address" => "",
            "address1" => "",
            "address2" => "",
            "city" => "",
            "state" => "",
            "zipcode" => "",
            "phonenumber" => "",
            "dob" => ""
        ];

        // Standard first, last, email, etc.
        $stdQueryKeys = ["first_name", "last_name", "email_address"];
        foreach ($stdQueryKeys as $key) {
            if (array_key_exists($key, $leadArray)) {
                if ($leadArray[$key] != "") {
                    $certificateArray[$key] = $leadArray[$key];
                }
            }
        }

        // Go through the certificate array, and place via outgoing fields and etc.
        foreach ($certificateArray as $queryKey => $value) {
            if (CampaignField::whereCampaignId($campaignId)->where('tf_value', $queryKey)->exists()) {
                $existingCampaignFieldTranslation = CampaignField::whereCampaignId($campaignId)->where('tf_value', $queryKey)->first();
                if($certificateArray[$queryKey] == "") {
                    if(isset($leadArray[$existingCampaignFieldTranslation->incoming_field])) {
                        $certificateArray[$queryKey] = $leadArray[$existingCampaignFieldTranslation->incoming_field];    
                    }
                }
            }
        }

        return $certificateArray;
    }

    /**
     * Has Point Or Empty.
     * @param $leadPointKey
     * @return string
     */
    public function hasPointOrEmpty($leadPointKey)
    {
        if (LeadPoint::whereLeadId($this->id)->where("key", $leadPointKey)->exists()) {
            try {
                return LeadPoint::whereLeadId($this->id)->where("key", $leadPointKey)->first()->value;
            } catch (\Exception $e) {
                return "";
            }
        } else {
            return "";
        }
    }
}