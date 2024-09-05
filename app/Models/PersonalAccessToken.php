<?php

namespace App\Models;

use App\Events\PersonalAccessTokens\PersonalAccessTokenWasCreated;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $casts = [
        'abilities' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'token',
        'secret',
        'abilities',
        'team_id',
    ];

    protected $dispatchesEvents = [
        'created' => PersonalAccessTokenWasCreated::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tokenable_id', 'id');
    }
}
