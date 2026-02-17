<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'event_time',
        'location',
        'type',
        'color',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'event_date' => 'date',
        'event_time' => 'datetime',
    ];

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->type) {
            'event'    => 'success',
            'meeting'  => 'info',
            'deadline' => 'danger',
            'training' => 'warning',
            'holiday'  => 'primary',
            default    => 'gray',
        };
    }

    /**
     * The datetime after which this event is considered fully over.
     * We treat an event as "over" 24 hours after its event_date begins,
     * so events on today still appear all day and disappear at midnight +24h.
     */
    public function getAutoHideAtAttribute(): Carbon
    {
        return $this->event_date->copy()->addHours(24);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Active events: is_active = true AND the event has not yet passed its
     * 24-hour grace window (event_date + 24 h > now).
     */
    public function scopeActive($query)
    {
        // event_date + INTERVAL 1 DAY > NOW()
        // Using raw expression for cross-DB compatibility (MySQL / SQLite / Postgres)
        return $query
            ->where('is_active', true)
            ->whereRaw("DATE_ADD(event_date, INTERVAL 1 DAY) > NOW()");
    }

    /**
     * Active events (SQLite-compatible alias — use this if your dev env uses SQLite).
     * Swap scopeActive above with this if needed:
     *
     *   ->where('event_date', '>=', now()->subDay()->toDateString())
     */

    /**
     * Upcoming events within the next N days (still respects the 24h grace).
     */
    public function scopeUpcoming($query, $days = 60)
    {
        return $query
            ->where('event_date', '>=', now()->subDay()->toDateString()) // keep today's + yesterday (grace)
            ->where('event_date', '<=', now()->addDays($days)->toDateString())
            ->orderBy('event_date')
            ->orderBy('event_time');
    }

    /**
     * Past events: fully expired (more than 24 h ago).
     */
    public function scopePast($query)
    {
        return $query
            ->whereRaw("DATE_ADD(event_date, INTERVAL 1 DAY) <= NOW()")
            ->orderBy('event_date', 'desc');
    }

    /**
     * Events whose 24-hour window has passed but are still marked active.
     * Used by the scheduled command.
     */
    public function scopeExpiredAndActive($query)
    {
        return $query
            ->where('is_active', true)
            ->whereRaw("DATE_ADD(event_date, INTERVAL 1 DAY) <= NOW()");
    }

    /**
     * This month's events.
     */
    public function scopeThisMonth($query)
    {
        return $query
            ->whereMonth('event_date', now()->month)
            ->whereYear('event_date', now()->year);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isUpcoming(): bool
    {
        return $this->event_date >= now()->toDateString();
    }

    public function isToday(): bool
    {
        return $this->event_date->isToday();
    }

    /**
     * Returns true if this event is within its 24-hour grace window.
     */
    public function isWithinGracePeriod(): bool
    {
        return $this->autoHideAt->isFuture();
    }

    public function getFormattedDateTimeAttribute(): string
    {
        return $this->event_date->format('M d, Y') . ' at ' . Carbon::parse($this->event_time)->format('g:i A');
    }
}
