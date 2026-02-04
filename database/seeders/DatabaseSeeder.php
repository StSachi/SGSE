<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar um utilizador de teste (pode ser substituído por seeders específicos)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Executa o seeder de configurações iniciais (percent_sinal, dias_min_pagamento_total)
        $this->call(\Database\Seeders\SettingsSeeder::class);

        // Executa o seeder de utilizadores (ADMIN, FUNCIONARIO, PROPRIETARIO, CLIENTE)
        $this->call(\Database\Seeders\UserSeeder::class);
    }
}
