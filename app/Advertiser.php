<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Advertiser
 *
 * @property int $id
 * @property int $owner_user_id
 * @property string $name
 * @property string $email
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Advertiser whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Advertiser whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Advertiser whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Advertiser whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Advertiser whereOwnerUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Advertiser whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Campaign[] $campaigns
 */
class Advertiser extends Model
{
    protected $guarded = ["created_at","updated_at"];

    /**
     * Retrieve all campaigns
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function campaigns() {
        return $this->hasMany(Campaign::class,"advertiser_id","id");
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
