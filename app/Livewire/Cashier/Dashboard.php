<?php

namespace App\Livewire\Cashier;

use App\Actions\TransitionOrderStatus;
use App\Enums\EventActor;
use App\Enums\OrderEventType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\OrderEventRecorder;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app')]
class Dashboard extends Component
{
    // State untuk modal pembayaran tunai
    public ?int $bayarOrderId = null;
    public string $uangDiterima = '';
    public ?int $kembalian = null;

    public function markAsPaid(int $orderId, OrderEventRecorder $recorder): void
    {
        $order = Order::findOrFail($orderId);

        if ($order->status_bayar === PaymentStatus::BelumBayar) {
            $order->update(['status_bayar' => PaymentStatus::Lunas]);

            $recorder->record(
                $order,
                OrderEventType::PaymentPaid,
                EventActor::Kasir,
                ['metode' => $order->metode_bayar->value]
            );
        }
    }

    public function updateStatus(int $orderId, string $nextStatus, TransitionOrderStatus $transition): void
    {
        $order = Order::findOrFail($orderId);
        $status = OrderStatus::from($nextStatus);

        try {
            $transition->handle($order, $status, EventActor::Kasir);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('transition', $e->getMessage());
        }
    }

    // === Modal Pembayaran Tunai ===

    public function openPayment(int $orderId): void
    {
        $this->resetErrorBag();
        $this->bayarOrderId = $orderId;
        $order = Order::findOrFail($orderId);
        // Format awal dengan titik
        $this->uangDiterima = number_format($order->total, 0, ',', '.');
        $this->kembalian = null;
    }
    public function tutupModal(): void
    {
        $this->bayarOrderId = null;
        $this->uangDiterima = '';
        $this->kembalian = null;
    }

    public function updatedUangDiterima($value)
    {
        // 1. Hapus semua karakter selain angka
        $clean = preg_replace('/[^0-9]/', '', $value);

        // 2. Format ulang dengan titik
        if ($clean !== '') {
            $this->uangDiterima = number_format((int) $clean, 0, ',', '.');
        } else {
            $this->uangDiterima = '';
        }
    }

    public function prosesBayarTunai(OrderEventRecorder $recorder): void
    {
        if (! $this->bayarOrderId) {
            return;
        }

        // Bersihkan titik untuk mendapatkan nilai integer
        $nominalBersih = (int) str_replace('.', '', $this->uangDiterima);

        $order = Order::findOrFail($this->bayarOrderId);

        if ($nominalBersih < $order->total) {
            $this->addError('uangDiterima', 'Uang diterima kurang dari total tagihan.');
            return;
        }

        $order->update(['status_bayar' => PaymentStatus::Lunas]);
        $this->kembalian = $nominalBersih - $order->total;

        // Simpan nominal bersih ke database/event
        $recorder->record(
            $order,
            OrderEventType::PaymentPaid,
            EventActor::Kasir,
            [
                'metode' => 'tunai',
                'uang_diterima' => $nominalBersih,
                'kembalian' => $this->kembalian,
            ]
        );

        if ($order->status === OrderStatus::Dikonfirmasi) {
            $order->update(['status' => OrderStatus::Diterima]);
            $recorder->record($order, OrderEventType::AcceptedByCashier, EventActor::Kasir, ['via' => 'bayar_tunai']);
        }
    }

    public function render()
    {
        $orders = Order::with(['items', 'cafeTable'])
            ->whereNotIn('status', [OrderStatus::Selesai, OrderStatus::Dibatalkan])
            ->orderBy('created_at', 'asc')
            ->get();

        $orderUntukBayar = $this->bayarOrderId ? Order::with(['items', 'cafeTable'])->find($this->bayarOrderId) : null;

        return view('livewire.cashier.dashboard', [
            'orders' => $orders,
            'orderUntukBayar' => $orderUntukBayar,
        ]);
    }
}
