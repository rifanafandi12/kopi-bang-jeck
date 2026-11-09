<?php

namespace App\Livewire\Customer;

use App\Enums\EventActor;
use App\Enums\OrderEventType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\CafeTable;
use App\Models\Order;
use App\Services\OrderEventRecorder;
use Livewire\Component;

class OrderConfirm extends Component
{
    public CafeTable $table;
    public Order $order;

    public string $metode = 'tunai'; // 'tunai' atau 'online'

    public function mount(string $token): void
    {
        $this->table = CafeTable::where('token', $token)->where('aktif', true)->firstOrFail();

        $orderId = session('order_id');
        abort_unless($orderId, 404, 'Pesanan tidak ditemukan.');

        $this->order = Order::with('items')
            ->where('id', $orderId)
            ->where('cafe_table_id', $this->table->id)
            ->firstOrFail();

        if ($this->order->items->isEmpty()) {
            $this->redirectRoute('customer.menu', ['token' => $this->table->token]);
        }
    }

    public function konfirmasi(OrderEventRecorder $recorder)
    {
        $this->validate([
            'metode' => ['required', 'in:tunai,online'],
        ]);

        $this->order->update([
            'status' => OrderStatus::Dikonfirmasi,
            'metode_bayar' => $this->metode === 'tunai' ? PaymentMethod::Tunai : PaymentMethod::Online,
        ]);

        // Catat event order_confirmed
        $recorder->record(
            $this->order,
            OrderEventType::OrderConfirmed,
            EventActor::Pelanggan,
            ['metode' => $this->metode]
        );

        return $this->redirectRoute('customer.status', ['token' => $this->table->token]);
    }

    public function render()
    {
        return view('livewire.customer.order-confirm')->layout('layouts.customer');
    }
}
