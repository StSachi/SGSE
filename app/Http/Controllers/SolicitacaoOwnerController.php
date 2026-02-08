<?php

namespace App\Http\Controllers;

use App\Models\SolicitacaoOwner;
use Illuminate\Http\Request;

class SolicitacaoOwnerController extends Controller
{
    // ======================
    // BACKOFFICE (FUNCIONARIO)
    // ======================

    /**
     * Listar solicitações de proprietários
     */
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

    /**
     * Aprovar solicitação
     * (NÃO cria Owner, apenas muda estado)
     */
    public function aprovar(SolicitacaoOwner $solicitacao)
    {
        $this->authorizeRoles(['FUNCIONARIO']);

        if ($solicitacao->estado !== SolicitacaoOwner::PENDENTE) {
            return back()->with('error', 'Esta solicitação já foi processada.');
        }

        $solicitacao->update([
            'estado'       => SolicitacaoOwner::APROVADA,
            'revisado_por' => auth()->id(),
            'revisado_em'  => now(),
        ]);

        return back()->with('success', 'Solicitação aprovada.');
    }

    /**
     * Rejeitar solicitação
     */
    public function rejeitar(Request $request, SolicitacaoOwner $solicitacao)
    {
        $this->authorizeRoles(['FUNCIONARIO']);

        if ($solicitacao->estado !== SolicitacaoOwner::PENDENTE) {
            return back()->with('error', 'Esta solicitação já foi processada.');
        }

        $data = $request->validate([
            'motivo_rejeicao' => ['required', 'string', 'max:1000'],
        ]);

        $solicitacao->update([
            'estado'          => SolicitacaoOwner::REJEITADA,
            'revisado_por'    => auth()->id(),
            'revisado_em'     => now(),
            'motivo_rejeicao' => $data['motivo_rejeicao'],
        ]);

        return back()->with('success', 'Solicitação rejeitada.');
    }

    /**
     * Autorização simples por papel
     */
    private function authorizeRoles(array $roles): void
    {
        $user  = auth()->user();
        $papel = $user->papel ?? $user->role ?? null;

        abort_unless(in_array($papel, $roles, true), 403);
    }
}
