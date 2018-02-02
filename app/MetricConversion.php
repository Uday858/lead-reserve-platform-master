<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\MetricConversion
 *
 * @property int $id
 * @property int $click_id
 * @property int $campaign_id
 * @property int $publisher_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\MetricConversion whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricConversion whereClickId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricConversion whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricConversion whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricConversion wherePublisherId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MetricConversion whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $cost
 * @property string $payout
 * @property string $margin
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MetricConversion whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MetricConversion whereMargin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MetricConversion wherePayout($value)
 */
class MetricConversion extends Model
{
    protected $guarded = ["created_at","updated_at"];
}
