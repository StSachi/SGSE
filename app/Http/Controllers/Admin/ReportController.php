<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Venue;
use Illuminate\Http\Request;
use PDF;

/**
 * Controller para relatórios (tela + PDF).
 */
class ReportController extends Controller
{
    // Reservas por período
    public function reservas(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $query = Reservation::query();
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);

        $reservas = $query->get();

        if ($request->input('format') === 'pdf') {
            $pdf = PDF::loadView('reports.reservas_pdf', compact('reservas','from','to'));
            return $pdf->download("relatorio_reservas_{$from}_{$to}.pdf");
        }

        return view('reports.reservas', compact('reservas','from','to'));
    }

    // Receitas por período
    public function receitas(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $query = Payment::query();
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);

        $payments = $query->get();

        $sinal = $payments->where('tipo','SINAL')->sum('valor');
        $total = $payments->where('tipo','TOTAL')->sum('valor');

        if ($request->input('format') === 'pdf') {
            $pdf = PDF::loadView('reports.receitas_pdf', compact('payments','from','to','sinal','total'));
            return $pdf->download("relatorio_receitas_{$from}_{$to}.pdf");
        }

        return view('reports.receitas', compact('payments','from','to','sinal','total'));
    }

    // Ocupação por salão (por período)
    public function ocupacao(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $venues = Venue::with(['reservations' => function($q) use ($from,$to){
            if ($from) $q->whereDate('data_evento','>=',$from);
            if ($to) $q->whereDate('data_evento','<=',$to);
        }])->get();

        if ($request->input('format') === 'pdf') {
            $pdf = PDF::loadView('reports.ocupacao_pdf', compact('venues','from','to'));
            return $pdf->download("relatorio_ocupacao_{$from}_{$to}.pdf");
        }

        return view('reports.ocupacao', compact('venues','from','to'));
    }
}
