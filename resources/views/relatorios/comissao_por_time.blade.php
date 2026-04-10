<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center no-print">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Relatório: Comissão Técnica por Time') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('relatorios.comissao-por-time.export', request()->all()) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 transition">
                   <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                   </svg>
                   Excel (CSV)
                </a>
                <button onclick="window.print()" 
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white active:bg-gray-900 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Filtros (Não aparece na impressão) -->
            @hasrole('Administrador')
            <div class="mb-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 no-print">
                <form action="{{ route('relatorios.comissao-por-time') }}" method="GET" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="time_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Time</label>
                        <select name="time_id" id="time_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Todos os Times</option>
                            @foreach($timesList as $time)
                                <option value="{{ $time->tim_id }}" {{ request('time_id') == $time->tim_id ? 'selected' : '' }}>
                                    {{ $time->tim_nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 transition">
                        Filtrar
                    </button>
                    @if(request()->filled('time_id'))
                        <a href="{{ route('relatorios.comissao-por-time') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                            Limpar
                        </a>
                    @endif
                </form>
            </div>
            @endhasrole

            <!-- Cabeçalho de Impressão (Apenas Impressão) -->
            <div class="print-only mb-8">
                <div class="flex justify-between items-center border-b-2 border-gray-900 pb-4">
                    <div class="flex items-center">
                        <img src="{{ asset('images/LOGO_LRV-150x150.png') }}" class="h-20 w-auto" alt="Logo Liga">
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold uppercase">Liga Regional de Voleibol</h1>
                            <p class="text-sm">Relação de Comissão Técnica por Time - Protocolo de Entrega de Credenciais</p>
                        </div>
                    </div>
                    <div class="text-right text-xs">
                        <p>Gerado em: {{ date('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Relatório -->
            @forelse($comissao as $timeId => $membrosTime)
                @php 
                    $time = $membrosTime->first()->time; 
                    $allIds = $membrosTime->pluck('id')->toArray();
                @endphp
                <div x-data="{ 
                        selectedIds: {{ json_encode($allIds) }},
                        toggleAll() {
                            if (this.selectedIds.length === {{ count($allIds) }}) {
                                this.selectedIds = [];
                            } else {
                                this.selectedIds = {{ json_encode($allIds) }};
                            }
                        }
                    }" 
                    class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden mb-8 {{ $loop->last ? '' : 'page-break-after' }}">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-700 flex flex-wrap justify-between items-center gap-2">
                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-gray-100 uppercase flex items-center">
                            {{ $time->tim_nome ?? 'Sem Time Vinculado' }}
                            <span class="print-only ml-2 text-sm font-normal normal-case text-gray-700">
                                (<span x-text="selectedIds.length">{{ count($allIds) }}</span> de {{ count($allIds) }} membros)
                            </span>
                        </h3>
                        <div class="no-print flex items-center shadow-sm rounded-full overflow-hidden border border-orange-200 dark:border-orange-800/50">
                            <div class="bg-orange-500 text-white px-3 py-1 text-[10px] font-black uppercase tracking-wider">
                                Comissão
                            </div>
                            <div class="bg-white dark:bg-gray-800 px-3 py-1 text-xs font-bold text-gray-700 dark:text-gray-300">
                                <span x-text="selectedIds.length">{{ count($allIds) }}</span> de {{ count($allIds) }} selecionados
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 dark:border-gray-600">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left no-print">
                                        <input type="checkbox" 
                                               @click="toggleAll()" 
                                               :checked="selectedIds.length === {{ count($allIds) }}"
                                               class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Registro LRV</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Função</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Cartão</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider print-only">Documentação</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider print-only w-1/4">Assinatura de Recebimento</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($membrosTime as $membro)
                                    <tr :class="!selectedIds.includes({{ $membro->id }}) ? 'opacity-30 no-print-row' : ''" class="transition-opacity">
                                        <td class="px-3 py-2 whitespace-nowrap no-print">
                                            <input type="checkbox" 
                                                   value="{{ $membro->id }}" 
                                                   x-model.number="selectedIds"
                                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-mono">
                                            {{ $membro->registro_lrv ?? '---' }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $membro->nome }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                            {{ $membro->funcao ?? 'S/F' }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs">
                                            @if($membro->cartaoImpresso())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border-green-200">
                                                    Impresso
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 border-yellow-200">
                                                    Pendente
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-[10px] text-gray-600 dark:text-gray-400 print-only">
                                            <span class="block">RG: {{ $membro->rg }}</span>
                                            <span class="block">CPF: {{ $membro->cpf ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-3 py-2 print-only min-w-[200px]">
                                            <div class="border-b border-gray-400 h-8"></div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Rodapé do Time (Apenas Impressão) -->
                    <div class="print-only px-4 py-8 mt-4 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-8 text-center pt-8">
                            <div>
                                <div class="border-t border-gray-900 pt-1">
                                    <p class="text-sm font-bold">{{ $time->tim_responsavel ?? 'Responsável pelo Time' }}</p>
                                    <p class="text-xs uppercase">{{ $time->tim_nome }}</p>
                                </div>
                            </div>
                            <div>
                                <div class="border-t border-gray-900 pt-1">
                                    <p class="text-sm font-bold">Diretoria LRV</p>
                                    <p class="text-xs uppercase">Liga Regional de Voleibol</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Nenhum membro da comissão encontrado com os filtros aplicados.</p>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        .no-print-row {
            /* Fallback case JS fail */
        }

        .print-only {
            display: none;
        }

        @media print {
            .no-print-row {
                display: none !important;
            }
            .no-print {
                display: none !important;
            }
            .print-only {
                display: table-cell !important;
            }
            /* Elementos que são blocos e não células de tabela */
            div.print-only {
                display: block !important;
            }
            body {
                background-color: white !important;
                color: black !important;
                font-size: 10pt;
            }
            .max-w-7xl {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .shadow-sm, .shadow, .sm\:rounded-lg {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }
            .bg-gray-100, .bg-gray-50, .bg-gray-800, .bg-gray-700 {
                background-color: transparent !important;
            }
            .text-gray-900, .text-gray-800, .text-gray-600, .text-gray-500, .dark\:text-gray-100, .dark\:text-gray-200, .dark\:text-gray-400 {
                color: black !important;
            }
            .divide-y > :not([hidden]) ~ :not([hidden]) {
                border-color: #e5e7eb !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            th, td {
                border: 1px solid #e5e7eb !important;
            }
            .page-break-after {
                page-break-after: always;
            }
            @page {
                size: landscape;
                margin: 1.5cm;
            }
            /* Garantir que o sidebar não apareça mesmo que no-print falhe */
            aside, nav {
                display: none !important;
            }
            .sm\:ml-64 {
                margin-left: 0 !important;
            }
            .pt-16 {
                padding-top: 0 !important;
            }
        }
    </style>
</x-app-layout>
