@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Relatório — Reservas</h1>

    <form class="mb-4">
        <input type="date" name="from" value="{{ $from }}" class="border p-2" />
        <input type="date" name="to" value="{{ $to }}" class="border p-2" />
        <button class="px-3 py-1 bg-blue-600 text-white">Filtrar</button>
        <a href="?from={{ $from }}&to={{ $to }}&format=pdf" class="ml-2 text-sm text-gray-700">Download PDF</a>
    </form>

    <table class="w-full border">
        <thead><tr><th>ID</th><th>Venue</th><th>Cliente</th><th>Data Evento</th><th>Estado</th></tr></thead>
        <tbody>
            @foreach($reservas as $r)
                <tr class="border-t"><td>{{ $r->id }}</td><td>{{ $r->venue->nome ?? '—' }}</td><td>{{ $r->client->name ?? '—' }}</td><td>{{ $r->data_evento }}</td><td>{{ $r->estado }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
