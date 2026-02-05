<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (
            ! $user
            || ! method_exists($user, 'isAdmin')
            || ! $user->isAdmin()
            || (isset($user->ativo) && ! $user->ativo)
        ) {
            abort(403);
        }

        $this->audit(
            'view_dashboard',
            'admin',
            $user->id,
            ['email' => $user->email],
            $request->ip()
        );

        return view('admin.dashboard');
    }
}
