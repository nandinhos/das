<!DOCTYPE html>
<html lang="pt-BR"
      x-data="appLayout()"
      :class="{ 'dark': darkMode }"
      class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CORAL 360 LTDA - Calculadora DAS (Anexo III)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">

    <div class="container mx-auto px-4 py-8 max-w-7xl">

        {{-- Header --}}
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Calculadora DAS - Anexo III
                </h1>
                <h2 class="text-lg text-gray-600 dark:text-gray-400">
                    CORAL 360 LTDA &mdash; CNPJ 52.507.002/0001-75
                </h2>
            </div>
            {{-- Toggle dark mode --}}
            <button @click="toggleDark()"
                    class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    :title="darkMode ? 'Modo claro' : 'Modo escuro'">
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
        </header>

        {{-- Tabs Navigation --}}
        <nav class="flex flex-wrap gap-2 mb-8" aria-label="Abas">
            @foreach(['revenue' => 'Receitas Mensais', 'calculate' => 'Calcular DAS', 'history' => 'Histórico', 'tables' => 'Tabelas Tributárias'] as $tab => $label)
            <button @click="activeTab = '{{ $tab }}'"
                    :class="activeTab === '{{ $tab }}'
                        ? 'bg-coral-500 text-white shadow-sm'
                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700'"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap">
                {{ $label }}
            </button>
            @endforeach
        </nav>

        {{-- Content Pages --}}
        <div x-show="activeTab === 'revenue'" x-cloak>
            @livewire('revenue-manager')
        </div>
        <div x-show="activeTab === 'calculate'" x-cloak>
            @livewire('das-calculator')
        </div>
        <div x-show="activeTab === 'history'" x-cloak>
            @livewire('calculation-history')
        </div>
        <div x-show="activeTab === 'tables'" x-cloak>
            @include('pages.tax-tables')
        </div>

    </div>

    {{-- Toast global --}}
    <div x-data="toastManager()"
         @flash-message.window="show($event.detail)"
         x-show="visible"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         :class="type === 'success' ? 'bg-green-500' : 'bg-red-500'"
         class="fixed bottom-4 right-4 z-50 px-5 py-3 rounded-lg text-white text-sm font-medium shadow-lg max-w-sm">
        <span x-text="message"></span>
    </div>

    <script>
    function appLayout() {
        return {
            activeTab: 'revenue',
            darkMode: localStorage.getItem('das_dark_mode') === 'true'
                   || (localStorage.getItem('das_dark_mode') === null
                       && window.matchMedia('(prefers-color-scheme: dark)').matches),

            toggleDark() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('das_dark_mode', this.darkMode);
            },

            init() {
                // Quando o componente de histórico emite "ver cálculo", vai para aba calcular
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
                this.message = detail.message ?? detail;
                this.type    = detail.type ?? 'success';
                this.visible = true;
                clearTimeout(this._timer);
                this._timer = setTimeout(() => { this.visible = false; }, 3500);
            }
        }
    }
    </script>
</body>
</html>
