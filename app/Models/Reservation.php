<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    // ---------- Estados (ERS) ----------
    public const ESTADO_PENDENTE_PAGAMENTO = 'PENDENTE_PAGAMENTO';
    public const ESTADO_CONFIRMADA         = 'CONFIRMADA';
    public const ESTADO_PAGA               = 'PAGA';
    public const ESTADO_CANCELADA          = 'CANCELADA';
    public const ESTADO_REJEITADA          = 'REJEITADA';

    protected $fillable = [
        'venue_id',
        'client_user_id',
        'data_evento',
        'estado',
        'valor_total',
        'valor_sinal',
        'percent_sinal',
    ];

    protected $casts = [
        'data_evento' => 'date',
        'valor_total' => 'decimal:2',
        'valor_sinal' => 'decimal:2',
        'percent_sinal' => 'decimal:2',
    ];

    // ---------- Relações ----------
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    // ---------- Helpers ----------
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
}
