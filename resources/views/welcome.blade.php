<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>{{ config('app.name', 'Laravel') }}</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        
        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <!-- Fallback Tailwind CSS for development without Vite running -->
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
        
        <!-- Alpine.js (TALL Stack) -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            body {
                font-family: 'Instrument Sans', sans-serif;
            }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="antialiased bg-slate-50 text-slate-900 dark:bg-[#0a0a0a] dark:text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white"
          x-data="{ scrolled: false }"
          @scroll.window="scrolled = (window.pageYOffset > 20)">

        <!-- Enhanced Glassmorphism Header -->
        <header class="fixed top-0 w-full z-50 transition-all duration-300"
                :class="scrolled ? 'bg-white/70 dark:bg-[#0a0a0a]/70 backdrop-blur-md shadow-sm border-b border-slate-200 dark:border-slate-800' : 'bg-transparent'">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo Area -->
                    <div class="flex-shrink-0 flex items-center cursor-pointer group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center transform transition-transform group-hover:scale-105 group-hover:rotate-3 shadow-lg shadow-indigo-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <span class="ml-3 text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">
                            {{ config('app.name', 'Laravel') }}
                        </span>
                    </div>

                    <!-- Navigation -->
                    @if (Route::has('login'))
                        <nav class="hidden md:flex items-center space-x-8">
                            <a href="#features" class="text-sm font-medium text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors">Features</a>
                            
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors">Log in</a>
                                
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-lg hover:bg-slate-800 dark:hover:bg-slate-100 transition-all hover:scale-105 active:scale-95 shadow-md">
                                        Get Started
                                    </a>
                                @endif
                            @endauth
                        </nav>
                        
                        <!-- Mobile Menu Button (Alpine) -->
                        <div class="md:hidden flex items-center" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-[#161615] transition-colors focus:ring-2 focus:ring-indigo-500 outline-none">
                                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            
                            <!-- Mobile Dropdown -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 @click.away="open = false"
                                 x-cloak
                                 class="absolute top-24 right-4 w-56 rounded-xl bg-white dark:bg-[#161615] shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-slate-100 dark:border-[#3E3E3A] py-2 flex flex-col z-50 overflow-hidden">
                                
                                <a href="#features" class="px-4 py-3 text-sm font-medium text-slate-700 dark:text-[#EDEDEC] hover:bg-slate-50 dark:hover:bg-[#3E3E3A]">Features</a>
                                <div class="h-px bg-slate-100 dark:bg-[#3E3E3A] my-1"></div>
                                
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="px-4 py-3 text-sm font-medium text-slate-700 dark:text-[#EDEDEC] hover:bg-slate-50 dark:hover:bg-[#3E3E3A]">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="px-4 py-3 text-sm font-medium text-slate-700 dark:text-[#EDEDEC] hover:bg-slate-50 dark:hover:bg-[#3E3E3A]">Log in</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="px-4 py-3 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-[#3E3E3A]">Get Started</a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden flex flex-col items-center justify-center min-h-[90vh]">
            <!-- Background Decoration (Gradients & Blurs) -->
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-indigo-100/50 via-slate-50 to-slate-50 dark:from-indigo-900/20 dark:via-[#0a0a0a] dark:to-[#0a0a0a]"></div>
            <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-[800px] h-[600px] bg-purple-300/30 dark:bg-purple-900/20 rounded-full blur-[120px] -z-10 mix-blend-multiply dark:mix-blend-lighten"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/3 w-[600px] h-[500px] bg-indigo-300/30 dark:bg-indigo-900/20 rounded-full blur-[100px] -z-10 mix-blend-multiply dark:mix-blend-lighten"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative w-full">
                <div class="text-center max-w-4xl mx-auto" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                    
                    <div x-show="show" x-transition:enter="transition-all duration-1000 ease-out" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="inline-flex items-center space-x-3 bg-white/60 dark:bg-[#161615]/80 backdrop-blur-md border border-slate-200 dark:border-[#3E3E3A] rounded-full px-5 py-2 mb-8 shadow-[0px_2px_8px_rgba(0,0,0,0.04)]">
                        <span class="flex h-2.5 w-2.5 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-indigo-500"></span>
                        </span>
                        <span class="text-xs font-semibold uppercase tracking-widest text-[#1b1b18] dark:text-[#A1A09A]">Modern TALL Stack Foundation</span>
                    </div>

                    <h1 x-show="show" x-transition:enter="transition-all duration-1000 delay-150 ease-out" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8 leading-[1.1]">
                        Build powerful software
                        <span class="block mt-2 text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">
                            faster than ever
                        </span>
                    </h1>
                    
                    <p x-show="show" x-transition:enter="transition-all duration-1000 delay-300 ease-out" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="mt-6 text-xl text-slate-600 dark:text-[#EDEDEC] max-w-2xl mx-auto leading-relaxed">
                        A beautifully designed, highly functional starting point for your next big idea. Fully responsive, dark-mode ready, and crafted with precision.
                    </p>
                    
                    <div x-show="show" x-transition:enter="transition-all duration-1000 delay-500 ease-out" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="mt-10 flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 text-base font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-all hover:-translate-y-1 hover:shadow-xl shadow-indigo-500/30 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 outline-none flex items-center justify-center">
                            Start Building Now
                            <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="#features" class="w-full sm:w-auto px-8 py-4 text-base font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:border-slate-300 hover:bg-slate-50 dark:bg-[#161615] dark:text-[#EDEDEC] dark:border-[#3E3E3A] dark:hover:border-[#62605b] dark:hover:bg-[#1b1b18] transition-all hover:-translate-y-1 hover:shadow-lg focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 outline-none flex items-center justify-center">
                            Explore Features
                        </a>
                    </div>
                </div>

                <!-- Preview UI Component Element -->
                <div class="mt-24 relative mx-auto max-w-5xl" x-data="{ show: false }" x-init="setTimeout(() => show = true, 800)">
                    <div x-show="show" x-transition:enter="transition-all duration-1000 ease-out" x-transition:enter-start="opacity-0 translate-y-16 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="rounded-2xl border border-slate-200/50 dark:border-[#3E3E3A] bg-white/40 dark:bg-[#161615]/60 backdrop-blur-xl shadow-2xl overflow-hidden ring-1 ring-black/5 dark:ring-white/10 p-2">
                        <div class="rounded-xl overflow-hidden border border-slate-200/80 dark:border-[#3E3E3A]/80 bg-slate-50 dark:bg-[#0a0a0a]">
                            <!-- Mock Header -->
                            <div class="flex items-center px-4 py-3 border-b border-slate-200 dark:border-[#3E3E3A] bg-white dark:bg-[#161615]">
                                <div class="flex gap-2">
                                    <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="h-6 max-w-sm rounded-md bg-slate-100 dark:bg-[#3E3E3A]/50 mx-auto w-full flex items-center justify-center text-[11px] text-[#706f6c] dark:text-[#A1A09A] font-mono tracking-wider">
                                        {{ request()->getHost() }}
                                    </div>
                                </div>
                            </div>
                            <!-- Mock Body -->
                            <div class="aspect-[16/9] md:aspect-[21/9] p-8 flex flex-col items-center justify-center text-center bg-[url('https://laravel.com/assets/img/welcome/background.svg')] dark:bg-[url('https://laravel.com/assets/img/welcome/background.svg')] bg-cover bg-center">
                                <div class="w-16 h-16 rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6 ring-8 ring-white dark:ring-[#161615] shadow-sm animate-bounce">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                </div>
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Ready for The World</h3>
                                <p class="text-[#706f6c] dark:text-[#A1A09A] max-w-md">The Laravel ecosystem provides all the tools you need to build robust, modern web applications at scale.</p>
                                
                                <div class="mt-8 grid grid-cols-2 gap-4 w-full max-w-sm">
                                    <div class="h-2 rounded bg-slate-200 dark:bg-[#3E3E3A] col-span-2"></div>
                                    <div class="h-2 rounded bg-slate-200 dark:bg-[#3E3E3A]"></div>
                                    <div class="h-2 rounded bg-indigo-500/50 dark:bg-indigo-500/30"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Feature Section -->
        <section id="features" class="py-24 bg-white dark:bg-[#161615] relative overflow-hidden ring-1 ring-slate-100 dark:ring-[#3E3E3A]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16" x-data="{ show: false }" x-intersect.once="show = true">
                    <h2 x-show="show" x-transition:enter="transition duration-700 ease-out" x-transition:enter-start="opacity-0 translate-y-8" class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">Everything you need</h2>
                    <p x-show="show" x-transition:enter="transition duration-700 delay-100 ease-out" x-transition:enter-start="opacity-0 translate-y-8" class="mt-4 text-lg text-[#706f6c] dark:text-[#A1A09A] max-w-2xl mx-auto">Built on top of industry-standard tools for rapid development and incredible performance.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8" x-data="{ show: false }" x-intersect.once="show = true">
                    <!-- Feature 1 -->
                    <div x-show="show" x-transition:enter="transition duration-700 delay-200 ease-out" x-transition:enter-start="opacity-0 translate-y-8" class="group p-8 rounded-2xl bg-slate-50 dark:bg-[#0a0a0a] border border-slate-100 dark:border-[#3E3E3A] transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:bg-white dark:hover:bg-[#1b1b18] hover:border-indigo-100 dark:hover:border-indigo-900/50 relative overflow-hidden">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 relative z-10">Lightning Fast</h3>
                        <p class="text-[#706f6c] dark:text-[#EDEDEC] leading-relaxed text-sm relative z-10">Optimized assets and server-side rendering capability with Laravel. Delivered smoothly via Vite.</p>
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-50/50 to-transparent dark:from-orange-900/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>

                    <!-- Feature 2 -->
                    <div x-show="show" x-transition:enter="transition duration-700 delay-300 ease-out" x-transition:enter-start="opacity-0 translate-y-8" class="group p-8 rounded-2xl bg-slate-50 dark:bg-[#0a0a0a] border border-slate-100 dark:border-[#3E3E3A] transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:bg-white dark:hover:bg-[#1b1b18] hover:border-indigo-100 dark:hover:border-indigo-900/50 relative overflow-hidden">
                        <div class="w-12 h-12 rounded-xl bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 relative z-10">Responsive UX</h3>
                        <p class="text-[#706f6c] dark:text-[#EDEDEC] leading-relaxed text-sm relative z-10">Tailwind CSS integrated from the start. Flawless rendering on mobile, tablet, and desktop displays natively.</p>
                        <div class="absolute inset-0 bg-gradient-to-br from-sky-50/50 to-transparent dark:from-sky-900/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>

                    <!-- Feature 3 -->
                    <div x-show="show" x-transition:enter="transition duration-700 delay-400 ease-out" x-transition:enter-start="opacity-0 translate-y-8" class="group p-8 rounded-2xl bg-slate-50 dark:bg-[#0a0a0a] border border-slate-100 dark:border-[#3E3E3A] transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:bg-white dark:hover:bg-[#1b1b18] hover:border-indigo-100 dark:hover:border-indigo-900/50 relative overflow-hidden">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 relative z-10">Secure Core</h3>
                        <p class="text-[#706f6c] dark:text-[#EDEDEC] leading-relaxed text-sm relative z-10">Built on Laravel's battle-tested foundation. CSRF protection, secure routing, and robust authentication built-in.</p>
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/50 to-transparent dark:from-emerald-900/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-50 dark:bg-[#0a0a0a] py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0 group cursor-pointer">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 dark:bg-[#3E3E3A] flex items-center justify-center mr-3 group-hover:bg-indigo-600 transition-colors">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        {{ config('app.name', 'Laravel') }}
                    </span>
                </div>
                
                <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </div>
                
                <div class="mt-4 md:mt-0 flex space-x-6 text-sm font-medium">
                    <a href="https://laravel.com/docs" target="_blank" class="text-[#706f6c] hover:text-indigo-600 dark:text-[#A1A09A] dark:hover:text-indigo-400 transition-colors">Documentation</a>
                    <a href="https://laracasts.com" target="_blank" class="text-[#706f6c] hover:text-indigo-600 dark:text-[#A1A09A] dark:hover:text-indigo-400 transition-colors">Laracasts</a>
                </div>
            </div>
        </footer>
    </body>
</html>
