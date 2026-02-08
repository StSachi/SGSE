<?php

namespace App\Http\Controllers;

use App\Http\Requests\HomeSearchRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index(HomeSearchRequest $request)
    {
        $filters = $request->validated();

        $q         = trim((string)($filters['q'] ?? ''));
        $provincia = trim((string)($filters['provincia'] ?? ''));
        $cidade    = trim((string)($filters['cidade'] ?? ''));
        $capMin    = $filters['capMin'] ?? null;
        $precoMax  = $filters['precoMax'] ?? null;
        $onlyAvail = (bool)($filters['onlyAvailable'] ?? false);

        $date = null;
        if (!empty($filters['data'])) {
            $date = Carbon::parse($filters['data'])->startOfDay();
        }

        $venuesTable = 'venues';

        // Colunas reais do SGSE (venues)
        $hasNome       = Schema::hasColumn($venuesTable, 'nome');
        $hasDescricao  = Schema::hasColumn($venuesTable, 'descricao');
        $hasProvincia  = Schema::hasColumn($venuesTable, 'provincia');
        $hasCidade     = Schema::hasColumn($venuesTable, 'cidade');
        $hasMunicipio  = Schema::hasColumn($venuesTable, 'municipio');

        // Preferir "cidade" se existir, senão "municipio"
        $cidadeCol = $hasCidade ? 'cidade' : ($hasMunicipio ? 'municipio' : null);

        $hasCapacidade = Schema::hasColumn($venuesTable, 'capacidade');
        $hasPrecoBase  = Schema::hasColumn($venuesTable, 'preco_base');
        $hasEstado     = Schema::hasColumn($venuesTable, 'estado');

        $venueModel = \App\Models\Venue::class;

        /** @var Builder $venuesQuery */
        $venuesQuery = $venueModel::query();

        // Select somente do que existe no schema
        $select = ['id', 'owner_id'];
        if ($hasNome) $select[] = 'nome';
        if ($hasDescricao) $select[] = 'descricao';
        if ($hasProvincia) $select[] = 'provincia';
        if ($cidadeCol) $select[] = $cidadeCol;
        if ($hasCapacidade) $select[] = 'capacidade';
        if ($hasPrecoBase) $select[] = 'preco_base';
        if ($hasEstado) $select[] = 'estado';

        $venuesQuery->select($select);

        // ✅ Apenas salões aprovados (públicos)
        if ($hasEstado) {
            $venuesQuery->where('estado', 'APROVADO');
        }

        // Pesquisa por nome/descrição
        if ($q !== '' && ($hasNome || $hasDescricao)) {
            $venuesQuery->where(function (Builder $w) use ($q, $hasNome, $hasDescricao) {
                if ($hasNome) $w->orWhere('nome', 'like', "%{$q}%");
                if ($hasDescricao) $w->orWhere('descricao', 'like', "%{$q}%");
            });
        }

        // Filtros
        if ($provincia !== '' && $hasProvincia) {
            $venuesQuery->where('provincia', 'like', "%{$provincia}%");
        }

        if ($cidade !== '' && $cidadeCol) {
            $venuesQuery->where($cidadeCol, 'like', "%{$cidade}%");
        }

        if (!is_null($capMin) && $hasCapacidade) {
            $venuesQuery->where('capacidade', '>=', (int)$capMin);
        }

        if (!is_null($precoMax) && $hasPrecoBase) {
            $venuesQuery->where('preco_base', '<=', (float)$precoMax);
        }

        // ---------- Disponibilidade (reservations) ----------
        $reservationsTable = 'reservations';
        $reservationModel = class_exists(\App\Models\Reservation::class) ? \App\Models\Reservation::class : null;

        $canCheckAvailability =
            $date &&
            $reservationModel &&
            Schema::hasTable($reservationsTable) &&
            Schema::hasColumn($reservationsTable, 'venue_id');

        $hasReservaEstado     = $canCheckAvailability && Schema::hasColumn($reservationsTable, 'estado');
        $hasReservaDataEvento = $canCheckAvailability && Schema::hasColumn($reservationsTable, 'data_evento');

        // ✅ ERS: só bloqueia a data se a reserva estiver CONFIRMADA ou PAGA
        $blockingStates = ['CONFIRMADA', 'PAGA'];

        // Filtrar só disponíveis (requer data)
        if ($canCheckAvailability && $onlyAvail && $hasReservaDataEvento) {
            $day = $date->toDateString();

            // Só funciona se existir relação reservations() no Venue model
            if (method_exists($venueModel, 'reservations')) {
                $venuesQuery->whereDoesntHave('reservations', function (Builder $r) use ($hasReservaEstado, $day, $blockingStates) {
                    if ($hasReservaEstado) {
                        $r->whereIn('estado', $blockingStates);
                    }
                    $r->whereDate('data_evento', '=', $day);
                });
            }
        }

        // Buscar venues e marcar disponibilidade (badge)
        $venues = $venuesQuery
            ->orderBy($hasNome ? 'nome' : 'id')
            ->limit(12)
            ->get()
            ->map(function ($v) use (
                $canCheckAvailability,
                $reservationModel,
                $hasReservaEstado,
                $hasReservaDataEvento,
                $date,
                $blockingStates
            ) {
                $v->is_available = null;

                if ($canCheckAvailability && $hasReservaDataEvento) {
                    $day = $date->toDateString();

                    $r = $reservationModel::query()
                        ->where('venue_id', $v->id)
                        ->whereDate('data_evento', '=', $day);

                    if ($hasReservaEstado) {
                        $r->whereIn('estado', $blockingStates);
                    }

                    // se existir reserva CONFIRMADA/PAGA nesse dia, então indisponível
                    $v->is_available = !$r->exists();
                }

                return $v;
            });

        // ---------- Events (opcional) ----------
        $events = collect();
        $eventModel = class_exists(\App\Models\Event::class) ? \App\Models\Event::class : null;

        if ($eventModel && Schema::hasTable('events') && Schema::hasColumn('events', 'start_date')) {
            $hasPublico = Schema::hasColumn('events', 'publico');

            $events = $eventModel::query()
                // opcional: melhora performance se existir relation venue()
                ->when(method_exists($eventModel, 'venue'), fn($q) => $q)
                ->when($hasPublico, fn($q) => $q->where('publico', true))
                ->where('start_date', '>=', now())
                ->orderBy('start_date')
                ->limit(8)
                ->get();
        }

        // ---------- Select lists ----------
        $provincias = collect();
        if ($hasProvincia) {
            $pq = $venueModel::query()->whereNotNull('provincia');
            if ($hasEstado) $pq->where('estado', 'APROVADO');
            $provincias = $pq->distinct()->orderBy('provincia')->limit(50)->pluck('provincia');
        }

        $cidades = collect();
        if ($cidadeCol) {
            $cq = $venueModel::query()->whereNotNull($cidadeCol);
            if ($hasEstado) $cq->where('estado', 'APROVADO');
            $cidades = $cq->distinct()->orderBy($cidadeCol)->limit(80)->pluck($cidadeCol);
        }

        return view('home', [
            'venues' => $venues,
            'events' => $events,
            'provincias' => $provincias,
            'cidades' => $cidades,
            'filters' => [
                'q' => $q,
                'provincia' => $provincia,
                'cidade' => $cidade,
                'data' => $filters['data'] ?? '',
                'capMin' => $capMin ?? '',
                'precoMax' => $precoMax ?? '',
                'onlyAvailable' => $onlyAvail,
            ],
            'cidadeCol' => $cidadeCol,
        ]);
    }
}
