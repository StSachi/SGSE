@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">{{ $venue->nome }}</h1>
    <p class="mb-2">{{ $venue->descricao }}</p>
    <p class="mb-2">Preço base: {{ number_format($venue->preco_base,2) }} Kz</p>

    <div class="mb-4">
        <h3 class="font-semibold">Reservar</h3>
        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-2 mb-2">{{ $errors->first() }}</div>
        @endif
        <form action="{{ route('cliente.reservations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="venue_id" value="{{ $venue->id }}" />
            <div class="mb-2">
                <label class="block text-sm">Data do evento</label>
                <input type="date" name="data_evento" class="border p-2" required />
            </div>
            <button class="px-4 py-2 bg-green-600 text-white rounded">Criar Reserva (PENDENTE_PAGAMENTO)</button>
        </form>
    </div>

    <div class="grid grid-cols-3 gap-2">
        @foreach($venue->images as $img)
            <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-40 object-cover" />
        @endforeach
    </div>
</div>
@endsection
