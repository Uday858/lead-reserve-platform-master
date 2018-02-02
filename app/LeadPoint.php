<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LeadPoint
 *
 * @property int $id
 * @property int $campaign_id
 * @property int $lead_id
 * @property string $key
 * @property string $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\LeadPoint whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LeadPoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LeadPoint whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LeadPoint whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LeadPoint whereLeadId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LeadPoint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LeadPoint whereValue($value)
 * @mixin \Eloquent
 */
class LeadPoint extends Model
{
    protected $guarded = ["created_at","updated_at"];
}
