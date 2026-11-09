<?php

namespace App\Models;

use App\Enums\EventActor;
use App\Enums\OrderEventType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEvent extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'event', 'occurred_at', 'actor', 'meta'];

    protected $casts = [
        'event' => OrderEventType::class,
        'actor' => EventActor::class,
        'occurred_at' => 'datetime',
        'meta' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
