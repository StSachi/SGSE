<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SGSE')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
<header class="sticky top-0 z-40 border-b bg-white/80 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="font-semibold tracking-tight">
            SGSE <span class="text-slate-500 font-normal">— Gestão & Marcação de Eventos</span>
        </a>

        <nav class="flex items-center gap-2">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition">
                    Abrir Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-100 transition">
                    Entrar
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition">
                        Criar conta
                    </a>
                @endif
            @endauth
        </nav>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="border-t bg-white mt-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 text-sm text-slate-500 flex flex-col sm:flex-row gap-2 justify-between">
        <div>© {{ date('Y') }} SGSE</div>
        <div class="flex gap-3">
            <a class="hover:text-slate-700" href="{{ route('home') }}">Home</a>
            @auth
                <a class="hover:text-slate-700" href="{{ route('dashboard') }}">Dashboard</a>
            @else
                <a class="hover:text-slate-700" href="{{ route('login') }}">Login</a>
            @endauth
        </div>
    </div>
</footer>
</body>
</html>
