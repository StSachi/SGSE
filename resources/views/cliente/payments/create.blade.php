@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Pagamento — Reserva #{{ $reservation->id }}</h1>

    <p>Data do evento: {{ $reservation->data_evento->format('Y-m-d') }}</p>
    <p>Valor total: {{ number_format($reservation->valor_total,2) }} Kz</p>
    <p>Percentual sinal: {{ $percent }}%</p>

    <form action="{{ route('cliente.payments.store', $reservation) }}" method="POST">
        @csrf
        <div class="mb-2">
            <label class="block">Tipo de pagamento</label>
            <select name="tipo" class="border p-2 w-full">
                <option value="SINAL">Sinal ({{ $percent }}%)</option>
                <option value="TOTAL">Total</option>
            </select>
        </div>
        <div class="mb-2">
            <label class="block">Valor</label>
            <input name="valor" type="number" step="0.01" class="border p-2 w-full" value="{{ $reservation->valor_sinal ?? round($reservation->valor_total * ($percent/100),2) }}" />
        </div>
        <div class="mb-2">
            <label class="block">Método (simulado)</label>
            <input name="metodo" class="border p-2 w-full" value="simulado" />
        </div>
        <div class="mb-2">
            <label class="block">Referência (opcional)</label>
            <input name="referencia" class="border p-2 w-full" />
        </div>
        <button class="px-4 py-2 bg-green-600 text-white rounded">Pagar</button>
    </form>
</div>
@endsection
