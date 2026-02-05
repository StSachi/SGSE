<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\Request;

class AuditService
{
    /**
     * Regista auditoria.
     *
     * Aceita:
     * - $user: User | int | null
     * - $detalhes: array | string | null
     * - $requestOrIp: Request | string(ip) | null
     *
     * @param  User|int|null              $user
     * @param  string                     $acao
     * @param  string                     $entidade
     * @param  mixed                      $entidadeId
     * @param  array|string|null          $detalhes
     * @param  Request|string|null        $requestOrIp
     * @return Audit
     */
    public function log($user, string $acao, string $entidade, $entidadeId = null, $detalhes = null, Request|string|null $requestOrIp = null): Audit
    {
        // user_id (aceita User, int ou null)
        $userId = null;
        if ($user instanceof User) {
            $userId = $user->id;
        } elseif (is_numeric($user)) {
            $userId = (int) $user;
        }

        // IP + User-Agent
        $ip = null;
        $userAgent = null;

        if ($requestOrIp instanceof Request) {
            $ip = $requestOrIp->ip();
            $userAgent = substr((string) $requestOrIp->userAgent(), 0, 255);
        } elseif (is_string($requestOrIp)) {
            $ip = $requestOrIp;
            $userAgent = substr((string) request()->userAgent(), 0, 255);
        } else {
            $ip = request()->ip();
            $userAgent = substr((string) request()->userAgent(), 0, 255);
        }

        // detalhes -> guardar string (json se vier array)
        if (is_array($detalhes)) {
            $detalhes = json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // entidade_id: guarda int quando possível; senão null
        $entidadeIdFinal = is_numeric($entidadeId) ? (int) $entidadeId : null;

        return Audit::create([
            'user_id' => $userId,
            'acao' => $acao,
            'entidade' => $entidade,
            'entidade_id' => $entidadeIdFinal,
            'detalhes' => $detalhes,
            'ip' => $ip,
            'user_agent' => $userAgent,
        ]);
    }
}
