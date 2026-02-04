<?php

namespace App\Services;

use App\Models\Audit;
use Illuminate\Http\Request;

/**
 * Serviço responsável por registar eventos de auditoria.
 * Centraliza a criação de registos de auditoria para facilitar testes
 * e garantir consistência no formato dos `detalhes`.
 */
class AuditService
{
    /**
     * Regista um evento de auditoria.
     * @param  \App\Models\User|null  $user
     * @param  string $acao
     * @param  string $entidade
     * @param  mixed $entidadeId
     * @param  array|string|null $detalhes
     * @param  \Illuminate\Http\Request|null $request
     */
    public function log($user, string $acao, string $entidade, $entidadeId = null, $detalhes = null, Request $request = null): Audit
    {
        $payload = is_array($detalhes) ? json_encode($detalhes, JSON_UNESCAPED_UNICODE) : $detalhes;

        return Audit::create([
            'user_id' => $user ? $user->id : null,
            'acao' => $acao,
            'entidade' => $entidade,
            'entidade_id' => $entidadeId ? (string) $entidadeId : null,
            'detalhes' => $payload,
            'ip' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
        ]);
    }
}
