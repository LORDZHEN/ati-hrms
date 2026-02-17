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
        'expires_at',   // auto-expire datetime (set from duration on create/edit)
        'created_by',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'publish_date' => 'date',
        'expiry_date'  => 'date',
        'expires_at'   => 'datetime',
    ];

    // ─── Duration options shown in the form ───────────────────────────────────

    /**
     * How many hours each duration label represents.
     * null = no auto-expire (manual control only).
     */
    public static function getDurationOptions(): array
    {
        return [
            ''    => 'No auto-expire (manual)',
            '24'  => '24 hours',
            '48'  => '48 hours',
            '72'  => '3 days',
            '168' => '7 days',
            '336' => '14 days',
            '720' => '30 days',
        ];
    }

    // ─── Icon options ─────────────────────────────────────────────────────────

    public static function getIconOptions(): array
    {
        return [
            'heroicon-o-megaphone'            => 'Megaphone',
            'heroicon-o-bell'                 => 'Bell',
            'heroicon-o-exclamation-triangle' => 'Warning',
            'heroicon-o-information-circle'   => 'Information',
            'heroicon-o-wrench-screwdriver'   => 'Maintenance',
            'heroicon-o-document-chart-bar'   => 'Report',
            'heroicon-o-gift'                 => 'Gift',
            'heroicon-o-calendar'             => 'Calendar',
            'heroicon-o-shield-check'         => 'Security',
            'heroicon-o-light-bulb'           => 'Idea',
        ];
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'high'   => 'danger',
            'medium' => 'warning',
            'low'    => 'info',
            default  => 'gray',
        };
    }

    /**
     * Human-readable time remaining until auto-expiry.
     */
    public function getExpiresInAttribute(): ?string
    {
        if (! $this->expires_at) {
            return null;
        }

        if ($this->expires_at->isPast()) {
            return 'Expired';
        }

        return $this->expires_at->diffForHumans();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Active announcements:
     *  - is_active = true
     *  - publish_date is null OR publish_date <= now
     *  - expiry_date is null OR expiry_date >= today
     *  - expires_at is null OR expires_at > now  (auto-expire datetime check)
     */
    public function scopeActive($query)
    {
        return $query
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('publish_date')
                  ->orWhere('publish_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Announcements whose auto-expire time has passed and are still marked active.
     * Used by the scheduled command.
     */
    public function scopeExpiredAndActive($query)
    {
        return $query
            ->where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Recent announcements (within last N days).
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isPublished(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->publish_date && $this->publish_date->isFuture()) {
            return false;
        }

        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Compute and set expires_at from a duration in hours.
     * Call this when saving from the form.
     *
     * @param  int|string|null $hours  Number of hours, or null/empty to clear.
     */
    public function setExpiresAtFromHours($hours): void
    {
        if ($hours !== null && $hours !== '') {
            $this->expires_at = now()->addHours((int) $hours);
        } else {
            $this->expires_at = null;
        }
    }
}
