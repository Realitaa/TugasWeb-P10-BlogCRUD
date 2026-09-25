<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'BlogPost'))</title>

    <script>
        (function () {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || ((!theme || theme === 'system') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- BladewindUI (no-preflight variant for apps compiling own Tailwind) -->
    <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/bladewind/css/bladewind-ui-no-preflight.min.css') }}" rel="stylesheet" />
    @bladewindScripts('dropmenu')
    <script src="{{ asset('vendor/bladewind/js/carousel.js') }}"></script>

    <!-- Application CSS & JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900 dark:bg-dark-900 dark:text-dark-100 min-h-screen antialiased flex flex-col">

    <!-- Top Navigation Header -->
    <header
        class="sticky top-0 z-30 bg-white/95 dark:bg-dark-800/95 backdrop-blur border-b border-gray-200 dark:border-dark-700 shadow-xs">
        <div class="w-full md:max-w-160 mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('posts.index') }}"
                class="text-xl font-bold tracking-tight text-primary-600 dark:text-primary-400 hover:opacity-90 transition">
                BlogPost
            </a>
            <div class="flex items-center gap-2 sm:gap-3">
                <x-bladewind.theme-switcher light_text="Terang" dark_text="Gelap" system_text="Sistem" />
                <a href="https://github.com/Realitaa" target="_blank" rel="noopener noreferrer"
                    class="flex items-center gap-2 hover:opacity-85 transition" title="Profil GitHub Realitaa">
                    <x-bladewind.avatar image="https://github.com/Realitaa.png" size="small" show_ring="true" />
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="w-full md:max-w-160 mx-auto px-0 md:px-4 py-4 md:py-6 flex-1">
        @if (session('success'))
            <div class="px-4 md:px-0 mb-4">
                <x-bladewind.alert type="success">
                    {{ session('success') }}
                </x-bladewind.alert>
            </div>
        @endif

        @if (session('error'))
            <div class="px-4 md:px-0 mb-4">
                <x-bladewind.alert type="error">
                    {{ session('error') }}
                </x-bladewind.alert>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>