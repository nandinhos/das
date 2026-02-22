<div>
    <x-das.section title="Tabela de Alíquotas e Parcela a Deduzir — Anexo III">
        <p class="text-sm das-text-muted mb-4">
            Para editar, <strong>clique sobre os valores</strong> de Alíquota ou Parcela a Deduzir, altere e pressione <kbd class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-xs font-mono">Enter</kbd>.
        </p>

        <x-das.table-wrapper>
            <x-das.table>
                <thead>
                    <tr>
                        <th>Faixa</th>
                        <th>Receita Bruta (RBT12)</th>
                        <th class="text-right">Alíquota (%)</th>
                        <th class="text-right">Deduzir (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brackets as $index => $row)
                        <tr>
                            <td class="font-medium">{{ $row['faixa'] }}ª</td>
                            <td class="das-text-secondary">
                                @if($row['min_rbt12'] == 0)
                                    Até R$ {{ number_format($row['max_rbt12'], 2, ',', '.') }}
                                @else
                                    De R$ {{ number_format($row['min_rbt12'], 2, ',', '.') }} a R$ {{ number_format($row['max_rbt12'], 2, ',', '.') }}
                                @endif
                            </td>
                            
                            <td class="text-right relative">
                                <div x-data="{ editing: false, val: '{{ number_format($row['aliquota_nominal'] * 100, 2, '.', '') }}' }" @click.away="editing = false">
                                    <span x-show="!editing" 
                                          @click="editing = true; $nextTick(() => $refs.aliquota_{{ $index }}.focus())" 
                                          class="cursor-pointer border-b border-dashed border-primary-300 dark:border-primary-600 hover:text-primary-500 dark:hover:text-primary-400 transition-colors">
                                        <span x-text="Number(val).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>%
                                    </span>
                                    
                                    <input x-show="editing" x-ref="aliquota_{{ $index }}" x-model="val" type="text"
                                           @keydown.enter="$wire.updateBracket({{ $index }}, 'aliquota_nominal', val); editing = false"
                                           @keydown.escape="editing = false"
                                           class="w-20 px-2 py-1 text-right text-sm border-2 border-primary-500 rounded-md focus:outline-none focus:ring-0 shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white absolute right-4 top-1/2 -translate-y-1/2" />
                                </div>
                            </td>

                            <td class="text-right relative">
                                <div x-data="{ editing: false, val: '{{ number_format($row['deducao'], 2, '.', '') }}' }" @click.away="editing = false">
                                    <span x-show="!editing" 
                                          @click="editing = true; $nextTick(() => $refs.deducao_{{ $index }}.focus())" 
                                          class="cursor-pointer border-b border-dashed border-red-300 dark:border-red-600 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                        R$ <span x-text="Number(val).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </span>
                                    
                                    <input x-show="editing" x-ref="deducao_{{ $index }}" x-model="val" type="text"
                                           @keydown.enter="$wire.updateBracket({{ $index }}, 'deducao', val); editing = false"
                                           @keydown.escape="editing = false"
                                           class="w-24 px-2 py-1 text-right text-sm border-2 border-primary-500 rounded-md focus:outline-none focus:ring-0 shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white absolute right-4 top-1/2 -translate-y-1/2" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-das.table>
        </x-das.table-wrapper>
    </x-das.section>

    <x-das.section title="Tabela de Percentual de Repartição dos Tributos — Anexo III">
        <p class="text-sm das-text-muted mb-4">
            Dê um <strong>clique sobre qualquer percentual</strong> para editá-lo. Pressione <kbd class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-xs font-mono">Enter</kbd> para confirmar a alteração.
        </p>

        <x-das.table-wrapper>
            <x-das.table>
                <thead>
                    <tr>
                        <th>Faixa</th>
                        <th class="text-right">IRPJ</th>
                        <th class="text-right">CSLL</th>
                        <th class="text-right">Cofins</th>
                        <th class="text-right">PIS/Pasep</th>
                        <th class="text-right">CPP</th>
                        <th class="text-right">ISS (*)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brackets as $index => $row)
                        <tr>
                            <td class="font-medium">{{ $row['faixa'] }}ª</td>
                            
                            @foreach(['irpj', 'csll', 'cofins', 'pis', 'cpp', 'iss'] as $tfield)
                                <td class="text-right relative">
                                    <div x-data="{ editing: false, val: '{{ number_format($row[$tfield] * 100, 2, '.', '') }}' }" @click.away="editing = false">
                                        <span x-show="!editing" 
                                              @click="editing = true; $nextTick(() => $refs.field_{{ $tfield }}_{{ $index }}.focus())" 
                                              class="cursor-pointer border-b border-dashed border-emerald-300 dark:border-emerald-700 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                            <span x-text="Number(val).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>%
                                        </span>
                                        
                                        <input x-show="editing" x-ref="field_{{ $tfield }}_{{ $index }}" x-model="val" type="text"
                                               @keydown.enter="$wire.updateBracket({{ $index }}, '{{ $tfield }}', val); editing = false"
                                               @keydown.escape="editing = false"
                                               class="w-16 px-1 py-1 text-right text-sm border-2 border-primary-500 rounded-md focus:outline-none focus:ring-0 shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white absolute right-4 top-1/2 -translate-y-1/2" />
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </x-das.table>
        </x-das.table-wrapper>
        <p class="mt-4 text-xs font-medium das-text-muted uppercase tracking-wide">
            (*) O percentual de ISS será fixo em 5% quando a empresa for impedida de optar pelo Simples Nacional, em razão de lei complementar ou lei orgânica municipal.
        </p>
    </x-das.section>
</div>
