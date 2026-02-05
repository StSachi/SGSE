<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Payment (Pagamento)
 *
 * Pagamentos simulados conforme ERS:
 * - Tipo: SINAL ou TOTAL
 * - Método e referência apenas informativos
 * - Impacto real ocorre na lógica da reserva
 */
class Payment extends Model
{
    use HasFactory;

    /**
     * Tipos de pagamento permitidos (ERS)
     */
    public const TIPO_SINAL = 'SINAL';
    public const TIPO_TOTAL = 'TOTAL';

    /**
     * Atributos permitidos para mass assignment
     */
    protected $fillable = [
        'reservation_id',
        'tipo',
        'valor',
        'metodo_texto',
        'referencia',
    ];

    /**
     * Casts para integridade de dados
     */
    protected $casts = [
        'reservation_id' => 'integer',
        'valor' => 'decimal:2',
    ];

    // ---------------- Relações ----------------

    /**
     * Reserva associada ao pagamento
     */
    public function reservation()
    {
        return $this->belongsTo(\App\Models\Reservation::class);
    }

    // ---------------- Helpers (ERS / regras) ----------------

    public function isSinal(): bool
    {
        return $this->tipo === self::TIPO_SINAL;
    }

    public function isTotal(): bool
    {
        return $this->tipo === self::TIPO_TOTAL;
    }
}
