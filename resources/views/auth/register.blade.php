@extends('layouts.guest')

@section('title', 'Criar conta — SGSE')

@section('content')
<section class="bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid lg:grid-cols-2 gap-10 items-center">

            {{-- Texto --}}
            <div class="space-y-5">
                <div class="inline-flex items-center gap-2 rounded-full border bg-white px-3 py-1 text-xs text-slate-600">
                    <span class="h-2 w-2 rounded-full bg-teal-500"></span>
                    Conta gratuita • Reservas • Eventos
                </div>

                <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight">
                    Criar conta no SGSE
                </h1>

                <p class="text-slate-600 leading-relaxed">
                    Crie a sua conta para reservar salões, acompanhar eventos e gerir pagamentos de forma segura.
                </p>

                <div class="flex flex-wrap gap-2 pt-2">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold hover:bg-slate-100 transition">
                        Voltar à Home
                    </a>

                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-700 transition">
                        Já tenho conta
                    </a>
                </div>
            </div>

            {{-- Formulário --}}
            <div class="bg-white rounded-2xl border shadow-sm p-6 sm:p-8">
                <h2 class="text-lg font-semibold">Criar nova conta</h2>
                <p class="text-sm text-slate-600 mt-1">
                    Preencha os dados abaixo.
                </p>

                <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
                    @csrf

                    {{-- Nome --}}
                    <div>
                        <x-input-label for="name" :value="__('Nome completo')" />
                        <x-text-input id="name"
                                      class="block mt-1 w-full"
                                      type="text"
                                      name="name"
                                      :value="old('name')"
                                      required
                                      autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email"
                                      class="block mt-1 w-full"
                                      type="email"
                                      name="email"
                                      :value="old('email')"
                                      required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" :value="__('Senha')" />
                        <x-text-input id="password"
                                      class="block mt-1 w-full"
                                      type="password"
                                      name="password"
                                      required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
                        <x-text-input id="password_confirmation"
                                      class="block mt-1 w-full"
                                      type="password"
                                      name="password_confirmation"
                                      required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="pt-2 flex items-center justify-between gap-3">
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-700 transition w-full sm:w-auto">
                            Criar conta
                        </button>

                        <a class="text-sm font-semibold text-slate-600 hover:text-slate-800"
                           href="{{ route('login') }}">
                            Já tenho conta
                        </a>
                    </div>
                </form>

                <div class="mt-6 text-xs text-slate-500">
                    Ao criar uma conta, você concorda com os termos de uso do SGSE.
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
