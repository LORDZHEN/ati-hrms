<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalnBusinessInterest extends Model
{
    // Laravel will automatically use 'saln_business_interests' table

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
