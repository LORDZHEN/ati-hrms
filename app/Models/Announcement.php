<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'priority',
        'icon',
        'is_active',
        'publish_date',
        'expiry_date',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'publish_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Available icon options
     */
    public static function getIconOptions(): array
    {
        return [
            'heroicon-o-megaphone' => 'Megaphone',
            'heroicon-o-bell' => 'Bell',
            'heroicon-o-exclamation-triangle' => 'Warning',
            'heroicon-o-information-circle' => 'Information',
            'heroicon-o-wrench-screwdriver' => 'Maintenance',
            'heroicon-o-document-chart-bar' => 'Report',
            'heroicon-o-gift' => 'Gift',
            'heroicon-o-calendar' => 'Calendar',
            'heroicon-o-shield-check' => 'Security',
            'heroicon-o-light-bulb' => 'Idea',
        ];
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'info',
            default => 'gray',
        };
    }

    /**
     * Scope: Active announcements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('publish_date')
                  ->orWhere('publish_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now());
            });
    }

    /**
     * Scope: Recent announcements (within last 30 days)
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Relationship: Creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if announcement is currently published
     */
    public function isPublished(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->publish_date && $this->publish_date->isFuture()) {
            return false;
        }

        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return false;
        }

        return true;
    }
}
