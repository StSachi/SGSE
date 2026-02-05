<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * ERS:
     * - Bloquear utilizadores inativos
     * - Auditoria de login
     * - Redirecionar para dashboard conforme role
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Segurança ERS: bloquear utilizadores inativos
        $user = $request->user();
        if ($user && property_exists($user, 'ativo') && ! $user->ativo) {
            Auth::logout();

            return back()
                ->withErrors(['email' => __('messages.account_inactive')])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        // Auditoria de login
        $this->audit(
            'login',
            'auth',
            $user?->id,
            ['email' => $user?->email, 'role' => $user?->role],
            $request->ip()
        );

        // Redirecionamento por role
        $route = match ($user?->role) {
            User::ROLE_ADMIN => 'admin.dashboard',
            User::ROLE_FUNCIONARIO => 'funcionario.dashboard',
            User::ROLE_PROPRIETARIO => 'proprietario.dashboard',
            default => 'cliente.dashboard',
        };

        return redirect()->intended(route($route, absolute: false));
    }

    /**
     * Destroy an authenticated session.
     *
     * ERS:
     * - Auditoria de logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Auditoria de logout (antes de terminar sessão)
        $this->audit(
            'logout',
            'auth',
            $user?->id,
            ['email' => $user?->email, 'role' => $user?->role],
            $request->ip(),
            $user?->id
        );

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
