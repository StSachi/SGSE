<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Venue;
use App\Models\User;
use App\Models\Owner;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function hasConfirmedOrPaidReservation_detects_existing_confirmed_or_paid(): void
    {
        // Criar user e owner e venue
        $user = User::factory()->create();
        $owner = Owner::create(['user_id' => $user->id, 'telefone' => null, 'estado' => 'APROVADO']);

        $venue = Venue::create([
            'owner_id' => $owner->id,
            'nome' => 'Sala Teste',
            'preco_base' => 1000,
            'estado' => 'APROVADO',
        ]);

        $res = Reservation::create([
            'venue_id' => $venue->id,
            'client_user_id' => $user->id,
            'data_evento' => now()->addDays(10)->toDateString(),
            'estado' => 'CONFIRMADA',
            'valor_total' => 1000,
        ]);

        $service = app(ReservationService::class);

        $this->assertTrue($service->hasConfirmedOrPaidReservation($venue->id, $res->data_evento));
    }
}
