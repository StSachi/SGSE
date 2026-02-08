@extends('layouts.guest')

@section('title', 'Solicitar cadastro de proprietário — SGSE')

@section('content')
<section class="bg-gradient-to-b from-white to-slate-50">
  <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-14">

    <div class="mb-8">
      <h1 class="text-3xl font-semibold tracking-tight">Solicitar cadastro de proprietário</h1>
      <p class="text-slate-600 mt-2">
        Preenche os dados abaixo. Um funcionário irá analisar e aprovar o teu acesso.
      </p>
    </div>

    @if ($errors->any())
      <div class="mb-4 rounded-2xl border bg-red-50 p-4 text-red-700">
        <ul class="list-disc pl-5">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <div class="bg-white rounded-2xl border shadow-sm p-6 sm:p-8">
      <form method="POST" action="{{ route('owner.request.store') }}" class="space-y-4">
        @csrf

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-semibold text-slate-700">Nome</label>
            <input name="nome" value="{{ old('nome') }}" class="mt-1 w-full rounded-xl border p-3" required>
          </div>

          <div>
            <label class="text-sm font-semibold text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-xl border p-3" required>
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-semibold text-slate-700">Telefone (opcional)</label>
            <input name="telefone" value="{{ old('telefone') }}" class="mt-1 w-full rounded-xl border p-3" inputmode="numeric" placeholder="9XXXXXXXX">
          </div>

          <div>
            <label class="text-sm font-semibold text-slate-700">NIF/BI (opcional)</label>
            <input name="nif" value="{{ old('nif') }}" class="mt-1 w-full rounded-xl border p-3" inputmode="numeric">
          </div>
        </div>

        <hr class="my-2">

        <div>
          <label class="text-sm font-semibold text-slate-700">Nome do salão</label>
          <input name="nome_salao" value="{{ old('nome_salao') }}" class="mt-1 w-full rounded-xl border p-3" required>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-semibold text-slate-700">Província</label>
            <select id="provincia" name="provincia" class="mt-1 w-full rounded-xl border p-3" required>
              <option value="">Selecione a província</option>
              @foreach ($provincias as $p)
                <option value="{{ $p }}" @selected(old('provincia') === $p)>{{ $p }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="text-sm font-semibold text-slate-700">Município</label>
            <select id="municipio" name="municipio" class="mt-1 w-full rounded-xl border p-3" required disabled>
              <option value="">Selecione primeiro a província</option>
            </select>
          </div>
        </div>

        <div class="pt-2 flex flex-wrap gap-2">
          <button class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-700 transition">
            Enviar solicitação
          </button>

          <a href="{{ route('login') }}"
             class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold hover:bg-slate-100 transition">
            Já tenho conta / Entrar
          </a>
        </div>
      </form>
    </div>

  </div>
</section>

<script>
  const MAP = {
    "Bengo": ["Ambriz","Dande","Dembos","Nambuangongo","Pango Aluquém"],
    "Benguela": ["Balombo","Baía Farta","Benguela","Bocoio","Caimbambo","Catumbela","Chongoroi","Cubal","Ganda","Lobito"],
    "Bié": ["Andulo","Camacupa","Catabola","Chinguar","Chitembo","Cuemba","Cunhinga","Kuito","Nharea"],
    "Cabinda": ["Belize","Buco-Zau","Cabinda","Cacongo"],
    "Cuando Cubango": ["Calai","Cuangar","Cuchi","Cuito Cuanavale","Dirico","Mavinga","Menongue","Nancova","Rivungo"],
    "Cuanza Norte": ["Ambaca","Banga","Bolongongo","Cambambe","Cazengo","Golungo Alto","Gonguembo","Lucala","Ngonguembo","Quiculungo","Samba Caju"],
    "Cuanza Sul": ["Amboim","Cassongue","Cela","Conda","Ebo","Libolo","Mussende","Porto Amboim","Quibala","Quilenda","Seles","Sumbe","Waku Kungo"],
    "Cunene": ["Cahama","Cuanhama","Curoca","Cuvelai","Namacunde","Ombadja"],
    "Huambo": ["Bailundo","Catchiungo","Caála","Ekunha","Huambo","Londuimbali","Longonjo","Mungo","Tchicala Tcholoanga","Tchinjenje","Ucuma"],
    "Huíla": ["Caconda","Cacula","Caluquembe","Chiange","Chibia","Chicomba","Chipindo","Cuvango","Humpata","Jamba","Lubango","Matala","Quilengues","Quipungo"],
    "Luanda": ["Belas","Cacuaco","Cazenga","Icolo e Bengo","Kilamba Kiaxi","Luanda","Talatona","Viana"],
    "Lunda Norte": ["Cambulo","Capenda-Camulemba","Caungula","Chitato","Cuango","Cuilo","Lóvua","Lubalo","Lucapa","Xá-Muteba"],
    "Lunda Sul": ["Cacolo","Dala","Muconda","Saurimo"],
    "Malanje": ["Cacuso","Calandula","Cambundi-Catembo","Cangandala","Caombo","Cuaba Nzoji","Cunda-Dia-Baze","Luquembo","Malanje","Marimba","Massango","Mucari","Quela","Quirima"],
    "Moxico": ["Alto Zambeze","Bundas","Camanongue","Cameia","Léua","Luau","Lucano","Luchazes","Moxico","Muconda"],
    "Namibe": ["Bibala","Camucuio","Moçâmedes","Tômbwa","Virei"],
    "Uíge": ["Ambuila","Bembe","Buengas","Bungo","Damba","Maquela do Zombo","Milunga","Mucaba","Negage","Puri","Quimbele","Quitexe","Sanza Pombo","Songo","Uíge"],
    "Zaire": ["Cuimba","M'banza Kongo","Noqui","Nzeto","Soyo","Tomboco"],
    "Icolo e Bengo": ["Sequele","Bom Jesus","Calumbo","Catete","Cassoneca"],
    "Moxico Leste": ["Cazombo","Lumbala N'guimbo","Luacano"],
    "Cuando": ["Quando","Cuito Cuanavale (zona)"]
  };

  const provinciaEl = document.getElementById('provincia');
  const municipioEl = document.getElementById('municipio');

  function setMunicipios(provincia, selectedValue) {
    const municipios = MAP[provincia] || [];
    municipioEl.innerHTML = '';

    if (!provincia) {
      municipioEl.disabled = true;
      const opt = document.createElement('option');
      opt.value = '';
      opt.textContent = 'Selecione primeiro a província';
      municipioEl.appendChild(opt);
      return;
    }

    municipioEl.disabled = false;

    const first = document.createElement('option');
    first.value = '';
    first.textContent = 'Selecione o município';
    municipioEl.appendChild(first);

    municipios.forEach(m => {
      const opt = document.createElement('option');
      opt.value = m;
      opt.textContent = m;
      if (selectedValue && selectedValue === m) opt.selected = true;
      municipioEl.appendChild(opt);
    });
  }

  provinciaEl.addEventListener('change', () => setMunicipios(provinciaEl.value, null));

  setMunicipios(provinciaEl.value, @json(old('municipio')));
</script>
@endsection
