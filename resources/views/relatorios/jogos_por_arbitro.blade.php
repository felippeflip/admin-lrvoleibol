<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Relatório: Jogos por Árbitro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('relatorios.jogos-por-arbitro') }}" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="w-full md:w-1/2">
                            <label for="arbitro_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Selecione o Árbitro/Apontador</label>
                            <select name="arbitro_id" id="arbitro_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">-- Selecione --</option>
                                @foreach($arbitros as $arbitro)
                                    <option value="{{ $arbitro->id }}" {{ request('arbitro_id') == $arbitro->id ? 'selected' : '' }}>
                                        {{ $arbitro->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="w-full md:w-1/4">
                            <label for="ano" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ano</label>
                            <input type="number" name="ano" id="ano" value="{{ $ano }}" min="2000" max="2100" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>

                        <div class="w-full md:w-1/4 flex gap-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                Gerar Relatório
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($arbitroSelecionado)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $arbitroSelecionado->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total de jogos no ano de {{ $ano }}: <strong class="text-lg text-blue-600 dark:text-blue-400">{{ $totalJogos }}</strong></p>
                        </div>
                        @if($totalJogos > 0)
                            <div class="mt-4 md:mt-0">
                                <a href="{{ route('relatorios.jogos-por-arbitro.print', ['arbitro_id' => $arbitroSelecionado->id, 'ano' => $ano]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Imprimir / Salvar PDF
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    @if($totalJogos > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Data e Hora</th>
                                    <th scope="col" class="px-6 py-3">Campeonato / Categoria</th>
                                    <th scope="col" class="px-6 py-3">Confronto</th>
                                    <th scope="col" class="px-6 py-3">Local</th>
                                    <th scope="col" class="px-6 py-3">Função Atuante</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jogos as $jogo)
                                    @php
                                        $funcao = [];
                                        if ($jogo->jgo_arbitro_principal == $arbitroSelecionado->id) $funcao[] = 'Árbitro Principal';
                                        if ($jogo->jgo_arbitro_secundario == $arbitroSelecionado->id) $funcao[] = 'Árbitro Secundário';
                                        if ($jogo->jgo_apontador == $arbitroSelecionado->id) $funcao[] = 'Apontador';
                                    @endphp
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <b>{{ $jogo->jgo_dt_jogo ? \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') : 'N/A' }}</b><br>
                                            <span class="text-xs">{{ $jogo->jgo_hora_jogo ? \Carbon\Carbon::parse($jogo->jgo_hora_jogo)->format('H:i') : 'N/A' }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <b>{{ $jogo->mandante->campeonato->cpo_nome ?? 'N/A' }}</b><br>
                                            <span class="text-xs">{{ $jogo->mandante->equipe->categoria->cto_nome ?? 'N/A' }} - {{ $jogo->jgo_fase }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-gray-900 dark:text-white">{{ $jogo->mandante->equipe->eqp_nome_detalhado ?? 'N/A' }}</span>
                                            <span class="mx-1 text-gray-400">vs</span>
                                            <span class="text-gray-900 dark:text-white">{{ $jogo->visitante->equipe->eqp_nome_detalhado ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $jogo->ginasio->gin_nome ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @foreach($funcao as $f)
                                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800 mb-1 inline-block">{{ $f }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                            Nenhum jogo encontrado para este árbitro no ano de {{ $ano }}.
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
