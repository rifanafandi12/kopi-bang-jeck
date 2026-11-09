<div class="max-w-md mx-auto pb-24" wire:poll.5s>
    <div class="mb-6 text-center">
        <p class="text-xs uppercase tracking-widest text-teks-aksen">Kode Pesanan</p>
        <h1 class="text-3xl font-mono text-espresso">{{ $order->kode }}</h1>
        <p class="text-sm text-teks-sekunder mt-1">Meja {{ $table->nomor }}</p>
    </div>

    <div class="bg-mentega rounded-lg p-5 mb-6 border border-espresso/10 text-center">
        <p class="text-xs uppercase tracking-widest text-teks-sekunder mb-2">Status Pesanan</p>

        @php
            $statusLabel = match ($order->status) {
                \App\Enums\OrderStatus::Dikonfirmasi => 'Menunggu konfirmasi kasir',
                \App\Enums\OrderStatus::Diterima => 'Pesanan diterima kasir',
                \App\Enums\OrderStatus::Diproses => 'Sedang disiapkan',
                \App\Enums\OrderStatus::Siap => 'Siap diambil!',
                \App\Enums\OrderStatus::Selesai => 'Selesai. Terima kasih!',
                \App\Enums\OrderStatus::Dibatalkan => 'Dibatalkan',
                default => 'Tidak diketahui',
            };

            $badgeColor = match ($order->status) {
                \App\Enums\OrderStatus::Dikonfirmasi,
                \App\Enums\OrderStatus::Diterima
                    => 'text-teks-aksen border-teks-aksen',
                \App\Enums\OrderStatus::Diproses => 'text-karamel border-karamel',
                \App\Enums\OrderStatus::Siap,
                \App\Enums\OrderStatus::Selesai
                    => 'text-hijau-zaitun border-hijau-zaitun',
                \App\Enums\OrderStatus::Dibatalkan => 'text-merah-bata border-merah-bata',
            };
        @endphp

        <div class="inline-block border {{ $badgeColor }} px-3 py-1 rounded-full text-sm font-medium">
            {{ $statusLabel }}
        </div>

        @if ($order->status_bayar->value === 'belum_bayar')
            <p class="mt-4 text-sm text-teks-sekunder">
                Silakan bayar di kasir.
            </p>
        @elseif($order->status_bayar->value === 'lunas')
            <p class="mt-4 text-sm text-hijau-zaitun font-medium">
                Lunas
            </p>
        @endif
    </div>

    <div class="bg-krim border border-espresso/20 rounded-lg p-4">
        <h3 class="text-xs uppercase tracking-widest text-teks-aksen mb-3">Rincian</h3>
        <ul class="space-y-2 text-sm">
            @foreach ($order->items as $item)
                <li class="flex justify-between">
                    <span>{{ $item->jumlah }}x {{ $item->nama_snapshot }}</span>
                    <span
                        class="font-mono">Rp{{ number_format($item->harga_snapshot * $item->jumlah, 0, ',', '.') }}</span>
                </li>
            @endforeach
        </ul>
        <div class="border-t border-espresso/20 mt-3 pt-3 flex justify-between font-medium">
            <span>Total</span>
            <span class="font-mono">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Tombol Pesan Lagi --}}
    <div class="mt-6">
        <button wire:click="pesanLagi"
            class="w-full bg-mentega border border-espresso/30 hover:bg-espresso/5 text-espresso font-medium rounded-lg py-3 min-h-[44px] transition-colors">
            + Pesan Lagi (Pesanan Baru)
        </button>
        <p class="text-center text-xs text-teks-sekunder mt-2">
            Pesanan saat ini tetap berjalan. Pesanan baru akan masuk ke antrean kasir.
        </p>
    </div>
</div>
