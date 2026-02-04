@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Aprovações — Proprietários</h1>

    @if(session('status'))
        <div class="bg-green-100 text-green-800 p-2 mb-4">{{ session('status') }}</div>
    @endif

    @foreach($owners as $owner)
        <div class="border rounded p-4 mb-3">
            <div class="flex justify-between items-center">
                <div>
                    <strong>{{ $owner->user->name }}</strong>
                    <div class="text-sm text-gray-600">{{ $owner->user->email }}</div>
                    <div class="text-sm">Telefone: {{ $owner->telefone }}</div>
                </div>
                <div>
                    <form action="{{ route('funcionario.approvals.approveOwner', $owner->id) }}" method="POST" class="inline">
                        @csrf
                        <button class="px-3 py-1 bg-green-600 text-white rounded">Aprovar</button>
                    </form>
                    <form action="{{ route('funcionario.approvals.rejectOwner', $owner->id) }}" method="POST" class="inline ml-2">
                        @csrf
                        <input type="text" name="motivo" placeholder="Motivo (opcional)" class="border px-2" />
                        <button class="px-3 py-1 bg-red-600 text-white rounded">Rejeitar</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
