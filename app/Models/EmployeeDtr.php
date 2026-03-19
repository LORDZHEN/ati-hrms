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
        'period_label',
        'import_batch',
    ];

    /**
     * Normalise file_path to a plain string.
     *
     * Filament's FileUpload component serialises the path as a single-element
     * array in the manual upload wizard, while the biometric import stores it
     * as a plain string. This accessor centralises resolution so all callers
     * get a consistent string regardless of how the record was created.
     *
     * NOTE: DailyTimeRecordResource::resolveFilePath() performs the same
     * normalisation at the resource layer. Both are kept for safety — the
     * accessor handles direct model access; resolveFilePath() handles cases
     * where the raw DB value is read before Eloquent hydration.
     */
    public function getFilePathAttribute($value): string
    {
        return is_array($value) ? ($value[0] ?? '') : (string) $value;
    }
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
