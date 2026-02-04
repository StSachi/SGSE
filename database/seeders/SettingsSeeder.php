<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Executa o seeder das configurações iniciais.
     */
    public function run(): void
    {
        // Percentual padrão do sinal (em percentagem)
        DB::table('settings')->updateOrInsert(
            ['key' => 'percent_sinal'],
            ['value' => '20', 'created_at' => now(), 'updated_at' => now()]
        );

        // Dias mínimos antes do evento para pagamento total
        DB::table('settings')->updateOrInsert(
            ['key' => 'dias_min_pagamento_total'],
            ['value' => '30', 'created_at' => now(), 'updated_at' => now()]
        );
    }
}
