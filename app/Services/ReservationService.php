<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

/**
 * Serviço para lógica de reservas: criação, validação de disponibilidade
 * e transições de estado. Regras complexas (sinal/total, bloqueio de data,
 * cancelamento automático) residirão aqui.
 */
class ReservationService
{
    protected $settings;

    public function __construct(SettingsService $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Verifica se existe já uma reserva CONFIRMADA ou PAGA para o mesmo
     * salão (`venue_id`) na mesma data (`data_evento`). Retorna bool.
     */
    public function hasConfirmedOrPaidReservation(int $venueId, string $dataEvento): bool
    {
        return Reservation::where('venue_id', $venueId)
            ->where('data_evento', $dataEvento)
            ->whereIn('estado', ['CONFIRMADA', 'PAGA'])
            ->exists();
    }

    /**
     * Cria uma reserva inicial (PENDENTE_PAGAMENTO). A data não fica bloqueada
     * até que o sinal ou o total seja pago.
     */
    public function createPending(array $data): Reservation
    {
        // transacção para garantir consistência ao criar
        return DB::transaction(function () use ($data) {
            return Reservation::create($data);
        });
    }

    // Outras operações (confirmar, marcar como paga, cancelar) serão implementadas
    // nas próximas fases, com registo de auditoria via AuditService.
}
