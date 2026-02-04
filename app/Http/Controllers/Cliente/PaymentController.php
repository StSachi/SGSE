<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\AuditService;
use App\Services\PaymentService;
use App\Services\ReservationService;
use App\Services\SettingsService;
use Illuminate\Http\Request;

/**
 * Controller para pagamentos simulados (CLIENTE).
 * Regras:
 * - Se faltarem menos de dias_min_pagamento_total dias para o evento, não permitir pagamento por sinal.
 * - Pagamento SINAL marca reserva como CONFIRMADA (se não houver conflito).
 * - Pagamento TOTAL marca reserva como PAGA.
 */
class PaymentController extends Controller
{
    protected $paymentService;
    protected $reservationService;
    protected $settings;
    protected $audit;

    public function __construct(PaymentService $paymentService, ReservationService $reservationService, SettingsService $settings, AuditService $audit)
    {
        $this->paymentService = $paymentService;
        $this->reservationService = $reservationService;
        $this->settings = $settings;
        $this->audit = $audit;
    }

    public function create(Reservation $reservation)
    {
        // Apenas o cliente dono da reserva pode pagar
        $this->authorize('view', $reservation);

        // Se já estiver cancelada, não permitir pagamento
        if ($reservation->estado === 'CANCELADA') {
            abort(400, 'Reserva cancelada.');
        }

        $diasMin = (int) $this->settings->get('dias_min_pagamento_total', 30);
        $percent = (float) $this->settings->get('percent_sinal', 20);

        return view('cliente.payments.create', compact('reservation', 'diasMin', 'percent'));
    }

    public function store(Request $request, Reservation $reservation)
    {
        $this->authorize('view', $reservation);

        $tipo = $request->input('tipo'); // 'SINAL' ou 'TOTAL'
        $valor = (float) $request->input('valor');

        $diasMin = (int) $this->settings->get('dias_min_pagamento_total', 30);
        $percent = (float) $this->settings->get('percent_sinal', 20);

        $dataEvento = \Carbon\Carbon::parse($reservation->data_evento);
        $diasFaltam = $dataEvento->diffInDays(now());

        if ($tipo === 'SINAL') {
            if ($diasFaltam < $diasMin) {
                return back()->withErrors(['tipo' => 'Pagamento por sinal não permitido tão perto da data do evento.']);
            }

            // Verifica conflitos: não pode existir outra reserva CONFIRMADA/PAGA para mesma venue/data
            if ($this->reservationService->hasConfirmedOrPaidReservation($reservation->venue_id, $reservation->data_evento)) {
                return back()->withErrors(['tipo' => 'Data já ocupada.']);
            }

            $payment = $this->paymentService->registerPayment($reservation, 'SINAL', $valor, $request->input('metodo') ?? 'simulado', $request->input('referencia'));

            // Marcar reserva como CONFIRMADA e guardar percent/valor_sinal
            $reservation->estado = 'CONFIRMADA';
            $reservation->valor_sinal = $valor;
            $reservation->percent_sinal = $percent;
            $reservation->save();

            $this->audit->log($request->user(), 'create', 'payments', $payment->id, ['tipo'=>'SINAL','valor'=>$valor,'reservation_id'=>$reservation->id], $request);

            return redirect()->route('cliente.dashboard')->with('status', 'Sinal registado. Reserva confirmada.');
        }

        if ($tipo === 'TOTAL') {
            // Regista pagamento total
            $payment = $this->paymentService->registerPayment($reservation, 'TOTAL', $valor, $request->input('metodo') ?? 'simulado', $request->input('referencia'));

            // Marcar reserva como PAGA
            $reservation->estado = 'PAGA';
            $reservation->save();

            $this->audit->log($request->user(), 'create', 'payments', $payment->id, ['tipo'=>'TOTAL','valor'=>$valor,'reservation_id'=>$reservation->id], $request);

            return redirect()->route('cliente.dashboard')->with('status', 'Pagamento total registado.');
        }

        return back()->withErrors(['tipo' => 'Tipo de pagamento inválido.']);
    }
}
