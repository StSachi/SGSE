<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Venue (Salão)
 *
 * Contém informação do espaço. Apenas venues com estado APROVADO
 * devem ser mostrados na pesquisa do cliente.
 */
class Venue extends Model
{
    use HasFactory;

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

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function images()
    {
        return $this->hasMany(VenueImage::class)->orderBy('ordem');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
