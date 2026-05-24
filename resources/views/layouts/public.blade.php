<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CLMS — @yield('title', 'Public')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface antialiased min-h-screen flex flex-col">

    {{-- Top bar — branding only --}}
    <header class="bg-white border-b border-black/10">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                <x-icon name="monitor" class="w-6 h-6 text-white" />
            </div>
            <div>
                <p class="text-primary font-heading font-semibold text-base leading-tight">CLMS</p>
                <p class="text-xs text-muted">USeP — Computer Laboratory Management</p>
            </div>
        </div>
    </header>

    {{-- Page content --}}
    <main class="flex-1 py-10 px-6">
        <div class="max-w-2xl mx-auto">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-black/10 py-4">
        <div class="max-w-4xl mx-auto px-6 text-center text-xs text-muted">
            University of Southeastern Philippines — Tagum-Mabini
        </div>
    </footer>

</body>
</html>
