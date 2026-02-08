<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Models\Venue;
use App\Services\ReservationService;
use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Criação de reservas por CLIENTE.
 *
 * ERS:
 * - Reserva inicia como PENDENTE_PAGAMENTO e NÃO bloqueia a data.
 * - Data é bloqueada apenas após SINAL (CONFIRMADA) ou TOTAL (PAGA).
 * - Cliente só pode reservar Venue APROVADO.
 * - Auditoria da ação.
 */
class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService,
        protected SettingsService $settings
    ) {}

    private function assertClienteAtivo(Request $request): void
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'isCliente') || ! $user->isCliente()) {
            abort(403);
        }

        // Só valida "ativo" se existir no User
        if (property_exists($user, 'ativo') && ! $user->ativo) {
            abort(403);
        }
    }

    public function store(ReservationRequest $request)
    {
        $this->assertClienteAtivo($request);

        $user = $request->user();

        /** @var Venue $venue */
        $venue = Venue::query()->findOrFail($request->input('venue_id'));

        // Cliente só pode reservar salão aprovado
        // (Preferir regra por estado, e manter helper se existir)
        if (property_exists($venue, 'estado')) {
            if ($venue->estado !== Venue::ESTADO_APROVADO) {
                abort(403, 'Salão não aprovado.');
            }
        } elseif (method_exists($venue, 'isAprovado') && ! $venue->isAprovado()) {
            abort(403, 'Salão não aprovado.');
        }

        $dataEvento = Carbon::parse($request->input('data_evento'))->startOfDay();

        // Não permitir datas no passado
        if ($dataEvento->lt(now()->startOfDay())) {
            return back()
                ->withErrors([
                    'data_evento' => __('validation.after_or_equal', [
                        'attribute' => 'data_evento',
                        'date' => now()->toDateString(),
                    ]),
                ])
                ->withInput();
        }

        // Bloqueia APENAS se já existe CONFIRMADA/PAGA (ERS)
        if ($this->reservationService->hasConfirmedOrPaidReservation($venue->id, $dataEvento->toDateString())) {
            return back()
                ->withErrors(['data_evento' => __('messages.date_conflict')])
                ->withInput();
        }

        // Valor total (preco_base)
        $valorTotal = (float) ($venue->preco_base ?? 0);

        if ($valorTotal <= 0) {
            return back()
                ->withErrors(['venue_id' => 'Este salão não tem preço base definido.'])
                ->withInput();
        }

        // Percentual do sinal (settings)
        $percent = (float) $this->settings->get('percent_sinal', 20);
        $percent = max(0, min(100, $percent));

        $valorSinal = round($valorTotal * ($percent / 100), 2);

        /** @var Reservation $reservation */
        $reservation = $this->reservationService->createPending([
            'venue_id'       => $venue->id,
            'client_user_id' => $user->id,
            'data_evento'    => $dataEvento->toDateString(),
            'estado'         => Reservation::ESTADO_PENDENTE_PAGAMENTO,
            'valor_total'    => $valorTotal,
            'valor_sinal'    => $valorSinal,
            'percent_sinal'  => (float) $percent,
        ]);

        // Auditoria (assumindo que existe este método no Controller base)
        $this->audit(
            'reservation_create',
            'reservations',
            $reservation->id,
            [
                'venue_id'       => $venue->id,
                'data_evento'    => $reservation->data_evento,
                'estado'         => $reservation->estado,
                'valor_total'    => $valorTotal,
                'valor_sinal'    => $valorSinal,
                'percent_sinal'  => (float) $percent,
            ],
            $request->ip()
        );

        return redirect()
            ->route('cliente.dashboard')
            ->with('status', __('messages.reservation_created_pending'));
    }
}
