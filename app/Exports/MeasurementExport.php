<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Collection;

class MeasurementExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // Tambahkan return type Enumerable agar sesuai dengan interface library
    public function collection(): Enumerable
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        return [
            'Kode Pesanan',
            'Nomor Meja',
            'Tanggal',
            'Waktu Input (detik)',
            'Waktu Kasir (detik)',
            'Waktu Proses (detik)',
            'Total Durasi (detik)',
        ];
    }

    public function map($row): array
    {
        return [
            $row['kode'],
            $row['meja'],
            $row['tanggal'],
            $row['waktu_input'] ?? '-',
            $row['waktu_kasir'] ?? '-',
            $row['waktu_proses'] ?? '-',
            $row['total_durasi'] ?? '-',
        ];
    }

        public function styles(Worksheet $sheet): ?array
    {
        return [
            1    => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], // Teks Putih
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2B1A10'] // Warna Espresso (#2B1A10)
                ]
            ],
        ];
    }
}
