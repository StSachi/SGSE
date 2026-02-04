<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Reservation;

/**
 * Serviço para registar pagamentos (simulados) e aplicar o efeito sobre a reserva.
 * A lógica de cálculo de sinal/total e transições ficará aqui.
 */
class PaymentService
{
    protected $reservationService;
    protected $settingsService;

    public function __construct(ReservationService $reservationService, SettingsService $settingsService)
    {
        $this->reservationService = $reservationService;
        $this->settingsService = $settingsService;
    }

    /**
     * Regista um pagamento simples e devolve o modelo Payment.
     * Não integra com gateway real — apenas simulação.
     */
    public function registerPayment(Reservation $reservation, string $tipo, float $valor, string $metodo = null, string $referencia = null): Payment
    {
        $payment = Payment::create([
            'reservation_id' => $reservation->id,
            'tipo' => $tipo,
            'valor' => $valor,
            'metodo_texto' => $metodo,
            'referencia' => $referencia,
        ]);

        // Efeito no estado da reserva tratado em métodos específicos (a implementar).

        return $payment;
    }
}
