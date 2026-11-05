<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin' }} - AptiMedha Admin</title>
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
<body class="min-h-screen bg-[#F7F9FC] text-[#243247] font-sans antialiased"
      :class="{ 'overflow-hidden': mobileSidebar }"
      x-data="{ sidebarOpen: true, mobileSidebar: false }">

    {{-- Mobile Sidebar Backdrop --}}
    <div x-show="mobileSidebar"
         @click="mobileSidebar = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"
         x-cloak></div>

    {{-- Sidebar --}}
    <aside :class="[sidebarOpen ? 'lg:w-64' : 'lg:w-16', mobileSidebar ? 'w-64 translate-x-0' : 'w-64 -translate-x-full lg:translate-x-0']"
           class="fixed inset-y-0 left-0 z-50 flex flex-col bg-[#153B70] transition-all duration-300 ease-in-out"
           x-cloak>

        {{-- Sidebar Header --}}
        <div class="flex h-16 items-center justify-between px-4 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2" x-show="sidebarOpen || mobileSidebar">
                <x-application-logo variant="light" size="sm" />
                <span class="text-xs font-medium text-white/70 bg-white/10 px-2 py-0.5 rounded">Admin</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" x-show="!sidebarOpen && !mobileSidebar" class="mx-auto" x-cloak>
                <span class="text-lg font-bold text-white">A</span>
            </a>
            {{-- Close button on mobile --}}
            <button @click="mobileSidebar = false" class="lg:hidden text-white/70 hover:text-white p-2 min-w-[40px] min-h-[40px] flex items-center justify-center rounded-lg hover:bg-white/10" aria-label="Close sidebar">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 scrollbar-thin">
            @php
                $navItems = [
                    ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard*'],
                    ['label' => 'MCQ Management', 'icon' => 'help-circle', 'route' => 'admin.mcqs.index', 'pattern' => 'admin.mcqs*'],
                    ['label' => 'Categories', 'icon' => 'folder-tree', 'route' => 'admin.categories.index', 'pattern' => 'admin.categories*'],
                    ['label' => 'Subjects', 'icon' => 'book-open', 'route' => 'admin.subjects.index', 'pattern' => 'admin.subjects*'],
                    ['label' => 'Topics', 'icon' => 'tag', 'route' => 'admin.topics.index', 'pattern' => 'admin.topics*'],
                    ['label' => 'Import MCQs', 'icon' => 'upload', 'route' => 'admin.mcqs.import.form', 'pattern' => 'admin.mcqs.import*'],
                    ['label' => 'Blog Management', 'icon' => 'file-text', 'route' => 'admin.blogs.index', 'pattern' => 'admin.blog*'],
                    ['label' => 'Placement Guides', 'icon' => 'briefcase', 'route' => 'admin.placement.index', 'pattern' => 'admin.placement*'],
                    ['label' => 'Current Affairs', 'icon' => 'calendar', 'route' => 'admin.current-affairs.index', 'pattern' => 'admin.current-affairs*'],
                    ['label' => 'User Management', 'icon' => 'users', 'route' => 'admin.users.index', 'pattern' => 'admin.users*'],
                    ['label' => 'Discussions', 'icon' => 'message-square', 'route' => 'admin.discussions.index', 'pattern' => 'admin.discussions*'],
                    ['label' => 'Reports', 'icon' => 'flag', 'route' => 'admin.reports.index', 'pattern' => 'admin.reports*'],
                    ['label' => 'Contact Messages', 'icon' => 'mail', 'route' => 'admin.contacts.index', 'pattern' => 'admin.contacts*'],
                    ['label' => 'Pages', 'icon' => 'file', 'route' => 'admin.pages.index', 'pattern' => 'admin.pages*'],
                    ['label' => 'Settings', 'icon' => 'settings', 'route' => 'admin.settings.index', 'pattern' => 'admin.settings*'],
                    ['label' => 'Cloudinary Test', 'icon' => 'cloud', 'route' => 'admin.cloudinary-test.index', 'pattern' => 'admin.cloudinary-test*'],
                ];
            @endphp

            @foreach($navItems as $item)
                <a @click="mobileSidebar = false"
                   href="{{ route($item['route']) }}"
                   @class([
                       'flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-200 group min-h-[42px]',
                       'bg-[#087F82] text-white' => request()->routeIs($item['pattern']),
                       'text-white/70 hover:bg-white/10 hover:text-white' => !request()->routeIs($item['pattern']),
                   ])>
                    <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="ml-3" x-show="sidebarOpen || mobileSidebar">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Sidebar Toggle (Desktop) --}}
        <div class="hidden lg:flex items-center justify-center border-t border-white/10 p-3">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="flex items-center justify-center w-full rounded-lg py-2 text-white/70 hover:bg-white/10 hover:text-white transition-colors">
                <i x-show="sidebarOpen" data-lucide="panel-left-close" class="w-5 h-5"></i>
                <i x-show="!sidebarOpen" data-lucide="panel-left-open" class="w-5 h-5" x-cloak></i>
            </button>
        </div>
    </aside>

    {{-- Main Content Wrapper --}}
    <div :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-16'" class="transition-all duration-300 ease-in-out">

        {{-- Top Bar --}}
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-3 sm:px-6 shadow-sm">
            <div class="flex items-center space-x-2 sm:space-x-4 min-w-0">
                {{-- Mobile Hamburger --}}
                <button @click="mobileSidebar = true" class="lg:hidden text-[#243247] p-2 rounded-lg hover:bg-gray-100 transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Open sidebar">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                {{-- Page Title --}}
                <h1 class="text-sm sm:text-lg font-semibold text-[#243247] truncate max-w-[170px] xs:max-w-xs sm:max-w-none">{{ $header ?? '' }}</h1>
            </div>

            {{-- Right Section --}}
            <div class="flex items-center space-x-2 sm:space-x-4 flex-shrink-0">
                {{-- Visit Site --}}
                <a href="{{ url('/') }}" target="_blank"
                   class="hidden sm:inline-flex items-center space-x-1 text-sm text-gray-500 hover:text-[#153B70] transition-colors">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    <span>View Site</span>
                </a>

                {{-- Admin User Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center space-x-2 text-sm text-[#243247] hover:text-[#153B70] transition-colors p-1 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-[#153B70] flex items-center justify-center text-white text-xs font-bold">
                            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                        </div>
                        <span class="hidden sm:block font-medium">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </button>
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-48 rounded-lg bg-white shadow-lg ring-1 ring-black/5 py-1 z-50"
                         x-cloak>
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-[#243247] hover:bg-gray-50">Admin Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-[#243247] hover:bg-gray-50">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="px-3 sm:px-6 pt-4">
            @if(session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif
            @if(session('error'))
                <x-alert type="error">{{ session('error') }}</x-alert>
            @endif
        </div>

        {{-- Main Content --}}
        <main class="p-3.5 sm:p-6">
            {{ $slot }}
        </main>
    </div>

    {{-- Initialize Lucide Icons --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    </script>
</body>
</html>
