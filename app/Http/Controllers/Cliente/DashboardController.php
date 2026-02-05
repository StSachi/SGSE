<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $reservas = Reservation::with('venue')
            ->where('client_user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $this->audit('view_dashboard', 'cliente', $user->id, null, $request->ip());

        return view('cliente.dashboard', compact('reservas'));
    }
}
