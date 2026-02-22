<div>
    <x-das.section title="Histórico de Cálculos">
        @php
            $months = [
                1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
            ];
        @endphp

        @if($calculations->isEmpty())
            <x-das.empty-state
                title="Nenhum cálculo no histórico"
                description="Calcule o DAS para ver o histórico aqui."
            />
        @else
            <div class="space-y-3">
                @foreach($calculations as $calc)
                <div wire:key="calc-{{ $calc->id }}"
                     x-data="{ open: false }"
                     class="das-card overflow-hidden">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 cursor-pointer das-card-hover"
                         @click="open = !open">

                        <div class="flex items-center gap-3">
                            <svg :class="open ? 'rotate-180' : ''"
                                 class="h-4 w-4 das-text-muted transition-transform duration-200 flex-shrink-0"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>

                            <span class="text-sm font-semibold das-text">
                                {{ $months[$calc->month] }}/{{ $calc->year }}
                            </span>

                            <x-das.badge variant="primary">
                                {{ $calc->tax_bracket }}ª Faixa
                            </x-das.badge>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-sm ml-7 sm:ml-0">
                            <div class="text-center">
                                <p class="text-xs das-text-muted">RPA</p>
                                <p class="font-medium das-text">R$ {{ number_format((float) $calc->rpa, 2, ',', '.') }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs das-text-muted">Alíquota Efetiva</p>
                                <p class="font-medium das-text">{{ number_format((float) $calc->aliquota_efetiva * 100, 2, ',', '.') }}%</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs das-text-muted">Valor DAS</p>
                                <p class="text-base font-bold text-primary-500">R$ {{ number_format((float) $calc->valor_total_das, 2, ',', '.') }}</p>
                            </div>
                            <div class="text-center hidden sm:block">
                                <p class="text-xs das-text-muted">Salvo em</p>
                                <p class="text-xs das-text-muted">{{ $calc->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 ml-7 sm:ml-0" @click.stop>
                            <button wire:click="view({{ $calc->id }})"
                                    class="text-sm text-primary-500 hover:text-primary-700 dark:hover:text-primary-300 font-medium touch-target inline-flex items-center justify-center px-2 rounded-lg transition-colors">
                                Ver cálculo
                            </button>
                            <button wire:click="confirmDelete({{ $calc->id }})"
                                    class="text-sm text-red-600 hover:text-red-800 dark:hover:text-red-400 font-medium touch-target inline-flex items-center justify-center px-2 rounded-lg transition-colors">
                                Excluir
                            </button>
                        </div>
                    </div>

                    <div x-show="open" x-collapse class="border-t border-slate-200 dark:border-slate-700">
                        <div class="p-4">

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
                                <div>
                                    <p class="text-xs font-medium das-text-muted uppercase tracking-wider">RBT12</p>
                                    <p class="mt-1 text-sm font-semibold das-text">R$ {{ number_format((float) $calc->rbt12, 2, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium das-text-muted uppercase tracking-wider">Alíquota Nominal</p>
                                    <p class="mt-1 text-sm font-semibold das-text">{{ number_format((float) $calc->aliquota_nominal * 100, 2, ',', '.') }}%</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium das-text-muted uppercase tracking-wider">Parcela a Deduzir</p>
                                    <p class="mt-1 text-sm font-semibold das-text">R$ {{ number_format((float) $calc->parcela_deduzir, 2, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium das-text-muted uppercase tracking-wider">Caso especial ≤ 180K</p>
                                    <p class="mt-1 text-sm font-semibold das-text">{{ $calc->special_case ? 'Sim' : 'Não' }}</p>
                                </div>
                            </div>

                            <p class="text-xs font-medium das-text-muted uppercase tracking-wider mb-2">
                                Composição da DAS (Repartição dos Tributos)
                            </p>
                            <x-das.table-wrapper>
                                <x-das.table>
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left">Tributo</th>
                                            <th class="px-4 py-2 text-right">%</th>
                                            <th class="px-4 py-2 text-right">Valor (R$)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(['irpj' => 'IRPJ', 'csll' => 'CSLL', 'cofins' => 'Cofins', 'pis' => 'PIS/Pasep', 'cpp' => 'CPP/INSS', 'iss' => 'ISS'] as $key => $label)
                                        <tr>
                                            <td class="px-4 py-2 font-medium">{{ $label }}</td>
                                            <td class="px-4 py-2 text-right das-text-secondary">{{ number_format((float) $calc->{$key.'_percent'} * 100, 2, ',', '.') }}%</td>
                                            <td class="px-4 py-2 text-right font-medium">R$ {{ number_format((float) $calc->{$key.'_value'}, 2, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                        <tr class="bg-slate-50 dark:bg-slate-800/50 font-bold">
                                            <td class="px-4 py-2">TOTAL</td>
                                            <td class="px-4 py-2 text-right">100,00%</td>
                                            <td class="px-4 py-2 text-right text-primary-500">R$ {{ number_format((float) $calc->valor_total_das, 2, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </x-das.table>
                            </x-das.table-wrapper>

                        </div>
                    </div>
                </div>
                @endforeach
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
