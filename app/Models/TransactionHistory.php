<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model: TransactionHistory
 *
 * Represents a single logged HRMS activity.
 *
 * Provides a static ::log() helper so any module can record a transaction
 * in one line without coupling to this model's internals:
 *
 *   TransactionHistory::log([
 *       'user_id'          => auth()->id(),
 *       'employee_name'    => $user->full_name,
 *       'transaction_type' => 'Leave Application',
 *       'module'           => 'Leave',
 *       'description'      => 'Filed a sick leave for 3 days',
 *       'status'           => 'pending',
 *       'icon'             => 'heroicon-o-calendar',
 *       'color'            => 'blue',
 *       'record_id'        => $leave->id,
 *       'record_url'       => route('filament.hrms.resources.leave-applications.view', $leave->id),
 *   ]);
 *
 * @property int         $id
 * @property int|null    $user_id
 * @property string      $employee_name
 * @property string      $transaction_type
 * @property string      $module
 * @property string      $description
 * @property string      $status
 * @property string|null $icon
 * @property string|null $color
 * @property int|null    $record_id
 * @property string|null $record_url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class TransactionHistory extends Model
{
    // ── Fillable ──────────────────────────────────────────────────────────────

    protected $fillable = [
        'user_id',
        'employee_name',
        'transaction_type',
        'module',
        'description',
        'status',
        'icon',
        'color',
        'record_id',
        'record_url',
    ];

    // ── Casts ─────────────────────────────────────────────────────────────────

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'record_id'  => 'integer',
        'user_id'    => 'integer',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * The user who performed the transaction.
     * Nullable — system-generated entries may not have a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name'     => 'System',
            'full_name' => 'System',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Static Helper
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Log a new HRMS transaction.
     *
     * This is the single entry-point for all modules to record activity.
     * Wraps model creation so callers never touch the model directly.
     *
     * @param  array{
     *     user_id?: int|null,
     *     employee_name: string,
     *     transaction_type: string,
     *     module: string,
     *     description: string,
     *     status?: string,
     *     icon?: string|null,
     *     color?: string|null,
     *     record_id?: int|null,
     *     record_url?: string|null,
     * } $data
     */
    public static function log(array $data): self
    {
        return static::create([
            'user_id'          => $data['user_id']          ?? null,
            'employee_name'    => $data['employee_name']    ?? 'Unknown',
            'transaction_type' => $data['transaction_type'] ?? 'Activity',
            'module'           => $data['module']           ?? 'General',
            'description'      => $data['description']      ?? '',
            'status'           => $data['status']           ?? 'pending',
            'icon'             => $data['icon']             ?? null,
            'color'            => $data['color']            ?? 'gray',
            'record_id'        => $data['record_id']        ?? null,
            'record_url'       => $data['record_url']       ?? null,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────────────────

    /** Filter by module slug (case-insensitive). */
    public function scopeForModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }

    /** Filter by status. */
    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers / Presentation
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Map of module → default Heroicon name.
     * Used as fallback when the stored icon field is empty.
     */
    public static function moduleIcon(string $module): string
    {
        return match (strtolower($module)) {
            'leave'    => 'heroicon-o-calendar',
            'travel'   => 'heroicon-o-briefcase',
            'locator'  => 'heroicon-o-map-pin',
            'saln'     => 'heroicon-o-document-text',
            'pds'      => 'heroicon-o-identification',
            'employee' => 'heroicon-o-user-plus',
            'dtr'      => 'heroicon-o-clock',
            default    => 'heroicon-o-bolt',
        };
    }

    /**
     * Map of status → Tailwind color key for badge rendering.
     */
    public static function statusColor(string $status): string
    {
        return match (strtolower($status)) {
            'pending'    => 'amber',
            'approved'   => 'success',
            'rejected'   => 'danger',
            'filed'      => 'info',
            'uploaded'   => 'info',
            'registered' => 'success',
            'submitted'  => 'info',
            'cancelled'  => 'gray',
            default      => 'gray',
        };
    }

    /**
     * Resolved icon — falls back to module map if stored value is empty.
     */
    public function getResolvedIconAttribute(): string
    {
        return $this->icon ?: static::moduleIcon($this->module);
    }

    /**
     * Initials extracted from employee_name for the avatar fallback.
     */
    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->employee_name));
        $first = strtoupper(substr($parts[0] ?? 'U', 0, 1));
        $last  = strtoupper(substr($parts[array_key_last($parts)] ?? '', 0, 1));

        return $first . ($last !== $first ? $last : '');
    }
}
