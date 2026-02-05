@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Auditoria / Logs</h1>

    <div class="space-y-3">
        @foreach($audits as $a)
            <x-card>
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-sm text-gray-600">{{ $a->created_at }} — {{ $a->user->email ?? 'Sistema' }}</div>
                        <div class="mt-1"><strong>{{ $a->acao }}</strong> — {{ $a->entidade }} ({{ $a->entidade_id }})</div>
                        <div class="mt-2 text-xs text-gray-700"><pre>{{ $a->detalhes }}</pre></div>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>

    <div class="mt-4">{{ $audits->links() }}</div>
</div>
@endsection
