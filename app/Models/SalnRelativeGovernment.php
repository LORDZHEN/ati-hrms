<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalnRelativeGovernment extends Model
{
    protected $table = 'saln_relatives_government'; // Explicitly set because of unusual pluralization

    protected $fillable = [
        'saln_id',
        'name_of_relative',
        'relationship',
        'position',
        'name_of_agency_office_address',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }
}
