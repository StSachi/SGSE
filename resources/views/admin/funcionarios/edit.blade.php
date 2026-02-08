<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Editar funcionário</h1>
                <p class="text-sm text-slate-600">
                    Deixa a senha em branco se não quiseres alterar.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.funcionarios.update', $funcionario) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Nome</label>
                        <input name="name" value="{{ old('name', $funcionario->name) }}"
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:outline-none focus:ring"
                               required>
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $funcionario->email) }}"
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:outline-none focus:ring"
                               required>
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Nova senha (opcional)</label>
                            <input type="password" name="password"
                                   class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:outline-none focus:ring">
                            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Confirmar nova senha</label>
                            <input type="password" name="password_confirmation"
                                   class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:outline-none focus:ring">
                        </div>
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="ativo" value="1"
                               {{ old('ativo', $funcionario->ativo) ? 'checked' : '' }}
                               class="rounded border-slate-300">
                        <span class="text-sm text-slate-700">Conta ativa</span>
                    </label>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition">
                            Atualizar
                        </button>

                        <a href="{{ route('admin.funcionarios.index') }}"
                           class="text-sm text-slate-700 hover:text-slate-900 underline">
                            Voltar
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
