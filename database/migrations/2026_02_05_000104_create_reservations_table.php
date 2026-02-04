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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->onDelete('cascade');
            $table->foreignId('client_user_id')->constrained('users')->onDelete('cascade');
            // data única do evento
            $table->date('data_evento');
            // estados: PENDENTE_PAGAMENTO, CONFIRMADA, PAGA, CANCELADA
            $table->string('estado')->default('PENDENTE_PAGAMENTO');
            $table->decimal('valor_total', 12, 2)->default(0);
            $table->decimal('valor_sinal', 12, 2)->nullable();
            $table->decimal('percent_sinal', 5, 2)->nullable();
            $table->timestamps();

            // índice para pesquisa por sala + data
            $table->index(['venue_id', 'data_evento']);
        });
    }

    /**
     * Reverte as migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
