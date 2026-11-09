<div class="max-w-md mx-auto pb-24">
    <div class="mb-6">
        <p class="text-xs uppercase tracking-widest text-teks-aksen">Meja {{ $table->nomor }}</p>
        <h1 class="text-2xl font-serif text-espresso mt-1">Cek ulang pesanan</h1>
    </div>

    <div class="bg-mentega rounded-lg p-4 mb-6 border border-espresso/10">
        <ul class="divide-y divide-espresso/10">
            @foreach ($order->items as $item)
                <li class="py-3 flex justify-between gap-3">
                    <div>
                        <p class="font-medium">{{ $item->nama_snapshot }}</p>
                        @if ($item->catatan)
                            <p class="text-xs text-teks-sekunder italic">"{{ $item->catatan }}"</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-mono">{{ $item->jumlah }} x
                            Rp{{ number_format($item->harga_snapshot, 0, ',', '.') }}</p>
                        <p class="font-mono font-medium mt-1">
                            Rp{{ number_format($item->harga_snapshot * $item->jumlah, 0, ',', '.') }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="border-t border-espresso/20 mt-2 pt-3 flex justify-between font-bold text-lg">
            <span>Total</span>
            <span class="font-mono">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="text-xs uppercase tracking-widest text-teks-aksen mb-3">Metode Pembayaran</h2>

        <div class="grid grid-cols-2 gap-3">
            {{-- Opsi Tunai --}}
            <label
                class="cursor-pointer relative border-2 rounded-lg p-3 text-center transition-all duration-150
                {{ $metode === 'tunai' ? 'border-karamel bg-karamel text-espresso' : 'border-espresso/20 bg-mentega hover:border-espresso/40 text-teks-sekunder' }}">

                <input type="radio" wire:model.live="metode" value="tunai" class="sr-only">

                {{-- Indikator Centang (Hanya muncul jika aktif) --}}
                @if ($metode === 'tunai')
                    <div class="absolute top-2 right-2 text-espresso">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif

                <span class="block font-medium">Bayar di Kasir</span>
                <span class="text-xs opacity-80 mt-1 block">Tunai</span>
            </label>

            {{-- Opsi Online --}}
            <label
                class="cursor-pointer relative border-2 rounded-lg p-3 text-center transition-all duration-150
                {{ $metode === 'online' ? 'border-karamel bg-karamel text-espresso' : 'border-espresso/20 bg-mentega hover:border-espresso/40 text-teks-sekunder' }}">

                <input type="radio" wire:model.live="metode" value="online" class="sr-only">

                {{-- Indikator Centang --}}
                @if ($metode === 'online')
                    <div class="absolute top-2 right-2 text-espresso">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif

                <span class="block font-medium">Online</span>
                <span class="text-xs opacity-80 mt-1 block">QRIS, e-wallet</span>
            </label>
        </div>
    </div>

    <div class="fixed bottom-16 left-0 right-0 bg-krim border-t border-espresso/20 z-40">
        <div class="max-w-md mx-auto p-4">
            <button wire:click="konfirmasi"
                class="w-full bg-karamel hover:bg-karamel-hover text-espresso font-medium rounded px-4 py-3 min-h-[44px]">
                Konfirmasi pesanan
            </button>
        </div>
    </div>
</div>
