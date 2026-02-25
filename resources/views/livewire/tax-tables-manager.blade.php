<div>
    <x-das.section title="Tabela de Alíquotas e Parcela a Deduzir — Anexo III">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
            <p class="text-sm das-text-muted">
                Para editar, <strong>clique sobre os valores</strong> de Alíquota ou Parcela a Deduzir, altere e pressione <kbd class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-xs font-mono">Enter</kbd>.
            </p>
            <button
                wire:click="checkForUpdates"
                wire:loading.class="opacity-50 cursor-not-allowed"
                class="flex-shrink-0 w-full sm:w-auto px-4 py-2.5 sm:py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2"
            >
                <span wire:loading.remove wire:target="checkForUpdates">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </span>
                <span wire:loading wire:target="checkForUpdates" class="animate-spin">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                Verificar Atualizações
            </button>
        </div>

        {{-- Cards: mobile (< 640px) --}}
        <div class="sm:hidden space-y-3 mb-2">
            @foreach($brackets as $index => $row)
                <div class="das-card p-4" wire:key="card-aliq-{{ $row['id'] }}">
                    <div class="mb-3">
                        <span class="text-sm font-semibold das-text">{{ $row['faixa'] }}ª Faixa</span>
                        <p class="text-xs das-text-muted mt-0.5">
                            @if($row['min_rbt12'] == 0)
                                Até R$ {{ number_format($row['max_rbt12'], 2, ',', '.') }}
                            @else
                                R$ {{ number_format($row['min_rbt12'], 2, ',', '.') }} a R$ {{ number_format($row['max_rbt12'], 2, ',', '.') }}
                            @endif
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Alíquota --}}
                        <div x-data="{ editing: false, val: '{{ number_format($row['aliquota_nominal'], 2, '.', '') }}' }" @click.away="editing = false">
                            <p class="text-xs font-medium das-text-muted mb-1">Alíquota (%)</p>
                            <span x-show="!editing"
                                  @click="editing = true; $nextTick(() => $refs.m_aliquota_{{ $index }}.focus())"
                                  class="inline-flex items-center gap-1 cursor-pointer text-sm font-semibold das-text border-b border-dashed border-primary-300 dark:border-primary-600 touch-target">
                                <span x-text="Number(val).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>%
                            </span>
                            <input x-show="editing" x-cloak x-ref="m_aliquota_{{ $index }}" x-model="val" type="text"
                                   inputmode="decimal"
                                   @keydown.enter="$wire.updateBracket({{ $index }}, 'aliquota_nominal', val); editing = false"
                                   @keydown.escape="editing = false"
                                   class="w-full px-2 py-1.5 text-sm border-2 border-primary-500 rounded-md focus:outline-none bg-white dark:bg-slate-800 text-slate-900 dark:text-white" />
                        </div>
                        {{-- Dedução --}}
                        <div x-data="{ editing: false, val: '{{ number_format($row['deducao'], 2, '.', '') }}' }" @click.away="editing = false">
                            <p class="text-xs font-medium das-text-muted mb-1">Parcela a Deduzir</p>
                            <span x-show="!editing"
                                  @click="editing = true; $nextTick(() => $refs.m_deducao_{{ $index }}.focus())"
                                  class="inline-flex items-center gap-1 cursor-pointer text-sm font-semibold das-text border-b border-dashed border-red-300 dark:border-red-600 touch-target">
                                R$ <span x-text="Number(val).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                            </span>
                            <input x-show="editing" x-cloak x-ref="m_deducao_{{ $index }}" x-model="val" type="text"
                                   inputmode="decimal"
                                   @keydown.enter="$wire.updateBracket({{ $index }}, 'deducao', val); editing = false"
                                   @keydown.escape="editing = false"
                                   class="w-full px-2 py-1.5 text-sm border-2 border-primary-500 rounded-md focus:outline-none bg-white dark:bg-slate-800 text-slate-900 dark:text-white" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tabela: desktop (>= 640px) --}}
        <div class="hidden sm:block">
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
                            <tr wire:key="aliq-row-{{ $row['id'] }}-{{ md5(json_encode($row)) }}">
                                <td class="font-medium">{{ $row['faixa'] }}ª</td>
                                <td class="das-text-secondary">
                                    @if($row['min_rbt12'] == 0)
                                        Até R$ {{ number_format($row['max_rbt12'], 2, ',', '.') }}
                                    @else
                                        De R$ {{ number_format($row['min_rbt12'], 2, ',', '.') }} a R$ {{ number_format($row['max_rbt12'], 2, ',', '.') }}
                                    @endif
                                </td>

                                <td class="text-right relative">
                                    <div x-data="{ editing: false, val: '{{ number_format($row['aliquota_nominal'], 2, '.', '') }}' }" @click.away="editing = false">
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
        </div>
    </x-das.section>

    <x-das.section title="Tabela de Percentual de Repartição dos Tributos — Anexo III">
        <p class="text-sm das-text-muted mb-4">
            Dê um <strong>clique sobre qualquer percentual</strong> para editá-lo. Pressione <kbd class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-xs font-mono">Enter</kbd> para confirmar a alteração.
        </p>

        {{-- Cards: mobile (< 640px) --}}
        <div class="sm:hidden space-y-3 mb-2">
            @foreach($brackets as $index => $row)
                <div class="das-card p-4" wire:key="card-rep-{{ $row['id'] }}">
                    <p class="text-sm font-semibold das-text mb-3">{{ $row['faixa'] }}ª Faixa</p>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                        @foreach(['irpj' => 'IRPJ', 'csll' => 'CSLL', 'cofins' => 'Cofins', 'pis' => 'PIS/Pasep', 'cpp' => 'CPP', 'iss' => 'ISS'] as $tfield => $tlabel)
                            <div x-data="{ editing: false, val: '{{ number_format($row[$tfield], 2, '.', '') }}' }" @click.away="editing = false">
                                <p class="text-xs font-medium das-text-muted mb-1">{{ $tlabel }}</p>
                                <span x-show="!editing"
                                      @click="editing = true; $nextTick(() => $refs.mc_{{ $tfield }}_{{ $index }}.focus())"
                                      class="inline-flex items-center cursor-pointer text-sm font-semibold das-text border-b border-dashed border-emerald-300 dark:border-emerald-700 touch-target">
                                    <span x-text="Number(val).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>%
                                </span>
                                <input x-show="editing" x-cloak x-ref="mc_{{ $tfield }}_{{ $index }}" x-model="val" type="text"
                                       inputmode="decimal"
                                       @keydown.enter="$wire.updateBracket({{ $index }}, '{{ $tfield }}', val); editing = false"
                                       @keydown.escape="editing = false"
                                       class="w-full px-2 py-1.5 text-sm border-2 border-primary-500 rounded-md focus:outline-none bg-white dark:bg-slate-800 text-slate-900 dark:text-white" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tabela: desktop (>= 640px) --}}
        <div class="hidden sm:block">
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
                            <tr wire:key="rep-row-{{ $row['id'] }}-{{ md5(json_encode($row)) }}">
                                <td class="font-medium">{{ $row['faixa'] }}ª</td>

                                @foreach(['irpj', 'csll', 'cofins', 'pis', 'cpp', 'iss'] as $tfield)
                                    <td class="text-right relative">
                                        <div x-data="{ editing: false, val: '{{ number_format($row[$tfield], 2, '.', '') }}' }" @click.away="editing = false">
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
        </div>

        <p class="mt-4 text-xs font-medium das-text-muted uppercase tracking-wide">
            (*) O percentual de ISS será fixo em 5% quando a empresa for impedida de optsr pelo Simples Nacional, em razção de lei complementar ou lei orgânica municipal.
        </p>
    </x-das.section>

    @if($checkResult)
        <div class="fixed inset-0 z-50 overflow-y-auto" style="display: flex;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end sm:items-center justify-center min-h-screen pt-4 px-4 pb-20 sm:pb-4 text-center sm:block sm:p-0" style="width: 100%;">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeModal" style="width: 100%; height: 100%;"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl w-full sm:my-8 sm:align-middle sm:max-w-lg sm:p-6" style="position: relative; z-index: 60;">
                    <div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">

                            @if($showConfirm)
                                <!-- Modal de Confirmação -->
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Confirmar Correção
                                </h3>

                                <div class="mt-4">
                                    <div class="flex items-center justify-center p-4 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg mb-4">
                                        <svg class="w-10 h-10 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span class="ml-3 text-yellow-700 dark:text-yellow-400 font-medium">Atenção! Esta ação alterará os dados.</span>
                                    </div>

                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                        O sistema irá atualizar <strong>{{ count($correctionSummary) }}</strong> campo(s) com os valores oficiais:
                                    </p>

                                    {{-- Cards: mobile --}}
                                    <div class="sm:hidden space-y-2 mb-4">
                                        @foreach($correctionSummary as $item)
                                            <div class="das-card p-3">
                                                <p class="text-xs font-semibold das-text mb-2">{{ $item['faixa'] }}ª Faixa — {{ $item['field'] }}</p>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <p class="text-xs das-text-muted mb-0.5">De</p>
                                                        <p class="text-sm font-semibold text-red-500">{{ is_numeric($item['current']) ? number_format($item['current'], 4, ',', '.') : $item['current'] }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs das-text-muted mb-0.5">Para</p>
                                                        <p class="text-sm font-semibold text-green-500">{{ is_numeric($item['official']) ? number_format($item['official'], 4, ',', '.') : $item['official'] }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Tabela: desktop --}}
                                    <div class="hidden sm:block max-h-40 overflow-y-auto mb-4 rounded-lg border border-slate-200 dark:border-slate-700">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                            <thead class="bg-gray-50 dark:bg-slate-700">
                                                <tr>
                                                    <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500">Faixa</th>
                                                    <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500">Campo</th>
                                                    <th class="px-2 py-1.5 text-right text-xs font-medium text-gray-500">De</th>
                                                    <th class="px-2 py-1.5 text-right text-xs font-medium text-gray-500">Para</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($correctionSummary as $item)
                                                    <tr>
                                                        <td class="px-2 py-1.5">{{ $item['faixa'] }}ª</td>
                                                        <td class="px-2 py-1.5">{{ $item['field'] }}</td>
                                                        <td class="px-2 py-1.5 text-right text-red-500">{{ is_numeric($item['current']) ? number_format($item['current'], 4, ',', '.') : $item['current'] }}</td>
                                                        <td class="px-2 py-1.5 text-right text-green-500">{{ is_numeric($item['official']) ? number_format($item['official'], 4, ',', '.') : $item['official'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                                    <button wire:click="cancelConfirm" type="button" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white dark:bg-slate-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none transition-colors">
                                        Cancelar
                                    </button>
                                    <button wire:click="confirmCorrection" type="button" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-green-600 text-sm font-medium text-white hover:bg-green-700 focus:outline-none transition-colors">
                                        Sim, aplicar correções
                                    </button>
                                </div>

                            @else
                                <!-- Modal de Resultado -->
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Verificação de Tabelas Tributárias
                                </h3>

                                <div class="mt-4">
                                    @if($checkResult['status'] === 'uptodate')
                                        <div class="flex items-center justify-center p-4 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                            <svg class="w-12 h-12 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="ml-3 text-green-700 dark:text-green-400 font-medium">Tabela atualizada! Nenhuma diferença encontrada.</span>
                                        </div>
                                    @elseif($checkResult['status'] === 'outdated')
                                        <div class="flex items-center justify-center p-4 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg mb-4">
                                            <svg class="w-10 h-10 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <span class="ml-3 text-yellow-700 dark:text-yellow-400 font-medium">Diferenças encontradas!</span>
                                        </div>

                                        <div class="text-left">
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                                Última verificação: {{ \Carbon\Carbon::parse($checkResult['checked_at'])->format('d/m/Y H:i') }}
                                            </p>

                                            {{-- Cards: mobile --}}
                                            <div class="sm:hidden space-y-2">
                                                @foreach($checkResult['differences'] as $diff)
                                                    <div class="das-card p-3">
                                                        <p class="text-xs font-semibold das-text mb-2">{{ $diff['faixa'] }}ª Faixa — {{ $diff['field'] }}</p>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <div>
                                                                <p class="text-xs das-text-muted mb-0.5">Atual</p>
                                                                <p class="text-sm font-semibold text-red-600">{{ is_numeric($diff['current_value']) ? number_format($diff['current_value'], 4, ',', '.') : $diff['current_value'] }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs das-text-muted mb-0.5">Oficial</p>
                                                                <p class="text-sm font-semibold text-green-600">{{ is_numeric($diff['official_value']) ? number_format($diff['official_value'], 4, ',', '.') : $diff['official_value'] }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Tabela: desktop --}}
                                            <div class="hidden sm:block max-h-60 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-700">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-slate-700">
                                                        <tr>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Faixa</th>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Campo</th>
                                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300">Atual</th>
                                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300">Oficial</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                        @foreach($checkResult['differences'] as $diff)
                                                            <tr>
                                                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $diff['faixa'] }}ª</td>
                                                                <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $diff['field'] }}</td>
                                                                <td class="px-3 py-2 text-sm text-right text-red-600">{{ is_numeric($diff['current_value']) ? number_format($diff['current_value'], 4, ',', '.') : $diff['current_value'] }}</td>
                                                                <td class="px-3 py-2 text-sm text-right text-green-600">{{ is_numeric($diff['official_value']) ? number_format($diff['official_value'], 4, ',', '.') : $diff['official_value'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center p-4 bg-red-100 dark:bg-red-900/30 rounded-lg">
                                            <svg class="w-12 h-12 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="ml-3 text-red-700 dark:text-red-400 font-medium">Erro ao verificar tabelas. Tente novamente mais tarde.</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(!$showConfirm)
                        <div class="mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                            @if($checkResult['status'] === 'outdated')
                                <button wire:click="prepareCorrection" type="button" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-green-600 text-sm font-medium text-white hover:bg-green-700 focus:outline-none transition-colors">
                                    Aplicar Correções
                                </button>
                            @endif
                            <button wire:click="closeModal" type="button" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-primary-600 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none transition-colors">
                                Fechar
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
