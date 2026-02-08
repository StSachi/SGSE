<?php

namespace App\Http\Controllers;

use App\Models\SolicitacaoOwner;
use App\Models\Owner; // <-- já existe no SGSE
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitacaoOwnerController extends Controller
{
    // ===== PÚBLICO =====

    public function create()
    {
        return view('solicitacoes_owner.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'       => ['required','string','max:120'],
            'email'      => ['required','email','max:150'],
            'telefone'   => ['nullable','string','max:30'],
            'nif'        => ['nullable','string','max:50'],
            'nome_salao' => ['nullable','string','max:150'],
            'provincia'  => ['nullable','string','max:100'],
            'municipio'  => ['nullable','string','max:100'],
        ]);

        // 1) Se já existe Owner com este email, não aceita solicitação
        if (Owner::where('email', $data['email'])->exists()) {
            return back()->withErrors([
                'email' => 'Este email já pertence a um proprietário registado. Se não consegue entrar, contacte o suporte.'
            ])->withInput();
        }

        // 2) Bloquear duplicação de solicitação pendente
        if (SolicitacaoOwner::where('email', $data['email'])
            ->where('estado', SolicitacaoOwner::PENDENTE)
            ->exists()) {
            return back()->withErrors([
                'email' => 'Já existe uma solicitação pendente para este email.'
            ])->withInput();
        }

        SolicitacaoOwner::create($data);

        return redirect()->route('solicitacoes_owner.sucesso');
    }

    public function sucesso()
    {
        return view('solicitacoes_owner.sucesso');
    }

    // ===== BACKOFFICE (ADMIN/FUNCIONARIO) =====

    public function index()
    {
        $this->authorizeRoles(['ADMIN','FUNCIONARIO']);

        $solicitacoes = SolicitacaoOwner::query()
            ->orderByRaw("FIELD(estado,'PENDENTE','REJEITADA','APROVADA')")
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.solicitacoes_owner.index', compact('solicitacoes'));
    }

    public function aprovar(SolicitacaoOwner $solicitacao)
    {
        $this->authorizeRoles(['ADMIN','FUNCIONARIO']);

        if ($solicitacao->estado !== SolicitacaoOwner::PENDENTE) {
            return back()->with('error', 'Esta solicitação já foi processada.');
        }

        // segurança extra: evita corrida/duplicação
        if (Owner::where('email', $solicitacao->email)->exists()) {
            return back()->with('error', 'Já existe um Owner com este email.');
        }

        DB::transaction(function () use ($solicitacao) {

            Owner::create([
                'nome'     => $solicitacao->nome,
                'email'    => $solicitacao->email,
                'telefone' => $solicitacao->telefone,
                'nif'      => $solicitacao->nif,
                'ativo'    => true, // ajusta se teu Owner usa outro campo
            ]);

            $solicitacao->update([
                'estado'         => SolicitacaoOwner::APROVADA,
                'revisado_por'   => auth()->id(),
                'revisado_em'    => now(),
                'motivo_rejeicao'=> null,
            ]);
        });

        return back()->with('success', 'Solicitação aprovada e Owner criado.');
    }

    public function rejeitar(Request $request, SolicitacaoOwner $solicitacao)
    {
        $this->authorizeRoles(['ADMIN','FUNCIONARIO']);

        if ($solicitacao->estado !== SolicitacaoOwner::PENDENTE) {
            return back()->with('error', 'Esta solicitação já foi processada.');
        }

        $data = $request->validate([
            'motivo_rejeicao' => ['required','string','max:1000'],
        ]);

        $solicitacao->update([
            'estado'          => SolicitacaoOwner::REJEITADA,
            'revisado_por'    => auth()->id(),
            'revisado_em'     => now(),
            'motivo_rejeicao' => $data['motivo_rejeicao'],
        ]);

        return back()->with('success', 'Solicitação rejeitada.');
    }

    private function authorizeRoles(array $roles): void
    {
        $papel = auth()->user()->papel ?? auth()->user()->role ?? null;
        abort_unless(in_array($papel, $roles, true), 403);
    }
}
