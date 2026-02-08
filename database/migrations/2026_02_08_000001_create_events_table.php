<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->string('titulo', 120);
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->boolean('publico')->default(true);
            $table->timestamps();

            $table->index(['venue_id','start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

