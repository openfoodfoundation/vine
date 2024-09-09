<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class VoucherTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = [
        'example_template_image_url',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the image url from AWS
     *
     * @return Attribute
     */
    public function exampleTemplateImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => (!is_null($this->voucher_example_template_path)) ? Storage::temporaryUrl(
                path: $this->voucher_example_template_path,
                expiration: now()->addHour()
            ) : '',
        );
    }
}
