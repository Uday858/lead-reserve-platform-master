<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Publisher
 *
 * @property int $id
 * @property int $owner_user_id
 * @property string $name
 * @property string $email
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Publisher whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Publisher whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Publisher whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Publisher whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Publisher whereOwnerUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Publisher whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PublisherCampaign[] $publisherCampaigns
 */
class Publisher extends Model
{
    protected $guarded = ["created_at","updated_at"];

    public function publisherCampaigns() {
        return $this->hasMany(PublisherCampaign::class,"publisher_id","id");
    }

    /**
     * Return foundation attribute based off of name.
     *
     * @param $attributeName
     * @return string
     */
    public function hasAttributeOrEmpty($attributeName)
    {
        if(FoundationAttribute::whereFoundationParentId($this->id)->whereName($attributeName)->exists()) {
            try {
                return FoundationAttribute::whereFoundationParentId($this->id)->whereName($attributeName)->first()->data->value;
            } catch(\Exception $e) {
                return "";
            }
        } else {
            return "";
        }
    }
}
