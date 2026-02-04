@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Pesquisar Salões</h1>

    <form method="GET" class="mb-4">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Pesquisar por nome..." class="border p-2 w-1/2" />
        <button class="px-3 py-1 bg-blue-600 text-white">Pesquisar</button>
    </form>

    <div class="grid grid-cols-3 gap-4">
        @foreach($venues as $venue)
            <div class="border rounded p-3">
                <h2 class="font-semibold">{{ $venue->nome }}</h2>
                <p class="text-sm text-gray-600">{{ Str::limit($venue->descricao, 100) }}</p>
                <p class="text-sm">Preço base: {{ number_format($venue->preco_base,2) }} Kz</p>
                <a href="{{ route('cliente.venues.show', $venue) }}" class="text-blue-600">Ver detalhe</a>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $venues->links() }}</div>
</div>
@endsection
