<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasRelativeDates
{
    /**
     * @return Attribute
     */
    public function createdAtRelative(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $this->created_at->diffForHumans(),
        );
    }

    /**
     * @return Attribute
     */
    public function createdAtDateTime(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $this->created_at->toDateTimeString(),
        );
    }
}
