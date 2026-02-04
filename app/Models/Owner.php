<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Owner (Proprietário)
 *
 * Representa um proprietário que está associado a um `User`.
 * Regras de negócio: o estado pode ser PENDENTE/APROVADO/REJEITADO.
 */
class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'telefone',
        'documento',
        'estado',
    ];

    // Relações
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }
}
