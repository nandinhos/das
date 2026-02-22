<div>
    <x-das.section title="{{ $editingId ? 'Editar Receita Mensal' : 'Registrar Nova Receita Mensal' }}">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-das.select
                    id="revenue-month"
                    label="Mês de Referência"
                    wire:model="month"
                    required
                >
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}">{{ $name }}</option>
                    @endforeach
                </x-das.select>

                <x-das.select
                    id="revenue-year"
                    label="Ano"
                    wire:model="year"
                    required
                >
                    @foreach($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </x-das.select>
            </div>

            <x-das.input
                id="revenue-amount"
                label="Valor da Receita Bruta (RPA)"
                type="text"
                wire:model="amount"
                prefix="R$"
                placeholder="0,00"
                inputmode="numeric"
                error-name="amount"
            />

            <div class="flex flex-col sm:flex-row justify-end gap-3">
                @if($editingId)
                    <x-das.button
                        type="button"
                        variant="secondary"
                        wire:click="cancelEdit"
                    >
                        Cancelar
                    </x-das.button>
                @endif
                <x-das.button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Atualizar Receita' : 'Salvar Receita' }}
                    </span>
                    <span wire:loading wire:target="save">Salvando...</span>
                </x-das.button>
            </div>
        </form>
    </x-das.section>

    <x-das.section title="Receitas Mensais Registradas">
        @if($revenues->isEmpty())
            <x-das.empty-state
                title="Nenhuma receita registrada"
                description="Comece registrando suas receitas mensais."
            />
        @else
            {{-- Cards: visível apenas em mobile (< 640px) --}}
            <div class="sm:hidden space-y-3">
                @foreach($revenues as $revenue)
                    <div class="das-card p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider das-text-muted">Período</p>
                                <p class="text-sm font-medium mt-0.5 das-text">
                                    {{ $months[$revenue->month] }}/{{ $revenue->year }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-medium uppercase tracking-wider das-text-muted">Receita Bruta</p>
                                <p class="text-sm font-medium mt-0.5 das-text">
                                    R$ {{ number_format((float) $revenue->amount, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-slate-100 dark:border-[#2d2d2a]">
                            <button
                                wire:click="edit({{ $revenue->id }})"
                                class="text-sm text-primary-500 hover:text-primary-700 dark:hover:text-primary-300 font-medium touch-target inline-flex items-center justify-center px-2 rounded-lg transition-colors"
                            >
                                Editar
                            </button>
                            <button
                                wire:click="confirmDelete({{ $revenue->id }})"
                                class="text-sm text-red-600 hover:text-red-800 dark:hover:text-red-400 font-medium touch-target inline-flex items-center justify-center px-2 rounded-lg transition-colors"
                            >
                                Excluir
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Tabela: visível apenas em sm+ (>= 640px) --}}
            <div class="hidden sm:block">
                <x-das.table-wrapper>
                    <x-das.table>
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th class="text-right">Receita Bruta (R$)</th>
                                <th class="text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($revenues as $revenue)
                                <tr>
                                    <td class="font-medium">{{ $months[$revenue->month] }}/{{ $revenue->year }}</td>
                                    <td class="text-right">R$ {{ number_format((float) $revenue->amount, 2, ',', '.') }}</td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <button
                                                wire:click="edit({{ $revenue->id }})"
                                                class="text-sm text-primary-500 hover:text-primary-700 dark:hover:text-primary-300 font-medium touch-target inline-flex items-center justify-center px-2 rounded-lg transition-colors"
                                            >
                                                Editar
                                            </button>
                                            <button
                                                wire:click="confirmDelete({{ $revenue->id }})"
                                                class="text-sm text-red-600 hover:text-red-800 dark:hover:text-red-400 font-medium touch-target inline-flex items-center justify-center px-2 rounded-lg transition-colors"
                                            >
                                                Excluir
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-das.table>
                </x-das.table-wrapper>
            </div>
        @endif
    </x-das.section>

    @if($showDeleteModal)
        <x-das.modal
            title="Confirmar Exclusão"
            :show="true"
        >
            <p class="das-text-secondary">{{ $deleteMessage }}</p>

            <x-slot:footer>
                <x-das.button
                    variant="secondary"
                    wire:click="cancelDelete"
                >
                    Cancelar
                </x-das.button>
                <x-das.button
                    variant="danger"
                    wire:click="delete"
                >
                    Excluir
                </x-das.button>
            </x-slot:footer>
        </x-das.modal>
    @endif
</div>
