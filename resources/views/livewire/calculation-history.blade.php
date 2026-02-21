<div>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Histórico de Cálculos</h2>

        @php
            $months = [
                1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
            ];
        @endphp

        @if($calculations->isEmpty())
            <p class="py-4 text-center text-gray-500 dark:text-gray-400">
                Nenhum cálculo no histórico.
            </p>
        @else
            <div class="space-y-3">
                @foreach($calculations as $calc)
                <div wire:key="calc-{{ $calc->id }}"
                     x-data="{ open: false }"
                     class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">

                    {{-- Linha principal (sempre visível) --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-700 cursor-pointer"
                         @click="open = !open">

                        <div class="flex items-center gap-3">
                            {{-- Chevron --}}
                            <svg :class="open ? 'rotate-180' : ''"
                                 class="h-4 w-4 text-gray-400 transition-transform duration-200 flex-shrink-0"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                      clip-rule="evenodd"/>
                            </svg>

                            {{-- Período --}}
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $months[$calc->month] }}/{{ $calc->year }}
                            </span>

                            {{-- Faixa badge --}}
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-coral-100 text-coral-700 dark:bg-coral-900/30 dark:text-coral-300">
                                {{ $calc->tax_bracket }}ª Faixa
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-sm ml-7 sm:ml-0">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">RPA</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    R$ {{ number_format((float) $calc->rpa, 2, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Alíquota Efetiva</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ number_format((float) $calc->aliquota_efetiva * 100, 2, ',', '.') }}%
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Valor DAS</p>
                                <p class="text-base font-bold text-coral-500">
                                    R$ {{ number_format((float) $calc->valor_total_das, 2, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-center hidden sm:block">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Salvo em</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $calc->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>

                        {{-- Ações --}}
                        <div class="flex items-center gap-3 ml-7 sm:ml-0" @click.stop>
                            <button wire:click="view({{ $calc->id }})"
                                    class="text-coral-500 hover:text-coral-700 text-sm font-medium transition-colors">
                                Ver cálculo
                            </button>
                            <button wire:click="confirmDelete({{ $calc->id }})"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                                Excluir
                            </button>
                        </div>
                    </div>

                    {{-- Detalhe expandido: métricas completas --}}
                    <div x-show="open" x-collapse class="border-t border-gray-200 dark:border-gray-700">
                        <div class="px-4 py-4 bg-white dark:bg-gray-800">

                            {{-- Resumo RBT12 + Alíquotas --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">RBT12</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                        R$ {{ number_format((float) $calc->rbt12, 2, ',', '.') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alíquota Nominal</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ number_format((float) $calc->aliquota_nominal * 100, 2, ',', '.') }}%
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Parcela a Deduzir</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                        R$ {{ number_format((float) $calc->parcela_deduzir, 2, ',', '.') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Caso especial ≤ 180K</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $calc->special_case ? 'Sim' : 'Não' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Tabela de repartição dos tributos --}}
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                Composição da DAS (Repartição dos Tributos)
                            </p>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tributo</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">%</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Valor (R$)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        @foreach(['irpj' => 'IRPJ', 'csll' => 'CSLL', 'cofins' => 'Cofins', 'pis' => 'PIS/Pasep', 'cpp' => 'CPP/INSS', 'iss' => 'ISS'] as $key => $label)
                                        <tr class="bg-white dark:bg-gray-800">
                                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $label }}</td>
                                            <td class="px-4 py-2 text-right text-gray-600 dark:text-gray-300">
                                                {{ number_format((float) $calc->{$key.'_percent'} * 100, 2, ',', '.') }}%
                                            </td>
                                            <td class="px-4 py-2 text-right text-gray-900 dark:text-white">
                                                R$ {{ number_format((float) $calc->{$key.'_value'}, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr class="bg-gray-50 dark:bg-gray-700 font-bold">
                                            <td class="px-4 py-2 text-gray-900 dark:text-white">TOTAL</td>
                                            <td class="px-4 py-2 text-right text-gray-900 dark:text-white">100,00%</td>
                                            <td class="px-4 py-2 text-right text-coral-500">
                                                R$ {{ number_format((float) $calc->valor_total_das, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Modal de Confirmação de Exclusão --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
             x-data x-show="true" x-transition>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Confirmar Exclusão
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">{{ $deleteMessage }}</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="cancelDelete"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="delete"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-60 text-white text-sm font-medium rounded-md transition-colors">
                        <span wire:loading.remove wire:target="delete">Excluir</span>
                        <span wire:loading wire:target="delete">Excluindo...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
