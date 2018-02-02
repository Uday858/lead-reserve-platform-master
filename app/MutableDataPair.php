<?php

namespace App;

use App\Providers\MutableDataProcessorProvider;
use Illuminate\Database\Eloquent\Model;

/**
 * App\MutableDataPair
 *
 * @property int $id
 * @property int $parent_id
 * @property string $parent_type
 * @property string $data_description
 * @property string $type
 * @property int $integer_value
 * @property float $float_value
 * @property bool $bool_value
 * @property string $string_value
 * @property string $json_value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereBoolValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereDataDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereFloatValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereIntegerValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereJsonValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereParentType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereStringValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\MutableDataPair whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $value
 */
class MutableDataPair extends Model
{
    protected $guarded = ["created_at","updated_at"];
    public $appends = ["value"];

    /**
     * Return the value.
     *
     * @return mixed
     */
    public function getValueAttribute()
    {
        return $this[$this->type . "_value"];
    }
}
