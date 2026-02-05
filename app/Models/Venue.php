<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Venue (Salão)
 *
 * ERS / Regras:
 * - Um salão pertence a um Proprietário (Owner).
 * - Apenas salões com estado APROVADO são visíveis para clientes.
 * - Workflow de aprovação: PENDENTE → APROVADO / REJEITADO.
 * - Preço base é o valor de referência para cálculo da reserva.
 */
class Venue extends Model
{
    use HasFactory;

    // ---------------- Estados (ERS) ----------------
    public const ESTADO_PENDENTE  = 'PENDENTE';
    public const ESTADO_APROVADO  = 'APROVADO';
    public const ESTADO_REJEITADO = 'REJEITADO';

    /**
     * Atributos permitidos para mass assignment
     */
    protected $fillable = [
        'owner_id',
        'nome',
        'descricao',
        'provincia',
        'municipio',
        'endereco',
        'capacidade',
        'preco_base',
        'regras_texto',
        'estado',
    ];

    /**
     * Casts para integridade de dados
     */
    protected $casts = [
        'owner_id'   => 'integer',
        'capacidade'=> 'integer',
        'preco_base'=> 'decimal:2',
    ];

    /**
     * Valor por defeito (ERS)
     */
    protected $attributes = [
        'estado' => self::ESTADO_PENDENTE,
    ];

    // ---------------- Relações ----------------

    public function owner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Owner::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(\App\Models\VenueImage::class)->orderBy('ordem');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(\App\Models\Reservation::class);
    }

    // ---------------- Helpers de estado (ERS) ----------------

    public function isPendente(): bool
    {
        return $this->estado === self::ESTADO_PENDENTE;
    }

    public function isAprovado(): bool
    {
        return $this->estado === self::ESTADO_APROVADO;
    }

    public function isRejeitado(): bool
    {
        return $this->estado === self::ESTADO_REJEITADO;
    }
}
