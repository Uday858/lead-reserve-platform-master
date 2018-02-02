<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MetricClick
 *
 * @property int $id
 * @property int $campaign_id
 * @property int $publisher_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\MetricClick whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricClick whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricClick whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricClick wherePublisherId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricClick whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MetricClick extends Model
{
    protected $guarded = ["created_at","updated_at"];
}
