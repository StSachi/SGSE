<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model VenueImage
 *
 * ERS / Regras:
 * - Imagens associadas a um salão (Venue).
 * - Limite máximo (ex: 5 imagens) imposto pela camada de validação/serviço.
 * - Ordem define a sequência de apresentação na galeria.
 */
class VenueImage extends Model
{
    use HasFactory;

    /**
     * Atributos permitidos para mass assignment
     */
    protected $fillable = [
        'venue_id',
        'path',
        'ordem',
    ];

    /**
     * Casts para integridade dos dados
     */
    protected $casts = [
        'venue_id' => 'integer',
        'ordem'    => 'integer',
    ];

    /**
     * Relação com o salão
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Venue::class);
    }
}
