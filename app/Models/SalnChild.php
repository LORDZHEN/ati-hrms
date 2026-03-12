<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 2025 SALN Format: Children table stores Name and Age only.
 * Date of birth is no longer required by the CSC 2025 form.
 */
class SalnChild extends Model
{
    protected $table = 'saln_children';

    protected $fillable = [
        'saln_id',
        'name',
        'age',
        // date_of_birth kept for backward compatibility with existing records
        'date_of_birth',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }
}
