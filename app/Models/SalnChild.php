<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalnChild extends Model
{
    protected $table = 'saln_children'; // Explicitly set table name

    protected $fillable = [
        'saln_id',
        'name',
        'date_of_birth',
        'age',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function saln(): BelongsTo
    {
        return $this->belongsTo(Saln::class);
    }
}
