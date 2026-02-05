<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use Illuminate\Http\Request;

/**
 * Auditorias (ADMIN)
 *
 * ERS:
 * - Apenas ADMIN pode consultar logs/auditorias
 * - Consulta deve suportar filtros
 * - Acesso ao relatório deve ser auditável
 */
class AuditController extends Controller
{
    private function assertAdmin(Request $request): void
    {
        $user = $request->user();

        if (
            ! $user
            || ! method_exists($user, 'isAdmin')
            || ! $user->isAdmin()
            || (property_exists($user, 'ativo') && ! $user->ativo)
        ) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->assertAdmin($request);

        $query = Audit::query()
            ->with('user')
            ->orderByDesc('created_at');

        // filtros (ERS / relatórios)
        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        if ($request->filled('acao')) {
            $query->where('acao', $request->input('acao'));
        }

        if ($request->filled('entidade')) {
            $query->where('entidade', $request->input('entidade'));
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->input('data_inicio'));
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->input('data_fim'));
        }

        $audits = $query->paginate(50)->withQueryString();

        // Auditoria do acesso ao relatório de auditorias
        $this->audit(
            'audit_list_view',
            'audits',
            null,
            [
                'filters' => $request->only(['user_id', 'acao', 'entidade', 'data_inicio', 'data_fim']),
            ],
            $request->ip()
        );

        return view('audits.index', compact('audits'));
    }
}
