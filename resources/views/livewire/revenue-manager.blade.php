<div>
    {{-- Formulário de Receita --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            {{ $editingId ? 'Editar Receita Mensal' : 'Registrar Nova Receita Mensal' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Mês --}}
                <div>
                    <label for="revenue-month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Mês de Referência
                    </label>
                    <select id="revenue-month" wire:model="month"
                            class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-coral-500 dark:text-white text-base">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('month')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ano --}}
                <div>
                    <label for="revenue-year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Ano
                    </label>
                    <select id="revenue-year" wire:model="year"
                            class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-coral-500 dark:text-white text-base">
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                    @error('year')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Valor da Receita com máscara Alpine.js --}}
            <div>
                <label for="revenue-amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Valor da Receita Bruta (RPA)
                </label>
                <div class="mt-1 relative rounded-md shadow-sm" x-data="currencyInput()">
                    <span class="currency-symbol text-gray-500 dark:text-gray-400">R$</span>
                    <input id="revenue-amount"
                           type="text"
                           wire:model="amount"
                           @input="format($event)"
                           placeholder="0,00"
                           inputmode="numeric"
                           class="block w-full input-currency py-2 pr-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-coral-500 focus:border-coral-500 dark:text-white text-base">
                </div>
                @error('amount')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                @if($editingId)
                    <button type="button" wire:click="cancelEdit"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Cancelar
                    </button>
                @endif
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-coral-500 hover:bg-coral-600 disabled:opacity-60 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-coral-500 transition-colors">
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Atualizar Receita' : 'Salvar Receita' }}
                    </span>
                    <span wire:loading wire:target="save">Salvando...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Tabela de Receitas --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            Receitas Mensais Registradas
        </h2>

        @if($revenues->isEmpty())
            <p class="py-4 text-center text-gray-500 dark:text-gray-400">
                Nenhuma receita registrada.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Período
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Receita Bruta (R$)
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($revenues as $revenue)
                            <tr wire:key="revenue-{{ $revenue->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $months[$revenue->month] }}/{{ $revenue->year }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    R$ {{ number_format((float) $revenue->amount, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-3">
                                    <button wire:click="edit({{ $revenue->id }})"
                                            class="text-coral-500 hover:text-coral-700 font-medium transition-colors">
                                        Editar
                                    </button>
                                    <button wire:click="confirmDelete({{ $revenue->id }})"
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
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $deleteMessage }}
                </p>
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
