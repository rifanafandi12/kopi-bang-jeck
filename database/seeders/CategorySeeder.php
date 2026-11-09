<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Kopi', 'urutan' => 1],
            ['nama' => 'Non-Kopi', 'urutan' => 2],
            ['nama' => 'Makanan Ringan', 'urutan' => 3],
            ['nama' => 'Makanan Berat', 'urutan' => 4],
        ];

        foreach ($data as $row) {
            Category::create($row);
        }
    }
}
