@extends('layouts.guest')

@section('title', 'Entrar — SGSE')

@section('content')
<section class="bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid lg:grid-cols-2 gap-10 items-center">

            <div class="space-y-5">
                <div class="inline-flex items-center gap-2 rounded-full border bg-white px-3 py-1 text-xs text-slate-600">
                    <span class="h-2 w-2 rounded-full bg-teal-500"></span>
                    Acesso seguro • Reservas • Pagamentos
                </div>

                <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight">
                    Entrar no SGSE
                </h1>

                <p class="text-slate-600 leading-relaxed">
                    Acesse a tua conta para reservar salões, acompanhar pagamentos e gerir eventos.
                </p>

                <div class="flex flex-wrap gap-2 pt-2">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold hover:bg-slate-100 transition">
                        Voltar à Home
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-700 transition">
                            Criar conta
                        </a>
                    @endif

                    <a href="{{ route('owner.request') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-teal-200 bg-teal-50 px-5 py-3 text-sm font-semibold text-teal-800 hover:bg-teal-100 transition">
                        Sou proprietário • Solicitar cadastro
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl border shadow-sm p-6 sm:p-8">
                <h2 class="text-lg font-semibold">Bem-vindo de volta</h2>
                <p class="text-sm text-slate-600 mt-1">
                    Entre com teu email e senha.
                </p>

                <x-auth-session-status class="mt-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input
                            id="email"
                            class="block mt-1 w-full"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="ex: nome@email.com"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="password" :value="__('Senha')" />

                            @if (Route::has('password.request'))
                                <a class="text-xs font-semibold text-teal-700 hover:text-teal-800"
                                   href="{{ route('password.request') }}">
                                    Esqueci a senha
                                </a>
                            @endif
                        </div>

                        <div class="relative mt-1">
                            <x-text-input
                                id="password"
                                class="block w-full pr-16"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />

                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 px-3 text-sm font-semibold text-slate-600 hover:text-slate-800"
                            >
                                <span id="toggleText">Mostrar</span>
                            </button>
                        </div>

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
                        <input id="remember_me"
                               type="checkbox"
                               class="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                               name="remember">
                        Manter sessão iniciada
                    </label>

                    <div class="pt-2 flex items-center justify-between gap-3">
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-700 transition w-full sm:w-auto">
                            Entrar
                        </button>

                        @if (Route::has('register'))
                            <a class="text-sm font-semibold text-slate-600 hover:text-slate-800"
                               href="{{ route('register') }}">
                                Não tenho conta
                            </a>
                        @endif
                    </div>
                </form>

                <div class="mt-6 text-xs text-slate-500">
                    Ao entrar, você concorda com as regras de uso do SGSE.
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const text = document.getElementById('toggleText');

        if (input.type === 'password') {
            input.type = 'text';
            text.textContent = 'Ocultar';
        } else {
            input.type = 'password';
            text.textContent = 'Mostrar';
        }
    }
</script>
@endsection
