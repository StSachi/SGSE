<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\PaymentService;
use App\Services\ReservationService;
use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Pagamentos simulados (CLIENTE)
 *
 * ERS:
 * - Apenas o cliente dono da reserva pode pagar
 * - Se faltar menos de dias_min_pagamento_total dias, não permite SINAL
 * - SINAL -> CONFIRMADA (se não houver conflito)
 * - TOTAL -> PAGA
 * - Auditoria de pagamentos
 */
class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected ReservationService $reservationService,
        protected SettingsService $settings,
    ) {}

    /**
     * Guard simples (evita depender de Policy se ainda não tiveres)
     */
    private function assertClienteOwner(Request $request, Reservation $reservation): void
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'isCliente') || ! $user->isCliente()) {
            abort(403);
        }

        if ((int) $reservation->client_user_id !== (int) $user->id) {
            abort(403);
        }

        if (property_exists($user, 'ativo') && ! $user->ativo) {
            abort(403);
        }
    }

    public function create(Request $request, Reservation $reservation)
    {
        $this->assertClienteOwner($request, $reservation);

        // Não permitir pagamento se já estiver cancelada ou paga
        if ($reservation->estado === Reservation::ESTADO_CANCELADA) {
            abort(400, 'Reserva cancelada.');
        }
        if ($reservation->estado === Reservation::ESTADO_PAGA) {
            abort(400, 'Reserva já está paga.');
        }

        $diasMin = (int) $this->settings->get('dias_min_pagamento_total', 30);
        $percent = (float) $this->settings->get('percent_sinal', 20);

        return view('cliente.payments.create', compact('reservation', 'diasMin', 'percent'));
    }

    public function store(Request $request, Reservation $reservation)
    {
        $this->assertClienteOwner($request, $reservation);

        // Não permitir pagamento se já estiver cancelada ou paga
        if ($reservation->estado === Reservation::ESTADO_CANCELADA) {
    return redirect()
        ->route('cliente.dashboard')
        ->with('error', 'Reserva cancelada. Não é possível pagar.');
        }

        if ($reservation->estado === Reservation::ESTADO_PAGA) {
            return redirect()
                ->route('cliente.dashboard')
                ->with('info', 'Esta reserva já está paga.');
        }


        $data = $request->validate([
            'tipo' => ['required', 'in:' . Payment::TIPO_SINAL . ',' . Payment::TIPO_TOTAL],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'metodo' => ['nullable', 'string', 'max:50'],
            'referencia' => ['nullable', 'string', 'max:100'],
        ]);

        $tipo = $data['tipo'];
        $valor = (float) $data['valor'];

        $diasMin = (int) $this->settings->get('dias_min_pagamento_total', 30);
        $percent = (float) $this->settings->get('percent_sinal', 20);

        $today = now()->startOfDay();
        $dataEvento = Carbon::parse($reservation->data_evento)->startOfDay();

        // diff com sinal (se evento já passou, fica negativo)
        $diasFaltam = $today->diffInDays($dataEvento, false);

        // Se evento já passou, não aceitar pagamentos
        if ($diasFaltam < 0) {
            return back()->withErrors(['tipo' => 'Data do evento já passou.'])->withInput();
        }

        if ($tipo === Payment::TIPO_SINAL) {

            // Se já está dentro da janela mínima, não permite SINAL (deve pagar TOTAL)
            if ($diasFaltam < $diasMin) {
                return back()->withErrors(['tipo' => __('messages.deposit_not_allowed')])->withInput();
            }

            // Não permitir confirmar se a data já foi ocupada por CONFIRMADA/PAGA
            if ($this->reservationService->hasConfirmedOrPaidReservation($reservation->venue_id, $reservation->data_evento)) {
                return back()->withErrors(['tipo' => __('messages.date_taken')])->withInput();
            }

            $payment = $this->paymentService->registerPayment(
                $reservation,
                Payment::TIPO_SINAL,
                $valor,
                $data['metodo'] ?? 'simulado',
                $data['referencia'] ?? null
            );

            // Atualiza reserva
            $reservation->estado = Reservation::ESTADO_CONFIRMADA;
            $reservation->valor_sinal = $valor;
            $reservation->percent_sinal = (int) $percent;
            $reservation->save();

            $this->audit(
                'payment_create',
                'payments',
                $payment->id,
                ['tipo' => Payment::TIPO_SINAL, 'valor' => $valor, 'reservation_id' => $reservation->id],
                $request->ip()
            );

            return redirect()
                ->route('cliente.dashboard')
                ->with('status', __('messages.deposit_registered'));
        }

        // TOTAL
        $payment = $this->paymentService->registerPayment(
            $reservation,
            Payment::TIPO_TOTAL,
            $valor,
            $data['metodo'] ?? 'simulado',
            $data['referencia'] ?? null
        );

        // Marca como paga (ou podes usar $reservation->isTotalQuitado() se quiseres exigir valor_total)
        $reservation->estado = Reservation::ESTADO_PAGA;
        $reservation->save();

        $this->audit(
            'payment_create',
            'payments',
            $payment->id,
            ['tipo' => Payment::TIPO_TOTAL, 'valor' => $valor, 'reservation_id' => $reservation->id],
            $request->ip()
        );

        return redirect()
            ->route('cliente.dashboard')
            ->with('status', __('messages.payment_registered'));
    }
}
