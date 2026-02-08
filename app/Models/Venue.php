<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    // ---------------- Estados (ERS) ----------------
    public const ESTADO_PENDENTE  = 'PENDENTE';
    public const ESTADO_APROVADO  = 'APROVADO';
    public const ESTADO_REJEITADO = 'REJEITADO';

    protected $fillable = [
        'owner_id',
        'nome',
        'descricao',
        'provincia',
        'cidade',
        'municipio',
        'endereco',
        'capacidade',
        'preco_base',
        'regras_texto',
        'estado',
    ];

    protected $casts = [
        'capacidade' => 'integer',
        'preco_base' => 'decimal:2',
    ];

    protected $attributes = [
        'estado' => self::ESTADO_PENDENTE,
        'preco_base' => 0.00,
    ];

    // ---------------- Relações ----------------

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(VenueImage::class)->orderBy('ordem');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'venue_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'venue_id');
    }

    // ---------------- Scopes ----------------

    public function scopeAprovados(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_APROVADO);
    }

    // ---------------- Helpers ----------------

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

    public function getPrecoBaseFormatadoAttribute(): string
    {
        return number_format((float) $this->preco_base, 2, ',', '.') . ' Kz';
    }
}
