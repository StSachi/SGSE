<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware simples para RBAC baseado no campo `users.role`.
 * Pode ser usado com parâmetros: ->middleware(RoleMiddleware::class . ':ADMIN')
 * ou para múltiplos papéis: RoleMiddleware::class . ':ADMIN|FUNCIONARIO'
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Se não estiver autenticado, devolve 403 (rota deverá usar também 'auth')
        if (! $user) {
            abort(403, 'Acesso não autorizado.');
        }

        // Se não forem especificados roles, negar (defensivo)
        if (empty($roles)) {
            abort(403, 'Papel não especificado.');
        }

        // Normaliza papéis permitidos (pode vir como 'ADMIN|FUNCIONARIO' numa string única)
        $allowed = [];
        foreach ($roles as $r) {
            foreach (explode('|', $r) as $part) {
                $allowed[] = trim($part);
            }
        }

        // Verifica se o papel do utilizador está nos permitidos
        if (! in_array($user->role, $allowed, true)) {
            abort(403, 'Permissão insuficiente.');
        }

        return $next($request);
    }
}
