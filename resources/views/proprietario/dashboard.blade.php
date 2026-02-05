<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard do Proprietário') }}
            </h2>
            <p class="text-sm text-gray-600">
                {{ __('Estado do seu cadastro e gestão de salões.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Estado do proprietário --}}
            <div class="bg-white shadow-sm rounded-xl">
                <div class="p-6">
                    <div class="text-lg font-semibold text-gray-900 mb-2">{{ __('Estado do cadastro') }}</div>

                    <div class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                        @if($owner->estado === 'APROVADO') bg-emerald-100 text-emerald-800
                        @elseif($owner->estado === 'REJEITADO') bg-rose-100 text-rose-800
                        @else bg-amber-100 text-amber-800 @endif
                    ">
                        {{ $owner->estado }}
                    </div>

                    @if($owner->estado !== 'APROVADO')
                        <div class="mt-3 text-sm text-gray-700">
                            {{ __('O seu cadastro ainda não foi aprovado. Assim que for aprovado, poderá cadastrar e gerir salões.') }}
                        </div>
                    @else
                        <div class="mt-3 text-sm text-gray-700">
                            {{ __('Cadastro aprovado. Já pode gerir os seus salões.') }}
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('proprietario.venues.index') }}"
                               class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                {{ __('Meus salões') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-xs text-gray-500">
                {{ __('Nota: as decisões de aprovação são realizadas pelo Funcionário e registadas em auditoria conforme o ERS.') }}
            </div>

        </div>
    </div>
</x-app-layout>
