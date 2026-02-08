<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-end justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Detalhes do funcionário</h1>
                    <p class="text-sm text-slate-600">Informações da conta.</p>
                </div>

                <a href="{{ route('admin.funcionarios.edit', $funcionario) }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50 transition">
                    Editar
                </a>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-500">Nome</div>
                    <div class="text-slate-900 font-medium">{{ $funcionario->name }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-500">Email</div>
                    <div class="text-slate-900 font-medium">{{ $funcionario->email }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-500">Papel</div>
                    <div class="text-slate-900 font-medium">{{ $funcionario->papel }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-500">Estado</div>
                    <div class="text-slate-900 font-medium">
                        {{ $funcionario->ativo ? 'Ativo' : 'Inativo' }}
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.funcionarios.index') }}"
                   class="text-sm text-slate-700 hover:text-slate-900 underline">
                    Voltar à lista
                </a>

                <form method="POST" action="{{ route('admin.funcionarios.destroy', $funcionario) }}"
                      onsubmit="return confirm('Desativar este funcionário?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-semibold text-red-700 hover:text-red-900 underline">
                        Desativar
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
