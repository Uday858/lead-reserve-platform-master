<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CampaignPublisherReport
 *
 * @property string $report_guid
 * @property string $timestamp
 * @property int $campaign_id
 * @property int $publisher_id
 * @property int|null $leads_generated
 * @property int|null $leads_accepted
 * @property int|null $leads_rejected
 * @property int|null $metric_impressions
 * @property int|null $metric_clicks
 * @property int|null $metric_conversions
 * @property float|null $revenue
 * @property float|null $payout
 * @property float|null $margin
 * @property string|null $cache_data
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereCacheData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereLeadsAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereLeadsGenerated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereLeadsRejected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereMargin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereMetricClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereMetricConversions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereMetricImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport wherePayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport wherePublisherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereReportGuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignPublisherReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CampaignPublisherReport extends Model
{
    protected $table = "generated_campaign_publisher_reports", $guarded = ["created_at","updated_at"];
}
