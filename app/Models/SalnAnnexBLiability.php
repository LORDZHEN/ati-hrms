<?php
// ============================================================================
//  ANNEX B MODELS — Declarant's Exclusive Properties
//  Place each class in its own file under App\Models\
// ============================================================================

// ── SalnAnnexBRealProperty.php ───────────────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalnAnnexBLiability extends Model
{
    // Table: saln_annex_b_liabilities
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
        static::saved(fn($m) => $m->saln?->calculateTotals());
        static::deleted(fn($m) => $m->saln?->calculateTotals());
    }
}
