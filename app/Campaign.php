<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Campaign
 *
 * @property int $id
 * @property int $advertiser_id
 * @property int $campaign_type_id
 * @property string $name
 * @property string $posting_url
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Campaign whereAdvertiserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Campaign whereCampaignTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Campaign whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Campaign whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Campaign wherePostingUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Campaign whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Advertiser $advertiser
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CampaignPostingParameter[] $posting_params
 * @property-read \App\CampaignType $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CampaignAttribute[] $attributes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CampaignField[] $fields
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CampaignReport[] $reports
 */
class Campaign extends Model
{
    protected $guarded = ["created_at","updated_at"];

    /**
     * Get full format of the campaign.
     * @return array
     */
    public function retrieveFullFormat()
    {
        return array_merge(
            $this->getAttributes(),
            [
                "type" => $this->type->name
            ],
            [
                "fields" => $this->fields->toArray(),
                "attributes" => $this->attributes
            ]
        );
    }

    /**
     * Return the correct advertiser object.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function advertiser() {
        return $this->hasOne(Advertiser::class,"id","advertiser_id");
    }

    /**
     * Return the correct type.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type() {
        return $this->hasOne(CampaignType::class,"id","campaign_type_id");
    }

    /**
     * Return the posting param(s).
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posting_params() {
        return $this->hasMany(CampaignPostingParameter::class,"campaign_id","id");
    }

    /**
     * Return the posting field(s).
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields() {
        return $this->hasMany(CampaignField::class,"campaign_id","id");
    }

    /**
     * Bring in all campaign attributes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributes() {
        return $this->hasMany(CampaignAttribute::class,"campaign_id","id");
    }

    public function reports() {
        return $this->hasMany(CampaignReport::class,"campaign_id","id");
    }

    public function publishers() {
        return $this->belongsToMany(Publisher::class,'publisher_campaigns','campaign_id','publisher_id');
    }

    /**
     * Return campaign attribute based off of name.
     *
     * @param $attributeName
     * @return string
     */
    public function hasAttributeOrEmpty($attributeName)
    {
        if(CampaignAttribute::whereCampaignId($this->id)->whereName($attributeName)->exists()) {
            try {
                return CampaignAttribute::whereCampaignId($this->id)->whereName($attributeName)->first()->data->value;
            } catch(\Exception $e) {
                return "";
            }
        } else {
            return "";
        }
    }
}
