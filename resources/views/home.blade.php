@extends('layouts.guest')

@section('title', 'SGSE — Pesquisar Salões e Eventos')

@section('content')
<section class="bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid lg:grid-cols-2 gap-10 items-center">
            <div class="space-y-5">
                <div class="inline-flex items-center gap-2 rounded-full border bg-white px-3 py-1 text-xs text-slate-600">
                    <span class="h-2 w-2 rounded-full bg-teal-500"></span>
                    Pesquisa • Disponibilidade • Próximos eventos
                </div>

                <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight">
                    Encontre salões, confirme disponibilidade e descubra eventos.
                </h1>

                <p class="text-slate-600 leading-relaxed">
                    Pesquise espaços por localização, capacidade e preço. Ao escolher uma data, o SGSE mostra
                    quais salões estão disponíveis e lista os eventos futuros em destaque.
                </p>

                <div class="pt-2">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-700 transition">
                            Abrir Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-700 transition">
                            Entrar para reservar
                        </a>
                    @endauth
                </div>
            </div>

            <div class="bg-white rounded-2xl border shadow-sm p-6">
                <h2 class="text-lg font-semibold">Pesquisar salões</h2>
                <p class="text-sm text-slate-600 mt-1">
                    Filtre e, se quiser, marque uma data para verificar disponibilidade.
                </p>

                <form method="GET" action="{{ route('home') }}" class="mt-5 space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Nome/descrição</label>
                        <input name="q" value="{{ $filters['q'] ?? '' }}"
                               class="mt-1 w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                               placeholder="Ex: casamento, auditório, salão vip..." />
                    </div>

                    <div class="grid sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Província</label>
                            <select name="provincia"
                                    class="mt-1 w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">Todas</option>
                                @foreach ($provincias as $p)
                                    <option value="{{ $p }}" @selected(($filters['provincia'] ?? '') === $p)>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-600">Cidade</label>
                            <select name="cidade"
                                    class="mt-1 w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">Todas</option>
                                @foreach ($cidades as $c)
                                    <option value="{{ $c }}" @selected(($filters['cidade'] ?? '') === $c)>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Data</label>
                            <input type="date" name="data" value="{{ $filters['data'] ?? '' }}"
                                   class="mt-1 w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500" />
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-600">Capacidade mín.</label>
                            <input type="number" min="0" name="capMin" value="{{ $filters['capMin'] ?? '' }}"
                                   class="mt-1 w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                                   placeholder="Ex: 200" />
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-600">Preço máx. (Kz)</label>
                            <input type="number" min="0" step="0.01" name="precoMax" value="{{ $filters['precoMax'] ?? '' }}"
                                   class="mt-1 w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500"
                                   placeholder="Ex: 150000" />
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="onlyAvailable" value="1" @checked(($filters['onlyAvailable'] ?? false))
                               class="rounded border-slate-300 text-teal-600 focus:ring-teal-500" />
                        Mostrar apenas disponíveis (requer data)
                    </label>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition">
                            Pesquisar
                        </button>

                        <a href="{{ route('home') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-100 transition">
                            Limpar
                        </a>
                    </div>

                    <p class="text-xs text-slate-500">
                        Disponibilidade: considera o salão <strong>ocupado</strong> se existir reserva em <strong>data_evento</strong>
                        com estado <strong>CONFIRMADA</strong> ou <strong>PAGA</strong>.
                        Reservas em <strong>PENDENTE_PAGAMENTO</strong> não bloqueiam a data.
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold">Salões encontrados</h2>
            <p class="text-sm text-slate-600 mt-1">
                Até 12 resultados. Use “Ver detalhes” para ver informações públicas do salão.
            </p>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        @forelse ($venues as $v)
            <div class="bg-white rounded-2xl border shadow-sm p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-lg font-semibold leading-tight">{{ $v->nome ?? '—' }}</div>
                        <div class="text-sm text-slate-600 mt-1">
                            {{ $v->provincia ?? '—' }} • {{ $v->cidade ?? ($v->municipio ?? '—') }}
                        </div>
                    </div>

                    @if (!empty($filters['data']) && !is_null($v->is_available))
                        @if ($v->is_available)
                            <span class="text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 px-3 py-1 border border-emerald-200">
                                Disponível
                            </span>
                        @else
                            <span class="text-xs font-semibold rounded-full bg-rose-50 text-rose-700 px-3 py-1 border border-rose-200">
                                Indisponível
                            </span>
                        @endif
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                    <div class="rounded-xl bg-slate-50 border p-3">
                        <div class="text-xs text-slate-500">Capacidade</div>
                        <div class="font-semibold">{{ $v->capacidade ?? '—' }}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 border p-3">
                        <div class="text-xs text-slate-500">Preço base</div>
                        <div class="font-semibold">
                            @if (!is_null($v->preco_base))
                                {{ number_format((float)$v->preco_base, 2, ',', '.') }} Kz
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>

                <p class="text-sm text-slate-600 mt-3 line-clamp-2">
                    {{ $v->descricao ?? 'Sem descrição pública.' }}
                </p>

                <div class="mt-4 flex gap-2">
                    <a href="{{ route('public.venues.show', $v) }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-100 transition">
                        Ver detalhes
                    </a>

                    @auth
                        <a href="{{ route('cliente.venues.show', $v) }}"
                           class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition">
                            Reservar
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition">
                            Entrar
                        </a>
                    @endauth
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border p-10 text-center text-slate-600">
                Nenhum salão encontrado com estes filtros.
            </div>
        @endforelse
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-14">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold">Próximos eventos</h2>
            <p class="text-sm text-slate-600 mt-1">
                Eventos públicos futuros (até 8).
            </p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4 mt-6">
        @forelse ($events as $e)
            <div class="bg-white rounded-2xl border shadow-sm p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="font-semibold text-lg leading-tight">{{ $e->titulo ?? 'Evento' }}</div>

                        <div class="text-sm text-slate-600 mt-1">
                            {{ \Carbon\Carbon::parse($e->start_date)->format('d/m/Y H:i') }}
                            @if (!empty($e->end_date))
                                — {{ \Carbon\Carbon::parse($e->end_date)->format('d/m/Y H:i') }}
                            @endif
                        </div>

                        <div class="text-sm text-slate-600 mt-1">
                            @if (isset($e->venue) && $e->venue)
                                {{ $e->venue->nome ?? '—' }} • {{ $e->venue->provincia ?? '—' }} • {{ $e->venue->cidade ?? ($e->venue->municipio ?? '—') }}
                            @else
                                Salão não associado
                            @endif
                        </div>
                    </div>

                    <span class="text-xs rounded-full bg-slate-100 px-3 py-1 border">Futuro</span>
                </div>

                <div class="mt-4">
                    <a href="{{ (isset($e->venue) && $e->venue) ? route('public.venues.show', $e->venue) : route('home') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-100 transition">
                        Ver salão
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border p-10 text-center text-slate-600">
                Ainda não há eventos públicos futuros registados.
            </div>
        @endforelse
    </div>
</section>
@endsection
