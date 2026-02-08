@extends('layouts.guest')

@section('title', 'Solicitação enviada — SGSE')

@section('content')
<section class="bg-gradient-to-b from-white to-slate-50">
  <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-14 text-center">
    <div class="bg-white rounded-2xl border shadow-sm p-8">
      <h1 class="text-2xl font-semibold">Solicitação enviada!</h1>
      <p class="text-slate-600 mt-2">
        Recebemos o teu pedido. Um funcionário irá analisar e dar retorno.
      </p>

      <div class="mt-6 flex justify-center gap-2">
        <a href="{{ route('home') }}"
           class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold hover:bg-slate-100 transition">
          Voltar à Home
        </a>
        <a href="{{ route('login') }}"
           class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-700 transition">
          Entrar
        </a>
      </div>
    </div>
  </div>
</section>
@endsection
