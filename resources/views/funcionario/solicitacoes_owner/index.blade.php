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
    @endif

    <form method="POST"
          action="{{ route('funcionario.solicitacoes_owners.destroy', $s->id) }}"
          onsubmit="return confirm('Eliminar esta solicitação?');">
        @csrf
        <button class="px-3 py-1 bg-gray-800 text-white rounded">
            Eliminar
        </button>
    </form>
</div>
