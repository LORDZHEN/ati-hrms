<?php
// ============================================================================
//  ANNEX B MODELS — Declarant's Exclusive Properties
//  Place each class in its own file under App\Models\
// ============================================================================

// ── SalnAnnexBRealProperty.php ───────────────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalnAnnexCPersonalProperty extends Model
{
    // Table: saln_annex_c_personal_properties
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
        static::saved(fn($m) => $m->saln?->calculateTotals());
        static::deleted(fn($m) => $m->saln?->calculateTotals());
    }
}
