@extends('layouts.guest')

@section('title', 'SGSE — ' . $venue->nome)

@section('content')
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-6 lg:items-start lg:justify-between">
        <div class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight">{{ $venue->nome }}</h1>
            <p class="text-slate-600">{{ $venue->provincia }} • {{ $venue->cidade }}</p>

            <div class="flex flex-wrap gap-2 pt-2">
                <span class="text-xs rounded-full bg-slate-100 px-3 py-1">Capacidade: {{ $venue->capacidade }}</span>
                <span class="text-xs rounded-full bg-slate-100 px-3 py-1">Preço: {{ $venue->preco }}</span>
            </div>
        </div>

        <div class="flex gap-2">
            @auth
                <a href="{{ route('cliente.venues.show', $venue) }}"
                   class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-700 transition">
                    Reservar agora
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-700 transition">
                    Entrar para reservar
                </a>
            @endauth

            <a href="{{ route('home') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold hover:bg-slate-100 transition">
                Voltar
            </a>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-4 mt-8">
        <div class="lg:col-span-2 bg-white rounded-2xl border shadow-sm p-6">
            <h2 class="text-lg font-semibold">Descrição</h2>
            <p class="text-slate-600 mt-2 leading-relaxed">
                {{ $venue->descricao }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border shadow-sm p-6">
            <h2 class="text-lg font-semibold">Próximos eventos</h2>
            <p class="text-sm text-slate-600 mt-1">Eventos públicos futuros neste salão.</p>

            <div class="mt-4 space-y-3">
                @forelse ($nextEvents as $e)
                    <div class="rounded-xl border p-4 bg-slate-50">
                        <div class="font-semibold">{{ $e->titulo }}</div>
                        <div class="text-sm text-slate-600 mt-1">
                            {{ \Carbon\Carbon::parse($e->start_date)->format('d/m/Y H:i') }}
                            @if ($e->end_date)
                                — {{ \Carbon\Carbon::parse($e->end_date)->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-slate-600">
                        Sem eventos públicos futuros registados.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
