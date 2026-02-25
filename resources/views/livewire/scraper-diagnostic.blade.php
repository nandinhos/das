<div class="space-y-8">

    {{-- Header: Título + Botão --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm das-text-muted mt-1">
                Inspeciona a saída do scraper tributário, a qualidade dos dados extraídos e verifica se as tabelas locais estão sincronizadas com a fonte oficial.
            </p>
        </div>
        <button
            wire:click="run"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-60 cursor-not-allowed"
            class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all duration-200 shadow-sm"
            style="background: linear-gradient(to right, oklch(70.7% 0.165 254.624), #2563eb);"
        >
            <span wire:loading.remove wire:target="run">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            <span wire:loading wire:target="run" class="flex items-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
            </span>
            <span wire:loading.remove wire:target="run">Executar Diagnóstico</span>
            <span wire:loading wire:target="run">Executando...</span>
        </button>
    </div>

    @if(!$ran)
        {{-- Estado inicial --}}
        <div class="das-section flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-[#1a1a1a] flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                </svg>
            </div>
            <p class="text-sm font-medium das-text-muted">Clique em "Executar Diagnóstico" para iniciar a análise</p>
        </div>
    @else

        {{-- SEÇÃO 1: Teste de Conexão HTTP --}}
        <x-das.section title="Teste de Conexão HTTP">
            <div class="diag-terminal rounded-xl overflow-hidden border border-[#3c3c3c] shadow-xl">

                {{-- Chrome bar --}}
                <div class="flex items-center justify-between px-4 py-2.5 bg-[#2d2d2d]">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-[#ff5f57]"></span>
                        <span class="w-3 h-3 rounded-full bg-[#febc2e]"></span>
                        <span class="w-3 h-3 rounded-full bg-[#28c840]"></span>
                    </div>
                    <span class="text-xs text-[#8a8a8a] font-mono tracking-wide">bash — cURL Diagnostic</span>
                    @if($connectionTest['success'])
                        <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-900/60 text-emerald-300 border border-emerald-700/60">ONLINE</span>
                    @elseif($connectionTest['error'])
                        <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full bg-rose-900/60 text-rose-300 border border-rose-700/60">TIMEOUT / ERRO</span>
                    @else
                        <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full bg-amber-900/60 text-amber-300 border border-amber-700/60">HTTP {{ $connectionTest['status_code'] }}</span>
                    @endif
                </div>

                {{-- Body do terminal --}}
                <div class="bg-[#1e1e1e] px-5 py-4 font-mono text-sm text-[#A9B7C6] space-y-3">

                    {{-- Linha de prompt + URL --}}
                    <div class="flex items-start gap-2 flex-wrap">
                        <span class="text-[#4ec9b0] select-none flex-shrink-0">$</span>
                        <span class="break-all">curl -sI&nbsp;<a href="{{ $connectionTest['url'] }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="text-[#6A9FB5] underline hover:opacity-75 transition-opacity">{{ $connectionTest['url'] }}</a></span>
                    </div>

                    {{-- Output: erro ou resposta OK --}}
                    @if($connectionTest['error'])
                        <div class="pl-4 border-l-2 border-[#3c3c3c]">
                            <pre class="whitespace-pre-wrap text-xs leading-relaxed text-rose-400">{{ $connectionTest['error'] }}</pre>
                        </div>
                    @elseif($connectionTest['success'])
                        <div class="pl-4 border-l-2 border-[#3c3c3c] text-xs">
                            <span class="text-[#28c840]">HTTP/</span><span class="text-[#6897BB]">{{ $connectionTest['status_code'] }}</span>
                            <span class="text-[#A9B7C6] ml-2">OK</span>
                        </div>
                    @endif

                    {{-- Footer: metadados --}}
                    <div class="flex items-center gap-4 pt-2 text-[10px] text-[#5a5a5a] border-t border-[#2a2a2a]">
                        @if($connectionTest['status_code'])
                            <span>
                                HTTP&nbsp;<span class="{{ $connectionTest['success'] ? 'text-[#28c840]' : 'text-[#CF6679]' }}">{{ $connectionTest['status_code'] }}</span>
                            </span>
                        @endif
                        <span>{{ $connectionTest['duration_ms'] }}ms</span>
                        @if(($connectionTest['html_size'] ?? 0) > 0)
                            <span>{{ number_format($connectionTest['html_size'] / 1024, 0) }} KB</span>
                        @endif
                        @if($connectionTest['error'])
                            <span class="text-[#CF6679]">exit: error</span>
                        @endif
                    </div>
                </div>
            </div>
        </x-das.section>

        {{-- SEÇÃO 2: Metadados do Scraper --}}
        @if(!empty($scraperMeta))
        <x-das.section title="Pipeline de Extração">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                {{-- Source --}}
                <div class="rounded-xl border border-slate-200 dark:border-[#3E3E3A] p-3">
                    <p class="text-[10px] uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500 mb-1">Fonte</p>
                    <p class="text-sm font-bold {{ $scraperMeta['source'] === 'site_planalto' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                        {{ $scraperMeta['source'] === 'site_planalto' ? 'Planalto (Online)' : 'Fallback (Local)' }}
                    </p>
                </div>
                {{-- Parser --}}
                <div class="rounded-xl border border-slate-200 dark:border-[#3E3E3A] p-3">
                    <p class="text-[10px] uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500 mb-1">Parser</p>
                    <p class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $scraperMeta['parser'] }}</p>
                </div>
                {{-- Faixas × Campos --}}
                <div class="rounded-xl border border-slate-200 dark:border-[#3E3E3A] p-3">
                    <p class="text-[10px] uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500 mb-1">Estrutura</p>
                    <p class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $scraperMeta['faixas'] }} faixas × {{ $scraperMeta['campos'] }} campos</p>
                </div>
                {{-- Duration --}}
                <div class="rounded-xl border border-slate-200 dark:border-[#3E3E3A] p-3">
                    <p class="text-[10px] uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500 mb-1">Tempo Total</p>
                    <p class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $scraperMeta['duration_ms'] }}ms</p>
                </div>
                {{-- Encoding --}}
                <div class="rounded-xl border border-slate-200 dark:border-[#3E3E3A] p-3">
                    <p class="text-[10px] uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500 mb-1">Encoding</p>
                    <p class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $scraperMeta['encoding'] }}</p>
                </div>
                {{-- Checked At --}}
                <div class="rounded-xl border border-slate-200 dark:border-[#3E3E3A] p-3">
                    <p class="text-[10px] uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500 mb-1">Verificado em</p>
                    <p class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $scraperMeta['checked_at'] }}</p>
                </div>
            </div>
        </x-das.section>
        @endif

        {{-- SEÇÃO 3: Dados Extraídos pelo Scraper --}}
        <x-das.section title="Dados Extraídos pelo Scraper">
            <div class="space-y-4">
                {{-- Badge de status --}}
                <div class="flex flex-wrap items-center gap-3">
                    @if(!$usedFallback)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            SCRAPING WEB ATIVO
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            FALLBACK APLICADO
                        </span>
                    @endif
                    <span class="text-xs das-text-muted">{{ count($scraped) }} faixa(s) retornadas</span>
                </div>

                {{-- Nota informativa sobre fallback --}}
                @if($usedFallback && $connectionTest['error'])
                    <div class="rounded-xl border border-slate-200 dark:border-[#2a2a2a] bg-slate-50 dark:bg-[#161615] px-4 py-3">
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            <span class="font-semibold">ℹ️ Modo de segurança:</span>
                            A conexão com o Planalto falhou, provavelmente por SSL/rede. Os dados do fallback são idênticos à legislação vigente e garantem a precisão dos cálculos.
                        </p>
                    </div>
                @endif

                {{-- Dados extraídos: cards mobile + tabela desktop --}}
                @if(!empty($scraped))
                    {{-- Cards: mobile (< 640px) --}}
                    <div class="sm:hidden space-y-3">
                        @foreach($scraped as $index => $row)
                            <div class="das-card p-4">
                                <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-[#2a2a2a] pb-2">
                                    <span class="text-xs font-bold text-primary-600 dark:text-primary-400 uppercase tracking-tighter">Faixa #{{ $index + 1 }}</span>
                                    @if(isset($row['FAIXA']))
                                        <span class="text-[10px] font-mono das-text-muted">ID: {{ $row['FAIXA'] }}</span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                                    @foreach($row as $key => $value)
                                        @if($key !== 'FAIXA')
                                            <div>
                                                <p class="text-[10px] font-semibold uppercase tracking-tight text-slate-400 dark:text-slate-500 mb-0.5">{{ $key }}</p>
                                                <p class="text-xs font-mono font-medium text-slate-700 dark:text-slate-300 break-all">{{ $value !== '' && $value !== null ? $value : '—' }}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Tabela: desktop (>= 640px) --}}
                    <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-200 dark:border-[#3E3E3A]">
                        <table class="min-w-max w-full text-xs font-mono">
                            <thead class="bg-slate-50 dark:bg-[#161615] border-b border-slate-200 dark:border-[#3E3E3A]">
                                <tr>
                                    @foreach(array_keys($scraped[0]) as $key)
                                        <th class="px-3 py-2.5 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-[10px]">
                                            {{ $key }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-[#2a2a2a]">
                                @foreach($scraped as $row)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-[#161615] transition-colors">
                                        @foreach($row as $key => $value)
                                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm das-text-muted text-center py-4">Nenhum dado retornado.</p>
                @endif
            </div>
        </x-das.section>

        {{-- SEÇÃO 4: Dados de Fallback (versão versionada) --}}
        <x-das.section title="Fallback — Dados de Referência (Versionados)">
            <div class="space-y-3">
                <p class="text-xs das-text-muted">
                    Dados completos com 11 campos — usados como fallback quando o scraping falha ou retorna dados incompletos.
                </p>

                @if(!empty($fallback))
                    {{-- Cards: mobile (< 640px) --}}
                    <div class="sm:hidden space-y-3">
                        @foreach($fallback as $index => $row)
                            <div class="das-card p-4">
                                <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-[#2a2a2a] pb-2">
                                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-tighter">Referência Faixa #{{ $index + 1 }}</span>
                                    <span class="text-[10px] font-mono das-text-muted">HARDCODED</span>
                                </div>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                                    @foreach($row as $key => $value)
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-tight text-slate-400 dark:text-slate-500 mb-0.5">{{ $key }}</p>
                                            <p class="text-xs font-mono font-medium text-slate-700 dark:text-slate-300 break-all">{{ $value !== '' && $value !== null ? $value : '—' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Tabela: desktop (>= 640px) --}}
                    <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-200 dark:border-[#3E3E3A]">
                        <table class="min-w-max w-full text-xs font-mono">
                            <thead class="bg-slate-50 dark:bg-[#161615] border-b border-slate-200 dark:border-[#3E3E3A]">
                                <tr>
                                    @foreach(array_keys($fallback[0]) as $key)
                                        <th class="px-3 py-2.5 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-[10px]">
                                            {{ $key }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-[#2a2a2a]">
                                @foreach($fallback as $row)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-[#161615] transition-colors">
                                        @foreach($row as $value)
                                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </x-das.section>

        {{-- SEÇÃO 5: Resultado do Comparador --}}
        <x-das.section title="Resultado do Comparador (checkForUpdates)">
            <div class="space-y-4">
                {{-- Status badge --}}
                @php
                    $status = $comparisonResult['status'] ?? 'error';
                    $source = $comparisonResult['source'] ?? '';
                @endphp

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" x-data="{ showConfirm: false }">
                    <div class="flex flex-wrap items-center gap-3">
                        @if($status === 'uptodate' || $corrected)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                SINCRONIZADO
                            </span>
                        @elseif($status === 'outdated')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                DESATUALIZADO
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                ERRO
                            </span>
                        @endif

                        <span class="text-xs font-mono px-2 py-1 rounded bg-slate-100 dark:bg-[#1a1a1a] text-slate-600 dark:text-slate-300">
                            source: {{ $source ?: 'N/A' }}
                        </span>
                    </div>

                    {{-- Botão Corrigir no lado direito do header --}}
                    @if($status === 'outdated' && !$corrected)
                        @if($comparisonResult['source'] !== 'fallback')
                            <button @click="showConfirm = true"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Corrigir Tabelas
                            </button>
                        @else
                            <button disabled
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 text-sm font-semibold text-slate-400 bg-slate-200 dark:bg-[#2a2a2a] dark:text-slate-500 rounded-lg cursor-not-allowed"
                                    title="Dados de fallback não são confiáveis para atualização automática">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Corrigir Tabelas
                            </button>
                        @endif
                    @endif

                    {{-- Modal de Confirmação Alpine.js --}}
                    <div x-show="showConfirm"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 flex items-center justify-center p-4"
                         style="display: none;">
                        {{-- Backdrop --}}
                        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showConfirm = false"></div>

                        {{-- Modal --}}
                        <div x-show="showConfirm"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="relative w-full max-w-md rounded-2xl bg-white dark:bg-[#1a1a1a] border border-slate-200 dark:border-[#3E3E3A] shadow-2xl p-6 space-y-4">

                            {{-- Ícone --}}
                            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30 mx-auto">
                                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>

                            {{-- Texto --}}
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Corrigir Tabelas Tributárias</h3>
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                    Tem certeza que deseja atualizar as <strong>{{ count($comparisonResult['differences']) }} campo(s)</strong>
                                    das tabelas locais com os dados oficiais do Planalto?
                                </p>
                                <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                                    Os novos valores serão usados imediatamente na calculadora DAS.
                                </p>
                            </div>

                            {{-- Botões --}}
                            <div class="flex gap-3 pt-2">
                                <button @click="showConfirm = false"
                                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#2a2a2a] hover:bg-slate-200 dark:hover:bg-[#333] rounded-xl transition-colors">
                                    Cancelar
                                </button>
                                <button @click="showConfirm = false; $wire.applyCorrections()"
                                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors shadow-sm">
                                    Sim, Corrigir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Feedback de correção --}}
                @if($correctionMessage)
                    <div class="rounded-lg px-4 py-3 text-sm {{ $corrected ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800' }}">
                        @if($corrected)
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                        {{ $correctionMessage }}
                    </div>
                @endif

                {{-- Diferenças --}}
                @if(!empty($comparisonResult['differences']) && !$corrected)
                    <div>
                        <p class="text-xs font-semibold das-text-muted mb-2">
                            {{ count($comparisonResult['differences']) }} diferença(s) encontrada(s):
                        </p>
                        {{-- Cards: mobile --}}
                        <div class="sm:hidden space-y-3">
                            @foreach($comparisonResult['differences'] as $diff)
                                <div class="das-card p-4 border-l-4 border-l-amber-500">
                                    <div class="flex items-center justify-between mb-3 border-b border-slate-100 dark:border-[#2a2a2a] pb-2">
                                        <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-tighter">{{ $diff['faixa'] ?? '-' }}ª Faixa</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">{{ $diff['field'] ?? '-' }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-tight text-slate-400 dark:text-slate-500 mb-1">Local</p>
                                            <p class="text-sm font-mono font-bold text-rose-600 dark:text-rose-400">{{ $diff['current_value'] ?? $diff['local'] ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-tight text-slate-400 dark:text-slate-500 mb-1">Oficial (Web)</p>
                                            <p class="text-sm font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ $diff['official_value'] ?? $diff['official'] ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Tabela: desktop --}}
                        <div class="hidden sm:block overflow-x-auto rounded-xl border border-slate-200 dark:border-[#3E3E3A]">
                            <table class="min-w-max w-full text-xs font-mono">
                                <thead class="bg-slate-50 dark:bg-[#161615] border-b border-slate-200 dark:border-[#3E3E3A]">
                                    <tr>
                                        <th class="px-3 py-2.5 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-[10px]">Faixa</th>
                                        <th class="px-3 py-2.5 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-[10px]">Campo</th>
                                        <th class="px-3 py-2.5 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-[10px]">Local</th>
                                        <th class="px-3 py-2.5 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-[10px]">Oficial</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-[#2a2a2a]">
                                    @foreach($comparisonResult['differences'] as $diff)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-[#161615] transition-colors">
                                            <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $diff['faixa'] ?? '-' }}</td>
                                            <td class="px-3 py-2 text-amber-600 dark:text-amber-400">{{ $diff['field'] ?? '-' }}</td>
                                            <td class="px-3 py-2 text-rose-600 dark:text-rose-400">{{ $diff['current_value'] ?? $diff['local'] ?? '-' }}</td>
                                            <td class="px-3 py-2 text-emerald-600 dark:text-emerald-400">{{ $diff['official_value'] ?? $diff['official'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif($status === 'uptodate' || $corrected)
                    <p class="text-xs das-text-muted">Nenhuma diferença encontrada — tabelas locais sincronizadas.</p>
                @endif

                {{-- JSON expansível com syntax highlighting --}}
                <div x-data="{ open: false, rendered: '' }"
                     x-init="rendered = window.highlightJson(@js($comparisonResult))">
                    <button @click="open = !open"
                            class="flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span x-text="open ? 'Ocultar JSON' : 'Ver payload completo (JSON)'"></span>
                    </button>
                    <div x-show="open" x-collapse class="mt-3">
                        <div class="rounded-xl bg-[#1e1e1e] border border-[#3c3c3c] p-4 overflow-x-auto shadow-xl">
                            <pre class="text-xs font-mono leading-relaxed"
                                 style="color: #A9B7C6;"
                                 x-html="rendered"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </x-das.section>

    @endif

</div>

<script>
window.highlightJson = function(jsonData) {
    var str = JSON.stringify(jsonData, null, 4);
    // Escape HTML entities
    str = str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    // Keys em ciano
    str = str.replace(/"([^"]+?)"(?=\s*:)/g,
        '<span style="color:#9CDCFE">"$1"</span>');
    // String values em laranja
    str = str.replace(/:\s*"([^"]*?)"/g,
        ': <span style="color:#CE9178">"$1"</span>');
    // Números em verde-claro
    str = str.replace(/:\s*(-?\d+\.?\d*)/g,
        ': <span style="color:#B5CEA8">$1</span>');
    // null, true, false em azul
    str = str.replace(/\b(null|true|false)\b/g,
        '<span style="color:#569CD6">$1</span>');
    // Brackets e chaves em amarelo
    str = str.replace(/([{}\[\]])/g,
        '<span style="color:#FFD700">$1</span>');
    return str;
};
</script>
