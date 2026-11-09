<?php

namespace App\Actions;

use App\Enums\EventActor;
use App\Enums\OrderEventType;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderEventRecorder;
use Illuminate\Validation\ValidationException;

class TransitionOrderStatus
{
    public function __construct(
        protected OrderEventRecorder $recorder
    ) {}

    public function handle(Order $order, OrderStatus $next, EventActor $actor, array $meta = []): Order
    {
        $allowed = $this->allowedTransitions($order->status);

        if (! in_array($next, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => "Transisi status dari {$order->status->value} ke {$next->value} tidak diizinkan.",
            ]);
        }

        $order->update(['status' => $next]);

        $event = match ($next) {
            OrderStatus::Diterima => OrderEventType::AcceptedByCashier,
            OrderStatus::Diproses => OrderEventType::Preparing,
            OrderStatus::Siap => OrderEventType::Ready,
            OrderStatus::Selesai => OrderEventType::Completed,
            OrderStatus::Dibatalkan => OrderEventType::Cancelled,
            default => null,
        };

        if ($event) {
            $this->recorder->record($order, $event, $actor, $meta);
        }

        return $order->refresh();
    }

    /**
     * @return array<int, OrderStatus>
     */
    protected function allowedTransitions(OrderStatus $current): array
    {
        return match ($current) {
            OrderStatus::Dikonfirmasi => [OrderStatus::Diterima, OrderStatus::Dibatalkan],
            OrderStatus::Diterima => [OrderStatus::Diproses, OrderStatus::Dibatalkan],
            OrderStatus::Diproses => [OrderStatus::Siap, OrderStatus::Dibatalkan],
            OrderStatus::Siap => [OrderStatus::Selesai],
            OrderStatus::Selesai, OrderStatus::Dibatalkan => [],
        };
    }
}
