<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SGFS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-10 bg-gray-100">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-slate-400 rounded-full"
               aria-label="{{ config('app.name', 'SGFS') }}">
                <x-application-logo />
                <span class="sr-only">{{ config('app.name', 'SGFS') }}</span>
            </a>

            <div class="w-full max-w-md mt-6 bg-white shadow-md overflow-hidden rounded-xl">
                <div class="px-6 py-5">
                    {{ $slot }}
                </div>
            </div>

            <div class="mt-6 text-xs text-gray-500">
                © {{ date('Y') }} {{ config('app.name', 'SGFS') }}
            </div>
        </div>
    </body>
</html>
