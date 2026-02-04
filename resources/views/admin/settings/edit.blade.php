@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Editar configuração: {{ $key }}</h1>

    <form action="{{ route('admin.settings.update', $key) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-2">
            <label class="block">Valor</label>
            <input name="value" class="border p-2 w-full" value="{{ $value }}" />
        </div>
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
    </form>
</div>
@endsection
