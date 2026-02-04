<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona o campo `role` para RBAC: ADMIN, FUNCIONARIO, PROPRIETARIO, CLIENTE
            $table->string('role')->default('CLIENTE')->after('email');
            // Indica se a conta do utilizador está activa
            $table->boolean('ativo')->default(true)->after('role');
        });
    }

    /**
     * Reverte as migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'ativo']);
        });
    }
};
