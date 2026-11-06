<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-seo :title="$title ?? null" :description="$metaDescription ?? null" :canonical="$canonicalUrl ?? null" />
    {{ $meta ?? '' }}

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
<body class="min-h-screen bg-[#F7F9FC] text-[#243247] font-sans antialiased flex flex-col justify-between"
      :class="{ 'overflow-hidden': mobileMenu || searchOpen }"
      x-data="{ mobileMenu: false, searchOpen: false }">

    {{-- Main Navbar (Clean, Premium, Properly Spaced matching Client Mockup) --}}
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-200/80 shadow-xs">
        <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">
            <div class="flex h-16 sm:h-18 items-center justify-between gap-2 sm:gap-4 lg:gap-8">
                
                {{-- Brand Logo --}}
                <div class="flex-shrink-0 min-w-0">
                    <a href="{{ url('/') }}" class="group transition-transform active:scale-95 block">
                        <x-application-logo />
                    </a>
                </div>

                {{-- Desktop Navigation Links --}}
                <nav class="hidden lg:flex items-center gap-1 xl:gap-2 font-medium text-sm">
                    <a href="{{ url('/') }}"
                       @class([
                           'px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap text-sm',
                           'text-[#087F82] font-bold bg-teal-50' => request()->is('/'),
                           'text-[#243247] hover:text-[#087F82] hover:bg-gray-50' => !request()->is('/'),
                       ])>
                        Home
                    </a>
                    <a href="{{ route('categories.show', 'arithmetic-aptitude') }}"
                       @class([
                           'px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap text-sm',
                           'text-[#087F82] font-bold bg-teal-50' => request()->is('categories/arithmetic-aptitude*'),
                           'text-[#243247] hover:text-[#087F82] hover:bg-gray-50' => !request()->is('categories/arithmetic-aptitude*'),
                       ])>
                        Aptitude
                    </a>
                    <a href="{{ route('categories.show', 'logical-reasoning') }}"
                       @class([
                           'px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap text-sm',
                           'text-[#087F82] font-bold bg-teal-50' => request()->is('categories/logical-reasoning*'),
                           'text-[#243247] hover:text-[#087F82] hover:bg-gray-50' => !request()->is('categories/logical-reasoning*'),
                       ])>
                        Reasoning
                    </a>
                    <a href="{{ route('categories.show', 'programming') }}"
                       @class([
                           'px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap text-sm',
                           'text-[#087F82] font-bold bg-teal-50' => request()->is('categories/programming*'),
                           'text-[#243247] hover:text-[#087F82] hover:bg-gray-50' => !request()->is('categories/programming*'),
                       ])>
                        Programming
                    </a>
                    <a href="{{ route('mcq.index') }}"
                       @class([
                           'px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap text-sm',
                           'text-[#087F82] font-bold bg-teal-50' => request()->routeIs('mcq.*'),
                           'text-[#243247] hover:text-[#087F82] hover:bg-gray-50' => !request()->routeIs('mcq.*'),
                       ])>
                        MCQ Practice
                    </a>
                    <a href="{{ route('placement.index') }}"
                       @class([
                           'px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap text-sm',
                           'text-[#087F82] font-bold bg-teal-50' => request()->routeIs('placement*'),
                           'text-[#243247] hover:text-[#087F82] hover:bg-gray-50' => !request()->routeIs('placement*'),
                       ])>
                        Placement Prep
                    </a>
                    <a href="{{ route('current-affairs.index') }}"
                       @class([
                           'px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap text-sm',
                           'text-[#087F82] font-bold bg-teal-50' => request()->routeIs('current-affairs*'),
                           'text-[#243247] hover:text-[#087F82] hover:bg-gray-50' => !request()->routeIs('current-affairs*'),
                       ])>
                        Current Affairs
                    </a>
                    <a href="{{ route('blog.index') }}"
                       @class([
                           'px-3.5 py-2 rounded-xl transition-colors whitespace-nowrap text-sm',
                           'text-[#087F82] font-bold bg-teal-50' => request()->routeIs('blog.*'),
                           'text-[#243247] hover:text-[#087F82] hover:bg-gray-50' => !request()->routeIs('blog.*'),
                       ])>
                        Blog
                    </a>
                </nav>

                {{-- Right Actions (Search & Auth) --}}
                <div class="flex items-center gap-1.5 sm:gap-3 flex-shrink-0">
                    {{-- Clean Search Icon Button matching mockup --}}
                    <button @click="searchOpen = true" 
                            type="button" 
                            class="min-w-[40px] min-h-[40px] p-2 text-[#243247]/80 hover:text-[#087F82] hover:bg-teal-50/70 rounded-full transition-colors cursor-pointer flex items-center justify-center"
                            aria-label="Search Topics"
                            title="Search Topics (Ctrl+K)">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>

                    @guest
                        <div class="hidden sm:flex items-center gap-2">
                            <a href="{{ route('login') }}"
                               class="px-3.5 py-2 text-sm font-semibold text-[#153B70] hover:text-[#087F82] hover:bg-gray-50 rounded-xl transition-colors whitespace-nowrap min-h-[40px] inline-flex items-center">
                                Log in
                            </a>
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center px-4.5 py-2 text-sm font-bold text-white bg-[#087F82] hover:bg-[#066668] rounded-xl shadow-xs transition-all whitespace-nowrap hover:shadow-sm active:scale-95 min-h-[40px]">
                                Sign up
                            </a>
                        </div>
                    @else
                        <div x-data="{ userMenu: false }" class="relative">
                            <button @click="userMenu = !userMenu"
                                    type="button"
                                    class="flex items-center space-x-1.5 sm:space-x-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors min-h-[40px]"
                                    aria-label="User Menu">
                                <div class="w-8 h-8 rounded-full bg-[#153B70] text-white flex items-center justify-center font-bold text-xs uppercase shadow-xs">
                                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="hidden md:inline text-sm font-medium text-[#243247] max-w-[120px] truncate">
                                    {{ Auth::user()->name }}
                                </span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                            </button>

                            <div x-show="userMenu"
                                 @click.away="userMenu = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-52 rounded-xl bg-white shadow-xl ring-1 ring-black/5 py-1 z-50 divide-y divide-gray-100"
                                 x-cloak>
                                <div class="px-4 py-3">
                                    <p class="text-xs text-gray-500">Signed in as</p>
                                    <p class="text-sm font-semibold text-[#243247] truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-[#243247] hover:bg-[#F7F9FC] hover:text-[#087F82]">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2.5 text-gray-400"></i>
                                        User Dashboard
                                    </a>
                                    <a href="{{ route('user.bookmarks') }}" class="flex items-center px-4 py-2 text-sm text-[#243247] hover:bg-[#F7F9FC] hover:text-[#087F82]">
                                        <i data-lucide="bookmark" class="w-4 h-4 mr-2.5 text-gray-400"></i>
                                        My Bookmarks
                                    </a>
                                    @if(Auth::user()->isAdmin ?? false)
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm font-semibold text-[#153B70] hover:bg-blue-50">
                                            <i data-lucide="shield-check" class="w-4 h-4 mr-2.5 text-[#153B70]"></i>
                                            Admin Panel
                                        </a>
                                    @endif
                                </div>
                                <div class="py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i data-lucide="log-out" class="w-4 h-4 mr-2.5 text-red-500"></i>
                                            Log out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endguest

                    {{-- Mobile Hamburger --}}
                    <button @click="mobileMenu = !mobileMenu"
                            type="button"
                            class="lg:hidden p-2 text-[#243247] hover:bg-gray-100 rounded-lg transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center"
                            aria-label="Toggle navigation menu">
                        <i x-show="!mobileMenu" data-lucide="menu" class="w-6 h-6"></i>
                        <i x-show="mobileMenu" data-lucide="x" class="w-6 h-6" x-cloak></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Backdrop --}}
        <div x-show="mobileMenu"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenu = false"
             class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs lg:hidden"
             x-cloak></div>

        {{-- Mobile Slide-down Navigation Menu --}}
        <div x-show="mobileMenu"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden relative z-50 border-t border-gray-200 bg-white px-4 pt-3 pb-6 space-y-1.5 shadow-xl max-h-[calc(100vh-4.5rem)] overflow-y-auto scrollbar-thin"
             x-cloak>
            <div class="flex items-center justify-between pb-2 border-b border-gray-100 lg:hidden">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Navigation Menu</span>
                <button @click="mobileMenu = false" class="p-1.5 text-gray-500 hover:text-[#153B70] rounded-lg" aria-label="Close menu">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <a @click="mobileMenu = false" href="{{ url('/') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('/') ? 'text-[#087F82] bg-teal-50' : 'text-[#243247] hover:bg-[#F7F9FC]' }}">
                <i data-lucide="home" class="w-4 h-4 mr-2.5 text-gray-400"></i> Home
            </a>
            <a @click="mobileMenu = false" href="{{ route('categories.show', 'arithmetic-aptitude') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('categories/arithmetic-aptitude*') ? 'text-[#087F82] bg-teal-50' : 'text-[#243247] hover:bg-[#F7F9FC]' }}">
                <i data-lucide="calculator" class="w-4 h-4 mr-2.5 text-gray-400"></i> Arithmetic Aptitude
            </a>
            <a @click="mobileMenu = false" href="{{ route('categories.show', 'logical-reasoning') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('categories/logical-reasoning*') ? 'text-[#087F82] bg-teal-50' : 'text-[#243247] hover:bg-[#F7F9FC]' }}">
                <i data-lucide="brain" class="w-4 h-4 mr-2.5 text-gray-400"></i> Logical Reasoning
            </a>
            <a @click="mobileMenu = false" href="{{ route('categories.show', 'programming') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('categories/programming*') ? 'text-[#087F82] bg-teal-50' : 'text-[#243247] hover:bg-[#F7F9FC]' }}">
                <i data-lucide="code-2" class="w-4 h-4 mr-2.5 text-gray-400"></i> Programming
            </a>
            <a @click="mobileMenu = false" href="{{ route('categories.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('categories.index') ? 'text-[#087F82] bg-teal-50' : 'text-[#243247] hover:bg-[#F7F9FC]' }}">
                <i data-lucide="layers" class="w-4 h-4 mr-2.5 text-gray-400"></i> All Categories
            </a>
            <a @click="mobileMenu = false" href="{{ route('mcq.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('mcq.*') ? 'text-[#087F82] bg-teal-50' : 'text-[#243247] hover:bg-[#F7F9FC]' }}">
                <i data-lucide="help-circle" class="w-4 h-4 mr-2.5 text-gray-400"></i> MCQ Practice
            </a>
            <a @click="mobileMenu = false" href="{{ route('placement.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('placement*') ? 'text-[#087F82] bg-teal-50' : 'text-[#243247] hover:bg-[#F7F9FC]' }}">
                <i data-lucide="graduation-cap" class="w-4 h-4 mr-2.5 text-gray-400"></i> Placement Preparation
            </a>
            <a @click="mobileMenu = false" href="{{ route('current-affairs.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('current-affairs*') ? 'text-[#087F82] bg-teal-50' : 'text-[#243247] hover:bg-[#F7F9FC]' }}">
                <i data-lucide="newspaper" class="w-4 h-4 mr-2.5 text-gray-400"></i> Current Affairs
            </a>
            <a @click="mobileMenu = false" href="{{ route('blog.index') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('blog.*') ? 'text-[#087F82] bg-teal-50' : 'text-[#243247] hover:bg-[#F7F9FC]' }}">
                <i data-lucide="file-text" class="w-4 h-4 mr-2.5 text-gray-400"></i> Blog & Guides
            </a>
            <a @click="mobileMenu = false" href="{{ route('about') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium text-[#243247] hover:bg-[#F7F9FC]">
                <i data-lucide="info" class="w-4 h-4 mr-2.5 text-gray-400"></i> About Us
            </a>
            <a @click="mobileMenu = false" href="{{ route('contact') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium text-[#243247] hover:bg-[#F7F9FC]">
                <i data-lucide="mail" class="w-4 h-4 mr-2.5 text-gray-400"></i> Contact
            </a>

            <div class="border-t border-gray-100 pt-3 mt-2">
                @guest
                    <div class="grid grid-cols-2 gap-3">
                        <a @click="mobileMenu = false" href="{{ route('login') }}" class="text-center px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-[#153B70] hover:bg-gray-50 flex items-center justify-center min-h-[44px]">Log in</a>
                        <a @click="mobileMenu = false" href="{{ route('register') }}" class="text-center px-4 py-2.5 rounded-xl bg-[#087F82] text-sm font-semibold text-white hover:bg-[#066668] flex items-center justify-center min-h-[44px]">Sign up</a>
                    </div>
                @else
                    <a @click="mobileMenu = false" href="{{ route('user.dashboard') }}" class="block px-3 py-2.5 rounded-xl text-sm font-semibold text-[#087F82] bg-teal-50 min-h-[44px] flex items-center">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2"></i> Go to Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="flex items-center w-full text-left px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-xl min-h-[44px]">
                            <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> Log out
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </header>

    {{-- Interactive Search Modal (Alpine.js) --}}
    <div x-show="searchOpen"
         @keydown.escape.window="searchOpen = false"
         class="fixed inset-0 z-50 overflow-y-auto p-3 sm:p-6 md:p-20 flex items-start sm:items-center justify-center pt-16 sm:pt-6"
         x-cloak>
        <div x-show="searchOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
             @click="searchOpen = false"></div>

        <div x-show="searchOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 overflow-hidden flex flex-col max-h-[85vh]"
             x-data="{ query: '' }">
            <div class="flex items-center px-4 border-b border-gray-200 flex-shrink-0">
                <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                <input type="text"
                       x-model="query"
                       placeholder="Search subjects, topics (e.g. Percentages, Python)..."
                       class="w-full border-0 py-3.5 sm:py-4 pl-3 pr-4 text-[#243247] placeholder-gray-400 focus:outline-none focus:ring-0 text-base sm:text-sm">
                <button @click="searchOpen = false" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-600 px-2 py-1 rounded">ESC</button>
            </div>

            <div class="p-4 overflow-y-auto scrollbar-thin">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Popular Practice Topics</p>
                <div class="space-y-1">
                    <a @click="searchOpen = false" href="{{ route('mcq.index', ['topic' => 'percentages']) }}" class="flex items-center justify-between p-2.5 rounded-lg hover:bg-teal-50 group">
                        <span class="text-sm font-medium text-[#243247] group-hover:text-[#087F82]">Percentages</span>
                        <span class="text-xs text-gray-400">Arithmetic Aptitude</span>
                    </a>
                    <a @click="searchOpen = false" href="{{ route('mcq.index', ['topic' => 'profit-and-loss']) }}" class="flex items-center justify-between p-2.5 rounded-lg hover:bg-teal-50 group">
                        <span class="text-sm font-medium text-[#243247] group-hover:text-[#087F82]">Profit and Loss</span>
                        <span class="text-xs text-gray-400">Arithmetic Aptitude</span>
                    </a>
                    <a @click="searchOpen = false" href="{{ route('mcq.index', ['topic' => 'number-series']) }}" class="flex items-center justify-between p-2.5 rounded-lg hover:bg-teal-50 group">
                        <span class="text-sm font-medium text-[#243247] group-hover:text-[#087F82]">Number Series & Puzzles</span>
                        <span class="text-xs text-gray-400">Logical Reasoning</span>
                    </a>
                    <a @click="searchOpen = false" href="{{ route('mcq.index', ['topic' => 'python']) }}" class="flex items-center justify-between p-2.5 rounded-lg hover:bg-teal-50 group">
                        <span class="text-sm font-medium text-[#243247] group-hover:text-[#087F82]">Python Programming</span>
                        <span class="text-xs text-gray-400">Programming</span>
                    </a>
                    <a @click="searchOpen = false" href="{{ route('mcq.index', ['topic' => 'time-and-work']) }}" class="flex items-center justify-between p-2.5 rounded-lg hover:bg-teal-50 group">
                        <span class="text-sm font-medium text-[#243247] group-hover:text-[#087F82]">Time and Work</span>
                        <span class="text-xs text-gray-400">Arithmetic Aptitude</span>
                    </a>
                    <a @click="searchOpen = false" href="{{ route('placement.index') }}" class="flex items-center justify-between p-2.5 rounded-lg hover:bg-teal-50 group">
                        <span class="text-sm font-medium text-[#243247] group-hover:text-[#087F82]">HR & Technical Interview Questions</span>
                        <span class="text-xs text-[#087F82] font-semibold">Placement Prep</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- System Alert Messages --}}
    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
            <x-alert type="success">{{ session('success') }}</x-alert>
        </div>
    @endif

    @if(session('error'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
            <x-alert type="error">{{ session('error') }}</x-alert>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer matching client mockup (#153B70 background) --}}
    <footer class="bg-[#153B70] text-white mt-20 border-t border-white/10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">
                
                {{-- Column 1: Brand & About --}}
                <div class="lg:col-span-2">
                    <a href="{{ url('/') }}" class="inline-block mb-4">
                        <x-application-logo variant="light" size="lg" />
                    </a>
                    <p class="text-sm text-white/75 leading-relaxed max-w-sm mb-6">
                        Practise Smart. Get Placement Ready. AptiMedha brings aptitude practice, technical learning, and career preparation together in one place.
                    </p>
                    <div class="flex items-center space-x-3 text-white/70">
                        <a href="https://linkedin.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-[#087F82] hover:text-white flex items-center justify-center transition-colors" title="LinkedIn">
                            <i data-lucide="linkedin" class="w-4 h-4"></i>
                        </a>
                        <a href="https://twitter.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-[#087F82] hover:text-white flex items-center justify-center transition-colors" title="Twitter / X">
                            <i data-lucide="twitter" class="w-4 h-4"></i>
                        </a>
                        <a href="https://github.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-[#087F82] hover:text-white flex items-center justify-center transition-colors" title="GitHub">
                            <i data-lucide="github" class="w-4 h-4"></i>
                        </a>
                        <a href="mailto:contact@aptimedha.com" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-[#087F82] hover:text-white flex items-center justify-center transition-colors" title="Email Us">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                {{-- Column 2: Subjects & MCQs --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#087F82] mb-4">Practice Subjects</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('categories.show', 'arithmetic-aptitude') }}" class="text-white/70 hover:text-white transition-colors">Arithmetic Aptitude</a></li>
                        <li><a href="{{ route('categories.show', 'data-interpretation') }}" class="text-white/70 hover:text-white transition-colors">Data Interpretation</a></li>
                        <li><a href="{{ route('categories.show', 'logical-reasoning') }}" class="text-white/70 hover:text-white transition-colors">Logical Reasoning</a></li>
                        <li><a href="{{ route('categories.show', 'verbal-ability') }}" class="text-white/70 hover:text-white transition-colors">Verbal Ability</a></li>
                        <li><a href="{{ route('categories.show', 'english') }}" class="text-white/70 hover:text-white transition-colors">English Grammar</a></li>
                        <li><a href="{{ route('categories.show', 'programming') }}" class="text-white/70 hover:text-white transition-colors">Programming (C, Java, Python)</a></li>
                        <li><a href="{{ route('categories.show', 'engineering') }}" class="text-white/70 hover:text-white transition-colors">Engineering MCQs</a></li>
                    </ul>
                </div>

                {{-- Column 3: Placement & Learning --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#087F82] mb-4">Placement Prep</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('placement.index') }}#hr-interviews" class="text-white/70 hover:text-white transition-colors">HR Interview Questions</a></li>
                        <li><a href="{{ route('placement.index') }}#technical-interviews" class="text-white/70 hover:text-white transition-colors">Technical Interviews</a></li>
                        <li><a href="{{ route('placement.index') }}#group-discussions" class="text-white/70 hover:text-white transition-colors">Group Discussions</a></li>
                        <li><a href="{{ route('placement.index') }}#resume-guide" class="text-white/70 hover:text-white transition-colors">Resume Guide</a></li>
                        <li><a href="{{ route('blog.index') }}" class="text-white/70 hover:text-white transition-colors">Articles & Tips</a></li>
                        <li><a href="{{ route('current-affairs.index') }}" class="text-white/70 hover:text-white transition-colors">Monthly Current Affairs</a></li>
                        <li><a href="{{ route('categories.index') }}" class="text-white/70 hover:text-white transition-colors">Explore All Categories</a></li>
                    </ul>
                </div>

                {{-- Column 4: Company & Legal --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#087F82] mb-4">Information & Legal</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('about') }}" class="text-white/70 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="text-white/70 hover:text-white transition-colors">Contact Us</a></li>
                        <li><a href="{{ route('disclaimer') }}" class="text-white/70 hover:text-white transition-colors">Disclaimer</a></li>
                        <li><a href="{{ route('terms') }}" class="text-white/70 hover:text-white transition-colors">Terms & Conditions</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-white/70 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('copyright') }}" class="text-white/70 hover:text-white transition-colors">Copyright Policy</a></li>
                    </ul>
                </div>
            </div>

            {{-- Bottom copyright line matching mockup --}}
            <div class="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between text-xs text-white/50 gap-4 text-center sm:text-left">
                <p>&copy; {{ date('Y') }} AptiMedha.com. All rights reserved.</p>
                <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2">
                    <a href="{{ route('disclaimer') }}" class="hover:text-white transition-colors">Disclaimer</a>
                    <a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms</a>
                    <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy</a>
                    <a href="{{ route('copyright') }}" class="hover:text-white transition-colors">Copyright</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Back to Top Floating Button (Spring Interactive & Accessible) --}}
    <div x-data="{ showTop: false }"
         @scroll.window="showTop = (window.pageYOffset > 400)"
         class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-40">
        <button x-show="showTop"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-3 scale-90"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-3 scale-90"
                @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                data-motion-spring
                type="button"
                class="w-11 h-11 rounded-xl bg-white/95 backdrop-blur-md border border-gray-200 shadow-md text-[#153B70] hover:text-[#087F82] hover:border-[#087F82] flex items-center justify-center transition-all cursor-pointer group"
                title="Back to top"
                x-cloak>
            <i data-lucide="arrow-up" class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform"></i>
        </button>
    </div>

    {{-- Initialize Lucide Icons & Global Shortcuts --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Keyboard shortcut Ctrl+K / Cmd+K for search modal
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    if (window.Alpine) {
                        const bodyEl = document.querySelector('body');
                        if (bodyEl && bodyEl._x_dataStack && bodyEl._x_dataStack[0]) {
                            bodyEl._x_dataStack[0].searchOpen = true;
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
