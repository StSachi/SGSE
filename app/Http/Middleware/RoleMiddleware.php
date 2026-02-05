<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1) Sessão expirada / não autenticado -> login (HTML) ou 401 (API/AJAX)
        $user = $request->user();
        if (! $user) {
            if ($request->expectsJson()) {
                abort(401, 'Não autenticado.');
            }
            return redirect()->route('login');
        }

        // 2) Conta desativada -> 403
        if (isset($user->ativo) && ! $user->ativo) {
            abort(403, 'Conta desativada.');
        }

        // 3) Papel do utilizador (fonte principal: papel; fallback: role)
        $papel = $user->papel ?? $user->role ?? null;

        // 4) Se não tem papel -> 403 (conta mal configurada)
        if (! $papel) {
            abort(403, 'Conta sem perfil definido.');
        }

        // 5) Normaliza papéis exigidos (aceita "ADMIN" ou "ADMIN,FUNCIONARIO")
        $allowed = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $allowed[] = $part;
                }
            }
        }

        // 6) Verifica se o papel do user está na lista
        if (! in_array($papel, $allowed, true)) {
            abort(403, 'Permissão insuficiente.');
        }

        return $next($request);
    }
}
