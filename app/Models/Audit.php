<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Audit (Registo de auditoria)
 */
class Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'acao',
        'entidade',
        'entidade_id',
        'detalhes',
        'ip',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
