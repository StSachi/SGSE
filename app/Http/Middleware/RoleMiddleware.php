<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                abort(401, 'Não autenticado.');
            }

            return redirect()->route('login');
        }

        if (isset($user->ativo) && ! $user->ativo) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('home')
                ->with('error', 'Conta desativada.');
        }

        $papel = $user->papel ?? $user->role ?? null;

        if (! $papel) {
            if ($request->expectsJson()) {
                abort(403, 'Conta sem perfil definido.');
            }

            return redirect()->route('home')
                ->with('error', 'A tua conta está sem perfil definido. Contacte o suporte.');
        }

        $allowed = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $allowed[] = $part;
                }
            }
        }

        if (! in_array($papel, $allowed, true)) {
            if ($request->expectsJson()) {
                abort(403, 'Permissão insuficiente.');
            }

            return redirect()->route('dashboard')
                ->with('error', 'Permissão insuficiente para aceder a esta área.');
        }

        return $next($request);
    }
}
