<div>
    {{-- Tabela 1: Alíquotas e Parcela a Deduzir --}}
    <div class="bg-white dark:bg-[#161615] p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-[#3E3E3A] mb-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-white mb-2 font-heading">
            Tabela de Alíquotas e Parcela a Deduzir — Anexo III
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
            Para editar, <strong>clique sobre os valores</strong> de Alíquota ou Parcela a Deduzir, altere e pressione <kbd class="px-1 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-xs font-mono">Enter</kbd>.
        </p>

        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-[#3E3E3A]">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-[#3E3E3A]">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase">Faixa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase">Receita Bruta (RBT12)</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase">Alíquota (%)</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase">Deduzir (R$)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#3E3E3A]/50">
                    @foreach($brackets as $index => $row)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                {{ $row['faixa'] }}ª
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                @if($row['min_rbt12'] == 0)
                                    Até R$ {{ number_format($row['max_rbt12'], 2, ',', '.') }}
                                @else
                                    De R$ {{ number_format($row['min_rbt12'], 2, ',', '.') }} a R$ {{ number_format($row['max_rbt12'], 2, ',', '.') }}
                                @endif
                            </td>
                            
                            {{-- Field Edit: Aliquota Nominal --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right relative">
                                <div x-data="{ editing: false, val: '{{ number_format($row['aliquota_nominal'] * 100, 2, '.', '') }}' }" @click.away="editing = false">
                                    <span x-show="!editing" 
                                          @click="editing = true; $nextTick(() => $refs.aliquota_{{ $index }}.focus())" 
                                          class="cursor-pointer border-b border-dashed border-indigo-300 dark:border-indigo-600 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors text-slate-800 dark:text-slate-200">
                                        <span x-text="Number(val).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>%
                                    </span>
                                    
                                    <input x-show="editing" x-ref="aliquota_{{ $index }}" x-model="val" type="text"
                                           @keydown.enter="$wire.updateBracket({{ $index }}, 'aliquota_nominal', val); editing = false"
                                           @keydown.escape="editing = false"
                                           class="w-20 px-2 py-1 text-right text-sm border-2 border-indigo-500 rounded-md focus:outline-none focus:ring-0 shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white absolute right-6 top-1/2 -translate-y-1/2" />
                                </div>
                            </td>

                            {{-- Field Edit: Deducao --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right relative">
                                <div x-data="{ editing: false, val: '{{ number_format($row['deducao'], 2, '.', '') }}' }" @click.away="editing = false">
                                    <span x-show="!editing" 
                                          @click="editing = true; $nextTick(() => $refs.deducao_{{ $index }}.focus())" 
                                          class="cursor-pointer border-b border-dashed border-rose-300 dark:border-rose-600 hover:text-rose-600 dark:hover:text-rose-400 transition-colors text-slate-800 dark:text-slate-200">
                                        R$ <span x-text="Number(val).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </span>
                                    
                                    <input x-show="editing" x-ref="deducao_{{ $index }}" x-model="val" type="text"
                                           @keydown.enter="$wire.updateBracket({{ $index }}, 'deducao', val); editing = false"
                                           @keydown.escape="editing = false"
                                           class="w-24 px-2 py-1 text-right text-sm border-2 border-indigo-500 rounded-md focus:outline-none focus:ring-0 shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white absolute right-6 top-1/2 -translate-y-1/2" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    {{-- Tabela 2: Repartição dos Tributos --}}
    <div class="bg-white dark:bg-[#161615] p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-[#3E3E3A]">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-white mb-2 font-heading">
            Tabela de Percentual de Repartição dos Tributos — Anexo III
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
            Dê um <strong>clique sobre qualquer percentual</strong> para editá-lo. Pressione <kbd class="px-1 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-xs font-mono">Enter</kbd> para confirmar a alteração.
        </p>

        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-[#3E3E3A]">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-[#3E3E3A]">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase">Faixa</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase">IRPJ</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase">CSLL</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase">Cofins</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase">PIS/Pasep</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase">CPP</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wider uppercase">ISS (*)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#3E3E3A]/50">
                    @foreach($brackets as $index => $row)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                {{ $row['faixa'] }}ª
                            </td>
                            
                            @foreach(['irpj', 'csll', 'cofins', 'pis', 'cpp', 'iss'] as $tfield)
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-right relative">
                                    <div x-data="{ editing: false, val: '{{ number_format($row[$tfield] * 100, 2, '.', '') }}' }" @click.away="editing = false">
                                        <span x-show="!editing" 
                                              @click="editing = true; $nextTick(() => $refs.field_{{ $tfield }}_{{ $index }}.focus())" 
                                              class="cursor-pointer border-b border-dashed border-emerald-300 dark:border-emerald-700 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors text-slate-600 dark:text-slate-300">
                                            <span x-text="Number(val).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>%
                                        </span>
                                        
                                        <input x-show="editing" x-ref="field_{{ $tfield }}_{{ $index }}" x-model="val" type="text"
                                               @keydown.enter="$wire.updateBracket({{ $index }}, '{{ $tfield }}', val); editing = false"
                                               @keydown.escape="editing = false"
                                               class="w-16 px-1 py-1 text-right text-sm border-2 border-indigo-500 rounded-md focus:outline-none focus:ring-0 shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white absolute right-4 top-1/2 -translate-y-1/2" />
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">
            (*) O percentual de ISS será fixo em 5% quando a empresa for impedida de optar pelo Simples Nacional, em razão de lei complementar ou lei orgânica municipal.
        </p>
    </div>
</div>
