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
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            // ligação ao user que é proprietário
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('telefone')->nullable();
            $table->string('documento')->nullable();
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
        Schema::dropIfExists('owners');
    }
};
