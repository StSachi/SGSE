<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed users: 1 ADMIN, 1 FUNCIONARIO, 1 PROPRIETARIO, 1 CLIENTE
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sgse.test'],
            ['name' => 'Admin SGSE', 'password' => Hash::make('password'), 'role' => 'ADMIN', 'ativo' => true]
        );

        User::updateOrCreate(
            ['email' => 'func@sgse.test'],
            ['name' => 'Funcionario SGSE', 'password' => Hash::make('password'), 'role' => 'FUNCIONARIO', 'ativo' => true]
        );

        User::updateOrCreate(
            ['email' => 'owner@sgse.test'],
            ['name' => 'Proprietario SGSE', 'password' => Hash::make('password'), 'role' => 'PROPRIETARIO', 'ativo' => true]
        );

        User::updateOrCreate(
            ['email' => 'client@sgse.test'],
            ['name' => 'Cliente SGSE', 'password' => Hash::make('password'), 'role' => 'CLIENTE', 'ativo' => true]
        );
    }
}
