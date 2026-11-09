<?php

namespace App\Livewire\Customer;

use App\Enums\EventActor;
use App\Enums\OrderEventType;
use App\Enums\OrderStatus;
use App\Models\CafeTable;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Services\OrderEventRecorder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MenuOrder extends Component
{
    public CafeTable $table;
    public Order $order;

    /** @var array<int, int> menu_item_id => jumlah */
    public array $cart = [];

    /** @var array<int, string> menu_item_id => catatan */
    public array $catatan = [];

    public string $pencarian = '';

    // Menandakan apakah pesanan sudah dikonfirmasi dan tidak bisa diubah lagi
    public bool $isLocked = false;

    protected OrderEventRecorder $recorder;

    public function boot(OrderEventRecorder $recorder): void
    {
        $this->recorder = $recorder;
    }

    public function mount(string $token): void
    {
        $this->table = CafeTable::where('token', $token)->where('aktif', true)->firstOrFail();

        $orderId = session('order_id');
        abort_unless($orderId, 404, 'Pesanan tidak ditemukan. Silakan scan QR ulang.');

        $this->order = Order::with('items')
            ->where('id', $orderId)
            ->where('cafe_table_id', $this->table->id)
            ->firstOrFail();

        // Cek apakah pelanggan sudah menekan "Konfirmasi pesanan"
        $hasConfirmed = $this->order->events()
            ->where('event', OrderEventType::OrderConfirmed->value)
            ->exists();

        if ($hasConfirmed || $this->order->status !== OrderStatus::Dikonfirmasi) {
            $this->isLocked = true;
        }

        // Muat keranjang dari database
        foreach ($this->order->items as $item) {
            if ($item->menu_item_id) {
                $this->cart[$item->menu_item_id] = $item->jumlah;
                if ($item->catatan) {
                    $this->catatan[$item->menu_item_id] = $item->catatan;
                }
            }
        }
    }

    public function tambah(int $menuItemId): void
    {
        if ($this->isLocked) return;

        $menu = MenuItem::where('id', $menuItemId)->where('tersedia', true)->firstOrFail();

        $this->cart[$menu->id] = ($this->cart[$menu->id] ?? 0) + 1;

        if (! $this->order->events()->where('event', OrderEventType::FirstItemAdded->value)->exists()) {
            $this->recorder->record(
                $this->order,
                OrderEventType::FirstItemAdded,
                EventActor::Pelanggan,
                ['menu_item_id' => $menu->id, 'menu_item_nama' => $menu->nama]
            );
        }

        $this->simpanKeranjang();
    }

    public function kurangi(int $menuItemId): void
    {
        if ($this->isLocked) return;
        if (! isset($this->cart[$menuItemId])) return;

        $this->cart[$menuItemId]--;

        if ($this->cart[$menuItemId] <= 0) {
            unset($this->cart[$menuItemId], $this->catatan[$menuItemId]);
        }

        $this->simpanKeranjang();
    }

    public function hapus(int $menuItemId): void
    {
        if ($this->isLocked) return;

        unset($this->cart[$menuItemId], $this->catatan[$menuItemId]);
        $this->simpanKeranjang();
    }

    public function updatedCatatan(int $menuItemId): void
    {
        if ($this->isLocked) return;
        $this->simpanKeranjang();
    }

    protected function simpanKeranjang(): void
    {
        if ($this->isLocked) return;

        DB::transaction(function () {
            $menuMap = MenuItem::whereIn('id', array_keys($this->cart))->get()->keyBy('id');

            $this->order->items()->delete();

            $subtotal = 0;
            foreach ($this->cart as $menuItemId => $jumlah) {
                if ($jumlah <= 0) continue;

                $menu = $menuMap[$menuItemId] ?? null;
                if (! $menu) continue;

                $harga = (int) $menu->harga;
                $this->order->items()->create([
                    'menu_item_id' => $menu->id,
                    'nama_snapshot' => $menu->nama,
                    'harga_snapshot' => $harga,
                    'jumlah' => $jumlah,
                    'catatan' => $this->catatan[$menuItemId] ?? null,
                ]);
                $subtotal += $harga * $jumlah;
            }

            $this->order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);
        });
    }

    public function lanjutKonfirmasi()
    {
        // Jika terkunci, arahkan ke halaman status, bukan halaman konfirmasi
        if ($this->isLocked) {
            return $this->redirectRoute('customer.status', ['token' => $this->table->token]);
        }

        if (empty($this->cart)) {
            $this->addError('cart', 'Keranjang masih kosong.');
            return null;
        }

        return $this->redirectRoute('customer.confirm', ['token' => $this->table->token]);
    }

    public function jumlahItem(): int
    {
        return array_sum($this->cart);
    }

    public function subtotal(): int
    {
        return (int) $this->order->subtotal;
    }

    public function render()
    {
        $categories = Category::with(['menuItems' => function ($q) {
            $q->where('tersedia', true)
              ->when($this->pencarian !== '', function ($q) {
                  $q->where('nama', 'like', '%' . $this->pencarian . '%');
              })
              ->orderBy('urutan');
        }])
            ->orderBy('urutan')
            ->get()
            ->filter(fn ($c) => $c->menuItems->isNotEmpty());

        return view('livewire.customer.menu-order', [
            'categories' => $categories,
        ])->layout('layouts.customer');
    }
}
