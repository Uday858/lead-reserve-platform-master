<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PlatformEvent
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property int $integer_value
 * @property float $float_value
 * @property bool $bool_value
 * @property string $string_value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\PlatformEvent whereBoolValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PlatformEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PlatformEvent whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PlatformEvent whereFloatValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PlatformEvent whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PlatformEvent whereIntegerValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PlatformEvent whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PlatformEvent whereStringValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PlatformEvent whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PlatformEvent whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $json_value
 * @method static \Illuminate\Database\Query\Builder|\App\PlatformEvent whereJsonValue($value)
 */
class PlatformEvent extends Model
{
    protected $guarded = ["created_at","updated_at"];
}
