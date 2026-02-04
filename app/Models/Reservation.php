<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Reservation (Reserva)
 *
 * Regras importantes documentadas:
 * - Reserva é para uma `data_evento` única.
 * - Estado inicial: PENDENTE_PAGAMENTO.
 * - A lógica de bloqueio de data e transições de estado é gerida em
 *   `app/Services/ReservationService.php`.
 */
class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'client_user_id',
        'data_evento',
        'estado',
        'valor_total',
        'valor_sinal',
        'percent_sinal',
    ];

    protected $dates = [
        'data_evento',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
