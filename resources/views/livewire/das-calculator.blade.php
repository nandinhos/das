<div>
    <x-das.section title="Calcular DAS para Período de Apuração">
        <form wire:submit="calculate" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-das.select
                    id="calc-month"
                    label="Mês de Apuração"
                    wire:model="month"
                >
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}">{{ $name }}</option>
                    @endforeach
                </x-das.select>

                <x-das.select
                    id="calc-year"
                    label="Ano"
                    wire:model="year"
                >
                    @foreach($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </x-das.select>
            </div>

            @if($errorMessage)
                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $errorMessage }}</p>
                </div>
            @endif

            <div class="flex justify-end">
                <x-das.button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="calculate">Calcular DAS</span>
                    <span wire:loading wire:target="calculate">Calculando...</span>
                </x-das.button>
            </div>
        </form>
    </x-das.section>

    @if($result)
        <x-das.section title="Resultado do Cálculo">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div class="das-card p-4">
                    <p class="text-sm das-text-muted mb-1">Período de Apuração (PA)</p>
                    <p class="text-lg font-semibold das-text">{{ $months[$result['month']] }}/{{ $result['year'] }}</p>
                    <p class="text-sm das-text-muted mt-3 mb-1">Receita Bruta do PA (RPA)</p>
                    <p class="text-lg font-semibold das-text">R$ {{ number_format($result['rpa'], 2, ',', '.') }}</p>
                </div>

                <div class="das-card p-4">
                    <p class="text-sm das-text-muted mb-1">Receita Bruta 12 meses (RBT12)</p>
                    <p class="text-lg font-semibold das-text">R$ {{ number_format($result['rbt12'], 2, ',', '.') }}</p>
                </div>

                <div class="das-card p-4">
                    <p class="text-sm das-text-muted mb-1">Faixa de Tributação</p>
                    <p class="text-lg font-semibold das-text">{{ $result['tax_bracket'] }}ª Faixa</p>
                </div>

                <div class="das-card p-4">
                    <p class="text-sm das-text-muted mb-1">Alíquota Nominal</p>
                    <p class="text-lg font-semibold das-text">{{ number_format($result['aliquota_nominal'] * 100, 2, ',', '.') }}%</p>
                    <p class="text-sm das-text-muted mt-3 mb-1">Parcela a Deduzir</p>
                    <p class="text-lg font-semibold das-text">R$ {{ number_format($result['parcela_deduzir'], 2, ',', '.') }}</p>
                </div>

                <div class="das-card p-4">
                    <p class="text-sm das-text-muted mb-1">Alíquota Efetiva</p>
                    <p class="text-lg font-semibold das-text">{{ number_format($result['aliquota_efetiva'] * 100, 2, ',', '.') }}%</p>
                </div>

                <div class="das-card p-4 text-white" style="background: linear-gradient(135deg, oklch(70.7% 0.165 254.624), #2563eb);">
                    <p class="text-sm opacity-90 mb-1">Valor Total DAS (Principal)</p>
                    <p class="text-xl font-bold">R$ {{ number_format($result['valor_total_das'], 2, ',', '.') }}</p>
                </div>
            </div>

            <div class="das-card p-6 mb-6"
                 x-data="{ open: false }">
                <button 
                    @click="open = !open" 
                    type="button"
                    class="flex items-center justify-between w-full text-left"
                >
                    <h3 class="text-base font-semibold das-text">Passos do Cálculo</h3>
                    <svg :class="open ? 'rotate-180' : ''"
                         class="h-5 w-5 das-text-muted transition-transform duration-200"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-collapse class="mt-4 das-steps space-y-4">
                    <div class="das-step">
                        <div class="flex-1">
                            <h4 class="font-medium das-text">Receita Bruta do Período de Apuração (RPA)</h4>
                            <p class="text-sm das-text-secondary mt-1">Valor da RPA: R$ {{ number_format($result['rpa'], 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="das-step">
                        <div class="flex-1">
                            <h4 class="font-medium das-text">Cálculo da Receita Bruta Acumulada em 12 meses (RBT12)</h4>
                            <p class="text-sm das-text-secondary mt-1">Soma das receitas dos 12 meses anteriores: R$ {{ number_format($result['rbt12'], 2, ',', '.') }}</p>
                            <div class="mt-2 text-sm das-text-muted border-l-2 border-slate-200 dark:border-slate-700 pl-3 space-y-1">
                                @php
                                    $sorted = collect($result['rbt12_data'])
                                        ->sortByDesc('year')
                                        ->sortByDesc(fn($m) => $m['year'] * 100 + $m['month']);
                                @endphp
                                @foreach($sorted as $m)
                                    <div>{{ $months[$m['month']] }}/{{ $m['year'] }}: R$ {{ number_format($m['amount'], 2, ',', '.') }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="das-step">
                        <div class="flex-1">
                            <h4 class="font-medium das-text">Identificação da Faixa de Tributação</h4>
                            <p class="text-sm das-text-secondary mt-1">RBT12 R$ {{ number_format($result['rbt12'], 2, ',', '.') }} se encaixa na {{ $result['tax_bracket'] }}ª Faixa</p>
                        </div>
                    </div>

                    <div class="das-step">
                        <div class="flex-1">
                            <h4 class="font-medium das-text">Cálculo da Alíquota Efetiva</h4>
                            <p class="text-sm das-text-secondary mt-1">
                                @if($result['special_case'])
                                    Caso especial — RBT12 ≤ R$ 180.000,00:<br>
                                    Alíquota Efetiva = Alíquota Nominal = {{ number_format($result['aliquota_nominal'] * 100, 2, ',', '.') }}%
                                @else
                                    ((R$ {{ number_format($result['rbt12'], 2, ',', '.') }}
                                    × {{ number_format($result['aliquota_nominal'] * 100, 2, ',', '.') }}%)
                                    − R$ {{ number_format($result['parcela_deduzir'], 2, ',', '.') }})
                                    ÷ R$ {{ number_format($result['rbt12'], 2, ',', '.') }}
                                    = {{ number_format($result['aliquota_efetiva'] * 100, 2, ',', '.') }}%
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="das-step">
                        <div class="flex-1">
                            <h4 class="font-medium das-text">Cálculo do Valor Total da DAS</h4>
                            <p class="text-sm das-text-secondary mt-1">
                                R$ {{ number_format($result['rpa'], 2, ',', '.') }}
                                × {{ number_format($result['aliquota_efetiva'] * 100, 2, ',', '.') }}%
                                = R$ {{ number_format($result['valor_total_das'], 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="das-card p-6">
                <h3 class="text-base font-semibold das-text mb-4">Composição da DAS (Repartição dos Tributos)</h3>
                <x-das.table-wrapper>
                    <x-das.table>
                        <thead>
                            <tr>
                                <th>Tributo</th>
                                <th class="text-right">Percentual</th>
                                <th class="text-right">Valor (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(['irpj' => 'IRPJ', 'csll' => 'CSLL', 'cofins' => 'Cofins', 'pis' => 'PIS/Pasep', 'cpp' => 'CPP/INSS', 'iss' => 'ISS'] as $key => $label)
                            <tr>
                                <td class="font-medium">{{ $label }}</td>
                                <td class="text-right das-text-secondary">{{ number_format($result["{$key}_percent"] * 100, 2, ',', '.') }}%</td>
                                <td class="text-right font-medium">R$ {{ number_format($result["{$key}_value"], 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <tr class="bg-slate-50 dark:bg-slate-800/50 font-bold">
                                <td>TOTAL</td>
                                <td class="text-right">100,00%</td>
                                <td class="text-right text-primary-500">R$ {{ number_format($result['valor_total_das'], 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </x-das.table>
                </x-das.table-wrapper>

                @if($calculationSaved)
                    <div class="mt-4 flex items-center justify-end gap-2 text-sm text-emerald-600 dark:text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Salvo no histórico
                    </div>
                @endif
            </div>
        </x-das.section>
    @endif
</div>
