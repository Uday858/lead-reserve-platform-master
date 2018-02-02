<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CampaignReport
 *
 * @property string $report_guid
 * @property string $timestamp
 * @property int $campaign_id
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereCacheData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereLeadsAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereLeadsGenerated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereLeadsRejected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereMargin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereMetricClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereMetricConversions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereMetricImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport wherePayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereReportGuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CampaignReport extends Model
{
    protected $table = "generated_campaign_reports", $guarded = ["created_at","updated_at"];
}
