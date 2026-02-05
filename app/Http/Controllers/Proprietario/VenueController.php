<?php

namespace App\Http\Controllers\Proprietario;

use App\Http\Controllers\Controller;
use App\Http\Requests\VenueRequest;
use App\Models\Owner;
use App\Models\Venue;
use App\Models\VenueImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * CRUD de Salões (Proprietário)
 *
 * ERS:
 * - Apenas PROPRIETARIO pode gerir salões
 * - Proprietário deve estar APROVADO
 * - Venue nasce PENDENTE e precisa de aprovação para ser público
 * - Máximo 5 imagens por salão
 * - Auditoria de ações críticas
 */
class VenueController extends Controller
{
    /**
     * Resolver Owner do utilizador e validar ERS (perfil + aprovação)
     */
    private function resolveApprovedOwner(Request $request): Owner
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'isProprietario') || ! $user->isProprietario()) {
            abort(403);
        }

        $owner = Owner::firstOrCreate(['user_id' => $user->id]);

        // exige aprovação do proprietário
        if (method_exists($owner, 'isAprovado') && ! $owner->isAprovado()) {
            abort(403, 'Proprietário não aprovado');
        }

        return $owner;
    }

    /**
     * Garantir que o venue pertence ao owner
     */
    private function assertVenueBelongsToOwner(Venue $venue, Owner $owner): void
    {
        if ((int) $venue->owner_id !== (int) $owner->id) {
            abort(403);
        }
    }

    // Lista de salões do proprietário autenticado
    public function index(Request $request)
    {
        $owner = $this->resolveApprovedOwner($request);

        $venues = Venue::query()
            ->where('owner_id', $owner->id)
            ->orderByDesc('id')
            ->get();

        return view('proprietario.venues.index', compact('venues'));
    }

    public function create(Request $request)
    {
        $this->resolveApprovedOwner($request);

        return view('proprietario.venues.create');
    }

    public function store(VenueRequest $request)
    {
        $owner = $this->resolveApprovedOwner($request);

        $uploaded = $request->file('images', []);
        if (count($uploaded) > 5) {
            return back()->withErrors(['images' => __('validation.custom.images.max')])->withInput();
        }

        $venue = DB::transaction(function () use ($request, $owner, $uploaded) {

            $data = $request->only([
                'nome', 'descricao', 'provincia', 'municipio', 'endereco',
                'capacidade', 'preco_base', 'regras_texto'
            ]);

            $data['owner_id'] = $owner->id;
            $data['estado'] = Venue::ESTADO_PENDENTE;

            $venue = Venue::create($data);

            // Upload das imagens (max 5)
            $ordem = 1;
            foreach ($uploaded as $file) {
                $path = $file->store('venues/' . $venue->id, 'public');
                VenueImage::create([
                    'venue_id' => $venue->id,
                    'path' => $path,
                    'ordem' => $ordem++,
                ]);
            }

            return $venue;
        });

        $this->audit(
            'venue_create',
            'venues',
            $venue->id,
            ['owner_id' => $owner->id, 'estado' => $venue->estado],
            $request->ip()
        );

        return redirect()
            ->route('proprietario.venues.index')
            ->with('status', __('messages.venue_sent_for_review'));
    }

    public function show(Request $request, Venue $venue)
    {
        $owner = $this->resolveApprovedOwner($request);
        $this->assertVenueBelongsToOwner($venue, $owner);

        return view('proprietario.venues.show', compact('venue'));
    }

    public function edit(Request $request, Venue $venue)
    {
        $owner = $this->resolveApprovedOwner($request);
        $this->assertVenueBelongsToOwner($venue, $owner);

        return view('proprietario.venues.edit', compact('venue'));
    }

    public function update(VenueRequest $request, Venue $venue)
    {
        $owner = $this->resolveApprovedOwner($request);
        $this->assertVenueBelongsToOwner($venue, $owner);

        $uploaded = $request->file('images', []);
        $existingCount = $venue->images()->count();

        if ($existingCount + count($uploaded) > 5) {
            return back()->withErrors(['images' => __('validation.custom.images.max')])->withInput();
        }

        DB::transaction(function () use ($request, $venue, $uploaded, $existingCount) {

            $venue->update($request->only([
                'nome', 'descricao', 'provincia', 'municipio', 'endereco',
                'capacidade', 'preco_base', 'regras_texto'
            ]));

            // Adicionar novas imagens respeitando o máximo
            $ordem = $existingCount + 1;
            foreach ($uploaded as $file) {
                $path = $file->store('venues/' . $venue->id, 'public');
                VenueImage::create([
                    'venue_id' => $venue->id,
                    'path' => $path,
                    'ordem' => $ordem++,
                ]);
            }
        });

        $this->audit(
            'venue_update',
            'venues',
            $venue->id,
            ['owner_id' => $owner->id],
            $request->ip()
        );

        return redirect()
            ->route('proprietario.venues.index')
            ->with('status', __('messages.venue_updated'));
    }

    public function destroy(Request $request, Venue $venue)
    {
        $owner = $this->resolveApprovedOwner($request);
        $this->assertVenueBelongsToOwner($venue, $owner);

        DB::transaction(function () use ($venue) {
            // Apaga imagens do disco e da BD
            foreach ($venue->images as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }

            $venue->delete();
        });

        $this->audit(
            'venue_delete',
            'venues',
            $venue->id,
            ['owner_id' => $owner->id],
            $request->ip()
        );

        return redirect()
            ->route('proprietario.venues.index')
            ->with('status', __('messages.venue_deleted'));
    }
}
