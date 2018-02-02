<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PlatformReport
 *
 * @property string $report_guid
 * @property string $timestamp
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereCacheData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereLeadsAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereLeadsGenerated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereLeadsRejected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereMargin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereMetricClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereMetricConversions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereMetricImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport wherePayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereReportGuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlatformReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PlatformReport extends Model
{
    protected $table = 'daily_platform_reports';
    protected $guarded = ["created_at","updated_at"];
}
