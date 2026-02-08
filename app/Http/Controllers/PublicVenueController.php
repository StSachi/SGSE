<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;

class PublicVenueController extends Controller
{
    public function show(\App\Models\Venue $venue)
    {
        $venuesTable = 'venues';
        $hasAtivo = Schema::hasColumn($venuesTable, 'ativo');
        $hasAprovado = Schema::hasColumn($venuesTable, 'aprovado');

        if (($hasAtivo && !$venue->ativo) || ($hasAprovado && !$venue->aprovado)) {
            abort(404);
        }

        $nextEvents = collect();

        if (class_exists(\App\Models\Event::class) && Schema::hasTable('events')) {
            $eventModel = \App\Models\Event::class;

            $q = $eventModel::query();

            if (Schema::hasColumn('events', 'venue_id')) {
                $q->where('venue_id', $venue->id);
            }

            if (Schema::hasColumn('events', 'publico')) {
                $q->where('publico', true);
            }

            if (Schema::hasColumn('events', 'start_date')) {
                $q->where('start_date', '>=', now())->orderBy('start_date');
            }

            $nextEvents = $q->limit(6)->get();
        }

        return view('public.venues.show', [
            'venue' => $venue,
            'nextEvents' => $nextEvents,
        ]);
    }
}
