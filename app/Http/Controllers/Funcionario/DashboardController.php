<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Dashboard do Funcionário
 *
 * ERS:
 * - Apenas utilizadores com role FUNCIONARIO
 * - Acesso auditável
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Segurança ERS: só funcionário
        if (! $user || ! method_exists($user, 'isFuncionario') || ! $user->isFuncionario()) {
            abort(403);
        }

        // Auditoria de acesso ao dashboard
        $this->audit(
            'view_dashboard',
            'funcionario',
            null,
            null,
            $request->ip()
        );

        return view('funcionario.dashboard');
    }
}
