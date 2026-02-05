<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Reservation (Reserva)
 *
 * ERS / Regras:
 * - Reserva pertence a um Venue (salão) e a um Cliente (User).
 * - data_evento é a data do evento (um salão não deve ter duas reservas confirmadas para a mesma data).
 * - Estado inicial: PENDENTE_PAGAMENTO.
 * - Transições e bloqueios são aplicados pela camada de serviço (ReservationService),
 *   mas o Model define constantes, casts e helpers para consistência.
 */
class Reservation extends Model
{
    use HasFactory;

    // ---------------- Estados (ERS) ----------------
    public const ESTADO_PENDENTE_PAGAMENTO = 'PENDENTE_PAGAMENTO';
    public const ESTADO_CONFIRMADA         = 'CONFIRMADA';
    public const ESTADO_PAGA               = 'PAGA';
    public const ESTADO_CANCELADA          = 'CANCELADA';

    /**
     * Atributos permitidos para mass assignment
     */
    protected $fillable = [
        'venue_id',
        'client_user_id',
        'data_evento',
        'estado',
        'valor_total',
        'valor_sinal',
        'percent_sinal',
    ];

    /**
     * Casts para integridade (Laravel 12: preferível a $casts ao $dates)
     */
    protected $casts = [
        'venue_id'        => 'integer',
        'client_user_id'  => 'integer',
        'data_evento'     => 'date',
        'valor_total'     => 'decimal:2',
        'valor_sinal'     => 'decimal:2',
        'percent_sinal'   => 'integer',
    ];

    /**
     * Defaults (ERS)
     */
    protected $attributes = [
        'estado' => self::ESTADO_PENDENTE_PAGAMENTO,
    ];

    // ---------------- Relações ----------------

    public function venue(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Venue::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'client_user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    // ---------------- Helpers de estado (ERS) ----------------

    public function isPendentePagamento(): bool
    {
        return $this->estado === self::ESTADO_PENDENTE_PAGAMENTO;
    }

    public function isConfirmada(): bool
    {
        return $this->estado === self::ESTADO_CONFIRMADA;
    }

    public function isPaga(): bool
    {
        return $this->estado === self::ESTADO_PAGA;
    }

    public function isCancelada(): bool
    {
        return $this->estado === self::ESTADO_CANCELADA;
    }

    // ---------------- Helpers financeiros (ERS) ----------------

    /**
     * Total pago (soma de pagamentos registados)
     */
    public function totalPago(): float
    {
        // evita problemas de decimal/string
        return (float) ($this->payments()->sum('valor') ?? 0);
    }

    /**
     * Indica se já atingiu o valor total
     */
    public function isTotalQuitado(): bool
    {
        return $this->totalPago() >= (float) $this->valor_total;
    }

    /**
     * Indica se já atingiu pelo menos o sinal
     */
    public function isSinalQuitado(): bool
    {
        return $this->totalPago() >= (float) $this->valor_sinal;
    }
}
