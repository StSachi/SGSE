<x-app-layout>
  <div class="max-w-xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-semibold mb-6">Solicitar Cadastro de Proprietário</h1>

    @if ($errors->any())
      <div class="mb-4 rounded bg-red-50 p-3 text-red-700">
        <ul class="list-disc pl-5">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('solicitacoes_owner.store') }}" class="space-y-4">
      @csrf

      <div>
        <label class="block text-sm font-medium">Nome</label>
        <input name="nome" value="{{ old('nome') }}" class="w-full rounded border p-2" required>
      </div>

      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded border p-2" required>
      </div>

      <div>
        <label class="block text-sm font-medium">Telefone (opcional)</label>
        <input name="telefone" value="{{ old('telefone') }}" class="w-full rounded border p-2">
      </div>

      <div>
        <label class="block text-sm font-medium">NIF/BI (opcional)</label>
        <input name="nif" value="{{ old('nif') }}" class="w-full rounded border p-2">
      </div>

      <hr class="my-4">

      <div>
        <label class="block text-sm font-medium">Nome do Salão (opcional)</label>
        <input name="nome_salao" value="{{ old('nome_salao') }}" class="w-full rounded border p-2">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium">Província</label>
          <input name="provincia" value="{{ old('provincia') }}" class="w-full rounded border p-2">
        </div>
        <div>
          <label class="block text-sm font-medium">Município</label>
          <input name="municipio" value="{{ old('municipio') }}" class="w-full rounded border p-2">
        </div>
      </div>

      <button class="w-full rounded bg-teal-600 text-white py-2 font-semibold hover:bg-teal-700">
        Enviar Solicitação
      </button>
    </form>
  </div>
</x-app-layout>
