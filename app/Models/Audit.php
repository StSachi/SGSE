<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Audit
 *
 * Responsável por registar ações críticas do sistema
 * (requisito de auditoria do ERS).
 */
class Audit extends Model
{
    use HasFactory;

    /**
     * Tabela associada (explícito para clareza no ERS)
     */
    protected $table = 'audits';

    /**
     * Atributos permitidos para mass assignment
     */
    protected $fillable = [
        'user_id',      // utilizador que executou a ação
        'acao',         // ação realizada (CREATE, UPDATE, DELETE, LOGIN, etc.)
        'entidade',     // entidade afetada (Reservation, Venue, Payment, etc.)
        'entidade_id',  // ID da entidade afetada
        'detalhes',     // detalhes adicionais (JSON/texto)
        'ip',           // IP do utilizador
        'user_agent',   // browser/dispositivo
    ];

    /**
     * Casts para garantir integridade dos dados
     */
    protected $casts = [
        'user_id'     => 'integer',
        'entidade_id' => 'integer',
        'detalhes'    => 'array',   // permite guardar JSON de forma limpa
    ];

    /**
     * Relação com o utilizador
     * (quem fez a ação)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
