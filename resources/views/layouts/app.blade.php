<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CLMS — @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface antialiased" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden">

    {{-- Mobile overlay (only when sidebar open) --}}
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="lg:hidden fixed inset-0 bg-black/50 z-40">
    </div>

    {{-- Sidebar — fixed/slide-in on mobile, normal flex on lg+ --}}
    <aside class="fixed lg:relative inset-y-0 left-0 z-50 w-64 bg-white border-r border-black/10 flex flex-col flex-shrink-0
                  transform transition-transform duration-200 ease-in-out lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo + mobile close --}}
        <div class="p-6 border-b border-black/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <x-icon name="monitor" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <p class="text-primary font-heading font-semibold text-base leading-tight">CLMS</p>
                    <p class="text-xs text-muted">USeP</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg hover:bg-surface transition-colors">
                <x-icon name="x" class="w-5 h-5 text-muted" />
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 p-4 overflow-y-auto" @click="sidebarOpen = false">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium
                              {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'text-[#2c2c2c] hover:bg-surface' }}">
                        <x-icon name="layout-dashboard" class="w-5 h-5 flex-shrink-0" />
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('logbook') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium
                              {{ request()->routeIs('logbook') || request()->routeIs('logs.*') ? 'bg-primary text-white' : 'text-[#2c2c2c] hover:bg-surface' }}">
                        <x-icon name="book-open" class="w-5 h-5 flex-shrink-0" />
                        Logbook
                    </a>
                </li>
                <li>
                    <a href="{{ route('enrollments.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium
                              {{ request()->routeIs('enrollments.*') ? 'bg-primary text-white' : 'text-[#2c2c2c] hover:bg-surface' }}">
                        <x-icon name="user" class="w-5 h-5 flex-shrink-0" />
                        Class List
                    </a>
                </li>
                <li>
                    <a href="{{ route('instructors.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium
                              {{ request()->routeIs('instructors.*') ? 'bg-primary text-white' : 'text-[#2c2c2c] hover:bg-surface' }}">
                        <x-icon name="users" class="w-5 h-5 flex-shrink-0" />
                        Instructors
                    </a>
                </li>
                <li>
                    <a href="{{ route('schedule') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium
                              {{ request()->routeIs('schedule') ? 'bg-primary text-white' : 'text-[#2c2c2c] hover:bg-surface' }}">
                        <x-icon name="calendar" class="w-5 h-5 flex-shrink-0" />
                        Schedule &amp; Booking
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventory') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium
                              {{ request()->routeIs('inventory') ? 'bg-primary text-white' : 'text-[#2c2c2c] hover:bg-surface' }}">
                        <x-icon name="package" class="w-5 h-5 flex-shrink-0" />
                        Inventory
                    </a>
                </li>
            </ul>
        </nav>

        {{-- Profile --}}
        <div class="p-4 border-t border-black/10">
            <div class="flex items-center gap-3 px-4 py-3">
                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                    {{ session('user_avatar', 'AD') }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-[#2c2c2c] truncate">{{ session('user_name', 'Administrator') }}</p>
                    <p class="text-xs text-muted truncate">{{ session('user_email', 'admin@usep.edu.ph') }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" title="Logout"
                            class="p-2 rounded-lg hover:bg-surface transition-colors">
                        <x-icon name="log-out" class="w-4 h-4 text-primary" />
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-auto">

        {{-- Mobile top bar (only visible < lg) --}}
        <div class="lg:hidden sticky top-0 z-30 bg-white border-b border-black/10 flex items-center gap-3 px-4 py-3">
            <button @click="sidebarOpen = true" class="p-2 -ml-2 rounded-lg hover:bg-surface transition-colors">
                <x-icon name="menu" class="w-6 h-6 text-primary" />
            </button>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-primary rounded flex items-center justify-center">
                    <x-icon name="monitor" class="w-4 h-4 text-white" />
                </div>
                <p class="text-primary font-heading font-semibold text-sm">CLMS</p>
            </div>
        </div>

        <div class="p-4 sm:p-6 lg:p-8">
            @yield('content')
        </div>
    </main>

</div>

@stack('modals')
<script>
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) window.location.replace("{{ route('login') }}");
    });
</script>
</body>
</html>
