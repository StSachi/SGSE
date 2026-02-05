<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     *
     * ERS:
     * - Ação sensível deve ser auditável
     * - Redirecionamento coerente por role
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        // Auditoria de confirmação de password
        $user = $request->user();
        $this->audit(
            'password_confirmed',
            'auth',
            $user?->id,
            ['email' => $user?->email, 'role' => $user?->role],
            $request->ip()
        );

        // Redirecionar para dashboard conforme role
        $route = match ($user?->role) {
            User::ROLE_ADMIN => 'admin.dashboard',
            User::ROLE_FUNCIONARIO => 'funcionario.dashboard',
            User::ROLE_PROPRIETARIO => 'proprietario.dashboard',
            default => 'cliente.dashboard',
        };

        return redirect()->intended(route($route, absolute: false));
    }
}
