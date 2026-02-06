<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait HasSystemFields
{
    public static function bootHasSystemFields(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->CreatedByID = Auth::id();
                $model->LastModifiedByID = Auth::id();
            }
            $model->CreatedDateTime = now();
            $model->LastModifiedDateTime = now();
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->LastModifiedByID = Auth::id();
            }
            $model->LastModifiedDateTime = now();
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'CreatedByID');
    }

    public function lastModifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'LastModifiedByID');
    }
}
