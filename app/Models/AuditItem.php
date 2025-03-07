<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class AuditItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditable_id',
        'auditable_type',
        'auditable_text',
        'auditable_team_id',
    ];
    protected $appends = [
        'dashboard_url',
        'admin_url',
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'auditable_team_id', 'id');
    }

    /**
     * Get the audit trails dashboard url
     *
     * @return Attribute
     */
    public function dashboardUrl(): Attribute
    {
        $url = '#';
        switch ($this->auditable_type) {
            case User::class:
            case Team::class:
            case PersonalAccessToken::class:
            case SanctumPersonalAccessToken::class:
            case TeamUser::class:
                $url = '/my-team';
                break;
            case VoucherSet::class:
                $url = '/voucher-set/' . $this->auditable_id;
                break;
        }

        return Attribute::make(
            get: fn ($value, $attributes) => $url,
        );
    }

    /**
     * Get the audit trails dashboard url
     *
     * @return Attribute
     */
    public function adminUrl(): Attribute
    {
        $url = '#';
        switch ($this->auditable_type) {
            case User::class:
                $url = '/admin/user/' . $this->auditable_id;
                break;
            case Team::class:
            case TeamUser::class:
                $url = '/admin/team/' . $this->auditable_id;
                break;
            case VoucherSet::class:
                $url = '/admin/voucher-set/' . $this->auditable_id;
                break;
            case PersonalAccessToken::class:
            case SanctumPersonalAccessToken::class:
                $url = '/admin/api-access-token/' . $this->auditable_id;
                break;

        }

        return Attribute::make(
            get: fn ($value, $attributes) => $url,
        );
    }
}
