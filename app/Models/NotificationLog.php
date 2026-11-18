<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'session_id',
        'type',
        'idempotency_key',
        'recipient_type',
        'recipient_number',
        'message',
        'scheduled_at',
        'sent_at',
        'status',
        'provider_message_id',
        'error_message',
        'failure_code',
        'attempts',
        'processing_started_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'date',
            'sent_at' => 'datetime',
            'processing_started_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}