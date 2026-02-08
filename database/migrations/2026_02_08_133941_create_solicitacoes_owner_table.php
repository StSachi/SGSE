<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes_owner', function (Blueprint $table) {
            $table->id();

            // Dados do solicitante (Owner)
            $table->string('nome', 120);
            $table->string('email', 150);
            $table->string('telefone', 30)->nullable();
            $table->string('nif', 50)->nullable();

            // Dados mínimos do salão (opcional nesta fase)
            $table->string('nome_salao', 150)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('municipio', 100)->nullable();

            // Estado do pedido
            $table->enum('estado', ['PENDENTE', 'APROVADA', 'REJEITADA'])
                  ->default('PENDENTE');

            // Revisão / Auditoria
            $table->foreignId('revisado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('revisado_em')->nullable();
            $table->text('motivo_rejeicao')->nullable();

            $table->timestamps();

            // Regras de unicidade para evitar duplicação
            $table->unique('email');

            // Se quiseres bloquear telefone repetido, descomenta:
            // $table->unique('telefone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_owner');
    }
};
