<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CampaignType
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignType whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignType whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignType whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignType whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CampaignType extends Model
{
    //
}
