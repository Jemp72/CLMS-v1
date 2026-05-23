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
<body class="bg-surface antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white border-r border-black/10 flex flex-col flex-shrink-0">

        {{-- Logo --}}
        <div class="p-6 border-b border-black/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <x-icon name="monitor" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <p class="text-primary font-heading font-semibold text-base leading-tight">CLMS</p>
                    <p class="text-xs text-muted">USeP</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 p-4">
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
                              {{ request()->routeIs('logbook') ? 'bg-primary text-white' : 'text-[#2c2c2c] hover:bg-surface' }}">
                        <x-icon name="book-open" class="w-5 h-5 flex-shrink-0" />
                        Logbook &amp; Students
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
        <div class="p-4 border-t border-black/10" x-data="{ open: false }">
            <div class="relative">
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-surface transition-colors text-left">
                    <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                        {{ session('user_avatar', 'AD') }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[#2c2c2c] truncate">{{ session('user_name', 'Administrator') }}</p>
                        <p class="text-xs text-muted truncate">{{ session('user_email', 'admin@usep.edu.ph') }}</p>
                    </div>
                    <span :class="open ? 'rotate-180' : ''" class="transition-transform duration-200 flex-shrink-0">
                        <x-icon name="chevron-down" class="w-4 h-4 text-muted" />
                    </span>
                </button>

                <div x-show="open"
                     x-cloak
                     @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="absolute bottom-full left-0 right-0 mb-2 bg-white border border-black/10 rounded-lg shadow-lg overflow-hidden z-50">
                    <a href="#" class="block px-4 py-2.5 text-sm text-[#2c2c2c] hover:bg-surface transition-colors">Profile Settings</a>
                    <a href="#" class="block px-4 py-2.5 text-sm text-[#2c2c2c] hover:bg-surface transition-colors">Change Password</a>
                    <div class="border-t border-black/10 my-1"></div>
                    <form method="POST" action="{{ route('switch-role') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-primary hover:bg-surface transition-colors">
                            <x-icon name="refresh-cw" class="w-4 h-4" />
                            Switch to {{ session('role') === 'admin' ? 'Instructor' : 'Admin' }} (Demo)
                        </button>
                    </form>
                    <div class="border-t border-black/10 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-primary hover:bg-surface transition-colors">
                            <x-icon name="log-out" class="w-4 h-4" />
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-auto">
        <div class="p-8">
            @yield('content')
        </div>
    </main>

</div>

@stack('modals')
</body>
</html>