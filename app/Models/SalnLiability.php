<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalnLiability extends Model
{
    // Laravel will automatically use 'saln_liabilities' table

    protected $fillable = [
        'saln_id',
        'nature',
        'name_of_creditors',
        'outstanding_balance',
    ];

    protected $casts = [
        'outstanding_balance' => 'decimal:2',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Recalculate totals when liability is created, updated, or deleted
        static::saved(function ($liability) {
            if ($liability->saln) {
                $liability->saln->calculateTotals();
            }
        });

        static::deleted(function ($liability) {
            if ($liability->saln) {
                $liability->saln->calculateTotals();
            }
        });
    }
}
