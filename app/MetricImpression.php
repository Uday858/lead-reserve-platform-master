<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MetricImpression
 *
 * @property int $id
 * @property int $campaign_id
 * @property int $publisher_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\MetricImpression whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricImpression whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricImpression whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricImpression wherePublisherId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricImpression whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MetricImpression extends Model
{
    protected $guarded = ["created_at","updated_at"];
}
