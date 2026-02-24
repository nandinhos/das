<!DOCTYPE html>
<html lang="pt-BR"
      x-data="appLayout()"
      :class="{ 'dark': darkMode }"
      class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CORAL 360 LTDA - Calculadora DAS (Anexo III)</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Instrument+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Instrument Sans', sans-serif; }
        
        /* Dark mode fallback */
        html.dark { color-scheme: dark; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-[#0a0a0a] text-slate-900 dark:text-slate-100 transition-colors duration-300 antialiased selection:bg-primary-500 selection:text-white"
      @scroll.window="scrolled = (window.pageYOffset > 10)">

    <!-- Background Elements for Premium Feel -->
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-100/40 via-slate-50 to-slate-50 dark:from-blue-900/10 dark:via-[#0a0a0a] dark:to-[#0a0a0a]"></div>
    <div class="fixed top-0 inset-x-0 h-[500px] bg-gradient-to-b from-blue-50/50 dark:from-blue-950/20 to-transparent -z-10"></div>

    <!-- Glassmorphism Header -->
    <header class="sticky top-0 z-40 w-full transition-all duration-300"
            :class="scrolled ? 'bg-white/70 dark:bg-[#161615]/70 backdrop-blur-md shadow-sm border-b border-slate-200 dark:border-[#3E3E3A]' : 'bg-transparent border-b border-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center py-4 gap-4">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-blue-600 flex items-center justify-center text-white font-bold font-heading text-xl"
                         style="background: linear-gradient(to bottom right, oklch(70.7% 0.165 254.624), #2563eb);">
                        C
                    </div>
                    <div>
                        <h1 class="text-xl font-bold font-heading text-slate-900 dark:text-white leading-tight">
                            Calculadora DAS <span class="text-sm font-medium text-primary-600 dark:text-primary-400 align-middle ml-1 px-2 py-0.5 rounded-full bg-blue-50 dark:bg-primary-500/10">Anexo III</span>
                        </h1>
                        <h2 class="text-xs font-medium tracking-wide text-slate-500 dark:text-slate-400 mt-0.5 uppercase">
                            Coral 360 LTDA &bull; CNPJ 52.507.002/0001-75
                        </h2>
                    </div>
                </div>

                <!-- Tools & Theme -->
                <div class="flex items-center gap-2">
                    {{-- Toggle dark mode --}}
                    <button @click="toggleDark()"
                            class="relative p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#3E3E3A] transition-all duration-200 outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-[#161615] touch-target"
                            :title="darkMode ? 'Mudar para Modo Claro' : 'Mudar para Modo Escuro'"
                            aria-label="Alternar tema">
                        
                        <!-- Sun icon -->
                        <svg x-show="!darkMode" x-transition:enter="transition-transform duration-300" x-transition:enter-start="-rotate-90 scale-50 opacity-0" x-transition:enter-end="rotate-0 scale-100 opacity-100" class="w-5 h-5 absolute inset-0 m-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        
                        <!-- Moon icon -->
                        <svg x-show="darkMode" x-cloak x-transition:enter="transition-transform duration-300" x-transition:enter-start="rotate-90 scale-50 opacity-0" x-transition:enter-end="rotate-0 scale-100 opacity-100" class="w-5 h-5 absolute inset-0 m-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>
                    @auth
                    <!-- User Profile & Logout -->
                    <div class="flex items-center gap-3 ml-2 border-l border-slate-200 dark:border-[#3E3E3A] pl-4">
                        <div class="hidden sm:flex flex-col items-end">
                            <span class="text-sm font-semibold text-slate-900 dark:text-white leading-none">{{ auth()->user()->name }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="w-8 h-8 rounded-full border-2 border-slate-200 dark:border-[#3E3E3A] overflow-hidden bg-slate-100 dark:bg-slate-800 flex-shrink-0 hidden sm:block">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 ml-1">
                            @csrf
                            <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 dark:hover:text-rose-400 rounded-lg transition-colors" title="Sair do Sistema">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="container mx-auto px-4 py-8 max-w-7xl pt-8">
        
        {{-- Modern Tabs Navigation --}}
        <div class="mb-8"
             x-data="{
                tabs: [
                    { id: 'revenue', label: 'Dashboard Receitas' },
                    { id: 'calculate', label: 'Calcular DAS' },
                    { id: 'history', label: 'Histórico' },
                    { id: 'tables', label: 'Tabelas Tributárias' }
                ]
             }">
            <nav class="grid grid-cols-2 gap-2 sm:flex sm:flex-row sm:gap-2" aria-label="Abas de Navegação">
                <template x-for="tab in tabs" :key="tab.id">
                    <button @click="activeTab = tab.id"
                            class="relative px-3 sm:px-5 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 sm:whitespace-nowrap outline-none focus-visible:ring-2 focus-visible:ring-primary-500 touch-target flex items-center justify-center w-full sm:w-auto"
                            :class="activeTab === tab.id
                                ? 'text-white'
                                : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-[#3E3E3A]'">

                        <!-- Active Background Bulb -->
                        <span x-show="activeTab === tab.id"
                              x-transition:enter="transition-transform duration-200 ease-out"
                              x-transition:enter-start="scale-95 opacity-0"
                              x-transition:enter-end="scale-100 opacity-100"
                              class="absolute inset-0 rounded-xl shadow-sm -z-10"
                              style="background: linear-gradient(to right, oklch(70.7% 0.165 254.624), #2563eb);"></span>

                        <span x-text="tab.label" class="relative z-10"></span>
                    </button>
                </template>
            </nav>
        </div>

        {{-- Dynamic View Area (Transparent Container) --}}
        <div class="min-h-[500px]">
            <div x-show="activeTab === 'revenue'" x-transition.opacity.duration.300ms x-cloak class="p-0 sm:p-2">
                @livewire('revenue-manager')
            </div>
            
            <div x-show="activeTab === 'calculate'" x-transition.opacity.duration.300ms x-cloak class="p-0 sm:p-2">
                @livewire('das-calculator')
            </div>
            
            <div x-show="activeTab === 'history'" x-transition.opacity.duration.300ms x-cloak class="p-0 sm:p-2">
                @livewire('calculation-history')
            </div>
            
            <div x-show="activeTab === 'tables'" x-transition.opacity.duration.300ms x-cloak class="p-4 sm:p-6">
                @livewire('tax-tables-manager')
            </div>
        </div>
        
    </main>

    {{-- System Footer --}}
    <footer class="container mx-auto max-w-7xl px-4 py-8 mt-auto border-t border-slate-200/60 dark:border-[#3E3E3A] flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 dark:text-slate-400">
        <p>&copy; {{ date('Y') }} Coral 360 LTDA. Todos os direitos reservados.</p>
        <p class="mt-2 sm:mt-0">TALL Stack Dashboard</p>
    </footer>

    {{-- macOS Style Global Toast System --}}
    <div x-data="toastManager()"
         @flash-message.window="show($event.detail)"
         x-show="visible"
         x-cloak
         x-transition:enter="transition cubic-bezier(0.175, 0.885, 0.32, 1.275) duration-400"
         x-transition:enter-start="opacity-0 translate-y-12 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90 translate-y-8"
         class="fixed bottom-4 inset-x-4 sm:bottom-6 sm:left-auto sm:right-6 sm:w-full z-50 flex items-center px-5 py-4 rounded-2xl border bg-white/90 dark:bg-[#161615]/95 backdrop-blur-xl shadow-2xl max-w-sm"
         :class="type === 'success' ? 'border-emerald-100 dark:border-emerald-900/50' : 'border-rose-100 dark:border-rose-900/50'">
        
        <!-- Icon logic -->
        <div class="flex-shrink-0 mr-3">
            <!-- Success SVG -->
            <svg x-show="type === 'success'" class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <!-- Error SVG -->
            <svg x-show="type === 'error'" class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>

        <div class="flex-1">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100" x-text="type === 'success' ? 'Sucesso' : 'Atenção'"></h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5" x-text="message"></p>
        </div>

        <button @click="visible = false" class="ml-4 flex-shrink-0 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors touch-target flex items-center justify-center rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Core App Scripting -->
    <script>
    function appLayout() {
        return {
            activeTab: Alpine.$persist('revenue').as('das_active_tab'),
            scrolled: false,
            darkMode: localStorage.getItem('das_dark_mode') === 'true' 
                   || (localStorage.getItem('das_dark_mode') === null 
                       && window.matchMedia('(prefers-color-scheme: dark)').matches),

            toggleDark() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('das_dark_mode', this.darkMode);
                document.documentElement.classList.toggle('dark', this.darkMode);
            },

            init() {
                document.documentElement.classList.toggle('dark', this.darkMode);
                window.addEventListener('view-calculation', () => {
                    this.activeTab = 'calculate';
                });
            }
        }
    }

    function toastManager() {
        return {
            visible: false,
            message: '',
            type: 'success',
            _timer: null,

            show(detail) {
                // Livewire 3 / Alpine Events podem embrulhar arrays multiplamente
                let extractData = function(obj) {
                    if (typeof obj === 'string') return { message: obj, type: 'success' };
                    if (Array.isArray(obj) && obj.length > 0) return extractData(obj[0]);
                    if (typeof obj === 'object' && obj !== null) return obj;
                    return { message: 'Ação realizada', type: 'success' };
                };
                
                let data = extractData(detail);
                
                this.message = data.message || 'Atualizado com sucesso!';
                this.type    = data.type || 'success';
                this.visible = true;
                
                // Animação de entrada macOS Style
                const el = this.$el;
                el.classList.remove('animate-pulse');
                
                clearTimeout(this._timer);
                this._timer = setTimeout(() => { this.visible = false; }, 4000);
            }
        }
    }
    </script>
</body>
</html>
