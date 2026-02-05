<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;

/**
 * Pesquisa de salões visíveis para CLIENTE
 *
 * ERS:
 * - Apenas CLIENTE ativo pode aceder
 * - Apenas salões APROVADOS são listados
 * - Acesso auditável
 */
class VenueSearchController extends Controller
{
    private function assertClienteAtivo(Request $request): void
    {
        $user = $request->user();

        if (
            ! $user
            || ! method_exists($user, 'isCliente')
            || ! $user->isCliente()
            || (property_exists($user, 'ativo') && ! $user->ativo)
        ) {
            abort(403);
        }
    }

    /**
     * Listagem / pesquisa
     */
    public function index(Request $request)
    {
        $this->assertClienteAtivo($request);

        $query = Venue::query()
            ->where('estado', Venue::ESTADO_APROVADO);

        // Pesquisa por nome
        if ($request->filled('q')) {
            $query->where('nome', 'like', '%' . $request->input('q') . '%');
        }

        // Filtro por província
        if ($request->filled('provincia')) {
            $query->where('provincia', $request->input('provincia'));
        }

        // Filtro por município
        if ($request->filled('municipio')) {
            $query->where('municipio', $request->input('municipio'));
        }

        $venues = $query
            ->orderBy('nome')
            ->paginate(12)
            ->withQueryString();

        // Auditoria de pesquisa
        $this->audit(
            'venue_search',
            'venues',
            null,
            [
                'q' => $request->input('q'),
                'provincia' => $request->input('provincia'),
                'municipio' => $request->input('municipio'),
            ],
            $request->ip()
        );

        return view('cliente.venues.index', compact('venues'));
    }

    /**
     * Detalhe do salão
     */
    public function show(Request $request, Venue $venue)
    {
        $this->assertClienteAtivo($request);

        // Apenas venues aprovados são visíveis
        if ($venue->estado !== Venue::ESTADO_APROVADO) {
            abort(404);
        }

        // Auditoria de visualização
        $this->audit(
            'venue_view',
            'venues',
            $venue->id,
            ['nome' => $venue->nome],
            $request->ip()
        );

        return view('cliente.venues.show', compact('venue'));
    }
}
