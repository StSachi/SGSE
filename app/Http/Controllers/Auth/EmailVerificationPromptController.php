<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     *
     * ERS:
     * - Fluxo de segurança auditável
     * - Redirecionamento coerente por role
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            $route = match ($user->role) {
                User::ROLE_ADMIN => 'admin.dashboard',
                User::ROLE_FUNCIONARIO => 'funcionario.dashboard',
                User::ROLE_PROPRIETARIO => 'proprietario.dashboard',
                default => 'cliente.dashboard',
            };

            return redirect()->intended(route($route, absolute: false));
        }

        // Auditoria: utilizador foi solicitado a verificar email
        $this->audit(
            'email_verification_prompt',
            'auth',
            $user->id,
            ['email' => $user->email, 'role' => $user->role],
            $request->ip()
        );

        return view('auth.verify-email');
    }
}
