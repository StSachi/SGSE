<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeder de configurações iniciais do sistema
        $this->call(SettingsSeeder::class);

        // Seeder de utilizadores (ADMIN, FUNCIONARIO, PROPRIETARIO, CLIENTE)
        $this->call(UserSeeder::class);
    }
}
