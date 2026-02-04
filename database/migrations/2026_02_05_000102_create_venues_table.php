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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('provincia')->nullable();
            $table->string('municipio')->nullable();
            $table->string('endereco')->nullable();
            $table->integer('capacidade')->nullable();
            $table->decimal('preco_base', 12, 2)->default(0);
            $table->text('regras_texto')->nullable();
            // estado: PENDENTE / APROVADO / REJEITADO
            $table->string('estado')->default('PENDENTE');
            $table->timestamps();
        });
    }

    /**
     * Reverte as migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
