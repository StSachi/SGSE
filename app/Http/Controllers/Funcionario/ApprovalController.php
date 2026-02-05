<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Venue;
use Illuminate\Http\Request;

/**
 * Controller do FUNCIONARIO para aprovar/rejeitar proprietários e salões.
 * ERS:
 * - Apenas FUNCIONARIO pode executar estas ações
 * - Todas as decisões são auditadas
 */
class ApprovalController extends Controller
{
    private function assertFuncionario(Request $request): void
    {
        $user = $request->user();
        if (! $user || ! method_exists($user, 'isFuncionario') || ! $user->isFuncionario()) {
            abort(403);
        }
    }

    // Lista proprietários pendentes
    public function owners(Request $request)
    {
        $this->assertFuncionario($request);

        $owners = Owner::query()
            ->where('estado', Owner::ESTADO_PENDENTE)
            ->with('user')
            ->orderByDesc('id')
            ->get();

        return view('funcionario.approvals.owners', compact('owners'));
    }

    // Aprovar proprietário
    public function approveOwner(Request $request, int $id)
    {
        $this->assertFuncionario($request);

        $owner = Owner::with('user')->findOrFail($id);
        $owner->estado = Owner::ESTADO_APROVADO;
        $owner->save();

        $this->audit(
            'approve_owner',
            'owners',
            $owner->id,
            ['email' => $owner->user?->email],
            $request->ip()
        );

        return redirect()->back()->with('status', __('messages.owner_approved'));
    }

    // Rejeitar proprietário
    public function rejectOwner(Request $request, int $id)
    {
        $this->assertFuncionario($request);

        $request->validate([
            'motivo' => ['nullable', 'string', 'max:255'],
        ]);

        $owner = Owner::with('user')->findOrFail($id);
        $owner->estado = Owner::ESTADO_REJEITADO;
        $owner->save();

        $this->audit(
            'reject_owner',
            'owners',
            $owner->id,
            ['email' => $owner->user?->email, 'motivo' => $request->input('motivo')],
            $request->ip()
        );

        return redirect()->back()->with('status', __('messages.owner_rejected'));
    }

    // Lista salões pendentes
    public function venues(Request $request)
    {
        $this->assertFuncionario($request);

        $venues = Venue::query()
            ->where('estado', Venue::ESTADO_PENDENTE)
            ->with('owner.user')
            ->orderByDesc('id')
            ->get();

        return view('funcionario.approvals.venues', compact('venues'));
    }

    // Aprovar salão
    public function approveVenue(Request $request, int $id)
    {
        $this->assertFuncionario($request);

        $venue = Venue::with('owner.user')->findOrFail($id);
        $venue->estado = Venue::ESTADO_APROVADO;
        $venue->save();

        $this->audit(
            'approve_venue',
            'venues',
            $venue->id,
            ['nome' => $venue->nome, 'owner_email' => $venue->owner?->user?->email],
            $request->ip()
        );

        return redirect()->back()->with('status', __('messages.venue_approved'));
    }

    // Rejeitar salão
    public function rejectVenue(Request $request, int $id)
    {
        $this->assertFuncionario($request);

        $request->validate([
            'motivo' => ['nullable', 'string', 'max:255'],
        ]);

        $venue = Venue::with('owner.user')->findOrFail($id);
        $venue->estado = Venue::ESTADO_REJEITADO;
        $venue->save();

        $this->audit(
            'reject_venue',
            'venues',
            $venue->id,
            [
                'nome' => $venue->nome,
                'owner_email' => $venue->owner?->user?->email,
                'motivo' => $request->input('motivo'),
            ],
            $request->ip()
        );

        return redirect()->back()->with('status', __('messages.venue_rejected'));
    }
}
