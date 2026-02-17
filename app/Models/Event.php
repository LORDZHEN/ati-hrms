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
        'is_active' => 'boolean',
        'event_date' => 'date',
        'event_time' => 'datetime',
    ];

    /**
     * Get type badge color
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match($this->type) {
            'event' => 'success',
            'meeting' => 'info',
            'deadline' => 'danger',
            'training' => 'warning',
            'holiday' => 'primary',
            default => 'gray',
        };
    }

    /**
     * Scope: Active events
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Upcoming events
     */
    public function scopeUpcoming($query, $days = 60)
    {
        return $query->where('event_date', '>=', now()->toDateString())
            ->where('event_date', '<=', now()->addDays($days)->toDateString())
            ->orderBy('event_date')
            ->orderBy('event_time');
    }

    /**
     * Scope: Past events
     */
    public function scopePast($query)
    {
        return $query->where('event_date', '<', now()->toDateString())
            ->orderBy('event_date', 'desc');
    }

    /**
     * Scope: This month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('event_date', now()->month)
            ->whereYear('event_date', now()->year);
    }

    /**
     * Relationship: Creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if event is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->event_date >= now()->toDateString();
    }

    /**
     * Check if event is today
     */
    public function isToday(): bool
    {
        return $this->event_date->isToday();
    }

    /**
     * Get formatted date and time
     */
    public function getFormattedDateTimeAttribute(): string
    {
        return $this->event_date->format('M d, Y') . ' at ' . Carbon::parse($this->event_time)->format('g:i A');
    }
}
