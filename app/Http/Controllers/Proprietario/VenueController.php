<?php

namespace App\Http\Controllers\Proprietario;

use App\Http\Controllers\Controller;
use App\Http\Requests\VenueRequest;
use App\Models\Owner;
use App\Models\Venue;
use App\Models\VenueImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Controller para CRUD de salões (Proprietário).
 * Regras principais:
 * - Estado inicial do salão é PENDENTE (não visível a clientes).
 * - Máximo 5 imagens por salão (verificadas no upload).
 */
class VenueController extends Controller
{
    // Lista de salões do proprietário autenticado
    public function index(Request $request)
    {
        $user = $request->user();

        // Garante que existe um owner associado ao user
        $owner = Owner::firstOrCreate(['user_id' => $user->id]);

        $venues = Venue::where('owner_id', $owner->id)->get();

        return view('proprietario.venues.index', compact('venues'));
    }

    public function create()
    {
        return view('proprietario.venues.create');
    }

    public function store(VenueRequest $request)
    {
        $user = $request->user();
        $owner = Owner::firstOrCreate(['user_id' => $user->id]);

        $data = $request->only(['nome','descricao','provincia','municipio','endereco','capacidade','preco_base','regras_texto']);
        $data['owner_id'] = $owner->id;
        $data['estado'] = 'PENDENTE';

        $venue = Venue::create($data);

        // Processar imagens (se existirem) — aplicar limite total de 5 imagens por salão
        if ($request->hasFile('images')) {
            $existingCount = $venue->images()->count();
            $uploaded = $request->file('images');
            if ($existingCount + count($uploaded) > 5) {
                // Remove venue criado para manter atomacidade simples e informar o erro
                $venue->delete();
                return back()->withErrors(['images' => __('validation.custom.images.max')])->withInput();
            }

            $ordem = $existingCount + 1;
            foreach ($uploaded as $file) {
                $path = $file->store('venues/'.$venue->id, 'public');
                VenueImage::create(['venue_id' => $venue->id, 'path' => $path, 'ordem' => $ordem]);
                $ordem++;
            }
        }

        return redirect()->route('proprietario.venues.index')->with('status', __('messages.venue_sent_for_review'));
    }

    public function show(Venue $venue)
    {
        // Apenas o proprietário dono do salão deve ver esta rota
        return view('proprietario.venues.show', compact('venue'));
    }

    public function edit(Venue $venue)
    {
        return view('proprietario.venues.edit', compact('venue'));
    }

    public function update(VenueRequest $request, Venue $venue)
    {
        // Só o proprietário do salão pode atualizar — verificações simplificadas
        $venue->update($request->only(['nome','descricao','provincia','municipio','endereco','capacidade','preco_base','regras_texto']));

        // Processar novas imagens, respeitando o máximo de 5
        if ($request->hasFile('images')) {
            $existingCount = $venue->images()->count();
            $uploaded = $request->file('images');
            if ($existingCount + count($uploaded) > 5) {
                return back()->withErrors(['images' => __('validation.custom.images.max')])->withInput();
            }

            $ordem = $existingCount + 1;
            foreach ($uploaded as $file) {
                $path = $file->store('venues/'.$venue->id, 'public');
                VenueImage::create(['venue_id' => $venue->id, 'path' => $path, 'ordem' => $ordem]);
                $ordem++;
            }
        }

        return redirect()->route('proprietario.venues.index')->with('status', __('messages.venue_updated'));
    }

    public function destroy(Venue $venue)
    {
        // Apaga imagens do disco
        foreach ($venue->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $venue->delete();

        return redirect()->route('proprietario.venues.index')->with('status', __('messages.venue_deleted'));
    }
}
