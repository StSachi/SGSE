@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Relatório — Receitas</h1>

    <form class="mb-4">
        <input type="date" name="from" value="{{ $from }}" class="border p-2" />
        <input type="date" name="to" value="{{ $to }}" class="border p-2" />
        <button class="px-3 py-1 bg-blue-600 text-white">Filtrar</button>
        <a href="?from={{ $from }}&to={{ $to }}&format=pdf" class="ml-2 text-sm text-gray-700">Download PDF</a>
    </form>

    <div class="mb-4">Sinal: {{ number_format($sinal,2) }} Kz — Total: {{ number_format($total,2) }} Kz</div>

    <table class="w-full border">
        <thead><tr><th>ID</th><th>Reserva</th><th>Tipo</th><th>Valor</th><th>Data</th></tr></thead>
        <tbody>
            @foreach($payments as $p)
                <tr class="border-t"><td>{{ $p->id }}</td><td>{{ $p->reservation_id }}</td><td>{{ $p->tipo }}</td><td>{{ number_format($p->valor,2) }}</td><td>{{ $p->created_at }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
