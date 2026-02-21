@php
use App\Services\DasCalculatorService;
$aliquotaTable = DasCalculatorService::getAliquotaTable();
$tributosTable  = DasCalculatorService::getTributosTable();
$months = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
    7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
];
@endphp

{{-- Tabela de Alíquotas --}}
<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
        Tabela de Alíquotas e Parcela a Deduzir — Anexo III
    </h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Faixa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Receita Bruta em 12 Meses (RBT12)</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Alíquota Nominal</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Parcela a Deduzir (R$)</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($aliquotaTable as $row)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $row['faixa'] }}ª Faixa
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @if($row['min'] == 0)
                            Até R$ {{ number_format($row['max'], 2, ',', '.') }}
                        @else
                            De R$ {{ number_format($row['min'], 2, ',', '.') }} a R$ {{ number_format($row['max'], 2, ',', '.') }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                        {{ number_format($row['nominal'] * 100, 2, ',', '.') }}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                        R$ {{ number_format($row['deducao'], 2, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Tabela de Repartição dos Tributos --}}
<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
        Tabela de Percentual de Repartição dos Tributos — Anexo III
    </h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Faixa</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">IRPJ</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">CSLL</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cofins</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">PIS/Pasep</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">CPP</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ISS (*)</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($tributosTable as $faixa => $t)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $faixa }}ª Faixa
                    </td>
                    @foreach(['irpj','csll','cofins','pis','cpp','iss'] as $tributo)
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                        {{ number_format($t[$tributo] * 100, 2, ',', '.') }}%
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
        (*) O percentual de ISS será fixo em 5% quando a empresa for impedida de optar pelo Simples Nacional,
        em razão de lei complementar ou lei orgânica municipal.
    </p>
</div>
