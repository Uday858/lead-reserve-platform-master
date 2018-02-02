<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PublisherCampaign
 *
 * @property int $id
 * @property int $publisher_id
 * @property int $campaign_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\PublisherCampaign whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PublisherCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PublisherCampaign whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PublisherCampaign wherePublisherId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PublisherCampaign whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Campaign $campaign
 * @property-read \App\Publisher $publisher
 * @property float $payout
 * @property int $lead_cap
 * @method static \Illuminate\Database\Query\Builder|\App\PublisherCampaign whereLeadCap($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PublisherCampaign wherePayout($value)
 */
class PublisherCampaign extends Model
{
    protected $guarded = ["created_at","guarded_at"];

    public function publisher() {
        return $this->hasOne(Publisher::class,"id","publisher_id");
    }
    public function campaign() {
        return $this->hasOne(Campaign::class,"id","campaign_id");
    }
}
