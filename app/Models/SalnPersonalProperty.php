<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalnPersonalProperty extends Model
{
    // Laravel will automatically use 'saln_personal_properties' table

    protected $fillable = [
        'saln_id',
        'description',
        'year_acquired',
        'acquisition_cost',
    ];

    protected $casts = [
        'acquisition_cost' => 'decimal:2',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Recalculate totals when personal property is created, updated, or deleted
        static::saved(function ($personalProperty) {
            if ($personalProperty->saln) {
                $personalProperty->saln->calculateTotals();
            }
        });

        static::deleted(function ($personalProperty) {
            if ($personalProperty->saln) {
                $personalProperty->saln->calculateTotals();
            }
        });
    }
}
