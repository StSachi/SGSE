<?php

namespace App\Http\Middleware;

use App\Models\Owner;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Se não houver user, o middleware auth já trata, mas garantimos.
        if (! $user) {
            return redirect()->route('login');
        }

        $owner = Owner::where('user_id', $user->id)->first();

        // Se ainda não existe owner, considera pendente (regra defensiva)
        if (! $owner) {
            return redirect()
                ->route('proprietario.dashboard')
                ->withErrors(['owner' => 'Proprietário não aprovado.']);
        }

        if ($owner->estado !== Owner::ESTADO_APROVADO) {
            return redirect()
                ->route('proprietario.dashboard')
                ->withErrors(['owner' => 'Proprietário não aprovado.']);
        }

        return $next($request);
    }
}
