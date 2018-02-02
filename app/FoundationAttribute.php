<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FoundationAttribute
 *
 * @property-read \App\MutableDataPair $data
 * @mixin \Eloquent
 * @property int $id
 * @property int $foundation_parent_id
 * @property string $name
 * @property int $storage_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\FoundationAttribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FoundationAttribute whereFoundationParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FoundationAttribute whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FoundationAttribute whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FoundationAttribute whereStorageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\FoundationAttribute whereUpdatedAt($value)
 */
class FoundationAttribute extends Model
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
