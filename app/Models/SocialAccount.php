<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OAuth Social Account — stores provider tokens per user.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $provider        google|facebook|apple
 * @property string      $provider_id
 * @property string|null $provider_email
 * @property string|null $provider_name
 * @property string|null $provider_avatar
 * @property string|null $access_token
 * @property string|null $refresh_token
 * @property \Carbon\Carbon|null $token_expires_at
 */
class SocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'provider_email',
        'provider_name',
        'provider_avatar',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    /** The user this social account belongs to. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
