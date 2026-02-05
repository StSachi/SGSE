<?php

namespace Tests\Feature;

use App\Jobs\CheckPendingDeposits;
use App\Models\Owner;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AutoCancelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function confirmed_reservation_without_total_payment_is_cancelled_by_job(): void
    {
        // Criar contexto
        $user = User::factory()->create();
        $owner = Owner::create(['user_id' => $user->id, 'telefone' => null, 'estado' => 'APROVADO']);
        $venue = Venue::create(['owner_id' => $owner->id, 'nome' => 'Sala', 'preco_base' => 1000, 'estado' => 'APROVADO']);

        // Criar reserva CONFIRMADA com data a 10 dias
        $res = Reservation::create([
            'venue_id' => $venue->id,
            'client_user_id' => $user->id,
            'data_evento' => now()->addDays(10)->toDateString(),
            'estado' => 'CONFIRMADA',
            'valor_total' => 1000,
        ]);

        // Ajustar setting para dias_min_pagamento_total = 15 (para que diasFaltam <= diasMin true)
        DB::table('settings')->updateOrInsert(['key' => 'dias_min_pagamento_total'], ['value' => '15', 'created_at' => now(), 'updated_at' => now()]);

        // Executar job
        $job = new CheckPendingDeposits();
        $job->handle();

        $res->refresh();
        $this->assertEquals('CANCELADA', $res->estado);
    }
}
