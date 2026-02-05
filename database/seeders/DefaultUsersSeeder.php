<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
        User::updateOrCreate(
            ['email' => 'admin@sgse.test'],
            [
                'name' => 'Admin SGSE',
                'password' => \Illuminate\Support\Facades\Hash::make('Admin@12345'),
                'papel' => 'ADMIN',
                'role' => 'ADMIN',
                'ativo' => 1,
            ]
        );

        // CLIENTE
        User::updateOrCreate(
            ['email' => 'client@sgse.test'],
            [
                'name' => 'Client SGSE',
                'password' => \Illuminate\Support\Facades\Hash::make('Client@12345'),
                'papel' => 'CLIENTE',
                'role' => 'CLIENTE',
                'ativo' => 1,
            ]
        );
    }
}
