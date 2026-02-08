<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <h1 class="text-2xl font-semibold">Pedido para virar Proprietário</h1>
            <p class="text-sm text-slate-600">Formulário mínimo (placeholder).</p>

            <form method="POST" action="{{ route('owner.request.store') }}" class="space-y-3">
                @csrf
                <button class="rounded-xl bg-teal-600 px-4 py-2 text-white font-semibold">
                    Enviar pedido
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
