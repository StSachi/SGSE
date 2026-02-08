<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Adicionar funcionário</h1>
                <p class="text-sm text-slate-600">Cria uma conta com papel <strong>FUNCIONARIO</strong>.</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.funcionarios.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Nome</label>
                        <input name="name" value="{{ old('name') }}"
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:outline-none focus:ring"
                               required>
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:outline-none focus:ring"
                               required>
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Senha</label>
                            <input type="password" name="password"
                                   class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:outline-none focus:ring"
                                   required>
                            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Confirmar senha</label>
                            <input type="password" name="password_confirmation"
                                   class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:outline-none focus:ring"
                                   required>
                        </div>
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="ativo" value="1" {{ old('ativo', 1) ? 'checked' : '' }}
                               class="rounded border-slate-300">
                        <span class="text-sm text-slate-700">Conta ativa</span>
                    </label>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition">
                            Guardar
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
