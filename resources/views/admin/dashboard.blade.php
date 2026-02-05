<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Painel do Administrador') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col gap-2">
                        <div class="text-lg font-semibold">{{ __('Atalhos') }}</div>

                        <div class="flex flex-wrap gap-3 mt-2">
                            <a href="{{ route('admin.settings.index') }}"
                               class="inline-flex items-center rounded-lg border px-4 py-2 hover:bg-gray-50">
                                {{ __('Configurações') }}
                            </a>

                            <a href="{{ route('admin.audits.index') }}"
                               class="inline-flex items-center rounded-lg border px-4 py-2 hover:bg-gray-50">
                                {{ __('Auditorias') }}
                            </a>

                            <a href="{{ route('admin.reports.reservas') }}"
                               class="inline-flex items-center rounded-lg border px-4 py-2 hover:bg-gray-50">
                                {{ __('Relatório de Reservas') }}
                            </a>

                            <a href="{{ route('admin.reports.receitas') }}"
                               class="inline-flex items-center rounded-lg border px-4 py-2 hover:bg-gray-50">
                                {{ __('Relatório de Receitas') }}
                            </a>

                            <a href="{{ route('admin.reports.ocupacao') }}"
                               class="inline-flex items-center rounded-lg border px-4 py-2 hover:bg-gray-50">
                                {{ __('Relatório de Ocupação') }}
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 text-sm text-gray-600">
                        {{ __('Este painel é reservado ao ADMIN e serve para gestão do sistema, relatórios e auditoria (ERS).') }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
