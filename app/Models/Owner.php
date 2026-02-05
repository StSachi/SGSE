<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Owner (Proprietário)
 *
 * Representa um proprietário associado a um utilizador do sistema.
 * ERS / Regras:
 * - Workflow: PENDENTE → APROVADO / REJEITADO
 * - Um proprietário pode ter vários salões (venues)
 */
class Owner extends Model
{
    use HasFactory;

    public const ESTADO_PENDENTE  = 'PENDENTE';
    public const ESTADO_APROVADO  = 'APROVADO';
    public const ESTADO_REJEITADO = 'REJEITADO';

    protected $fillable = [
        'user_id',
        'telefone',
        'documento',
        'estado',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    protected $attributes = [
        'estado' => self::ESTADO_PENDENTE,
    ];

    // ---------------- Relações ----------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(\App\Models\Venue::class);
    }

    // ---------------- Helpers de estado ----------------

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
