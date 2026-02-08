@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Solicitações de Proprietários</h1>

    {{-- Credenciais (aparecem 1 vez após aprovar) --}}
    @if(session('credenciais_owner'))
        <div class="bg-yellow-100 text-yellow-900 p-3 rounded mb-4">
            <strong>Credenciais do Proprietário (copiar e enviar):</strong><br>
            Email: {{ session('credenciais_owner.email') }}<br>
            Senha: <span class="font-mono">{{ session('credenciais_owner.senha') }}</span>
        </div>
    @endif

    {{-- Mensagens --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-2 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4">
        @forelse($solicitacoes as $s)
            <x-card>
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <strong class="text-lg">{{ $s->nome }}</strong><br>
                        <span class="text-sm text-gray-600">{{ $s->email }}</span><br>

                        <div class="text-sm mt-1">
                            Telefone: <span class="text-gray-700">{{ $s->telefone ?? '—' }}</span>
                        </div>

                        <div class="text-sm">
                            Salão: <span class="text-gray-700">{{ $s->nome_salao ?? '—' }}</span>
                        </div>

                        <div class="text-sm">
                            Local: <span class="text-gray-700">{{ $s->provincia ?? '—' }}{{ $s->municipio ? ' / '.$s->municipio : '' }}</span>
                        </div>

                        <div class="mt-2">
                            @php
                                $badge = match($s->estado) {
                                    'PENDENTE'  => 'bg-yellow-100 text-yellow-800',
                                    'APROVADA'  => 'bg-green-100 text-green-800',
                                    'REJEITADA' => 'bg-red-100 text-red-800',
                                    default    => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <span class="inline-flex px-2 py-1 rounded text-xs font-semibold {{ $badge }}">
                                {{ $s->estado }}
                            </span>
                        </div>

                        @if($s->estado === 'REJEITADA' && !empty($s->motivo_rejeicao))
                            <div class="text-xs text-gray-600 mt-2">
                                Motivo: {{ $s->motivo_rejeicao }}
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        @if($s->estado === 'PENDENTE')
                            <form method="POST" action="{{ route('funcionario.solicitacoes_owners.aprovar', $s->id) }}">
                                @csrf
                                <button class="px-3 py-1 bg-green-600 text-white rounded">
                                    Aprovar
                                </button>
                            </form>

                            <form method="POST" action="{{ route('funcionario.solicitacoes_owners.rejeitar', $s->id) }}">
                                @csrf
                                <input type="hidden" name="motivo_rejeicao" value="Rejeitado pelo funcionário">
                                <button class="px-3 py-1 bg-red-600 text-white rounded">
                                    Rejeitar
                                </button>
                            </form>
                        @else
                            <span class="text-sm text-gray-500">—</span>
                        @endif
                    </div>
                </div>
            </x-card>
        @empty
            <div class="bg-white p-4 rounded shadow-sm text-gray-600">
                Nenhuma solicitação encontrada.
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div class="mt-4">
        {{ $solicitacoes->links() }}
    </div>
</div>
@endsection
