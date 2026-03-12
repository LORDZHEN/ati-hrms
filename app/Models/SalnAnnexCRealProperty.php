<?php
// ============================================================================
//  ANNEX B MODELS — Declarant's Exclusive Properties
//  Place each class in its own file under App\Models\
// ============================================================================

// ── SalnAnnexBRealProperty.php ───────────────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalnAnnexCRealProperty extends Model
{
    // Table: saln_annex_c_real_properties
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
        'assessed_value'            => 'decimal:2',
        'current_fair_market_value' => 'decimal:2',
        'acquisition_cost'          => 'decimal:2',
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
