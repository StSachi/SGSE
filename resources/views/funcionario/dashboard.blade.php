<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard do Funcionário') }}
            </h2>
            <p class="text-sm text-gray-600">
                {{ __('Gestão e validação de proprietários e salões.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Ações rápidas --}}
            <div class="bg-white shadow-sm rounded-xl">
                <div class="p-6">
                    <div class="text-lg font-semibold text-gray-900 mb-2">
                        {{ __('Ações rápidas') }}
                    </div>

                    <ul class="list-disc pl-5 space-y-2 text-gray-700">
                        <li>
                            <a href="{{ route('funcionario.approvals.owners') }}"
                               class="text-blue-600 hover:underline">
                                {{ __('Aprovar Proprietários') }}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('funcionario.approvals.venues') }}"
                               class="text-blue-600 hover:underline">
                                {{ __('Aprovar Salões') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Nota ERS --}}
            <div class="text-xs text-gray-500">
                {{ __('Nota: todas as ações do funcionário são registadas em auditoria conforme o ERS.') }}
            </div>

        </div>
    </div>
</x-app-layout>
