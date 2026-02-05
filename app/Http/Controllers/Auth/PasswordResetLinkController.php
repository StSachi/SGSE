<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * ERS:
     * - Bloquear utilizador inativo
     * - Auditar pedido de reset (ação sensível)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Segurança ERS: não permitir reset para conta inativa
        $user = User::where('email', $request->input('email'))->first();
        if ($user && isset($user->ativo) && ! $user->ativo) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('messages.account_inactive')]);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Auditoria do pedido de reset (mesmo se email não existir)
        $this->audit(
            'password_reset_link_requested',
            'auth',
            $user?->id,
            ['email' => $request->input('email')],
            $request->ip()
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
