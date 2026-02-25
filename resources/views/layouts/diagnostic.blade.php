<!DOCTYPE html>
<html lang="pt-BR"
      x-data="diagnosticLayout()"
      :class="{ 'dark': darkMode }"
      class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico do Scraper — DAS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Instrument+Sans:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Instrument Sans', sans-serif; }
        html.dark { color-scheme: dark; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-[#0a0a0a] text-slate-900 dark:text-slate-100 transition-colors duration-300 antialiased">

    <div class="fixed inset-0 -z-10 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-100/40 via-slate-50 to-slate-50 dark:from-blue-900/10 dark:via-[#0a0a0a] dark:to-[#0a0a0a]"></div>

    <header class="sticky top-0 z-40 w-full bg-white/70 dark:bg-[#161615]/70 backdrop-blur-md shadow-sm border-b border-slate-200 dark:border-[#3E3E3A]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">

                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}"
                       class="flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors mr-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Voltar
                    </a>
                    <div class="w-px h-5 bg-slate-200 dark:bg-[#3E3E3A]"></div>
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-sm sm:text-base font-semibold font-heading text-slate-900 dark:text-white leading-tight">
                            Diagnóstico do Scraper
                        </h1>
                        <p class="hidden sm:block text-xs text-slate-500 dark:text-slate-400">Tabelas Tributárias — Anexo III</p>
                    </div>
                </div>

                <button @click="toggleDark()"
                        class="p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#3E3E3A] transition-colors"
                        :title="darkMode ? 'Modo Claro' : 'Modo Escuro'">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <script>
    function diagnosticLayout() {
        return {
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
            }
        }
    }
    </script>
</body>
</html>
