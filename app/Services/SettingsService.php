<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Serviço para aceder às configurações (settings) da aplicação.
 * Fornece métodos simples para obter valores com fallback para tipos.
 */
class SettingsService
{
    /**
     * Obtém o valor de um setting por chave; retorna $default se não existir.
     */
    public function get(string $key, $default = null)
    {
        $row = DB::table('settings')->where('key', $key)->first();

        if (! $row) {
            return $default;
        }

        return $row->value;
    }

    /**
     * Define/atualiza um setting.
     */
    public function set(string $key, $value): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => (string) $value, 'updated_at' => now(), 'created_at' => now()]
        );
    }
}
