@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Editar Salão</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 mb-4">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('proprietario.venues.update', $venue) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-2">
            <label class="block text-sm">Nome</label>
            <input name="nome" class="w-full border rounded p-2" value="{{ old('nome', $venue->nome) }}" required />
        </div>
        <div class="mb-2">
            <label class="block text-sm">Descrição</label>
            <textarea name="descricao" class="w-full border rounded p-2">{{ old('descricao', $venue->descricao) }}</textarea>
        </div>
        <div class="mb-2">
            <label class="block text-sm">Preço Base (Kz)</label>
            <input name="preco_base" type="number" step="0.01" class="w-full border rounded p-2" value="{{ old('preco_base', $venue->preco_base) }}" />
        </div>

        <div class="mb-2">
            <label class="block text-sm">Imagens atuais</label>
            <div class="grid grid-cols-3 gap-2">
                @foreach($venue->images as $img)
                    <div class="border p-1">
                        <img src="{{ asset('storage/' . $img->path) }}" alt="" class="w-full h-32 object-cover" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-2">
            <label class="block text-sm">Adicionar imagens (até 5 no total)</label>
            <input type="file" name="images[]" multiple accept="image/*" class="w-full" />
        </div>

        <button class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
    </form>
</div>
@endsection
