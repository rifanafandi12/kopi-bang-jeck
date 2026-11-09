<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Pemilik Demo', 'email' => 'pemilik@kopibangjeck.test', 'role' => 'pemilik'],
            ['name' => 'Admin Demo', 'email' => 'admin@kopibangjeck.test', 'role' => 'admin'],
            ['name' => 'Kasir Demo', 'email' => 'kasir@kopibangjeck.test', 'role' => 'kasir'],
        ];

        foreach ($users as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole($data['role']);
        }
    }
}
