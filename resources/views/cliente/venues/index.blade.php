@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Pesquisar Salões</h1>

    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Pesquisar por nome..." class="border p-2 flex-1 rounded" />
        <button class="px-3 py-1 bg-blue-600 text-white rounded">Pesquisar</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($venues as $venue)
            <x-card>
                <div class="h-40 bg-gray-100 rounded overflow-hidden mb-3">
                    @if($venue->images->first())
                        <img src="{{ asset('storage/' . $venue->images->first()->path) }}" class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">Sem imagem</div>
                    @endif
                </div>
                <h3 class="font-semibold">{{ $venue->nome }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($venue->descricao, 120) }}</p>
                <div class="mt-2 flex items-center justify-between">
                    <div class="text-sm text-gray-700">{{ number_format($venue->preco_base,2) }} Kz</div>
                    <a href="{{ route('cliente.venues.show', $venue) }}" class="text-blue-600">Ver</a>
                </div>
            </x-card>
        @endforeach
    </div>

    <div class="mt-4">{{ $venues->links() }}</div>
</div>
@endsection
