<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CampaignAttribute
 *
 * @property int $id
 * @property int $campaign_id
 * @property string $name
 * @property int $storage_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignAttribute whereCampaignId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignAttribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignAttribute whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignAttribute whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignAttribute whereStorageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CampaignAttribute whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\MutableDataPair $data
 */
class CampaignAttribute extends Model
{
    protected $guarded = ["created_at","updated_at"];

    /**
     * Retrieve the storage item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function data()
    {
        return $this->hasOne(MutableDataPair::class,"id","storage_id");
    }
}
