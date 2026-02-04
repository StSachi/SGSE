@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Meus Salões</h1>

    <a href="{{ route('proprietario.venues.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Criar Salão</a>

    <div class="mt-6">
        @foreach($venues as $v)
            <div class="border rounded p-4 mb-4">
                <h2 class="font-semibold">{{ $v->nome }} <span class="text-sm text-gray-500">({{ $v->estado }})</span></h2>
                <p class="text-sm text-gray-700">{{ $v->descricao }}</p>
                <div class="mt-2">
                    <a href="{{ route('proprietario.venues.edit', $v) }}" class="text-blue-600">Editar</a>
                    <form action="{{ route('proprietario.venues.destroy', $v) }}" method="POST" class="inline-block ml-2">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">Eliminar</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
