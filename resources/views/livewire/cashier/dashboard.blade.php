<div class="max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="mb-8 flex items-end justify-between border-b border-espresso/20 pb-4">
        <div>
            <p class="text-xs uppercase tracking-widest text-teks-aksen">Area Kasir</p>
            <h1 class="text-3xl font-serif text-espresso mt-1">Antrean Pesanan</h1>
        </div>
        <div class="text-right">
            <p class="text-sm text-teks-sekunder">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    @if ($orders->isEmpty())
        <div class="bg-mentega border border-espresso/10 rounded-lg p-12 text-center text-teks-sekunder">
            <p class="text-lg font-serif text-espresso">Belum ada pesanan aktif.</p>
            <p class="text-sm mt-2">Pesanan yang masuk dari pelanggan akan muncul di sini secara otomatis.</p>
        </div>
    @else
        {{-- Grid layout untuk tablet landscape --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach ($orders as $order)
                <div class="bg-krim border-2 border-espresso/20 rounded-lg overflow-hidden flex flex-col">

                    {{-- Header Kartu --}}
                    <div class="bg-espresso text-krim px-4 py-3 flex justify-between items-center">
                        <div>
                            <p class="font-mono text-lg font-bold">{{ $order->kode }}</p>
                            <p class="text-xs text-mentega">Meja {{ $order->cafeTable->nomor }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-mentega uppercase tracking-wider">
                                {{ $order->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Badge Status --}}
                    <div class="px-4 py-2 bg-mentega border-b border-espresso/10 flex justify-between items-center">
                        <span
                            class="text-xs font-bold uppercase tracking-wider
                            @if ($order->status === \App\Enums\OrderStatus::Dikonfirmasi) text-teks-aksen
                            @elseif($order->status === \App\Enums\OrderStatus::Diterima) text-karamel
                            @elseif($order->status === \App\Enums\OrderStatus::Diproses) text-karamel
                            @else text-hijau-zaitun @endif">
                            {{ $order->status->value }}
                        </span>

                        @if ($order->status_bayar === \App\Enums\PaymentStatus::Lunas)
                            <span
                                class="text-xs font-bold uppercase tracking-wider text-hijau-zaitun bg-hijau-zaitun/10 px-2 py-0.5 rounded">Lunas</span>
                        @else
                            <span
                                class="text-xs font-bold uppercase tracking-wider text-merah-bata bg-merah-bata/10 px-2 py-0.5 rounded">
                                {{ $order->metode_bayar->value === 'tunai' ? 'Bayar Tunai' : 'Online' }}
                            </span>
                        @endif
                    </div>

                    {{-- Daftar Item --}}
                    <div class="p-4 flex-1">
                        <p class="text-sm font-medium mb-2">{{ $order->nama }}</p>
                        <ul class="space-y-1 text-sm">
                            @foreach ($order->items as $item)
                                <li class="flex justify-between">
                                    <span class="flex-1">{{ $item->jumlah }}x {{ $item->nama_snapshot }}</span>
                                    <span
                                        class="font-mono">Rp{{ number_format($item->harga_snapshot * $item->jumlah, 0, ',', '.') }}</span>
                                </li>
                                @if ($item->catatan)
                                    <li class="text-xs text-teks-sekunder italic -mt-1 mb-1">"{{ $item->catatan }}"
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    {{-- Total & Tombol Aksi --}}
                    <div class="border-t border-espresso/10 p-4 bg-mentega/50">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-bold">Total</span>
                            <span
                                class="text-lg font-mono font-bold">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>

                        {{-- Tombol Transisi Status --}}
                        <div class="flex gap-2">
                            @if ($order->status === \App\Enums\OrderStatus::Dikonfirmasi)
                                <button wire:click="updateStatus({{ $order->id }}, 'diterima')"
                                    class="flex-1 bg-karamel hover:bg-karamel-hover text-espresso font-medium py-2 rounded text-sm transition-colors">Terima
                                    Pesanan</button>
                            @elseif ($order->status === \App\Enums\OrderStatus::Diterima)
                                <button wire:click="updateStatus({{ $order->id }}, 'diproses')"
                                    class="flex-1 bg-karamel hover:bg-karamel-hover text-espresso font-medium py-2 rounded text-sm transition-colors">Mulai
                                    Proses</button>
                            @elseif ($order->status === \App\Enums\OrderStatus::Diproses)
                                <button wire:click="updateStatus({{ $order->id }}, 'siap')"
                                    class="flex-1 bg-hijau-zaitun hover:bg-hijau-zaitun/90 text-krim font-medium py-2 rounded text-sm transition-colors">Pesanan
                                    Siap</button>
                            @elseif ($order->status === \App\Enums\OrderStatus::Siap)
                                <button wire:click="updateStatus({{ $order->id }}, 'selesai')"
                                    class="flex-1 bg-espresso hover:bg-espresso/90 text-krim font-medium py-2 rounded text-sm transition-colors">Selesaikan</button>
                            @endif

                            {{-- Tombol Lunas --}}
                            @if (
                                $order->status_bayar === \App\Enums\PaymentStatus::BelumBayar &&
                                    $order->metode_bayar === \App\Enums\PaymentMethod::Tunai)
                                <button wire:click="openPayment({{ $order->id }})"
                                    class="px-3 bg-hijau-zaitun hover:bg-hijau-zaitun/90 text-krim font-medium py-2 rounded text-sm transition-colors">
                                    Bayar Tunai
                                </button>
                            @endif
                        </div>

                        {{-- Tombol Batal --}}
                        @if (in_array($order->status, [
                                \App\Enums\OrderStatus::Dikonfirmasi,
                                \App\Enums\OrderStatus::Diterima,
                                \App\Enums\OrderStatus::Diproses,
                            ]))
                            <button wire:click="updateStatus({{ $order->id }}, 'dibatalkan')"
                                wire:confirm="Yakin ingin membatalkan pesanan ini?"
                                class="w-full mt-2 bg-transparent text-merah-bata hover:underline text-xs py-1">Batalkan
                                pesanan</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($errors->has('transition'))
        <div class="fixed bottom-4 right-4 bg-merah-bata text-white px-4 py-2 rounded shadow-lg">
            {{ $errors->first('transition') }}
        </div>
    @endif

    {{-- Modal Pembayaran Tunai --}}
    @if ($orderUntukBayar)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-espresso/60 p-4">
            <div class="bg-krim w-full max-w-md rounded-lg border-2 border-espresso/30 overflow-hidden">
                <div class="bg-espresso text-krim px-4 py-3">
                    <h2 class="font-serif text-lg">Pembayaran Tunai</h2>
                    <p class="text-xs text-mentega font-mono">{{ $orderUntukBayar->kode }} · Meja
                        {{ $orderUntukBayar->cafeTable->nomor }}</p>
                </div>

                <div class="p-4 space-y-4">
                    <div class="flex justify-between items-center border-b border-espresso/20 pb-2">
                        <span class="text-sm">Total Tagihan</span>
                        <span
                            class="font-mono font-bold text-lg">Rp{{ number_format($orderUntukBayar->total, 0, ',', '.') }}</span>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest text-teks-aksen mb-1">Uang
                            Diterima</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-espresso font-mono text-lg">Rp</span>
                            <input type="text" wire:model.live="uangDiterima"
                                class="w-full bg-mentega border border-espresso/30 rounded pl-12 pr-3 py-2 font-mono text-lg text-espresso focus:outline-none focus:border-espresso"
                                placeholder="0">
                        </div>
                        @error('uangDiterima')
                            <p class="text-merah-bata text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($kembalian !== null)
                        <div class="bg-hijau-zaitun/10 border border-hijau-zaitun rounded p-3">
                            <p class="text-xs uppercase tracking-widest text-hijau-zaitun">Kembalian</p>
                            <p class="font-mono font-bold text-2xl text-hijau-zaitun mt-1">
                                Rp{{ number_format($kembalian, 0, ',', '.') }}</p>
                        </div>
                    @else
                        @php
                            $nominalInput = (int) str_replace('.', '', $uangDiterima);
                        @endphp
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-teks-sekunder">Kembalian (estimasi)</span>
                            <span class="font-mono">
                                Rp{{ number_format(max(0, $nominalInput - $orderUntukBayar->total), 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="p-4 border-t border-espresso/20 flex gap-2">
                    @if ($kembalian === null)
                        <button wire:click="tutupModal"
                            class="flex-1 border border-espresso/30 text-espresso py-2 rounded min-h-[44px] hover:bg-mentega">
                            Batal
                        </button>
                        <button wire:click="prosesBayarTunai"
                            class="flex-1 bg-karamel hover:bg-karamel-hover text-espresso font-medium py-2 rounded min-h-[44px]">
                            Tandai Lunas
                        </button>
                    @else
                        <button wire:click="tutupModal"
                            class="flex-1 border border-espresso/30 text-espresso py-2 rounded min-h-[44px] hover:bg-mentega">
                            Tutup
                        </button>
                        <button onclick="window.print()"
                            class="flex-1 bg-espresso hover:bg-espresso/90 text-krim font-medium py-2 rounded min-h-[44px]">
                            Cetak Struk
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Struk Cetak 80mm (hanya muncul saat print) --}}
    @if ($orderUntukBayar && $kembalian !== null)
        <div id="struk-print" class="hidden print:block print-receipt">
            <div class="text-center mb-2">
                <h1 class="font-serif text-lg font-bold">Kopi Bang Jeck</h1>
                <p class="text-xs">Pesan dari meja, tanpa menunggu.</p>
            </div>
            <div class="border-t border-dashed border-black my-1"></div>
            <div class="text-xs">
                <div class="flex justify-between">
                    <span>Kode</span>
                    <span class="font-mono">{{ $orderUntukBayar->kode }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Meja</span>
                    <span>{{ $orderUntukBayar->cafeTable->nomor }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Pelanggan</span>
                    <span>{{ $orderUntukBayar->nama }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tanggal</span>
                    <span>{{ $orderUntukBayar->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <div class="border-t border-dashed border-black my-1"></div>
            <div class="text-xs">
                @foreach ($orderUntukBayar->items as $item)
                    <div class="flex justify-between">
                        <span>{{ $item->nama_snapshot }}</span>
                        <span
                            class="font-mono">Rp{{ number_format($item->harga_snapshot * $item->jumlah, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-[10px] text-left">
                        {{ $item->jumlah }} x Rp{{ number_format($item->harga_snapshot, 0, ',', '.') }}
                    </div>
                    @if ($item->catatan)
                        <div class="text-[10px] italic">"{{ $item->catatan }}"</div>
                    @endif
                @endforeach
            </div>
            <div class="border-t border-dashed border-black my-1"></div>
            <div class="text-xs">
                <div class="flex justify-between font-bold">
                    <span>Total</span>
                    <span class="font-mono">Rp{{ number_format($orderUntukBayar->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tunai</span>
                    <span class="font-mono">Rp{{ number_format($uangDiterima, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Kembalian</span>
                    <span class="font-mono">Rp{{ number_format($kembalian, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="border-t border-dashed border-black my-1"></div>
            <p class="text-center text-[10px] mt-2">Terima kasih. Silakan menunggu pesanan disiapkan.</p>
        </div>
    @endif

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #struk-print,
            #struk-print * {
                visibility: visible;
            }

            #struk-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm;
                padding: 2mm;
                font-family: 'Courier New', monospace;
                color: #000;
                background: #fff;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</div>
