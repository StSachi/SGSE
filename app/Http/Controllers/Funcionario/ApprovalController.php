<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Venue;
use App\Services\AuditService;
use Illuminate\Http\Request;

/**
 * Controller utilizado por `FUNCIONARIO` para aprovar/rejeitar proprietários e salões.
 * Cada decisão é registada na tabela `audits` via `AuditService`.
 */
class ApprovalController extends Controller
{
    protected $audit;

    public function __construct(AuditService $audit)
    {
        $this->audit = $audit;
    }

    // Lista proprietários pendentes
    public function owners()
    {
        $owners = Owner::where('estado', 'PENDENTE')->with('user')->get();
        return view('funcionario.approvals.owners', compact('owners'));
    }

    // Aprovar proprietário
    public function approveOwner(Request $request, $id)
    {
        $owner = Owner::findOrFail($id);
        $owner->estado = 'APROVADO';
        $owner->save();

        // Auditoria
        $this->audit->log($request->user(), 'approve', 'owners', $owner->id, ['email' => $owner->user->email], $request);

        return redirect()->back()->with('status', 'Proprietário aprovado.');
    }

    // Rejeitar proprietário
    public function rejectOwner(Request $request, $id)
    {
        $owner = Owner::findOrFail($id);
        $owner->estado = 'REJEITADO';
        $owner->save();

        $this->audit->log($request->user(), 'reject', 'owners', $owner->id, ['email' => $owner->user->email, 'motivo' => $request->input('motivo')], $request);

        return redirect()->back()->with('status', 'Proprietário rejeitado.');
    }

    // Lista salões pendentes
    public function venues()
    {
        $venues = Venue::where('estado', 'PENDENTE')->with('owner.user')->get();
        return view('funcionario.approvals.venues', compact('venues'));
    }

    // Aprovar salão
    public function approveVenue(Request $request, $id)
    {
        $venue = Venue::findOrFail($id);
        $venue->estado = 'APROVADO';
        $venue->save();

        $this->audit->log($request->user(), 'approve', 'venues', $venue->id, ['nome' => $venue->nome], $request);

        return redirect()->back()->with('status', 'Salão aprovado.');
    }

    // Rejeitar salão
    public function rejectVenue(Request $request, $id)
    {
        $venue = Venue::findOrFail($id);
        $venue->estado = 'REJEITADO';
        $venue->save();

        $this->audit->log($request->user(), 'reject', 'venues', $venue->id, ['nome' => $venue->nome, 'motivo' => $request->input('motivo')], $request);

        return redirect()->back()->with('status', 'Salão rejeitado.');
    }
}
