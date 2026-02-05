<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', __('messages.profile_updated'));
    }

    /**
     * Delete the user's account.
     *
     * ERS / Segurança:
     * - Não permitir apagar o último ADMIN ativo
     * - Registar auditoria do auto-delete (quando permitido)
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Bloquear apagar se for o último ADMIN ativo
        if ($user->role === User::ROLE_ADMIN) {
            $adminsAtivos = User::query()
                ->where('role', User::ROLE_ADMIN)
                ->where('ativo', true)
                ->count();

            if ($adminsAtivos <= 1) {
                return Redirect::route('profile.edit')
                    ->withErrors(['userDeletion' => __('messages.cannot_delete_last_admin')]);
            }
        }

        // Auditoria (antes do logout e antes do delete)
        $this->audit(
            'user_self_delete',
            'users',
            $user->id,
            [
                'email' => $user->email,
                'role'  => $user->role,
            ],
            $request->ip(),
            $user->id
        );

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
