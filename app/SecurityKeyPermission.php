<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SecurityKeyPermission
 *
 * @property int $id
 * @property int $security_key_id
 * @property string $action
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SecurityKeyPermission whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SecurityKeyPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SecurityKeyPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SecurityKeyPermission whereSecurityKeyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SecurityKeyPermission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SecurityKeyPermission extends Model
{
    protected $guarded = [
        "created_at",
        "updated_at"
    ];
}
