<?php

namespace App\Livewire\Customer;

use App\Enums\EventActor;
use App\Enums\OrderEventType;
use App\Models\CafeTable;
use App\Models\Order;
use App\Services\OrderEventRecorder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class TableEntry extends Component
{
    public CafeTable $table;

    public string $nama = '';
    public string $telepon = '';
    public string $email = '';
    public string $catatan = '';

    public function mount(string $token): void
    {
        $this->table = CafeTable::where('token', $token)
            ->where('aktif', true)
            ->firstOrFail();
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function submit(OrderEventRecorder $recorder): void
    {
        $this->validate();

        $order = DB::transaction(function () use ($recorder) {
            $order = Order::create([
                'kode' => $this->generateKode(),
                'cafe_table_id' => $this->table->id,
                'nama' => $this->nama,
                'telepon' => $this->telepon ?: null,
                'email' => $this->email ?: null,
                'catatan' => $this->catatan ?: null,
            ]);

            $recorder->record($order, OrderEventType::QrScanned, EventActor::Pelanggan);
            $recorder->record($order, OrderEventType::DataDiriSubmitted, EventActor::Pelanggan, [
                'table_nomor' => $this->table->nomor,
            ]);

            return $order;
        });

        session(['order_id' => $order->id]);

        $this->redirectRoute('customer.menu', ['token' => $this->table->token]);
    }

    protected function generateKode(): string
    {
        do {
            $kode = strtoupper(Str::random(6));
        } while (Order::where('kode', $kode)->exists());

        return $kode;
    }

    public function render()
    {
        return view('livewire.customer.table-entry')
            ->layout('layouts.customer');
    }
}
