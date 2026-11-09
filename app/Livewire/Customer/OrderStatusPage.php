<?php

namespace App\Livewire\Customer;

use App\Models\CafeTable;
use App\Models\Order;
use App\Enums\OrderStatus;
use Livewire\Component;
use Livewire\Attributes\On;

class OrderStatusPage extends Component
{
    public CafeTable $table;
    public Order $order;
    public bool $showSuccessPopup = false;

    public function mount(string $token): void
    {
        $this->table = CafeTable::where('token', $token)->where('aktif', true)->firstOrFail();

        $orderId = session('order_id');
        abort_unless($orderId, 404, 'Pesanan tidak ditemukan.');

        $this->order = Order::with('items')
            ->where('id', $orderId)
            ->where('cafe_table_id', $this->table->id)
            ->firstOrFail();
    }

    // Listener untuk WebSocket
    #[On('refresh-status')]
    public function refreshStatus()
    {
        $this->order->refresh();

        // Jika status berubah jadi Siap atau Selesai, munculkan popup
        if ($this->order->status === OrderStatus::Siap || $this->order->status === OrderStatus::Selesai) {
            $this->showSuccessPopup = true;
        }
    }

    public function pesanLagi()
    {
        // Simpan data pelanggan lama
        $nama = $this->order->nama;
        $telepon = $this->order->telepon;
        $email = $this->order->email;
        $catatan = $this->order->catatan;

        // Hapus session order lama
        session()->forget('order_id');

        // Buat pesanan baru (kosong)
        $newOrder = \App\Models\Order::create([
            'kode' => $this->generateKode(),
            'cafe_table_id' => $this->table->id,
            'nama' => $nama,
            'telepon' => $telepon,
            'email' => $email,
            'catatan' => $catatan,
        ]);

        // Catat event QR Scanned untuk pesanan baru
        app(\App\Services\OrderEventRecorder::class)->record(
            $newOrder,
            \App\Enums\OrderEventType::QrScanned,
            \App\Enums\EventActor::Pelanggan
        );

        // Simpan session order baru
        session(['order_id' => $newOrder->id]);

        return $this->redirectRoute('customer.menu', ['token' => $this->table->token]);
    }

    protected function generateKode(): string
    {
        do {
            $kode = strtoupper(\Illuminate\Support\Str::random(6));
        } while (\App\Models\Order::where('kode', $kode)->exists());

        return $kode;
    }

    public function render()
    {
        return view('livewire.customer.order-status-page')->layout('layouts.customer');
    }
}
