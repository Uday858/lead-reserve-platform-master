<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CampaignPostingParameter
 *
 * @property int $id
 * @property int $campaign_id
 * @property string $type
 * @property string $incoming_field
 * @property string $outgoing_field
 * @property string $label
 * @property bool $is_static
 * @property string $static_value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignPostingParameter whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignPostingParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignPostingParameter whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignPostingParameter whereIncomingField($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignPostingParameter whereIsStatic($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignPostingParameter whereLabel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignPostingParameter whereOutgoingField($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignPostingParameter whereStaticValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignPostingParameter whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignPostingParameter whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CampaignPostingParameter extends Model
{
    protected $guarded = ["created_at","updated_at"];
}
