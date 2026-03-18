<?php
// ============================================================================
//  ANNEX B MODELS — Declarant's Exclusive Properties
//  Place each class in its own file under App\Models\
// ============================================================================

// ── SalnAnnexBRealProperty.php ───────────────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalnAnnexBBusinessInterest extends Model
{
    protected $fillable = [
        'saln_id',
        'name_of_entity',
        'business_address',
        'nature_of_business_interest',
        'date_of_acquisition',
    ];

    protected $casts = [
        'date_of_acquisition' => 'date',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }
}
