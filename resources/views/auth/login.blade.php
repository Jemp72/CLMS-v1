<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CLMS — Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface antialiased">

<div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
                <x-icon name="monitor" class="w-12 h-12 text-white" />
            </div>
            <h1 class="text-primary text-3xl mb-2">CLMS Login</h1>
            <p class="text-muted text-sm">Computer Laboratory Management System</p>
            <p class="text-muted text-sm">University of Southeastern Philippines</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-xl shadow-lg border border-black/10 p-8">

            @if ($errors->any())
                <div class="mb-5 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="/login" class="space-y-5" x-data="{ showPassword: false }">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="Enter your email"
                           class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                           required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#2c2c2c] mb-2">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'"
                               name="password"
                               placeholder="Enter your password"
                               class="w-full px-4 py-3 pr-12 border border-black/10 rounded-lg bg-surface text-sm
                                      focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                               required />
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-muted hover:text-[#2c2c2c] transition-colors">
                            <span x-show="!showPassword">
                                <x-icon name="eye" class="w-5 h-5" />
                            </span>
                            <span x-show="showPassword" x-cloak>
                                <x-icon name="eye-off" class="w-5 h-5" />
                            </span>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="w-full px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark
                               transition-colors shadow-sm font-medium text-sm">
                    Login
                </button>

                <div class="mt-3 p-3 rounded-lg bg-surface border border-black/10 text-sm text-[#2c2c2c]">
                    <strong>Test account:</strong> admin@usep.edu.ph / password123
                </div>

                <div class="text-center">
                    <a href="#" class="text-sm text-primary hover:underline">Forgot Password?</a>
                </div>
            </form>
        </div>

        <div class="mt-6 text-center">
            <p class="text-muted text-sm">© 2026 University of Southeastern Philippines</p>
        </div>

    </div>
</div>

</body>
</html>