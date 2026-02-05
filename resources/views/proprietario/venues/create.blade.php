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
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium">Nome</label>
                <input name="nome" class="mt-1 block w-full rounded border-gray-200" value="{{ old('nome') }}" required />
            </div>

            <div>
                <label class="block text-sm font-medium">Descrição</label>
                <textarea name="descricao" class="mt-1 block w-full rounded border-gray-200">{{ old('descricao') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Preço Base (Kz)</label>
                    <input name="preco_base" type="number" step="0.01" class="mt-1 block w-full rounded border-gray-200" value="{{ old('preco_base') }}" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Capacidade</label>
                    <input name="capacidade" type="number" class="mt-1 block w-full rounded border-gray-200" value="{{ old('capacidade') }}" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Imagens (até 5)</label>
                <input type="file" name="images[]" multiple accept="image/*" class="mt-1" />
            </div>

            <div>
                <button class="px-4 py-2 bg-green-600 text-white rounded">Criar</button>
            </div>
        </div>
    </form>
</div>
@endsection
