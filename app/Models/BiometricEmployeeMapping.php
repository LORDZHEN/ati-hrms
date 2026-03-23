<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BiometricEmployeeMapping
 *
 * Maps a biometric device enrollment number to a system user.
 *
 * KEY DESIGN DECISIONS
 * ────────────────────
 * • device_id is stored as a string. ZKTeco devices use integers (1–9999)
 *   but other vendors may use alphanumeric IDs. String storage is safer.
 *
 * • is_active = false (soft-deactivation) is used instead of deletion so
 *   that historical import records remain auditable. A deleted mapping row
 *   would break any retrospective lookup of "which person had device ID 5
 *   in February 2026?"
 *
 * • The findByDeviceId() static helper is intentionally kept lean — it
 *   returns null on no match so the caller decides what to do, rather than
 *   throwing an exception. XlsLogParser uses this for its primary lookup.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $device_id
 * @property string|null $device_name
 * @property bool        $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User   $user
 */
class BiometricEmployeeMapping extends Model
{

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /**
     * Scope to only active mappings.
     * Usage: BiometricEmployeeMapping::active()->get()
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ── Static helpers ────────────────────────────────────────────────────────

    /**
     * Find the User linked to a biometric device ID.
     *
     * This is the PRIMARY lookup used by XlsLogParser::detectEmployees().
     * Returns null — never throws — so the caller can decide whether to
     * fall back to the legacy users.employee_id lookup or log a warning.
     *
     * @param  string|int $deviceId  The enrollment number from the XLS file
     * @return User|null
     */
    public static function findUserByDeviceId(string|int $deviceId): ?User
    {
        $mappingsByDeviceId = BiometricEmployeeMapping::active()
            ->with('user')
            ->get()
            ->keyBy('device_id');

        $user = $mappingsByDeviceId->get($deviceId)?->user;
    }

    /**
     * Deactivate all currently active mappings for a given device ID.
     *
     * Called by the Filament resource before creating a new active mapping
     * to prevent the unique(device_id, is_active) constraint from firing.
     *
     * /**
     * Deactivate all currently active mappings for a given device ID.
     *
     * @param  string|int $deviceId
     * @param  int|null   $excludeId  Exclude this mapping ID from deactivation (for edit operations)
     */
    public static function deactivateByDeviceId(string|int $deviceId, ?int $excludeId = null): void
    {
        if (empty(trim((string) $deviceId))) {
            return; // Guard against accidental deactivation of all empty-device-id rows
        }

        $query = static::active()
            ->where('device_id', trim((string) $deviceId));

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        $query->update(['is_active' => false]);
    }
}
