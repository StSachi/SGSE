<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Services\AuditService;
use App\Services\SettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job que verifica reservas com sinal pago (CONFIRMADA) e cancela aquelas
 * cujo pagamento total não foi efetuado até `dias_min_pagamento_total` dias antes do evento.
 */
class CheckPendingDeposits implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $settings;
    protected $audit;

    public function __construct(SettingsService $settings = null, AuditService $audit = null)
    {
        $this->settings = $settings ?: app(SettingsService::class);
        $this->audit = $audit ?: app(AuditService::class);
    }

    public function handle(): void
    {
        $diasMin = (int) $this->settings->get('dias_min_pagamento_total', 30);

        // Encontrar reservas CONFIRMADA cujo evento esteja a <= diasMin dias
        $today = now();

        $reservas = Reservation::where('estado', 'CONFIRMADA')->get();

        foreach ($reservas as $res) {
            $dataEvento = \Carbon\Carbon::parse($res->data_evento);
            $diasFaltam = $dataEvento->diffInDays($today);

            if ($diasFaltam <= $diasMin) {
                // Verifica se o total já foi pago
                $totalPago = $res->payments()->where('tipo', 'TOTAL')->sum('valor');
                if ($totalPago <= 0) {
                    $res->estado = 'CANCELADA';
                    $res->save();

                    $this->audit->log(null, 'auto_cancel', 'reservations', $res->id, ['reason' => 'sinal nao complementado'], null);
                }
            }
        }
    }
}
