<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard do Cliente') }}
            </h2>
            <p class="text-sm text-gray-600">
                {{ __('Acompanhe as suas reservas e efetue pagamentos simulados.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Ações rápidas --}}
            <div class="bg-white shadow-sm rounded-xl">
                <div class="p-6 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">{{ __('Ações rápidas') }}</div>
                        <div class="text-sm text-gray-600">{{ __('Pesquise salões aprovados e faça uma reserva.') }}</div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('cliente.venues.index') }}"
                           class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            {{ __('Ver salões') }}
                        </a>

                        <a href="{{ route('profile.edit') }}"
                           class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                            {{ __('Meu perfil') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Resumo --}}
            @php
                $pendentes   = ($reservas ?? collect())->where('estado', \App\Models\Reservation::ESTADO_PENDENTE_PAGAMENTO)->count();
                $confirmadas = ($reservas ?? collect())->where('estado', \App\Models\Reservation::ESTADO_CONFIRMADA)->count();
                $pagas       = ($reservas ?? collect())->where('estado', \App\Models\Reservation::ESTADO_PAGA)->count();
                $canceladas  = ($reservas ?? collect())->where('estado', \App\Models\Reservation::ESTADO_CANCELADA)->count();
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="text-sm text-gray-600">{{ __('Pendentes') }}</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $pendentes }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="text-sm text-gray-600">{{ __('Confirmadas') }}</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $confirmadas }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="text-sm text-gray-600">{{ __('Pagas') }}</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $pagas }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="text-sm text-gray-600">{{ __('Canceladas') }}</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $canceladas }}</div>
                </div>
            </div>

            {{-- Lista de reservas --}}
            <div class="bg-white shadow-sm rounded-xl">
                <div class="p-6 border-b">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">{{ __('Minhas reservas') }}</div>
                        <div class="text-sm text-gray-600">{{ __('Reservas mais recentes primeiro.') }}</div>
                    </div>
                </div>

                <div class="p-6">
                    @if(empty($reservas) || $reservas->count() === 0)
                        <div class="text-sm text-gray-600">
                            {{ __('Ainda não tens reservas. Vai em "Ver salões" e faz a tua primeira reserva.') }}
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-600 border-b">
                                        <th class="py-2 pr-4">{{ __('Salão') }}</th>
                                        <th class="py-2 pr-4">{{ __('Data do evento') }}</th>
                                        <th class="py-2 pr-4">{{ __('Estado') }}</th>
                                        <th class="py-2 pr-4">{{ __('Total') }}</th>
                                        <th class="py-2 pr-4">{{ __('Sinal') }}</th>
                                        <th class="py-2">{{ __('Ações') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($reservas as $r)
                                        <tr>
                                            <td class="py-3 pr-4 font-medium text-gray-900">
                                                {{ $r->venue->nome ?? __('(Salão removido)') }}
                                            </td>
                                            <td class="py-3 pr-4">
                                                {{ \Carbon\Carbon::parse($r->data_evento)->format('d/m/Y') }}
                                            </td>
                                            <td class="py-3 pr-4">
                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                                    @if($r->estado === \App\Models\Reservation::ESTADO_PENDENTE_PAGAMENTO) bg-amber-100 text-amber-800
                                                    @elseif($r->estado === \App\Models\Reservation::ESTADO_CONFIRMADA) bg-blue-100 text-blue-800
                                                    @elseif($r->estado === \App\Models\Reservation::ESTADO_PAGA) bg-emerald-100 text-emerald-800
                                                    @elseif($r->estado === \App\Models\Reservation::ESTADO_CANCELADA) bg-rose-100 text-rose-800
                                                    @else bg-gray-100 text-gray-800 @endif
                                                ">
                                                    {{ $r->estado }}
                                                </span>
                                            </td>
                                            <td class="py-3 pr-4">
                                                {{ number_format((float)($r->valor_total ?? 0), 2, ',', '.') }}
                                            </td>
                                            <td class="py-3 pr-4">
                                                {{ number_format((float)($r->valor_sinal ?? 0), 2, ',', '.') }}
                                            </td>
                                            <td class="py-3">
                                                <div class="flex flex-wrap gap-2">

                                                    {{-- Ver salão --}}
                                                    @if($r->venue_id)
                                                        <a href="{{ route('cliente.venues.show', $r->venue_id) }}"
                                                           class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-800 hover:bg-slate-50">
                                                            {{ __('Ver salão') }}
                                                        </a>
                                                    @endif

                                                    {{-- Ação de pagamento --}}
                                                    @if(
                                                        $r->estado === \App\Models\Reservation::ESTADO_PENDENTE_PAGAMENTO ||
                                                        $r->estado === \App\Models\Reservation::ESTADO_CONFIRMADA
                                                    )
                                                        <a href="{{ route('cliente.payments.create', $r->id) }}"
                                                           class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">
                                                            {{ __('Pagar') }}
                                                        </a>
                                                    @elseif($r->estado === \App\Models\Reservation::ESTADO_PAGA)
                                                        <span class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                                            {{ __('Já paga') }}
                                                        </span>
                                                    @elseif($r->estado === \App\Models\Reservation::ESTADO_CANCELADA)
                                                        <span class="inline-flex items-center rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">
                                                            {{ __('Cancelada') }}
                                                        </span>
                                                    @endif

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Nota ERS --}}
            <div class="text-xs text-gray-500">
                {{ __('Nota: o sistema mostra apenas salões aprovados ao cliente e impede pagamentos duplicados.') }}
            </div>

        </div>
    </div>
</x-app-layout>
