<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Models\Venue;
use App\Services\AuditService;
use App\Services\ReservationService;
use App\Services\SettingsService;
use Illuminate\Http\Request;

/**
 * Controller para criação de reservas por `CLIENTE`.
 * A reserva criada inicia como `PENDENTE_PAGAMENTO` e NÃO bloqueia a data.
 */
class ReservationController extends Controller
{
    protected $reservationService;
    protected $audit;
    protected $settings;

    public function __construct(ReservationService $reservationService, AuditService $audit, SettingsService $settings)
    {
        $this->reservationService = $reservationService;
        $this->audit = $audit;
        $this->settings = $settings;
    }

    public function store(ReservationRequest $request)
    {
        $user = $request->user();
        $venue = Venue::findOrFail($request->input('venue_id'));

        // Se já existe uma reserva CONFIRMADA ou PAGA para o mesmo salão e data,
        // não permitir nova reserva (data ocupada).
        if ($this->reservationService->hasConfirmedOrPaidReservation($venue->id, $request->input('data_evento'))) {
            return back()->withErrors(['data_evento' => __('messages.date_conflict')]);
        }

        // Calcular valores padrão
        $valorTotal = $venue->preco_base;
        $percent = (float) $this->settings->get('percent_sinal', 20);
        $valorSinal = round($valorTotal * ($percent / 100), 2);

        $reservation = $this->reservationService->createPending([
            'venue_id' => $venue->id,
            'client_user_id' => $user->id,
            'data_evento' => $request->input('data_evento'),
            'estado' => 'PENDENTE_PAGAMENTO',
            'valor_total' => $valorTotal,
            'valor_sinal' => $valorSinal,
            'percent_sinal' => $percent,
        ]);

        // Registar auditoria da criação da reserva
        $this->audit->log($user, 'create', 'reservations', $reservation->id, ['data_evento' => $reservation->data_evento, 'venue_id' => $venue->id], $request);

        return redirect()->route('cliente.dashboard')->with('status', __('messages.reservation_created_pending'));
    }
}
