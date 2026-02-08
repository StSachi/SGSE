<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sgse.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'papel' => 'ADMIN',
                'ativo' => true,
            ]
        );
    }
}

