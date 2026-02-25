<div class="space-y-8">

    {{-- Header: Título + Botão --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm das-text-muted mt-1">
                Inspeciona a saída do scraper tributário, a qualidade dos dados extraídos e verifica se o fallback está sendo aplicado silenciosamente.
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
            <style>
                .diag-terminal .diag-token-url     { color: #6A9FB5; text-decoration: underline; cursor: pointer; }
                .diag-terminal .diag-token-keyword { color: #CC7832; }
                .diag-terminal .diag-token-number  { color: #6897BB; }
                .diag-terminal .diag-token-method  { color: #FFC66D; }
                .diag-terminal .diag-token-errcode { color: #CF6679; }
            </style>

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
                               class="diag-token-url hover:opacity-75 transition-opacity">{{ $connectionTest['url'] }}</a></span>
                    </div>

                    {{-- Output: erro ou resposta OK --}}
                    @if($connectionTest['error'])
                        <div class="pl-4 border-l-2 border-[#3c3c3c]">
                            <pre class="whitespace-pre-wrap text-xs leading-relaxed">{!! $this->highlightCurlError($connectionTest['error']) !!}</pre>
                        </div>
                    @elseif($connectionTest['success'])
                        <div class="pl-4 border-l-2 border-[#3c3c3c] text-xs">
                            <span class="text-[#28c840]">HTTP/</span><span class="diag-token-number">{{ $connectionTest['status_code'] }}</span>
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
                        @if($connectionTest['error'])
                            <span class="text-[#CF6679]">exit: error</span>
                        @endif
                    </div>
                </div>
            </div>
        </x-das.section>

        {{-- SEÇÃO 2: Dados Extraídos (Web) --}}
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
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            FALLBACK APLICADO
                        </span>
                    @endif
                    <span class="text-xs das-text-muted">{{ count($scraped) }} faixa(s) retornadas</span>
                </div>

                {{-- Alerta sobre bug do parseRow --}}
                @php
                    $firstScraped = $scraped[0] ?? [];
                    $missingTribute = array_diff(\App\Livewire\ScraperDiagnostic::$tributeFields, array_keys($firstScraped));
                @endphp

                @if(!empty($missingTribute))
                    <div class="rounded-xl border border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 px-4 py-3">
                        <p class="text-xs font-semibold text-amber-800 dark:text-amber-300 mb-1">
                            Bug confirmado: <code class="font-mono">parseRow()</code> extrai apenas {{ count($firstScraped) }} campos
                        </p>
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            Campos ausentes: <span class="font-mono font-semibold">{{ implode(', ', $missingTribute) }}</span>
                        </p>
                        <p class="text-xs text-amber-600 dark:text-amber-500 mt-1">
                            Os tributos por repartição nunca são extraídos da fonte online — o fallback hardcoded sempre supre esses dados.
                        </p>
                    </div>
                @endif

                {{-- Tabela dos dados extraídos --}}
                @if(!empty($scraped))
                    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-[#3E3E3A]">
                        <table class="w-full text-xs font-mono">
                            <thead class="bg-slate-50 dark:bg-[#161615] border-b border-slate-200 dark:border-[#3E3E3A]">
                                <tr>
                                    @foreach(array_keys($scraped[0]) as $key)
                                        <th class="px-3 py-2.5 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-[10px]
                                            {{ in_array($key, \App\Livewire\ScraperDiagnostic::$tributeFields) ? 'text-rose-400 dark:text-rose-500' : '' }}">
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

                {{-- Campos ausentes destacados --}}
                @if(!empty($missingTribute))
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 mt-2">
                        @foreach(\App\Livewire\ScraperDiagnostic::$tributeFields as $field)
                            @php $present = isset($firstScraped[$field]); @endphp
                            <div class="rounded-lg px-3 py-2 text-center text-xs font-mono font-semibold
                                {{ $present
                                    ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800'
                                    : 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800' }}">
                                {{ $field }}
                                <div class="text-[10px] font-normal mt-0.5 opacity-70">
                                    {{ $present ? 'presente' : 'ausente' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-das.section>

        {{-- SEÇÃO 3: OFFICIAL_BRACKETS (referência hardcoded) --}}
        <x-das.section title="OFFICIAL_BRACKETS — Referência Hardcoded">
            <div class="space-y-3">
                <p class="text-xs das-text-muted">
                    Dados completos com 11 campos — usados como fallback quando o scraping falha.
                </p>

                @if(!empty($fallback))
                    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-[#3E3E3A]">
                        <table class="w-full text-xs font-mono">
                            <thead class="bg-slate-50 dark:bg-[#161615] border-b border-slate-200 dark:border-[#3E3E3A]">
                                <tr>
                                    @foreach(array_keys($fallback[0]) as $key)
                                        <th class="px-3 py-2.5 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-[10px]
                                            {{ in_array($key, \App\Livewire\ScraperDiagnostic::$tributeFields) ? 'text-primary-500 dark:text-primary-400' : '' }}">
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

        {{-- SEÇÃO 4: Resultado do Comparador --}}
        <x-das.section title="Resultado do Comparador (checkForUpdates)">
            <div class="space-y-4">
                {{-- Status badge --}}
                @php
                    $status = $comparisonResult['status'] ?? 'error';
                    $source = $comparisonResult['source'] ?? '';
                    $sourceMismatch = $source === 'site_planalto' && $usedFallback;
                @endphp

                <div class="flex flex-wrap items-center gap-3">
                    @if($status === 'uptodate')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            ATUALIZADO
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

                {{-- Alerta de observabilidade --}}
                @if($sourceMismatch)
                    <div class="rounded-xl border border-rose-300 dark:border-rose-700 bg-rose-50 dark:bg-rose-900/20 px-4 py-3">
                        <p class="text-xs font-semibold text-rose-800 dark:text-rose-300 mb-1">
                            Bug de observabilidade detectado
                        </p>
                        <p class="text-xs text-rose-700 dark:text-rose-400">
                            <code class="font-mono">checkForUpdates()</code> reporta <code class="font-mono">source = "site_planalto"</code>,
                            mas o fallback foi aplicado na prática (campos de tributos ausentes ou conexão falhou).
                        </p>
                    </div>
                @endif

                {{-- Diferenças --}}
                @if(!empty($comparisonResult['differences']))
                    <div>
                        <p class="text-xs font-semibold das-text-muted mb-2">
                            {{ count($comparisonResult['differences']) }} diferença(s) encontrada(s):
                        </p>
                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-[#3E3E3A]">
                            <table class="w-full text-xs font-mono">
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
                                            <td class="px-3 py-2 text-rose-600 dark:text-rose-400">{{ $diff['local'] ?? '-' }}</td>
                                            <td class="px-3 py-2 text-emerald-600 dark:text-emerald-400">{{ $diff['official'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif($status === 'uptodate')
                    <p class="text-xs das-text-muted">Nenhuma diferença encontrada — tabelas locais sincronizadas.</p>
                @endif

                {{-- JSON expansível --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span x-text="open ? 'Ocultar JSON' : 'Ver payload completo (JSON)'"></span>
                    </button>
                    <div x-show="open" x-collapse class="mt-3">
                        <pre class="rounded-xl bg-slate-900 dark:bg-[#0d0d0d] text-slate-200 p-4 text-xs overflow-x-auto border border-slate-700 dark:border-[#2a2a2a]">{{ json_encode($comparisonResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        </x-das.section>

    @endif

</div>
