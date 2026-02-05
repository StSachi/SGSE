<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Services\AuditService;
use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job: cancela reservas CONFIRMADAS cujo pagamento total não foi efetuado
 * até X dias antes da data do evento (dias_min_pagamento_total).
 *
 * ERS:
 * - Reserva confirmada por sinal (CONFIRMADA)
 * - Se faltar <= dias_min_pagamento_total dias e não estiver quitada, cancela
 * - Regista auditoria automática
 */
class CheckPendingDeposits implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected SettingsService $settings;
    protected AuditService $audit;

    public function __construct()
    {
        // Em jobs, não dependas de injeção opcional; resolve no container.
        $this->settings = app(SettingsService::class);
        $this->audit = app(AuditService::class);
    }

    public function handle(): void
    {
        $diasMin = (int) $this->settings->get('dias_min_pagamento_total', 30);

        $today = now()->startOfDay();

        // Reservas confirmadas (por sinal) que ainda podem ser canceladas
        $reservas = Reservation::query()
            ->where('estado', Reservation::ESTADO_CONFIRMADA)
            ->get();

        foreach ($reservas as $res) {
            $dataEvento = Carbon::parse($res->data_evento)->startOfDay();

            // diff com sinal: se evento já passou, fica negativo
            $diasFaltam = $today->diffInDays($dataEvento, false);

            // Se já passou o evento ou já está dentro da janela mínima
            if ($diasFaltam <= $diasMin) {

                // Se já está totalmente quitada, não cancela
                // (usa o helper do Model que soma todos pagamentos)
                if ($res->isTotalQuitado()) {
                    // opcional: se quiseres, podes marcar como PAGA aqui,
                    // mas normalmente isso é feito no PaymentService.
                    continue;
                }

                // Cancela a reserva
                $res->estado = Reservation::ESTADO_CANCELADA;
                $res->save();

                // Auditoria (ação automática do sistema)
                $this->audit->log(
                    null,
                    'auto_cancel',
                    'reservations',
                    $res->id,
                    [
                        'reason' => 'pagamento_total_nao_efetuado',
                        'dias_min_pagamento_total' => $diasMin,
                        'dias_faltam' => $diasFaltam,
                        'valor_total' => (float) $res->valor_total,
                        'total_pago' => (float) $res->totalPago(),
                    ],
                    null
                );
            }
        }
    }
}
