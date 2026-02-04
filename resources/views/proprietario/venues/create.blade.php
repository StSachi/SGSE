@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Criar Salão</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 mb-4">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('proprietario.venues.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-2">
            <label class="block text-sm">Nome</label>
            <input name="nome" class="w-full border rounded p-2" value="{{ old('nome') }}" required />
        </div>
        <div class="mb-2">
            <label class="block text-sm">Descrição</label>
            <textarea name="descricao" class="w-full border rounded p-2">{{ old('descricao') }}</textarea>
        </div>
        <div class="mb-2">
            <label class="block text-sm">Preço Base (Kz)</label>
            <input name="preco_base" type="number" step="0.01" class="w-full border rounded p-2" value="{{ old('preco_base') }}" />
        </div>
        <div class="mb-2">
            <label class="block text-sm">Imagens (até 5)</label>
            <input type="file" name="images[]" multiple accept="image/*" class="w-full" />
        </div>
        <button class="px-4 py-2 bg-green-600 text-white rounded">Criar</button>
    </form>
</div>
@endsection
