<?php

namespace App\Http\Controllers;

use App\Models\SolicitacaoOwner;
use App\Models\User;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SolicitacaoOwnerController extends Controller
{
    public function index()
    {
        $this->authorizeRoles(['FUNCIONARIO']);

        $solicitacoes = SolicitacaoOwner::query()
            ->orderByRaw("
                CASE estado
                    WHEN 'PENDENTE'  THEN 1
                    WHEN 'REJEITADA' THEN 2
                    WHEN 'APROVADA'  THEN 3
                    ELSE 99
                END
            ")
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('funcionario.solicitacoes_owner.index', compact('solicitacoes'));
    }

    public function aprovar(SolicitacaoOwner $solicitacao)
    {
        $this->authorizeRoles(['FUNCIONARIO']);

        if ($solicitacao->estado !== SolicitacaoOwner::PENDENTE) {
            return back()->with('error', 'Esta solicitação já foi processada.');
        }

        if (User::where('email', $solicitacao->email)->exists()) {
            return back()->with('error', 'Já existe um utilizador com este email.');
        }

        $senha = Str::random(10);

        DB::transaction(function () use ($solicitacao, $senha) {

            $user = User::create([
                'name'     => $solicitacao->nome,
                'email'    => $solicitacao->email,
                'papel'    => User::ROLE_PROPRIETARIO,
                'ativo'    => true,
                'password' => Hash::make($senha),
            ]);

            Owner::create([
                'user_id'  => $user->id,
                'telefone' => $solicitacao->telefone,
                'nif'      => $solicitacao->nif,
                'estado'   => 'APROVADO',
            ]);

            $solicitacao->update([
                'estado'       => SolicitacaoOwner::APROVADA,
                'revisado_por' => auth()->id(),
                'revisado_em'  => now(),
            ]);
        });

        return back()->with('credenciais_owner', [
            'email' => $solicitacao->email,
            'senha' => $senha,
        ]);
    }

    public function rejeitar(Request $request, SolicitacaoOwner $solicitacao)
    {
        $this->authorizeRoles(['FUNCIONARIO']);

        if ($solicitacao->estado !== SolicitacaoOwner::PENDENTE) {
            return back()->with('error', 'Esta solicitação já foi processada.');
        }

        $request->validate([
            'motivo_rejeicao' => ['required', 'string', 'max:1000'],
        ]);

        $solicitacao->update([
            'estado'          => SolicitacaoOwner::REJEITADA,
            'revisado_por'    => auth()->id(),
            'revisado_em'     => now(),
            'motivo_rejeicao' => $request->motivo_rejeicao,
        ]);

        return back()->with('success', 'Solicitação rejeitada.');
    }

    public function destroy(SolicitacaoOwner $solicitacao)
    {
        $this->authorizeRoles(['FUNCIONARIO']);

        $solicitacao->delete();

        return back()->with('success', 'Solicitação eliminada.');
    }

    private function authorizeRoles(array $roles): void
    {
        $user  = auth()->user();
        $papel = $user->papel ?? $user->role ?? null;

        abort_unless(in_array($papel, $roles, true), 403);
    }
}
