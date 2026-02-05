@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Auditoria / Logs</h1>

    <table class="w-full border">
        <thead><tr><th>Quando</th><th>Utilizador</th><th>Ação</th><th>Entidade</th><th>Detalhes</th></tr></thead>
        <tbody>
            @foreach($audits as $a)
                <tr class="border-t">
                    <td>{{ $a->created_at }}</td>
                    <td>{{ $a->user->email ?? 'Sistema' }}</td>
                    <td>{{ $a->acao }}</td>
                    <td>{{ $a->entidade }} ({{ $a->entidade_id }})</td>
                    <td><pre class="text-xs">{{ $a->detalhes }}</pre></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $audits->links() }}</div>
</div>
@endsection
