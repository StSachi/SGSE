<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

/**
 * Controller base da aplicação
 *
 * ERS:
 * - Centraliza autorização
 * - Centraliza validações
 * - Centraliza auditoria
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Auditoria padronizada
     */
    protected function audit(
        string $acao,
        string $entidade,
        ?int $entidadeId = null,
        ?array $detalhes = null,
        ?string $ip = null
    ): void {
        app(AuditService::class)->log(
            Auth::id(),
            $acao,
            $entidade,
            $entidadeId,
            $detalhes,
            $ip ?? request()->ip()
        );
    }
}
