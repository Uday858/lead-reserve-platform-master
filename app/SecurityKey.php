<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SecurityKey
 *
 * @property int $id
 * @property string|null $label
 * @property string $secret
 * @property string $hash
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SecurityKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SecurityKey whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SecurityKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SecurityKey whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SecurityKey whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SecurityKey whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SecurityKeyPermission[] $permissions
 */
class SecurityKey extends Model
{
    protected $guarded = [
        "created_at",
        "updated_at"
    ];

    /**
     * Return the permissions for this exact security key.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions() {
        return $this->hasMany(SecurityKeyPermission::class,"security_key_id","id");
    }
}
