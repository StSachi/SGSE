<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitacaoOwner extends Model
{
    use HasFactory;

    protected $table = 'solicitacoes_owner';

    protected $fillable = [
        'nome','email','telefone','nif',
        'nome_salao','provincia','municipio',
        'estado','revisado_por','revisado_em','motivo_rejeicao',
    ];

    public const PENDENTE  = 'PENDENTE';
    public const APROVADA  = 'APROVADA';
    public const REJEITADA = 'REJEITADA';

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }
}
