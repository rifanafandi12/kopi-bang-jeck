{{-- BUNGKUS DENGAN DIV BARU --}}
<div>
    <div class="max-w-md mx-auto pb-24 relative" wire:poll.5s>

        {{-- KONTEN UTAMA --}}
        <div class="mb-6 text-center">
            <p class="text-xs uppercase tracking-widest text-teks-aksen">Kode Pesanan</p>
            <h1 class="text-3xl font-mono text-espresso">{{ $order->kode }}</h1>
            <p class="text-sm text-teks-sekunder mt-1">Meja {{ $table->nomor }}</p>
        </div>

        <div class="bg-mentega rounded-lg p-5 mb-6 border border-espresso/10 text-center">
            <p class="text-xs uppercase tracking-widest text-teks-sekunder mb-2">Status Pesanan</p>

            @php
                $statusLabel = match($order->status) {
                    \App\Enums\OrderStatus::Dikonfirmasi => 'Menunggu konfirmasi kasir',
                    \App\Enums\OrderStatus::Diterima => 'Pesanan diterima kasir',
                    \App\Enums\OrderStatus::Diproses => 'Sedang disiapkan',
                    \App\Enums\OrderStatus::Siap => 'Siap diambil!',
                    \App\Enums\OrderStatus::Selesai => 'Selesai. Terima kasih!',
                    \App\Enums\OrderStatus::Dibatalkan => 'Dibatalkan',
                    default => 'Tidak diketahui',
                };

                $badgeColor = match($order->status) {
                    \App\Enums\OrderStatus::Dikonfirmasi, \App\Enums\OrderStatus::Diterima => 'text-teks-aksen border-teks-aksen',
                    \App\Enums\OrderStatus::Diproses => 'text-karamel border-karamel',
                    \App\Enums\OrderStatus::Siap, \App\Enums\OrderStatus::Selesai => 'text-hijau-zaitun border-hijau-zaitun',
                    \App\Enums\OrderStatus::Dibatalkan => 'text-merah-bata border-merah-bata',
                };
            @endphp

            <div class="inline-block border {{ $badgeColor }} px-3 py-1 rounded-full text-sm font-medium">
                {{ $statusLabel }}
            </div>

            @if($order->status_bayar->value === 'belum_bayar')
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
                @foreach($order->items as $item)
                    <li class="flex justify-between">
                        <span>{{ $item->jumlah }}x {{ $item->nama_snapshot }}</span>
                        <span class="font-mono">Rp{{ number_format($item->harga_snapshot * $item->jumlah, 0, ',', '.') }}</span>
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
            <button
                wire:click="pesanLagi"
                class="w-full bg-mentega border border-espresso/30 hover:bg-espresso/5 text-espresso font-medium rounded-lg py-3 min-h-[44px] transition-colors"
            >
                + Pesan Lagi (Pesanan Baru)
            </button>
            <p class="text-center text-xs text-teks-sekunder mt-2">
                Pesanan saat ini tetap berjalan. Pesanan baru akan masuk ke antrean kasir.
            </p>
        </div>
    </div>

    {{-- POP-UP NOTIFIKASI DI LUAR DIV UTAMA AGAR TIDAK BENTROK --}}
    <div x-data="{ show: @entangle('showSuccessPopup') }"
         x-show="show"
         x-cloak
         x-transition.opacity.duration.300ms
         id="popup-notifikasi"
         style="display: none; position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; background-color: rgba(43, 26, 16, 0.8) !important; z-index: 99999 !important; align-items: center !important; justify-content: center !important;">

        <div style="background-color: #F6E7B4 !important; border: 2px solid #5B7040 !important; border-radius: 12px !important; padding: 32px 24px !important; text-align: center !important; width: 320px !important; max-width: 90% !important; box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important; position: relative !important; margin: 0 auto !important;">

            <div style="width: 64px !important; height: 64px !important; background-color: #5B7040 !important; border-radius: 50% !important; display: flex !important; align-items: center !important; justify-content: center !important; margin: 0 auto 16px auto !important;">
                <svg style="width: 32px !important; height: 32px !important; color: #F6F1E3 !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>

            <h2 style="font-family: serif !important; font-size: 24px !important; color: #2B1A10 !important; font-weight: bold !important; margin: 0 0 8px 0 !important; background: transparent !important;">Pesanan Siap!</h2>
            <p style="color: #6B5645 !important; font-size: 14px !important; margin: 0 0 24px 0 !important; line-height: 1.5 !important; background: transparent !important;">Pesanan Anda sudah siap. Silakan nikmati hidangan Anda.</p>

            <button x-on:click="show = false" style="width: 100% !important; background-color: #2B1A10 !important; color: #F6F1E3 !important; padding: 12px !important; border-radius: 6px !important; font-weight: bold !important; border: none !important; cursor: pointer !important; display: block !important;">
                Tutup
            </button>
        </div>
    </div>

    {{-- CSS Tambahan untuk memaksa posisi --}}
    <style>
        #popup-notifikasi[x-show] {
            display: flex !important;
        }
        /* Menghilangkan highlight text biru yang tidak sengaja */
        #popup-notifikasi ::selection {
            background: transparent;
        }
    </style>
</div>

{{-- Script Listener WebSocket --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.Echo !== 'undefined') {
            window.Echo.channel('order.{{ $order->kode }}')
                .listen('OrderStatusUpdated', (e) => {
                    Livewire.dispatch('refresh-status');
                });
        }
    });
</script>
