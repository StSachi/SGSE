<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * ERS:
     * - Ação sensível deve ser auditável
     * - Redirecionamento coerente por role
     */
    public function store(Request $request): RedirectResponse
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

        $user->sendEmailVerificationNotification();

        // Auditoria do envio de verificação
        $this->audit(
            'email_verification_sent',
            'auth',
            $user->id,
            ['email' => $user->email, 'role' => $user->role],
            $request->ip()
        );

        return back()->with('status', __('messages.verification_link_sent'));
    }
}
