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
     * - Cria User (PROPRIETARIO) + Owner
     * - Marca a solicitação como APROVADA
     * - Devolve credenciais (senha) via session para o funcionário copiar/enviar
     */
    public function aprovar(SolicitacaoOwner $solicitacao)
    {
        $this->authorizeRoles(['FUNCIONARIO']);

        if ($solicitacao->estado !== SolicitacaoOwner::PENDENTE) {
            return back()->with('error', 'Esta solicitação já foi processada.');
        }

        // Se já existir user com este email, não cria outra conta
        if (User::where('email', $solicitacao->email)->exists()) {
            return back()->with('error', 'Já existe um utilizador com este email. Use o login ou recupere a senha.');
        }

        // Gera senha inicial
        $senhaGerada = Str::random(10);

        DB::transaction(function () use ($solicitacao, $senhaGerada) {

            // 1) cria o utilizador PROPRIETARIO
            $user = User::create([
                'name'     => $solicitacao->nome,
                'email'    => $solicitacao->email,
                'papel'    => User::ROLE_PROPRIETARIO, // usa o campo oficial
                'ativo'    => true,
                'password' => Hash::make($senhaGerada),
            ]);

            // 2) cria o Owner ligado ao user
            // Ajusta os campos conforme o teu model/migration de Owner
            Owner::create([
                'user_id'  => $user->id,
                'telefone' => $solicitacao->telefone,
                'nif'      => $solicitacao->nif,
                // se tiveres campos de estado/ativo no Owner, podes adicionar aqui
                // 'estado' => Owner::ESTADO_APROVADO,
                // 'ativo'  => true,
            ]);

            // 3) marca solicitação como aprovada e liga ao user (se existir coluna user_id)
            $update = [
                'estado'       => SolicitacaoOwner::APROVADA,
                'revisado_por' => auth()->id(),
                'revisado_em'  => now(),
                'motivo_rejeicao' => null,
            ];

            // só seta user_id se a coluna existir na tabela (evita crash)
            if (array_key_exists('user_id', $solicitacao->getAttributes()) || $solicitacao->getConnection()
                ->getSchemaBuilder()->hasColumn($solicitacao->getTable(), 'user_id')) {
                $update['user_id'] = $user->id;
            }

            $solicitacao->update($update);
        });

        // mostrar credenciais uma vez para o funcionário copiar/enviar
        return back()
            ->with('success', 'Solicitação aprovada. Conta do proprietário criada.')
            ->with('credenciais_owner', [
                'email' => $solicitacao->email,
                'senha' => $senhaGerada,
            ]);
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
