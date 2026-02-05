@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-semibold mb-4">{{ __('Dashboard') }} — {{ __('Funcionário') }}</h1>

        <x-card>
            <p class="text-gray-700">{{ __('quick_actions') }}</p>
            <ul class="list-disc pl-5 mt-3 text-gray-700">
                <li><a href="{{ route('funcionario.approvals.owners') }}" class="text-blue-600">{{ __('approve_owners') }}</a></li>
                <li><a href="{{ route('funcionario.approvals.venues') }}" class="text-blue-600">{{ __('approve_venues') }}</a></li>
            </ul>
        </x-card>
    </div>
@endsection
