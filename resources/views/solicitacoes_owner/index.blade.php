<x-app-layout>
  <div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-semibold mb-6">Solicitações de Owner</h1>

    @if(session('success'))
      <div class="mb-4 rounded bg-green-50 p-3 text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="mb-4 rounded bg-red-50 p-3 text-red-700">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto rounded border">
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="p-3 text-left">Nome</th>
            <th class="p-3 text-left">Email</th>
            <th class="p-3 text-left">Estado</th>
            <th class="p-3 text-left">Criado</th>
            <th class="p-3 text-left">Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach($solicitacoes as $s)
            <tr class="border-t">
              <td class="p-3">{{ $s->nome }}</td>
              <td class="p-3">{{ $s->email }}</td>
              <td class="p-3"><span class="px-2 py-1 rounded border">{{ $s->estado }}</span></td>
              <td class="p-3">{{ optional($s->created_at)->format('d/m/Y H:i') }}</td>
              <td class="p-3">
                @if($s->estado === 'PENDENTE')
                  <form class="inline" method="POST" action="{{ route('admin.solicitacoes_owner.aprovar', $s) }}">
                    @csrf
                    <button class="rounded bg-teal-600 text-white px-3 py-1">Aprovar</button>
                  </form>

                  <form class="inline" method="POST" action="{{ route('admin.solicitacoes_owner.rejeitar', $s) }}">
                    @csrf
                    <input name="motivo_rejeicao" placeholder="Motivo..." class="rounded border p-1 mx-1" required>
                    <button class="rounded bg-red-600 text-white px-3 py-1">Rejeitar</button>
                  </form>
                @else
                  <span class="text-slate-500">—</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $solicitacoes->links() }}
    </div>
  </div>
</x-app-layout>
