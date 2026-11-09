<?php

namespace App\Services;

use App\Enums\EventActor;
use App\Enums\OrderEventType;
use App\Models\Order;
use App\Models\OrderEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderEventRecorder
{
    public function record(
        Order $order,
        OrderEventType $event,
        EventActor $actor,
        array $meta = [],
        ?Carbon $occurredAt = null
    ): OrderEvent {
        return DB::transaction(function () use ($order, $event, $actor, $meta, $occurredAt) {
            return OrderEvent::create([
                'order_id' => $order->id,
                'event' => $event,
                'occurred_at' => $occurredAt ?? now(),
                'actor' => $actor,
                'meta' => $meta ?: null,
            ]);
        });
    }
}
