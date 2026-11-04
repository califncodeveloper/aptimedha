<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - AptiMedha</title>
    <link rel="icon" type="image/png" href="{{ asset('images/aptimedha-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-[#F7F9FC] text-[#243247] font-sans antialiased">

    {{-- Top Navigation --}}
    <header class="sticky top-0 z-50 bg-[#153B70] shadow-lg" x-data="{ mobileMenu: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                {{-- Logo --}}
                <a href="{{ url('/') }}" class="inline-flex items-center">
                    <x-application-logo variant="light" size="sm" />
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ url('/') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition-colors">Home</a>
                    <a href="{{ route('mcq.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition-colors">MCQ Practice</a>
                    <a href="{{ route('blog.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition-colors">Blog</a>
                </nav>

                {{-- User Dropdown --}}
                <div class="hidden md:block" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 text-white/90 hover:text-white transition-colors">
                        <div class="w-8 h-8 rounded-full bg-[#087F82] flex items-center justify-center text-white text-xs font-bold">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="text-sm font-medium">{{ Auth::user()->name ?? 'User' }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </button>
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="absolute right-4 mt-2 w-48 rounded-lg bg-white shadow-lg ring-1 ring-black/5 py-1"
                         x-cloak>
                        <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-sm text-[#243247] hover:bg-gray-50">Dashboard</a>
                        <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-sm text-[#243247] hover:bg-gray-50">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-[#243247] hover:bg-gray-50">Logout</button>
                        </form>
                    </div>
                </div>

                {{-- Mobile Menu Button --}}
                <button @click="mobileMenu = !mobileMenu" class="md:hidden text-white p-2 rounded-lg hover:bg-white/10">
                    <i x-show="!mobileMenu" data-lucide="menu" class="w-6 h-6"></i>
                    <i x-show="mobileMenu" data-lucide="x" class="w-6 h-6" x-cloak></i>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-transition class="md:hidden border-t border-white/10" x-cloak>
            <div class="px-4 py-3 space-y-1">
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10">Home</a>
                <a href="{{ route('mcq.index') }}" class="block px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10">MCQ Practice</a>
                <a href="{{ route('blog.index') }}" class="block px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10">Blog</a>
                <div class="border-t border-white/10 pt-2 mt-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Desktop Sidebar --}}
            <aside class="hidden lg:block w-56 flex-shrink-0">
                <nav class="sticky top-24 space-y-1">
                    @php
                        $userNav = [
                            ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'user.dashboard', 'pattern' => 'user.dashboard*'],
                            ['label' => 'My Profile', 'icon' => 'user', 'route' => 'user.profile', 'pattern' => 'user.profile*'],
                            ['label' => 'Bookmarks', 'icon' => 'bookmark', 'route' => 'user.bookmarks', 'pattern' => 'user.bookmarks*'],
                            ['label' => 'Discussions', 'icon' => 'message-square', 'route' => 'user.discussions', 'pattern' => 'user.discussions*'],
                            ['label' => 'Reported Questions', 'icon' => 'flag', 'route' => 'user.reports', 'pattern' => 'user.reports*'],
                        ];
                    @endphp

                    @foreach($userNav as $item)
                        <a href="{{ route($item['route']) }}"
                           @class([
                               'flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-200',
                               'bg-[#153B70] text-white shadow-sm' => request()->routeIs($item['pattern']),
                               'text-[#243247] hover:bg-gray-100' => !request()->routeIs($item['pattern']),
                           ])>
                            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 flex-shrink-0"></i>
                            <span class="ml-3">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </aside>

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mb-4">
                        <x-alert type="success">{{ session('success') }}</x-alert>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4">
                        <x-alert type="error">{{ session('error') }}</x-alert>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- Mobile Bottom Navigation --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-200 shadow-lg">
        <div class="flex items-center justify-around py-2">
            @php
                $mobileNav = [
                    ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'user.dashboard', 'pattern' => 'user.dashboard*'],
                    ['label' => 'Profile', 'icon' => 'user', 'route' => 'user.profile', 'pattern' => 'user.profile*'],
                    ['label' => 'Bookmarks', 'icon' => 'bookmark', 'route' => 'user.bookmarks', 'pattern' => 'user.bookmarks*'],
                    ['label' => 'Discuss', 'icon' => 'message-square', 'route' => 'user.discussions', 'pattern' => 'user.discussions*'],
                    ['label' => 'Reports', 'icon' => 'flag', 'route' => 'user.reports', 'pattern' => 'user.reports*'],
                ];
            @endphp

            @foreach($mobileNav as $item)
                <a href="{{ route($item['route']) }}"
                   @class([
                       'flex flex-col items-center px-2 py-1 text-xs font-medium transition-colors',
                       'text-[#153B70]' => request()->routeIs($item['pattern']),
                       'text-gray-400' => !request()->routeIs($item['pattern']),
                   ])>
                    <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 mb-0.5"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Initialize Lucide Icons --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    </script>
</body>
</html>
