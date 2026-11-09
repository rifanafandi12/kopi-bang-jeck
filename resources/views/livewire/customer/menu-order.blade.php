<div class="max-w-md mx-auto pb-32" x-data>
    {{-- Header info meja --}}
    <div class="mb-6">
        <p class="text-xs uppercase tracking-widest text-teks-aksen">Meja {{ $table->nomor }} · {{ $order->nama }}</p>
        <h1 class="text-3xl font-serif text-espresso mt-1">Menu</h1>
        <p class="text-teks-sekunder mt-1 text-sm">Kode pesanan: <span
                class="font-mono font-medium">{{ $order->kode }}</span></p>
    </div>

    {{-- Banner Terkunci --}}
    @if ($isLocked)
        <div
            class="bg-hijau-zaitun/10 border border-hijau-zaitun text-hijau-zaitun p-4 rounded-lg mb-6 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                </path>
            </svg>
            <div>
                <p class="font-medium">Pesanan sudah dikonfirmasi.</p>
                <p class="mt-1">Silakan tunggu di meja. Anda bisa memantau status pesanan di tab
                    <strong>Pesanan</strong> di bawah.
                </p>
            </div>
        </div>
    @endif

    {{-- Pencarian --}}
    @if (!$isLocked)
        <div class="mb-6">
            <input type="text" wire:model.live.debounce.300ms="pencarian" placeholder="Cari menu..."
                class="w-full bg-mentega border border-espresso/30 rounded px-3 py-2 text-espresso focus:outline-none focus:border-espresso">
        </div>
    @endif

    {{-- Daftar menu bergaya menu cetak --}}
    @forelse ($categories as $category)
        <section class="mb-8">
            <h2 class="text-xs uppercase tracking-widest text-teks-aksen border-b border-espresso/20 pb-2 mb-4">
                {{ $category->nama }}
            </h2>

            <ul class="space-y-4">
                @foreach ($category->menuItems as $menu)
                    <li class="flex items-start gap-3 mb-6">

                        {{-- BAGIAN GAMBAR --}}
                        @if ($menu->foto)
                            <div
                                class="w-20 h-20 flex-shrink-0 bg-mentega rounded overflow-hidden border border-espresso/20">
                                @if (filter_var($menu->foto, FILTER_VALIDATE_URL))
                                    {{-- Jika dari URL Google --}}
                                    <img src="{{ $menu->foto }}" alt="{{ $menu->nama }}"
                                        class="w-full h-full object-cover">
                                @else
                                    {{-- Jika dari upload file lokal (WebP) --}}
                                    <img src="{{ Storage::url($menu->foto) }}" alt="{{ $menu->nama }}"
                                        class="w-full h-full object-cover">
                                @endif
                            </div>
                        @endif

                        {{-- TEKS MENU --}}
                        <div class="flex-1">
                            <div class="flex items-baseline gap-2">
                                <span class="font-serif text-espresso">{{ $menu->nama }}</span>
                                @if ($menu->is_best_seller)
                                    <span
                                        class="text-[10px] uppercase tracking-widest text-hijau-zaitun border border-hijau-zaitun px-1.5 py-0.5 rounded-sm">Terlaris</span>
                                @endif
                                <span class="flex-1 border-b border-dotted border-espresso/30 mx-1"></span>
                                <span
                                    class="font-mono text-espresso">Rp{{ number_format($menu->harga, 0, ',', '.') }}</span>
                            </div>
                            @if ($menu->deskripsi)
                                <p class="text-xs text-teks-sekunder mt-1">{{ $menu->deskripsi }}</p>
                            @endif

                            {{-- Kontrol jumlah --}}
                            @if (!$isLocked)
                                <div class="flex items-center gap-2 mt-2">
                                    @if (isset($cart[$menu->id]) && $cart[$menu->id] > 0)
                                        <button wire:click="kurangi({{ $menu->id }})"
                                            class="w-8 h-8 border border-espresso/40 rounded text-espresso hover:bg-mentega transition-colors duration-150"
                                            aria-label="Kurangi {{ $menu->nama }}">−</button>
                                        <span class="font-mono w-6 text-center">{{ $cart[$menu->id] }}</span>
                                    @endif
                                    <button wire:click="tambah({{ $menu->id }})"
                                        class="min-h-[32px] px-3 bg-karamel hover:bg-karamel-hover text-espresso text-sm rounded transition-colors duration-150">Tambah</button>

                                    @if (isset($cart[$menu->id]) && $cart[$menu->id] > 0)
                                        <input type="text" wire:model.blur="catatan.{{ $menu->id }}"
                                            wire:keydown.enter="updatedCatatan({{ $menu->id }})"
                                            placeholder="Catatan item..."
                                            class="flex-1 bg-mentega border border-espresso/30 rounded px-2 py-1 text-xs">
                                    @endif
                                </div>
                            @else
                                {{-- Tampilan saat terkunci --}}
                                @if (isset($cart[$menu->id]) && $cart[$menu->id] > 0)
                                    <p class="text-sm mt-2 font-mono text-teks-sekunder">Dipesan:
                                        {{ $cart[$menu->id] }} porsi</p>
                                @endif
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @empty
        <p class="text-teks-sekunder text-sm">Tidak ada menu yang cocok.</p>
    @endforelse

    {{-- Ringkasan keranjang sticky bawah --}}
    @if ($this->jumlahItem() > 0)
        <div class="fixed bottom-16 left-0 right-0 bg-espresso text-krim border-t-2 border-karamel z-40">
            <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-widest text-mentega">{{ $this->jumlahItem() }} item</p>
                    <p class="font-mono text-lg">Rp{{ number_format($this->subtotal(), 0, ',', '.') }}</p>
                </div>

                @if ($isLocked)
                    <button wire:click="lanjutKonfirmasi"
                        class="min-h-[44px] px-5 bg-hijau-zaitun hover:bg-hijau-zaitun/90 text-krim font-medium rounded transition-colors duration-150">
                        Lihat Status Pesanan
                    </button>
                @else
                    <button wire:click="lanjutKonfirmasi"
                        class="min-h-[44px] px-5 bg-karamel hover:bg-karamel-hover text-espresso font-medium rounded transition-colors duration-150">
                        Konfirmasi pesanan
                    </button>
                @endif
            </div>
            @error('cart')
                <p class="text-merah-bata text-xs text-center pb-2">{{ $message }}</p>
            @enderror
        </div>
    @endif
</div>
