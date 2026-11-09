<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Bagian Filter dan Tombol --}}
        <div style="background-color: #F6E7B4; border: 1px solid rgba(43, 26, 16, 0.2); border-radius: 8px; padding: 16px; display: flex; flex-wrap: wrap; align-items: flex-end; gap: 16px;">
            <div>
                <label style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #8A5F1E; margin-bottom: 4px;">Dari Tanggal</label>
                <input type="date" wire:model.live="startDate" style="padding: 8px; border-radius: 6px; border: 1px solid rgba(43, 26, 16, 0.3); background-color: #F6F1E3; color: #2B1A10;">
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #8A5F1E; margin-bottom: 4px;">Sampai Tanggal</label>
                <input type="date" wire:model.live="endDate" style="padding: 8px; border-radius: 6px; border: 1px solid rgba(43, 26, 16, 0.3); background-color: #F6F1E3; color: #2B1A10;">
            </div>

            <div style="display: flex; gap: 8px;">
                <button wire:click="exportExcel" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; font-weight: 500; color: #2B1A10; background-color: #B8863A; border-radius: 6px; border: none; cursor: pointer; transition: 0.15s;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Ekspor ke Excel
                </button>

                <button wire:click="resetFilter" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #2B1A10; background-color: transparent; border: 1px solid rgba(43, 26, 16, 0.4); border-radius: 6px; cursor: pointer;">
                    Reset
                </button>
            </div>
        </div>

        {{-- Bagian Tabel Data --}}
        <div style="background-color: #F6F1E3; border: 1px solid rgba(43, 26, 16, 0.2); border-radius: 8px; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; text-align: left; border-collapse: collapse; font-size: 14px;">
                    <thead style="background-color: #2B1A10; color: #F6F1E3; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">
                        <tr>
                            <th style="padding: 12px 16px; font-weight: 500;">Kode</th>
                            <th style="padding: 12px 16px; font-weight: 500;">Meja</th>
                            <th style="padding: 12px 16px; font-weight: 500; text-align: center;">Waktu Input (dtk)</th>
                            <th style="padding: 12px 16px; font-weight: 500; text-align: center;">Waktu Kasir (dtk)</th>
                            <th style="padding: 12px 16px; font-weight: 500; text-align: center;">Waktu Proses (dtk)</th>
                            <th style="padding: 12px 16px; font-weight: bold; text-align: center; color: #F6E7B4;">Total (dtk)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->getDataProperty() as $row)
                            <tr style="border-bottom: 1px solid rgba(43, 26, 16, 0.1);">
                                <td style="padding: 12px 16px; font-family: monospace; color: #2B1A10;">{{ $row['kode'] }}</td>
                                <td style="padding: 12px 16px; color: #6B5645;">{{ $row['meja'] }}</td>
                                <td style="padding: 12px 16px; text-align: center; color: #6B5645;">{{ $row['waktu_input'] ?? '-' }}</td>
                                <td style="padding: 12px 16px; text-align: center; color: #6B5645;">{{ $row['waktu_kasir'] ?? '-' }}</td>
                                <td style="padding: 12px 16px; text-align: center; color: #6B5645;">{{ $row['waktu_proses'] ?? '-' }}</td>
                                <td style="padding: 12px 16px; text-align: center; font-weight: bold; color: #8A5F1E;">{{ $row['total_durasi'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 32px; text-align: center; color: #6B5645;">
                                    Belum ada data pesanan pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-filament-panels::page>
