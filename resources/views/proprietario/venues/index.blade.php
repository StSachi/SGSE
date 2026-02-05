@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Meus Salões</h1>

    <div class="flex items-center justify-between">
        <a href="{{ route('proprietario.venues.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded">Criar Salão</a>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($venues as $v)
            <x-card>
                <div class="flex items-start gap-4">
                    <div class="flex-1">
                        <h2 class="text-lg font-semibold">{{ $v->nome }} <span class="text-sm text-gray-500">({{ $v->estado }})</span></h2>
                        <p class="text-sm text-gray-700 mt-2">{{ Str::limit($v->descricao, 160) }}</p>
                        <div class="mt-3 text-sm text-gray-600">Preço base: {{ number_format($v->preco_base,2) }} Kz</div>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('proprietario.venues.edit', $v) }}" class="text-blue-600">Editar</a>
                        <form action="{{ route('proprietario.venues.destroy', $v) }}" method="POST" class="inline-block ml-2">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Eliminar</button>
                        </form>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
</div>
@endsection
