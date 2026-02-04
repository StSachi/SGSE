<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model VenueImage
 *
 * Guarda caminhos das imagens associadas a um `Venue`. Máximo 5 imagens
 * imposto pela lógica de aplicação (validação no upload).
 */
class VenueImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'path',
        'ordem',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
