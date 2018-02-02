<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\UserType
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\UserType whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserType whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserType whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserType whereUserId($value)
 * @mixin \Eloquent
 */
class UserType extends Model
{
    protected $guarded = ["created_at","updated_at"];
}
