@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Configurações</h1>

    <div class="grid grid-cols-1 gap-4">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium">Percentual do Sinal</div>
                    <div class="text-xs text-gray-600">Valor atual: {{ $data['percent_sinal'] }}%</div>
                </div>
                <a href="{{ route('admin.settings.edit', 'percent_sinal') }}" class="text-blue-600">Editar</a>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium">Dias mínimos para pagamento total</div>
                    <div class="text-xs text-gray-600">Valor atual: {{ $data['dias_min_pagamento_total'] }} dias</div>
                </div>
                <a href="{{ route('admin.settings.edit', 'dias_min_pagamento_total') }}" class="text-blue-600">Editar</a>
            </div>
        </x-card>
    </div>
</div>
@endsection
