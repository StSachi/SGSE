<?php

namespace App\Http\Controllers\Proprietario;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $owner = Owner::firstOrCreate(
            ['user_id' => $user->id],
            ['estado' => Owner::ESTADO_PENDENTE]
        );

        $this->audit('view_dashboard', 'proprietario', $user->id, ['estado' => $owner->estado], $request->ip());

        return view('proprietario.dashboard', compact('owner'));
    }
}
