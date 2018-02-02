<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CampaignField
 *
 * @property int $id
 * @property int $campaign_id
 * @property string $type
 * @property string $label
 * @property string $incoming_field
 * @property string $outgoing_field
 * @property string $hardcoded_value
 * @property string $system_value
 * @property string $inclusion_value
 * @property string $random_value
 * @property string $spec_caption
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereHardcodedValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereInclusionValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereIncomingField($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereLabel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereOutgoingField($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereRandomValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereSpecCaption($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereSystemValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignField whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $tf_value
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CampaignField whereTfValue($value)
 */
class CampaignField extends Model
{
    protected $guarded = ["created_at","updated_at"];
}
