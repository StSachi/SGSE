@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Solicitações de Proprietários</h1>

    <div class="grid grid-cols-1 gap-4">
        @foreach($solicitacoes as $s)
            <x-card>
                <div class="flex justify-between items-center">
                    <div>
                        <strong>{{ $s->nome }}</strong><br>
                        <span class="text-sm text-gray-600">{{ $s->email }}</span><br>
                        <span class="text-sm">Salão: {{ $s->nome_salao }}</span>
                    </div>

                    <div class="flex gap-2">
                        @if($s->estado === 'PENDENTE')
                            <form method="POST"
                                  action="{{ route('funcionario.solicitacoes_owners.aprovar', $s->id) }}">
                                @csrf
                                <button class="px-3 py-1 bg-green-600 text-white rounded">
                                    Aprovar
                                </button>
                            </form>

                            <form method="POST"
                                  action="{{ route('funcionario.solicitacoes_owners.rejeitar', $s->id) }}">
                                @csrf
                                <input type="hidden" name="motivo_rejeicao" value="Rejeitado pelo funcionário">
                                <button class="px-3 py-1 bg-red-600 text-white rounded">
                                    Rejeitar
                                </button>
                            </form>
                        @else
                            <span class="text-sm text-gray-500">{{ $s->estado }}</span>
                        @endif
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
</div>
@endsection

