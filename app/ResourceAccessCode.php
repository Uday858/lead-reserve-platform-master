<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ResourceAccessCode
 *
 * @property int $id
 * @property bool $is_active
 * @property string $resource_name
 * @property string $resource_path
 * @property string $access_secret
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\ResourceAccessCode whereAccessSecret($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ResourceAccessCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ResourceAccessCode whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ResourceAccessCode whereIsActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ResourceAccessCode whereResourceName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ResourceAccessCode whereResourcePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ResourceAccessCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ResourceAccessCode extends Model
{
    protected $guarded = ["created_at","updated_at"];
}
