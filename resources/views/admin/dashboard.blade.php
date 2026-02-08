<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Painel do Administrador') }}
        </h2>
    </x-slot>

    @php
        $totalFuncionarios  = \App\Models\User::where('papel', 'FUNCIONARIO')->count();
        $ativosFuncionarios = \App\Models\User::where('papel', 'FUNCIONARIO')->where('ativo', true)->count();
        $inativosFuncionarios = $totalFuncionarios - $ativosFuncionarios;
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm text-gray-600">{{ __('Funcionários') }}</div>
                        <div class="mt-2 text-2xl font-semibold">{{ $totalFuncionarios }}</div>
                        <div class="mt-2 text-xs text-gray-600">
                            {{ $ativosFuncionarios }} {{ __('ativos') }} • {{ $inativosFuncionarios }} {{ __('inativos') }}
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm text-gray-600">{{ __('Relatórios') }}</div>
                        <div class="mt-2 text-2xl font-semibold">3</div>
                        <div class="mt-2 text-xs text-gray-600">
                            {{ __('Reservas, Receitas, Ocupação') }}
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm text-gray-600">{{ __('Auditoria') }}</div>
                        <div class="mt-2 text-2xl font-semibold">{{ __('Acesso') }}</div>
                        <div class="mt-2 text-xs text-gray-600">
                            {{ __('Registos e ações críticas do sistema') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col gap-2">
                        <div class="text-lg font-semibold">
                            {{ __('Atalhos') }}
                        </div>

                        <div class="flex flex-wrap gap-3 mt-2">
                            <a href="{{ route('admin.settings.index') }}"
                               class="inline-flex items-center rounded-lg border px-4 py-2 hover:bg-gray-50">
                                {{ __('Configurações') }}
                            </a>

                            <a href="{{ route('admin.audits.index') }}"
                               class="inline-flex items-center rounded-lg border px-4 py-2 hover:bg-gray-50">
                                {{ __('Auditorias') }}
                            </a>

                            <a href="{{ route('admin.funcionarios.index') }}"
                               class="inline-flex items-center rounded-lg border px-4 py-2 hover:bg-gray-50">
                                {{ __('Funcionários') }}
                            </a>

                            <a href="{{ route('admin.funcionarios.create') }}"
                               class="inline-flex items-center rounded-lg border px-4 py-2 hover:bg-gray-50">
                                {{ __('Adicionar Funcionário') }}
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
