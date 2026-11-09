<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Enums\OrderEventType;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\MeasurementExport;
use Maatwebsite\Excel\Facades\Excel;

class MeasurementPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Data Pengukuran';
    protected static ?string $title = 'Pengukuran Waktu Pesanan';

    protected string $view = 'filament.pages.measurement-page';

    // Properti filter tanggal
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount()
    {
        // Default: 30 hari terakhir
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function getDataProperty()
    {
        // Ambil order beserta events-nya
        $orders = Order::with(['events', 'cafeTable'])
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->get();

        $data = [];

        foreach ($orders as $order) {
            $events = $order->events->keyBy(fn($e) => $e->event->value);

            // Fungsi bantu untuk menghitung selisih detik
            $diff = function ($start, $end) use ($events) {
                if (isset($events[$start]) && isset($events[$end])) {
                    return $events[$start]->occurred_at->diffInSeconds($events[$end]->occurred_at);
                }
                return null;
            };

            // Rumus durasi (bisa disesuaikan dengan dosen)
            $waktuInput = $diff('qr_scanned', 'order_confirmed');
            $waktuKasir = $diff('order_confirmed', 'accepted_by_cashier');
            $waktuProses = $diff('accepted_by_cashier', 'ready');

            $data[] = [
                'kode' => $order->kode,
                'meja' => $order->cafeTable->nomor,
                'tanggal' => $order->created_at->format('d/m/Y H:i'),
                'waktu_input' => $waktuInput,
                'waktu_kasir' => $waktuKasir,
                'waktu_proses' => $waktuProses,
                'total_durasi' => ($waktuInput !== null && $waktuProses !== null) ? $waktuInput + $waktuKasir + $waktuProses : null,
            ];
        }

        return $data;
    }

    public function exportExcel()
    {
        // Pastikan getDataProperty() mengembalikan array
        $data = $this->getDataProperty();
        $filename = 'Data_Pengukuran_Kopi_Bang_Jeck_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new MeasurementExport($data), $filename);
    }

    public function resetFilter()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }
}
