<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalnRealProperty extends Model
{
    // Laravel will automatically use 'saln_real_properties' table

    protected $fillable = [
        'saln_id',
        'description',
        'kind',
        'exact_location',
        'assessed_value',
        'current_fair_market_value',
        'acquisition_year',
        'mode_of_acquisition',
        'acquisition_cost',
    ];

    protected $casts = [
        'assessed_value' => 'decimal:2',
        'current_fair_market_value' => 'decimal:2',
        'acquisition_cost' => 'decimal:2',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Recalculate totals when real property is created, updated, or deleted
        static::saved(function ($realProperty) {
            if ($realProperty->saln) {
                $realProperty->saln->calculateTotals();
            }
        });

        static::deleted(function ($realProperty) {
            if ($realProperty->saln) {
                $realProperty->saln->calculateTotals();
            }
        });
    }
}
