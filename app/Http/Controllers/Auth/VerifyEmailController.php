<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * ERS:
     * - Ação sensível auditável
     * - Redirecionamento coerente por role
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Se já estiver verificado, apenas redireciona
        if ($user->hasVerifiedEmail()) {
            return $this->redirectByRole($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            // Auditoria da verificação de email
            $this->audit(
                'email_verified',
                'auth',
                $user->id,
                ['email' => $user->email, 'role' => $user->role],
                $request->ip()
            );
        }

        return $this->redirectByRole($user);
    }

    /**
     * Redirecionar para dashboard conforme role
     */
    private function redirectByRole(User $user): RedirectResponse
    {
        $route = match ($user->role) {
            User::ROLE_ADMIN => 'admin.dashboard',
            User::ROLE_FUNCIONARIO => 'funcionario.dashboard',
            User::ROLE_PROPRIETARIO => 'proprietario.dashboard',
            default => 'cliente.dashboard',
        };

        return redirect()->intended(route($route, absolute: false) . '?verified=1');
    }
}
