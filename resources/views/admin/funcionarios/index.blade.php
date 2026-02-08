<x-app-layout>
    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Funcionários</h1>
                    <p class="text-sm text-slate-600">
                        Gestão de contas de funcionários (apenas ADMIN).
                    </p>
                </div>

                <a href="{{ route('admin.funcionarios.create') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition">
                    Adicionar funcionário
                </a>
            </div>

            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Nome</th>
                            <th class="px-4 py-3 text-left font-semibold">Email</th>
                            <th class="px-4 py-3 text-left font-semibold">Ativo</th>
                            <th class="px-4 py-3 text-right font-semibold">Ações</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($funcionarios as $f)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-slate-900">{{ $f->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $f->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $f->ativo ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $f->ativo ? 'Sim' : 'Não' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap justify-end gap-3">

                                        <a href="{{ route('admin.funcionarios.show', $f) }}"
                                           class="text-slate-700 hover:text-slate-900 underline">
                                            Ver
                                        </a>

                                        <a href="{{ route('admin.funcionarios.edit', $f) }}"
                                           class="text-teal-700 hover:text-teal-900 underline">
                                            Editar
                                        </a>

                                        {{-- Ativar/Desativar (sem senha) --}}
                                        <form method="POST" action="{{ route('admin.funcionarios.toggle', $f) }}"
                                              onsubmit="return confirm('Confirmas esta alteração?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="confirm" value="1">

                                            <button type="submit"
                                                class="{{ $f->ativo ? 'text-amber-700 hover:text-amber-900' : 'text-green-700 hover:text-green-900' }} underline">
                                                {{ $f->ativo ? 'Desativar' : 'Ativar' }}
                                            </button>
                                        </form>

                                        {{-- Eliminar (apenas confirmação, sem senha) --}}
                                        <form method="POST" action="{{ route('admin.funcionarios.destroy', $f) }}"
                                              class="flex items-center gap-2"
                                              onsubmit="return confirm('Tens a certeza que queres ELIMINAR este funcionário? Esta ação não pode ser desfeita.');">
                                            @csrf
                                            @method('DELETE')

                                            <label class="inline-flex items-center gap-2 text-xs text-slate-600">
                                                <input type="checkbox" name="confirm" value="1" required>
                                                Confirmo
                                            </label>

                                            <button type="submit" class="text-red-700 hover:text-red-900 underline">
                                                Eliminar
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-slate-600" colspan="4">
                                    Nenhum funcionário registado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $funcionarios->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
