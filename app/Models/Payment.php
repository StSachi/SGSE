<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Payment (Pagamento)
 *
 * Pagamentos são apenas simulados; o sistema regista `tipo` (SINAL/TOTAL),
 * valor, método e referência. A lógica de aplicação decide o impacto
 * sobre a reserva (marcar como paga/confirmada, etc.).
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'tipo',
        'valor',
        'metodo_texto',
        'referencia',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
