<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = Category::pluck('id', 'nama');

        $menu = [
            // Kopi
            ['Kopi Hitam', 'Kopi tubruk klasik, diseduh manual', 12000, 'Kopi', true],
            ['Kopi Susu Gula Aren', 'Espresso, susu, gula aren cair', 18000, 'Kopi', true],
            ['Americano Dingin', 'Espresso dengan es batu', 15000, 'Kopi', false],

            // Non-Kopi
            ['Teh Tarik', 'Teh hitam dengan susu kental', 14000, 'Non-Kopi', false],
            ['Matcha Latte', 'Matcha Jepang dengan susu segar', 20000, 'Non-Kopi', true],
            ['Cokelat Panas', 'Cokelat bubuk premium, disajikan hangat', 16000, 'Non-Kopi', false],

            // Makanan Ringan
            ['Pisang Goreng Keju', 'Pisang goreng dengan keju parut', 15000, 'Makanan Ringan', true],
            ['Kentang Goreng', 'Kentang goreng renyah dengan saus', 13000, 'Makanan Ringan', false],
            ['Roti Bakar Cokelat', 'Roti bakar isi cokelat lumer', 12000, 'Makanan Ringan', false],

            // Makanan Berat
            ['Nasi Goreng Kampung', 'Nasi goreng dengan telur dan ayam', 25000, 'Makanan Berat', true],
            ['Mie Goreng Spesial', 'Mie goreng dengan sayur dan telur', 22000, 'Makanan Berat', false],
            ['Ayam Geprek Sambal Ijo', 'Ayam goreng dengan sambal ijo', 26000, 'Makanan Berat', false],
        ];

        $urutan = 1;
        foreach ($menu as [$nama, $deskripsi, $harga, $kategoriNama, $bestSeller]) {
            MenuItem::create([
                'category_id' => $kategori[$kategoriNama],
                'nama' => $nama,
                'deskripsi' => $deskripsi,
                'harga' => $harga,
                'tersedia' => true,
                'is_best_seller' => $bestSeller,
                'urutan' => $urutan++,
            ]);
        }
    }
}
