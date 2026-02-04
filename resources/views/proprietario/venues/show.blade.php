@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">{{ $venue->nome }}</h1>
    <p class="mb-2">Estado: {{ $venue->estado }}</p>
    <p class="mb-2">{{ $venue->descricao }}</p>

    <div class="grid grid-cols-3 gap-2">
        @foreach($venue->images as $img)
            <div class="border p-1">
                <img src="{{ asset('storage/' . $img->path) }}" alt="" class="w-full h-40 object-cover" />
            </div>
        @endforeach
    </div>
</div>
@endsection
