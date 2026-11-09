<?php

namespace Database\Seeders;

use App\Models\CafeTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CafeTableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            CafeTable::create([
                'nomor' => (string) $i,
                'token' => (string) Str::ulid(),
                'aktif' => true,
            ]);
        }
    }
}
