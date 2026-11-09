<?php

use App\Enums\EventActor;
use App\Enums\OrderEventType;
use App\Enums\OrderStatus;
use App\Livewire\Customer\MenuOrder;
use App\Livewire\Customer\OrderConfirm;
use App\Livewire\Customer\TableEntry;
use App\Models\CafeTable;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Setup data dummy untuk tes
    $this->category = Category::create(['nama' => 'Kopi', 'urutan' => 1]);
    $this->menu = MenuItem::create([
        'category_id' => $this->category->id,
        'nama' => 'Kopi Susu',
        'harga' => 18000,
        'tersedia' => true,
        'urutan' => 1,
    ]);
    $this->table = CafeTable::create([
        'nomor' => '1',
        'token' => '01TESTTOKEN1234567890ABCDEF',
        'aktif' => true,
    ]);
});

test('pelanggan dapat mengisi data diri dan sistem mencatat event QR scanned', function () {
    Livewire::test(TableEntry::class, ['token' => $this->table->token])
        ->set('nama', 'Budi')
        ->set('telepon', '08123456789')
        ->call('submit')
        ->assertRedirect(route('customer.menu', ['token' => $this->table->token]));

    $order = Order::first();

    expect($order)->not->toBeNull()
        ->and($order->nama)->toBe('Budi')
        ->and($order->status)->toBe(OrderStatus::Dikonfirmasi);

    // Pastikan event qr_scanned dan data_diri_submitted tercatat
    $this->assertDatabaseHas('order_events', [
        'order_id' => $order->id,
        'event' => OrderEventType::QrScanned->value,
        'actor' => EventActor::Pelanggan->value,
    ]);

    $this->assertDatabaseHas('order_events', [
        'order_id' => $order->id,
        'event' => OrderEventType::DataDiriSubmitted->value,
    ]);
});

test('pelanggan dapat menambah menu ke keranjang dan total dihitung di server', function () {
    // Buat order via TableEntry dulu
    Livewire::test(TableEntry::class, ['token' => $this->table->token])
        ->set('nama', 'Budi')
        ->call('submit');

    $order = Order::first();
    session(['order_id' => $order->id]);

    // Uji komponen menu
    Livewire::test(MenuOrder::class, ['token' => $this->table->token])
        ->call('tambah', $this->menu->id)
        ->call('tambah', $this->menu->id) // Tambah 2x
        ->assertSet('cart.' . $this->menu->id, 2);

    $order->refresh();

    // Harga di server harus 2 x 18000 = 36000
    expect($order->subtotal)->toBe(36000)
        ->and($order->total)->toBe(36000)
        ->and($order->items()->count())->toBe(1);

    // Event first_item_added harus tercatat
    $this->assertDatabaseHas('order_events', [
        'order_id' => $order->id,
        'event' => OrderEventType::FirstItemAdded->value,
    ]);
});

test('pelanggan dapat mengonfirmasi pesanan dan status terkunci', function () {
    Livewire::test(TableEntry::class, ['token' => $this->table->token])
        ->set('nama', 'Budi')
        ->call('submit');

    $order = Order::first();
    session(['order_id' => $order->id]);

    // Tambah menu
    Livewire::test(MenuOrder::class, ['token' => $this->table->token])
        ->call('tambah', $this->menu->id);

    // Konfirmasi
    Livewire::test(OrderConfirm::class, ['token' => $this->table->token])
        ->set('metode', 'tunai')
        ->call('konfirmasi')
        ->assertRedirect(route('customer.status', ['token' => $this->table->token]));

    $order->refresh();

    // Event order_confirmed harus tercatat
    $this->assertDatabaseHas('order_events', [
        'order_id' => $order->id,
        'event' => OrderEventType::OrderConfirmed->value,
    ]);

    // Cek metode bayar
    expect($order->metode_bayar->value)->toBe('tunai');
});
