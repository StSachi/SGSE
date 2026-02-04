<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;

/**
 * Pesquisa de salões visíveis para `CLIENTE`.
 * Apenas mostra venues com `estado = APROVADO`.
 */
class VenueSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Venue::where('estado', 'APROVADO');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('nome', 'like', "%{$q}%");
        }

        $venues = $query->paginate(12);

        return view('cliente.venues.index', compact('venues'));
    }

    public function show(Venue $venue)
    {
        // Apenas venues aprovados devem ser mostrados ao cliente
        if ($venue->estado !== 'APROVADO') {
            abort(404);
        }

        return view('cliente.venues.show', compact('venue'));
    }
}
