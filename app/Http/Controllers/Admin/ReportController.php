<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Venue;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Relatórios (ADMIN) - tela + PDF
 *
 * ERS:
 * - Apenas ADMIN pode aceder aos relatórios
 * - Geração de PDF e acessos são auditáveis
 */
class ReportController extends Controller
{
    /**
     * Garante:
     * - se sessão expirou -> redireciona para login (não 403)
     * - se conta desativada -> 403
     * - se não for admin -> 403
     *
     * Retorna Response (redirect) ou null.
     */
    private function assertAdmin(Request $request)
    {
        $user = $request->user();

        // Sessão expirada / não autenticado
        if (! $user) {
            return redirect()->route('login');
        }

        // Conta desativada
        if (isset($user->ativo) && ! $user->ativo) {
            abort(403, 'Conta desativada.');
        }

        // Verificação de ADMIN (prioriza isAdmin() se existir)
        $isAdmin = method_exists($user, 'isAdmin')
            ? $user->isAdmin()
            : (($user->papel ?? null) === 'ADMIN');

        if (! $isAdmin) {
            abort(403, 'Permissão insuficiente.');
        }

        return null;
    }

    private function validatePeriod(Request $request): array
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'format' => ['nullable', 'in:html,pdf'],
        ]);

        return [
            'from' => $data['from'] ?? null,
            'to' => $data['to'] ?? null,
            'format' => $data['format'] ?? 'html',
        ];
    }

    private function filenamePeriod(?string $from, ?string $to): string
    {
        $fromSafe = $from ?: 'inicio';
        $toSafe   = $to ?: 'hoje';
        return "{$fromSafe}_{$toSafe}";
    }

    // Reservas por período
    public function reservas(Request $request)
    {
        if ($resp = $this->assertAdmin($request)) {
            return $resp;
        }

        ['from' => $from, 'to' => $to, 'format' => $format] = $this->validatePeriod($request);

        $query = Reservation::query()
            ->with([
                'venue',
                'client', // nome do cliente no relatório
            ])
            ->orderByDesc('created_at');

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $reservas = $query->get();

        // Auditoria
        $this->audit(
            $format === 'pdf' ? 'report_reservas_pdf' : 'report_reservas_view',
            'reports',
            null,
            ['from' => $from, 'to' => $to, 'count' => $reservas->count()],
            $request->ip()
        );

        if ($format === 'pdf') {
            $period = $this->filenamePeriod($from, $to);

            return Pdf::loadView('reports.reservas_pdf', compact('reservas', 'from', 'to'))
                ->download("relatorio_reservas_{$period}.pdf");
        }

        return view('reports.reservas', compact('reservas', 'from', 'to'));
    }

    // Receitas por período
    public function receitas(Request $request)
    {
        if ($resp = $this->assertAdmin($request)) {
            return $resp;
        }

        ['from' => $from, 'to' => $to, 'format' => $format] = $this->validatePeriod($request);

        $query = Payment::query()
            ->with([
                'reservation',
                'reservation.venue',
                'reservation.client',
            ])
            ->orderByDesc('created_at');

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $payments = $query->get();

        $sinal = (float) $payments->where('tipo', Payment::TIPO_SINAL)->sum('valor');
        $total = (float) $payments->where('tipo', Payment::TIPO_TOTAL)->sum('valor');
        $geral = (float) $payments->sum('valor');

        $this->audit(
            $format === 'pdf' ? 'report_receitas_pdf' : 'report_receitas_view',
            'reports',
            null,
            [
                'from' => $from,
                'to' => $to,
                'count' => $payments->count(),
                'sinal' => $sinal,
                'total' => $total,
                'geral' => $geral,
            ],
            $request->ip()
        );

        if ($format === 'pdf') {
            $period = $this->filenamePeriod($from, $to);

            return Pdf::loadView('reports.receitas_pdf', compact('payments', 'from', 'to', 'sinal', 'total', 'geral'))
                ->download("relatorio_receitas_{$period}.pdf");
        }

        return view('reports.receitas', compact('payments', 'from', 'to', 'sinal', 'total', 'geral'));
    }

    // Ocupação por salão (por período) usando data_evento
    public function ocupacao(Request $request)
    {
        if ($resp = $this->assertAdmin($request)) {
            return $resp;
        }

        ['from' => $from, 'to' => $to, 'format' => $format] = $this->validatePeriod($request);

        $venues = Venue::query()
            ->with(['reservations' => function ($q) use ($from, $to) {
                $q->with(['client']);
                $q->orderBy('data_evento');

                if ($from) {
                    $q->whereDate('data_evento', '>=', $from);
                }

                if ($to) {
                    $q->whereDate('data_evento', '<=', $to);
                }
            }])
            ->orderBy('nome')
            ->get();

        $this->audit(
            $format === 'pdf' ? 'report_ocupacao_pdf' : 'report_ocupacao_view',
            'reports',
            null,
            ['from' => $from, 'to' => $to, 'venues' => $venues->count()],
            $request->ip()
        );

        if ($format === 'pdf') {
            $period = $this->filenamePeriod($from, $to);

            return Pdf::loadView('reports.ocupacao_pdf', compact('venues', 'from', 'to'))
                ->download("relatorio_ocupacao_{$period}.pdf");
        }

        return view('reports.ocupacao', compact('venues', 'from', 'to'));
    }
}
