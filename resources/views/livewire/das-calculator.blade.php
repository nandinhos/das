<div>
    {{-- Formulário de Seleção do Período --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            Calcular DAS para Período de Apuração
        </h2>

        <form wire:submit="calculate" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Mês --}}
                <div>
                    <label for="calc-month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Mês de Apuração
                    </label>
                    <select id="calc-month" wire:model="month"
                            class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-coral-500 dark:text-white text-base">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Ano --}}
                <div>
                    <label for="calc-year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Ano
                    </label>
                    <select id="calc-year" wire:model="year"
                            class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-coral-500 dark:text-white text-base">
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Mensagem de erro --}}
            @if($errorMessage)
                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $errorMessage }}</p>
                </div>
            @endif

            <div class="flex justify-end">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-coral-500 hover:bg-coral-600 disabled:opacity-60 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-coral-500 transition-colors">
                    <span wire:loading.remove wire:target="calculate">Calcular DAS</span>
                    <span wire:loading wire:target="calculate">Calculando...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Resultado do Cálculo --}}
    @if($result)
    <div>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Resultado do Cálculo</h2>

        {{-- Cards de Métricas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            {{-- PA e RPA --}}
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Período de Apuração (PA)</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $months[$result['month']] }}/{{ $result['year'] }}
                </p>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-2">Receita Bruta do PA (RPA)</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    R$ {{ number_format($result['rpa'], 2, ',', '.') }}
                </p>
            </div>

            {{-- RBT12 --}}
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Receita Bruta 12 meses (RBT12)</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    R$ {{ number_format($result['rbt12'], 2, ',', '.') }}
                </p>
            </div>

            {{-- Faixa --}}
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Faixa de Tributação</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $result['tax_bracket'] }}ª Faixa
                </p>
            </div>

            {{-- Alíquotas --}}
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Alíquota Nominal</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ number_format($result['aliquota_nominal'] * 100, 2, ',', '.') }}%
                </p>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-2">Parcela a Deduzir</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    R$ {{ number_format($result['parcela_deduzir'], 2, ',', '.') }}
                </p>
            </div>

            {{-- Alíquota Efetiva --}}
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Alíquota Efetiva</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ number_format($result['aliquota_efetiva'] * 100, 2, ',', '.') }}%
                </p>
            </div>

            {{-- Valor Total DAS --}}
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor Total DAS (Principal)</h3>
                <p class="text-xl font-bold text-coral-500">
                    R$ {{ number_format($result['valor_total_das'], 2, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Passos do Cálculo (colapsável) --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6"
             x-data="{ open: false }">
            <button @click="open = !open" type="button"
                    class="flex items-center justify-between w-full text-left">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Passos do Cálculo</h3>
                <svg :class="open ? 'rotate-180' : ''"
                     class="h-5 w-5 text-gray-500 transition-transform duration-200"
                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"/>
                </svg>
            </button>

            <div x-show="open" x-collapse class="mt-4 steps-container space-y-4">
                {{-- Passo 1: RPA --}}
                <div class="step">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Receita Bruta do Período de Apuração (RPA)
                        </h4>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Valor da RPA: R$ {{ number_format($result['rpa'], 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Passo 2: RBT12 --}}
                <div class="step">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Cálculo da Receita Bruta Acumulada em 12 meses (RBT12)
                        </h4>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Soma das receitas dos 12 meses anteriores: R$ {{ number_format($result['rbt12'], 2, ',', '.') }}
                        </p>
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 border-l-2 border-gray-200 dark:border-gray-700 pl-3 space-y-1">
                            @php
                                $sorted = collect($result['rbt12_data'])
                                    ->sortByDesc('year')
                                    ->sortByDesc(fn($m) => $m['year'] * 100 + $m['month']);
                            @endphp
                            @foreach($sorted as $m)
                                <div>
                                    {{ $months[$m['month']] }}/{{ $m['year'] }}:
                                    R$ {{ number_format($m['amount'], 2, ',', '.') }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Passo 3: Faixa --}}
                <div class="step">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Identificação da Faixa de Tributação
                        </h4>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            RBT12 R$ {{ number_format($result['rbt12'], 2, ',', '.') }}
                            se encaixa na {{ $result['tax_bracket'] }}ª Faixa
                        </p>
                    </div>
                </div>

                {{-- Passo 4: Alíquota Efetiva --}}
                <div class="step">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Cálculo da Alíquota Efetiva
                        </h4>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            @if($result['special_case'])
                                Caso especial — RBT12 ≤ R$ 180.000,00:<br>
                                Alíquota Efetiva = Alíquota Nominal = {{ number_format($result['aliquota_nominal'] * 100, 2, ',', '.') }}%
                            @else
                                ((R$ {{ number_format($result['rbt12'], 2, ',', '.') }}
                                × {{ number_format($result['aliquota_nominal'] * 100, 2, ',', '.') }}%)
                                &minus; R$ {{ number_format($result['parcela_deduzir'], 2, ',', '.') }})
                                &divide; R$ {{ number_format($result['rbt12'], 2, ',', '.') }}
                                = {{ number_format($result['aliquota_efetiva'] * 100, 2, ',', '.') }}%
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Passo 5: Valor DAS --}}
                <div class="step">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            Cálculo do Valor Total da DAS
                        </h4>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            R$ {{ number_format($result['rpa'], 2, ',', '.') }}
                            &times; {{ number_format($result['aliquota_efetiva'] * 100, 2, ',', '.') }}%
                            = R$ {{ number_format($result['valor_total_das'], 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Composição da DAS --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Composição da DAS (Repartição dos Tributos)
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tributo</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Percentual</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valor (R$)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach(['irpj' => 'IRPJ', 'csll' => 'CSLL', 'cofins' => 'Cofins', 'pis' => 'PIS/Pasep', 'cpp' => 'CPP/INSS', 'iss' => 'ISS'] as $key => $label)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $label }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                                {{ number_format($result["{$key}_percent"] * 100, 2, ',', '.') }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                R$ {{ number_format($result["{$key}_value"], 2, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">TOTAL</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white text-right">100,00%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white text-right">
                                R$ {{ number_format($result['valor_total_das'], 2, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end items-center gap-4">
                @if($calculationSaved)
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                        ✓ Salvo no histórico
                    </span>
                @endif
                <button wire:click="saveToHistory"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-coral-500 hover:bg-coral-600 disabled:opacity-60 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-coral-500 transition-colors">
                    <span wire:loading.remove wire:target="saveToHistory">Salvar no Histórico</span>
                    <span wire:loading wire:target="saveToHistory">Salvando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
