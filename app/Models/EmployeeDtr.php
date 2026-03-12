<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDtr extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'file_path',
        'notes',
        'record_type',
        'period_label',  // e.g. "2026/02/01 ~ 02/28" — used by re-scan filter
        'import_batch',  // UUID grouping all records from one Submit click
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
