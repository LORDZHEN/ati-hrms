<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Notification extends Model
{
    use Notifiable;

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Relation to the user or model that this notification belongs to
    public function notifiable()
    {
        return $this->morphTo();
    }

    // Helper: Check if notification is read
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    // Mark as read
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }
}
