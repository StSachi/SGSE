@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Configurações</h1>

    <div class="mb-4">
        <a href="{{ route('admin.settings.edit', 'percent_sinal') }}" class="text-blue-600">Editar percent_sinal</a>
    </div>
    <div class="mb-4">
        <a href="{{ route('admin.settings.edit', 'dias_min_pagamento_total') }}" class="text-blue-600">Editar dias_min_pagamento_total</a>
    </div>
</div>
@endsection
