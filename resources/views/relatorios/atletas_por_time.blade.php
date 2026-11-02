<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center no-print">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Relatório: Atletas por Time') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('relatorios.atletas-por-time.export', request()->all()) }}" 
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

    <div class="py-6" x-data="{
        historyModalOpen: false,
        modalAtletaNome: '',
        modalHistorico: [],
        openHistoryModal(nome, hist) {
            this.modalAtletaNome = nome;
            this.modalHistorico = hist;
            this.historyModalOpen = true;
        }
    }">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Filtros (Não aparece na impressão) -->
            <!-- Filtros (Não aparece na impressão) -->
            <div class="mb-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 no-print">
                <form action="{{ route('relatorios.atletas-por-time') }}" method="GET" class="flex flex-wrap items-end gap-4">
                    @hasrole('Administrador')
                    <div class="flex-1 min-w-[200px]">
                        <label for="time_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Time</label>
                        <select name="time_id" id="time_id" onchange="this.form.submit()" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Todos os Times</option>
                            @foreach($timesList as $time)
                                <option value="{{ $time->tim_id }}" {{ request('time_id') == $time->tim_id ? 'selected' : '' }}>
                                    {{ $time->tim_nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endhasrole
                    
                    <div class="flex-1 min-w-[200px]">
                        <label for="equipe_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Equipe/Categoria</label>
                        <select name="equipe_id" id="equipe_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Todas as Equipes</option>
                            @foreach($equipesList as $equipe)
                                <option value="{{ $equipe->eqp_id }}" {{ request('equipe_id') == $equipe->eqp_id ? 'selected' : '' }}>
                                    {{ $equipe->time->tim_nome ?? '' }} - {{ $equipe->categoria->cto_nome ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 transition">
                        Filtrar
                    </button>
                    @if(request()->filled('time_id') || request()->filled('equipe_id'))
                        <a href="{{ route('relatorios.atletas-por-time') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                            Limpar
                        </a>
                    @endif
                </form>
            </div>

            <!-- Cabeçalho de Impressão (Apenas Impressão) -->
            <div class="print-only mb-8">
                <div class="flex justify-between items-center border-b-2 border-gray-900 pb-4">
                    <div class="flex items-center">
                        <img src="{{ asset('images/LOGO_LRV-150x150.png') }}" class="h-20 w-auto" alt="Logo Liga">
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold uppercase">Liga Regional de Voleibol</h1>
                            <p class="text-sm">Relação de Atletas por Time - Protocolo de Entrega de Carteirinhas</p>
                        </div>
                    </div>
                    <div class="text-right text-xs">
                        <p>Gerado em: {{ date('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Relatório -->
            @forelse($atletas as $timeId => $atletasTime)
                @php 
                    $time = $atletasTime->first()->time; 
                    $allIds = $atletasTime->pluck('atl_id')->toArray();
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
                                (<span x-text="selectedIds.length">{{ count($allIds) }}</span> de {{ count($allIds) }} atletas)
                            </span>
                        </h3>
                        <div class="no-print flex items-center shadow-sm rounded-full overflow-hidden border border-orange-200 dark:border-orange-800/50">
                            <div class="bg-orange-500 text-white px-3 py-1 text-[10px] font-black uppercase tracking-wider">
                                Atletas
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
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Atleta</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoria / DN</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Cartão</th>
                                    @hasrole('Administrador')
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transferência</th>
                                    @endhasrole
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider print-only">Documentação</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider print-only w-1/4">Assinatura de Recebimento</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($atletasTime as $atleta)
                                    <tr :class="!selectedIds.includes({{ $atleta->atl_id }}) ? 'opacity-30 no-print-row' : ''" class="transition-opacity">
                                        <td class="px-3 py-2 whitespace-nowrap no-print">
                                            <input type="checkbox" 
                                                   value="{{ $atleta->atl_id }}" 
                                                   x-model.number="selectedIds"
                                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-mono">
                                            {{ $atleta->atl_resg ?? '---' }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $atleta->atl_nome }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                            {{ $atleta->categoria->cto_nome ?? 'S/C' }} - {{ $atleta->atl_dt_nasc ? date('d/m/Y', strtotime($atleta->atl_dt_nasc)) : '' }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs">
                                            @if($atleta->cartaoImpresso())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border-green-200">
                                                    Impresso
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 border-yellow-200">
                                                    Pendente
                                                </span>
                                            @endif
                                        </td>
                                        @hasrole('Administrador')
                                        <td class="px-3 py-2 whitespace-nowrap text-xs">
                                            @php
                                                $transferencias = $atleta->historicoTransferencias ?? collect();
                                                $totalTransf = $transferencias->count();
                                                $ultimaTransf = $transferencias->first();
                                                $historicoJson = $transferencias->map(function($t) {
                                                    return [
                                                        'origem' => $t->timeOrigem->tim_nome ?? 'N/A',
                                                        'destino' => $t->timeDestino->tim_nome ?? 'N/A',
                                                        'data' => $t->created_at ? $t->created_at->format('d/m/Y H:i') : 'S/D'
                                                    ];
                                                })->values();
                                            @endphp

                                            @if($totalTransf > 0 && $ultimaTransf)
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="inline-flex items-center gap-1 font-semibold text-gray-800 dark:text-gray-200">
                                                        <svg class="w-3.5 h-3.5 text-amber-500 shrink-0 no-print" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                        </svg>
                                                        <span>Veio de: <strong class="text-indigo-600 dark:text-indigo-400">{{ $ultimaTransf->timeOrigem->tim_nome ?? 'Time Anterior' }}</strong></span>
                                                    </span>
                                                    <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                                                        <span>{{ $ultimaTransf->created_at ? $ultimaTransf->created_at->format('d/m/Y') : '' }}</span>
                                                        @if($totalTransf > 1)
                                                            <button type="button" 
                                                                    @click="openHistoryModal('{{ addslashes($atleta->atl_nome) }}', {{ json_encode($historicoJson) }})" 
                                                                    class="no-print inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 hover:bg-amber-200 transition cursor-pointer">
                                                                +{{ $totalTransf - 1 }} anterior{{ $totalTransf - 1 > 1 ? 'es' : '' }}
                                                            </button>
                                                            <span class="print-only text-[10px]">({{ $totalTransf }} transf.)</span>
                                                        @else
                                                            <button type="button" 
                                                                    @click="openHistoryModal('{{ addslashes($atleta->atl_nome) }}', {{ json_encode($historicoJson) }})" 
                                                                    class="no-print text-indigo-500 hover:underline text-[10px] cursor-pointer">
                                                                detalhes
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500 font-medium">—</span>
                                            @endif
                                        </td>
                                        @endhasrole
                                        <td class="px-3 py-2 whitespace-nowrap text-[10px] text-gray-600 dark:text-gray-400 print-only">
                                            <span class="block">RG: {{ $atleta->atl_rg }}</span>
                                            <span class="block">CPF: {{ $atleta->atl_cpf ?? 'N/A' }}</span>
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
                    <p class="text-gray-500 dark:text-gray-400">Nenhum atleta encontrado com os filtros aplicados.</p>
                </div>
            @endforelse
        </div>

        <!-- Modal Histórico de Transferências (Admin) -->
        @hasrole('Administrador')
        <div x-show="historyModalOpen" 
             style="display: none;"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto no-print" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="historyModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="historyModalOpen = false" 
                     class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="historyModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">
                    
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Histórico de Transferências</span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="modalAtletaNome"></h3>
                        </div>
                        <button @click="historyModalOpen = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="relative border-l-2 border-indigo-200 dark:border-indigo-800 ml-4 space-y-6">
                            <template x-for="(item, index) in modalHistorico" :key="index">
                                <div class="relative pl-6">
                                    <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-indigo-600 border-2 border-white dark:border-gray-800"></div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400" x-text="item.data"></span>
                                        <span x-show="index === 0" class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">Mais recente</span>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                                        <span class="font-medium text-gray-500 dark:text-gray-400">De:</span> 
                                        <span class="font-bold text-red-600 dark:text-red-400" x-text="item.origem"></span>
                                        <span class="mx-1 font-bold">➔</span>
                                        <span class="font-medium text-gray-500 dark:text-gray-400">Para:</span> 
                                        <span class="font-bold text-green-600 dark:text-green-400" x-text="item.destino"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-3 flex justify-end border-t border-gray-100 dark:border-gray-700">
                        <button type="button" 
                                @click="historyModalOpen = false" 
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-bold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endhasrole
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
