@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Relatório — Ocupação por Salão</h1>

    <form class="mb-4">
        <input type="date" name="from" value="{{ $from }}" class="border p-2" />
        <input type="date" name="to" value="{{ $to }}" class="border p-2" />
        <button class="px-3 py-1 bg-blue-600 text-white">Filtrar</button>
        <a href="?from={{ $from }}&to={{ $to }}&format=pdf" class="ml-2 text-sm text-gray-700">{{ __('Download PDF') }}</a>
    </form>

    @foreach($venues as $v)
        <div class="border rounded p-3 mb-3">
            <h2 class="font-semibold">{{ $v->nome }}</h2>
            <div>Reservas no período: {{ $v->reservations->count() }}</div>
        </div>
    @endforeach
</div>
@endsection
