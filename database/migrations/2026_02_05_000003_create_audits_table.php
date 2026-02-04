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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            // utilizador que executou a ação (pode ser null para sistemas)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // acao executada (login, logout, create, update, delete, approve, reject...)
            $table->string('acao');
            // entidade afectada (owners, venues, reservations, payments, settings)
            $table->string('entidade');
            // id da entidade afectada (quando aplicável)
            $table->string('entidade_id')->nullable();
            // detalhes adicionais / payload em texto/JSON
            $table->text('detalhes')->nullable();
            // informação de request
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverte as migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
