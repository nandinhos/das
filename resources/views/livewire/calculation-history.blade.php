<div>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Histórico de Cálculos</h2>

        @if($calculations->isEmpty())
            <p class="py-4 text-center text-gray-500 dark:text-gray-400">
                Nenhum cálculo no histórico.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Data do Cálculo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Período de Apuração
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                RPA (R$)
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Alíquota Efetiva
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Valor DAS (R$)
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @php
                            $months = [
                                1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                                5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                                9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
                            ];
                        @endphp
                        @foreach($calculations as $calc)
                            <tr wire:key="calc-{{ $calc->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $calc->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $months[$calc->month] }}/{{ $calc->year }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                                    R$ {{ number_format((float) $calc->rpa, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                                    {{ number_format((float) $calc->aliquota_efetiva * 100, 2, ',', '.') }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white text-right">
                                    R$ {{ number_format((float) $calc->valor_total_das, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-3">
                                    <button wire:click="view({{ $calc->id }})"
                                            class="text-coral-500 hover:text-coral-700 font-medium transition-colors">
                                        Ver
                                    </button>
                                    <button wire:click="confirmDelete({{ $calc->id }})"
                                            class="text-red-600 hover:text-red-800 font-medium transition-colors">
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
