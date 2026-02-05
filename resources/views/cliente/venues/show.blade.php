@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">{{ $venue->nome }}</h1>
    <p class="mb-2">{{ $venue->descricao }}</p>
    <p class="mb-2">Preço base: {{ number_format($venue->preco_base,2) }} Kz</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="font-semibold mb-2">Reservar</h3>
            @if($errors->any())
                <div class="bg-red-100 text-red-800 p-2 mb-2">{{ $errors->first() }}</div>
            @endif
            <form action="{{ route('cliente.reservations.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="venue_id" value="{{ $venue->id }}" />
                <div>
                    <label class="block text-sm">Data do evento</label>
                    <input type="date" name="data_evento" class="mt-1 block w-full rounded border-gray-200" required />
                </div>
                <div>
                    <button class="px-4 py-2 bg-green-600 text-white rounded">Criar Reserva (PENDENTE_PAGAMENTO)</button>
                </div>
            </form>
        </div>

        <div>
            <div class="grid grid-cols-1 gap-2">
                @foreach($venue->images as $img)
                    <div class="rounded overflow-hidden h-48 bg-gray-100">
                        <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
