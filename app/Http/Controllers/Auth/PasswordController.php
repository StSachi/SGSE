<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Segurança ERS: não permitir alteração se utilizador estiver inativo
        if (isset($user->ativo) && ! $user->ativo) {
            abort(403, 'Conta desativada.');
        }

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->audit(
            'password_update',
            'auth',
            $user->id,
            ['email' => $user->email, 'role' => $user->role],
            $request->ip()
        );

        return back()->with('status', __('messages.password_updated'));
    }
}
