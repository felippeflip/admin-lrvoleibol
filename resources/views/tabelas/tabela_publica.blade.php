<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classificação - {{ $campeonato->cpo_nome }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        principal: '#1e3a8a', // blue-900 (pode personalizar)
                        secundario: '#ea580c', // orange-600 (pode personalizar)
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom Scrolls for mobile */
        .table-scroll::-webkit-scrollbar {
            height: 6px;
        }
        .table-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .table-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .table-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-transparent font-sans text-gray-800 antialiased p-2 sm:p-4">

    <!-- Container Principal do Iframe -->
    <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-principal to-blue-700 p-6 text-white text-center md:text-left flex flex-col md:flex-row items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight uppercase">{{ $campeonato->cpo_nome }}</h1>
                <p class="text-blue-100 mt-1 font-medium text-lg">Categoria: <span class="text-secundario font-bold">{{ $categoria->cto_nome }}</span></p>
            </div>
            <!-- Logo ou Escudo Opcional (se tiver logo do evento depois) -->
            <div class="mt-4 md:mt-0 hidden sm:block">
                <svg class="w-12 h-12 text-blue-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
        </div>

        @php 
            $grupos = $dados['grupos']; 
            $equipesParticipantes = $dados['equipes'];
            $jogosTodos = $dados['jogos_todos'];
            $seriesPlayoffs = $dados['series_playoffs'] ?? [];
            $temPlayoffs = $dados['tem_playoffs'] ?? false;
        @endphp

        @if($temPlayoffs)
        <!-- Abas Interativas de Fases (Ativas quando há jogos de Semi-finais / Finais) -->
        <div class="bg-gray-100 border-b border-gray-300 px-4 pt-3 flex space-x-2 overflow-x-auto table-scroll shadow-inner" id="page-tabs-bar">
            <button onclick="changePageTab('classificatoria')" id="page-tab-btn-classificatoria" 
                    class="page-tab-btn px-4 py-2.5 font-black text-sm uppercase rounded-t-lg transition-all flex items-center gap-2 bg-white text-principal border-t-2 border-l border-r border-secundario shadow-sm">
                <svg class="w-4 h-4 text-principal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                Fase Classificatória
            </button>
            @foreach($seriesPlayoffs as $sKey => $serie)
                <button onclick="changePageTab('{{ $serie['slug'] }}')" id="page-tab-btn-{{ $serie['slug'] }}" 
                        class="page-tab-btn px-4 py-2.5 font-black text-sm uppercase rounded-t-lg transition-all flex items-center gap-2 bg-gray-200 text-gray-600 hover:bg-gray-300">
                    @if($sKey == 'ouro') 🏆 @elseif($sKey == 'prata') 🥈 @elseif($sKey == 'bronze') 🥉 @else ⚔️ @endif
                    {{ $serie['nome'] }}
                </button>
            @endforeach
        </div>
        @endif

        <div id="page-tab-content-classificatoria" class="page-tab-content block">
        <!-- EQUIPES PARTICIPANTES -->
        <div class="px-6 pt-6 pb-4 bg-white">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4 uppercase flex items-center">
                <svg class="w-5 h-5 mr-2 text-principal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Equipes Participantes
            </h3>
            <div class="flex flex-wrap gap-3" id="equipes-filtros-container">
                <button onclick="filtrarJogosEquipe(event, 'todos')" class="filter-eqp-btn active px-3 py-1.5 bg-principal text-white border border-principal text-sm font-semibold rounded-md shadow-sm whitespace-nowrap hover:opacity-90 transition-opacity">
                    Todas as Equipes
                </button>
                @forelse($equipesParticipantes as $eqp)
                    <button onclick="filtrarJogosEquipe(event, {{ $eqp->eqp_cpo_id }})" class="filter-eqp-btn px-3 py-1.5 bg-blue-50 border border-blue-200 text-blue-800 hover:bg-principal hover:text-white hover:border-principal text-sm font-semibold rounded-md shadow-sm whitespace-nowrap transition-colors">
                        {{ $eqp->equipe->time->tim_nome ?? 'Time Desconhecido' }}
                    </button>
                @empty
                    <span class="text-gray-500 text-sm italic">Nenhuma equipe configurada para esta categoria.</span>
                @endforelse
            </div>
        </div>

        <!-- JOGOS DA CATEGORIA -->
        <div class="bg-gray-50 border-t border-b border-gray-200">
            <div class="p-6">
                <!-- Controle de Filtros Dinâmico -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-300 pb-3 mb-4 gap-4">
                    <h3 class="text-lg font-bold text-gray-800 uppercase flex items-center whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2 text-principal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Tabela de Jogos
                    </h3>
                    
                    @php
                        // Obter lista única de Grupos/Fases para gerar botões
                        $fasesUnicas = collect($jogosTodos)->pluck('jgo_fase')->map(function($fase) {
                            $fStr = $fase ?? 'classificatoria';
                            if (preg_match('/(Grupo\s[A-Z0-9]+)/i', $fStr, $matches)) {
                                return mb_strtoupper($matches[1]);
                            }
                            return mb_strtoupper($fStr);
                        })->unique()->filter()->sort();
                    @endphp
                    
                    @if($fasesUnicas->count() > 1)
                        <div class="flex items-center space-x-2 w-full sm:w-auto overflow-x-auto pb-1 table-scroll">
                            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Filtro:</span>
                            <button onclick="filtrarJogos(event, 'todos')" class="filter-jogo-btn active px-3 py-1.5 bg-principal text-white border border-principal text-xs font-bold rounded shadow-sm whitespace-nowrap hover:opacity-90 transition-opacity">TODOS</button>
                            @foreach($fasesUnicas as $fu)
                                <button onclick="filtrarJogos(event, '{{ Str::slug($fu) }}')" class="filter-jogo-btn px-3 py-1.5 bg-white border border-gray-300 text-gray-600 hover:bg-gray-100 text-xs font-bold rounded shadow-sm whitespace-nowrap transition-colors">{{ $fu }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="jogos-grid-container">
                    @forelse($jogosTodos as $jogo)
                        @php
                            $fStrItem = $jogo->jgo_fase ?? 'classificatoria';
                            if (preg_match('/(Grupo\s[A-Z0-9]+)/i', $fStrItem, $matches)) {
                                $fSimplificada = mb_strtoupper($matches[1]);
                            } else {
                                $fSimplificada = mb_strtoupper($fStrItem);
                            }
                        @endphp
                        <div class="jogo-card bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow p-3 relative overflow-hidden flex flex-col justify-between" data-fase="{{ Str::slug($fSimplificada) }}" data-mandante="{{ $jogo->jgo_eqp_cpo_mandante_id }}" data-visitante="{{ $jogo->jgo_eqp_cpo_visitante_id }}">
                            <!-- Tipo de Fase -->
                            <div class="absolute top-0 right-0 bg-gray-100 text-gray-500 text-[9px] font-bold px-2 py-1 rounded-bl-lg uppercase border-l border-b border-gray-200">
                                {{ $jogo->jgo_fase ?? 'classificatoria' }}
                            </div>
                            
                            <!-- Data e Status -->
                            <div class="text-[11px] text-gray-500 mb-2 font-medium flex items-center mt-1">
                                @if(in_array($jogo->jgo_res_status, ['pendente', 'aprovado']))
                                    <span class="bg-green-100 text-green-800 px-1.5 py-0.5 rounded mr-2 font-bold tracking-tight">REALIZADO</span>
                                @else
                                    <span class="bg-orange-100 text-orange-800 px-1.5 py-0.5 rounded mr-2 font-bold tracking-tight">A REALIZAR</span>
                                @endif
                                <span class="truncate">
                                    {{ $jogo->jgo_dt_jogo ? \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') : 'A Definir' }} 
                                    {{ $jogo->jgo_hora_jogo ? ' às '.\Carbon\Carbon::parse($jogo->jgo_hora_jogo)->format('H:i') : '' }}
                                </span>
                            </div>

                            <!-- Placar / Times -->
                            <div class="flex flex-col space-y-1 relative mb-2">
                                <!-- Mandante -->
                                <div class="flex justify-between items-center bg-gray-50 rounded p-1.5 {{ (int)$jogo->jgo_vencedor_mandante === 1 ? 'border border-green-300 ring-1 ring-green-300' : '' }}">
                                    <span class="font-bold text-gray-800 text-[11px] truncate max-w-[70%]" title="{{ $jogo->mandante->equipe->time->tim_nome ?? 'A Definir' }}">
                                        {{ $jogo->mandante->equipe->time->tim_nome ?? 'A Definir' }}
                                    </span>
                                    @if(in_array($jogo->jgo_res_status, ['pendente', 'aprovado']))
                                        <div class="flex space-x-1">
                                            @php $setsM = 0; @endphp
                                            @foreach($jogo->resultadoSets as $set)
                                                @php if(($set->set_pontos_mandante ?? 0) > ($set->set_pontos_visitante ?? 0)) $setsM++; @endphp
                                                <span class="text-[9px] w-4 h-4 flex items-center justify-center bg-gray-200 text-gray-700 rounded-sm">{{ $set->set_pontos_mandante ?? 0 }}</span>
                                            @endforeach
                                            <span class="text-xs font-black w-5 h-5 flex items-center justify-center {{ (int)$jogo->jgo_vencedor_mandante === 1 ? 'bg-principal text-white rounded ml-1' : 'text-gray-400 ml-1 bg-white border border-gray-200' }}">{{ $setsM }}</span>
                                        </div>
                                    @endif
                                </div>
                                <!-- Vis -->
                                <div class="flex justify-between items-center bg-gray-50 rounded p-1.5 {{ (int)$jogo->jgo_vencedor_mandante === 0 ? 'border border-green-300 ring-1 ring-green-300' : '' }}">
                                    <span class="font-bold text-gray-800 text-[11px] truncate max-w-[70%]" title="{{ $jogo->visitante->equipe->time->tim_nome ?? 'A Definir' }}">
                                        {{ $jogo->visitante->equipe->time->tim_nome ?? 'A Definir' }}
                                    </span>
                                    @if(in_array($jogo->jgo_res_status, ['pendente', 'aprovado']))
                                        <div class="flex space-x-1">
                                            @php $setsV = 0; @endphp
                                            @foreach($jogo->resultadoSets as $set)
                                                @php if(($set->set_pontos_visitante ?? 0) > ($set->set_pontos_mandante ?? 0)) $setsV++; @endphp
                                                <span class="text-[9px] w-4 h-4 flex items-center justify-center bg-gray-200 text-gray-700 rounded-sm">{{ $set->set_pontos_visitante ?? 0 }}</span>
                                            @endforeach
                                            <span class="text-xs font-black w-5 h-5 flex items-center justify-center {{ (int)$jogo->jgo_vencedor_mandante === 0 ? 'bg-principal text-white rounded ml-1' : 'text-gray-400 ml-1 bg-white border border-gray-200' }}">{{ $setsV }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="absolute inset-y-0 left-1/2 flex items-center justify-center -translate-x-1/2">
                                     <span class="text-[8px] text-gray-300 font-bold bg-white px-0.5 rounded shadow-sm border border-gray-100">X</span>
                                </div>
                            </div>
                            
                            <!-- Local -->
                            @if($jogo->ginasio)
                            <div class="text-[10px] text-gray-400 truncate flex items-center pt-1 border-t border-gray-100 mt-auto">
                                <svg class="w-3 h-3 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="truncate">{{ $jogo->ginasio->gin_nome }}</span>
                            </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-500 italic py-2 text-sm">
                            Nenhum jogo cadastrado para esta categoria até o momento.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TABELA DE CLASSIFICAÇÃO (PONTUAÇÃO) -->
        <div class="px-6 pt-6 pb-2 bg-white">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2 uppercase flex items-center">
                <svg class="w-5 h-5 mr-2 text-secundario" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Classificação
            </h3>
        </div>
        @if(count($grupos) > 1)
        <div class="bg-gray-50 border-b border-gray-200 px-4 pt-4 flex space-x-2 overflow-x-auto table-scroll" id="tabs-container">
            @php $i = 0; @endphp
            @foreach($grupos as $nomeGrupo => $equipes)
                <button onclick="changeTab('{{ Str::slug($nomeGrupo) }}')" id="tab-btn-{{ Str::slug($nomeGrupo) }}" class="tab-btn pb-3 px-4 font-bold text-sm uppercase transition-colors whitespace-nowrap {{ $i === 0 ? 'text-principal border-b-4 border-secundario' : 'text-gray-500 hover:text-gray-700 border-b-4 border-transparent' }}">
                    {{ $nomeGrupo }}
                </button>
                @php $i++; @endphp
            @endforeach
        </div>
        @endif

        <!-- Conteúdo dos Grupos -->
        <div class="p-0 sm:p-2" id="tab-contents">
            @php $i = 0; @endphp
            @foreach($grupos as $nomeGrupo => $equipes)
            <div id="tab-{{ Str::slug($nomeGrupo) }}" class="tab-content transition-opacity duration-300 {{ $i === 0 ? 'block' : 'hidden' }}">
                
                @if(count($grupos) == 1)
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-xl font-bold text-gray-800">{{ $nomeGrupo }}</h2>
                    </div>
                @endif

                <div class="overflow-x-auto table-scroll">
                    <table class="w-full text-sm text-left whitespace-nowrap">
                        <thead class="text-xs text-gray-600 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-center w-12 rounded-tl-lg">#</th>
                                <th scope="col" class="px-4 py-3 font-bold">Equipe</th>
                                <th scope="col" class="px-3 py-3 text-center" title="Pontos">PTS</th>
                                <th scope="col" class="px-3 py-3 text-center text-gray-500" title="Jogos">J</th>
                                <th scope="col" class="px-3 py-3 text-center text-green-600" title="Vitórias">V</th>
                                <th scope="col" class="px-3 py-3 text-center text-red-600" title="Derrotas">D</th>
                                <th scope="col" class="px-3 py-3 text-center" title="Sets Pró">SP</th>
                                <th scope="col" class="px-3 py-3 text-center" title="Sets Contra">SC</th>
                                <th scope="col" class="px-3 py-3 text-center font-bold text-principal" title="Set Average">SA</th>
                                <th scope="col" class="px-3 py-3 text-center" title="Pontos Pró">PP</th>
                                <th scope="col" class="px-3 py-3 text-center" title="Pontos Contra">PC</th>
                                <th scope="col" class="px-4 py-3 text-center font-bold text-principal rounded-tr-lg" title="Ponto Average">PA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipes as $index => $eqp)
                            <tr class="border-b border-gray-100 hover:bg-blue-50/50 transition-colors {{ $index < 4 ? 'bg-white' : 'bg-gray-50' }}">
                                <td class="px-4 py-3 text-center font-bold {{ $index === 0 ? 'text-secundario text-base' : 'text-gray-500' }}">
                                    {{ $index + 1 }}º
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-800">
                                    {{ $eqp['nome'] }}
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-gray-900 bg-gray-100/50">
                                    {{ $eqp['pontos'] }}
                                </td>
                                <td class="px-3 py-3 text-center text-gray-600">{{ $eqp['jogos'] }}</td>
                                <td class="px-3 py-3 text-center text-green-600 font-medium">{{ $eqp['vitorias'] }}</td>
                                <td class="px-3 py-3 text-center text-red-600 font-medium">{{ $eqp['derrotas'] }}</td>
                                <td class="px-3 py-3 text-center text-gray-600">{{ $eqp['sets_pro'] }}</td>
                                <td class="px-3 py-3 text-center text-gray-600">{{ $eqp['sets_con'] }}</td>
                                <td class="px-3 py-3 text-center font-bold text-principal">
                                    {{ $eqp['set_avg'] == 999 ? 'MAX' : number_format($eqp['set_avg'], 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-center text-gray-600">{{ $eqp['pontos_pro'] }}</td>
                                <td class="px-3 py-3 text-center text-gray-600">{{ $eqp['pontos_con'] }}</td>
                                <td class="px-4 py-3 text-center font-bold text-principal">
                                    {{ $eqp['pt_avg'] == 999 ? 'MAX' : number_format($eqp['pt_avg'], 3, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                            @if(count($equipes) == 0)
                                <tr>
                                    <td colspan="12" class="px-4 py-8 text-center text-gray-500 italic">Nenhuma equipe ou jogo processado.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

            </div>
            @php $i++; @endphp
            @endforeach
        </div>

        <!-- Legenda -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-xs text-gray-500 flex flex-wrap gap-x-4 gap-y-2">
            <span><strong>PTS:</strong> Pontos</span>
            <span><strong>J:</strong> Jogos</span>
            <span><strong>V:</strong> Vitórias</span>
            <span><strong>D:</strong> Derrotas</span>
            <span><strong>SP:</strong> Sets Pró</span>
            <span><strong>SC:</strong> Sets Contra</span>
            <span><strong>SA:</strong> Set Average (SP/SC)</span>
            <span><strong>PP:</strong> Pontos Pró</span>
            <span><strong>PC:</strong> Pontos Contra</span>
            <span class="w-full mt-1 text-gray-400 italic">* Regra: 3x0 ou 3x1 (3 pts vencedor). 3x2 (2 pts vencedor, 1 pt perdedor).</span>
        </div>
        </div> <!-- Fim #page-tab-content-classificatoria -->

        @if($temPlayoffs)
            @foreach($seriesPlayoffs as $sKey => $serie)
                <div id="page-tab-content-{{ $serie['slug'] }}" class="page-tab-content hidden p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                    
                    <!-- Header da Série -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-200 mb-6 gap-2">
                        <div>
                            <h2 class="text-2xl font-black uppercase text-principal tracking-tight flex items-center gap-2">
                                @if($sKey == 'ouro') 🏆 @elseif($sKey == 'prata') 🥈 @elseif($sKey == 'bronze') 🥉 @else ⚔️ @endif
                                {{ $serie['nome'] }}
                            </h2>
                            <p class="text-xs text-gray-500 font-medium">Confrontos eliminatórios da Categoria: <span class="font-bold text-secundario">{{ $categoria->cto_nome }}</span></p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase bg-secundario text-white shadow-sm w-fit">
                            Fase Eliminatória
                        </span>
                    </div>

                    @if($sKey === 'quartas' && !empty($serie['jogos']))
                        <!-- Quartas de Final -->
                        <div class="mb-8">
                            <h3 class="text-lg font-black text-gray-800 uppercase mb-4 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                Quartas de Final
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($serie['jogos'] as $jogo)
                                    @include('tabelas.partials.card_jogo_playoff', ['jogo' => $jogo])
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Semifinal -->
                        @if(!empty($serie['semifinal']))
                            <div class="mb-8">
                                <h3 class="text-lg font-black text-gray-800 uppercase mb-4 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                    {{ $serie['nome'] }} - Semifinal
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    @foreach($serie['semifinal'] as $jogo)
                                        @include('tabelas.partials.card_jogo_playoff', ['jogo' => $jogo])
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Final -->
                        @if(!empty($serie['final']))
                            <div class="mb-8">
                                <h3 class="text-lg font-black text-gray-800 uppercase mb-4 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                                    {{ $serie['nome'] }} - Final
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    @foreach($serie['final'] as $jogo)
                                        @include('tabelas.partials.card_jogo_playoff', ['jogo' => $jogo])
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Disputa de 3º e 4º Colocados -->
                        @if(!empty($serie['disputa_3']))
                            <div class="mb-8">
                                <h3 class="text-lg font-black text-gray-800 uppercase mb-4 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span>
                                    {{ $serie['nome'] }} - Disputa de 3º e 4º Colocados
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    @foreach($serie['disputa_3'] as $jogo)
                                        @include('tabelas.partials.card_jogo_playoff', ['jogo' => $jogo])
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Classificação Final / Pódio da Série -->
                        <div class="mt-10 mb-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-black text-center text-gray-800 uppercase tracking-tight mb-8 flex items-center justify-center gap-2">
                                <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                Classificação Final - {{ $serie['nome'] }}
                            </h3>

                            <div class="flex flex-col md:flex-row items-end justify-center gap-4 max-w-2xl mx-auto pt-6">
                                <!-- 2º Lugar -->
                                <div class="w-full md:w-1/3 flex flex-col items-center order-2 md:order-1">
                                    <div class="w-12 h-12 rounded-full bg-gray-200 border-2 border-gray-400 flex items-center justify-center font-black text-gray-700 text-lg shadow-sm mb-2">
                                        2º
                                    </div>
                                    <div class="text-center font-bold text-sm text-gray-800 mb-2 truncate w-full px-2" title="{{ $serie['podio']['segundo'] ?? 'A Definir' }}">
                                        {{ $serie['podio']['segundo'] ?? 'A Definir' }}
                                    </div>
                                    <div class="w-full bg-gradient-to-t from-gray-300 to-gray-200 h-28 rounded-t-xl flex items-center justify-center text-gray-600 font-extrabold uppercase text-xs shadow-inner">
                                        Vice-Campeão
                                    </div>
                                </div>

                                <!-- 1º Lugar (Campeão) -->
                                <div class="w-full md:w-1/3 flex flex-col items-center order-1 md:order-2">
                                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-yellow-300 to-amber-500 border-4 border-yellow-200 flex items-center justify-center font-black text-white text-2xl shadow-lg mb-2 transform -translate-y-2">
                                        🏆
                                    </div>
                                    <div class="text-center font-black text-base text-principal mb-2 truncate w-full px-2" title="{{ $serie['podio']['primeiro'] ?? 'A Definir' }}">
                                        {{ $serie['podio']['primeiro'] ?? 'A Definir' }}
                                    </div>
                                    <div class="w-full bg-gradient-to-t from-yellow-500 to-amber-400 h-40 rounded-t-xl flex flex-col items-center justify-center text-white font-black uppercase text-sm shadow-md">
                                        <span>1º Lugar</span>
                                        <span class="text-[10px] text-yellow-100 font-semibold tracking-wider">CAMPEÃO</span>
                                    </div>
                                </div>

                                <!-- 3º Lugar -->
                                <div class="w-full md:w-1/3 flex flex-col items-center order-3">
                                    <div class="w-12 h-12 rounded-full bg-amber-100 border-2 border-amber-600 flex items-center justify-center font-black text-amber-800 text-lg shadow-sm mb-2">
                                        3º
                                    </div>
                                    <div class="text-center font-bold text-sm text-gray-800 mb-2 truncate w-full px-2" title="{{ $serie['podio']['terceiro'] ?? 'A Definir' }}">
                                        {{ $serie['podio']['terceiro'] ?? 'A Definir' }}
                                    </div>
                                    <div class="w-full bg-gradient-to-t from-amber-200 to-amber-100 h-20 rounded-t-xl flex items-center justify-center text-amber-800 font-extrabold uppercase text-xs shadow-inner">
                                        3º Lugar
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        @endif

        <div class="bg-gray-800 text-center py-3 text-xs text-gray-400 rounded-b-2xl">
            Gerado automaticamente por Liga Regional de Voleibol Campinas em {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        </div>

    </div>

    <!-- JS Limitado Estrito para Tabs -->
    <script>
        // Troca de Abas Principais (Classificatória x Séries dos Playoffs)
        function changePageTab(targetSlug) {
            document.querySelectorAll('.page-tab-content').forEach(c => {
                c.classList.add('hidden');
                c.classList.remove('block');
            });
            document.querySelectorAll('.page-tab-btn').forEach(b => {
                b.classList.remove('bg-white', 'text-principal', 'border-t-2', 'border-l', 'border-r', 'border-secundario', 'shadow-sm');
                b.classList.add('bg-gray-200', 'text-gray-600');
            });

            const targetContent = document.getElementById('page-tab-content-' + targetSlug);
            if(targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('block');
            }
            const targetBtn = document.getElementById('page-tab-btn-' + targetSlug);
            if(targetBtn) {
                targetBtn.classList.remove('bg-gray-200', 'text-gray-600');
                targetBtn.classList.add('bg-white', 'text-principal', 'border-t-2', 'border-l', 'border-r', 'border-secundario', 'shadow-sm');
            }
        }

        function changeTab(targetSlug) {
            // Esconde todo conteúdo
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(c => {
                c.classList.add('hidden');
                c.classList.remove('block');
            });
            // Tira ativação dos botoões
            const btns = document.querySelectorAll('.tab-btn');
            btns.forEach(b => {
                b.classList.remove('text-principal', 'border-b-4', 'border-secundario');
                b.classList.add('text-gray-500', 'hover:text-gray-700', 'border-b-4', 'border-transparent');
            });

            // Mostra o target
            document.getElementById('tab-' + targetSlug).classList.remove('hidden');
            document.getElementById('tab-' + targetSlug).classList.add('block');
            // Dá estado de UI ativo no botao target
            const tabBtn = document.getElementById('tab-btn-' + targetSlug);
            if(tabBtn) {
                tabBtn.classList.remove('text-gray-500', 'hover:text-gray-700', 'border-transparent');
                tabBtn.classList.add('text-principal', 'border-secundario');
            }
        }

        // Filtros globais
        let currentFase = 'todos';
        let currentEquipe = 'todos';

        function applyFilters() {
            const cards = document.querySelectorAll('.jogo-card');
            let anyVisible = false;

            cards.forEach(c => {
                const faseCard = c.getAttribute('data-fase');
                const mandanteCard = c.getAttribute('data-mandante');
                const visitanteCard = c.getAttribute('data-visitante');

                let matchFase = (currentFase === 'todos' || faseCard === currentFase);
                let matchEquipe = (currentEquipe === 'todos' || mandanteCard === String(currentEquipe) || visitanteCard === String(currentEquipe));

                if(matchFase && matchEquipe) {
                    c.style.display = 'flex';
                    anyVisible = true;
                } else {
                    c.style.display = 'none';
                }
            });

            let fallbackMsg = document.getElementById('filt-fallback-msg');
            if(!anyVisible) {
                if(!fallbackMsg) {
                    fallbackMsg = document.createElement('div');
                    fallbackMsg.id = 'filt-fallback-msg';
                    fallbackMsg.className = 'col-span-full text-center text-gray-500 italic py-4 text-sm';
                    fallbackMsg.innerText = 'Nenhum jogo encontrado para os filtros selecionados.';
                    document.getElementById('jogos-grid-container').appendChild(fallbackMsg);
                }
                fallbackMsg.style.display = 'block';
            } else if(fallbackMsg) {
                fallbackMsg.style.display = 'none';
            }
        }

        // JS Filtragem de Jogos por Fase/Grupo
        function filtrarJogos(event, fase) {
            currentFase = fase;
            const btns = document.querySelectorAll('.filter-jogo-btn');
            btns.forEach(b => {
                b.classList.remove('bg-principal', 'text-white', 'border-principal');
                b.classList.add('bg-white', 'text-gray-600', 'border-gray-300');
            });
            event.currentTarget.classList.remove('bg-white', 'text-gray-600', 'border-gray-300');
            event.currentTarget.classList.add('bg-principal', 'text-white', 'border-principal');

            applyFilters();
        }

        // JS Filtragem de Jogos por Equipe
        function filtrarJogosEquipe(event, equipeId) {
            currentEquipe = equipeId;
            const btns = document.querySelectorAll('.filter-eqp-btn');
            btns.forEach(b => {
                b.classList.remove('bg-principal', 'text-white', 'border-principal');
                b.classList.add('bg-blue-50', 'text-blue-800', 'border-blue-200');
            });
            event.currentTarget.classList.remove('bg-blue-50', 'text-blue-800', 'border-blue-200');
            event.currentTarget.classList.add('bg-principal', 'text-white', 'border-principal');

            // Scroll para tabela de jogos suavemente apenas se não for 'todos'
            document.getElementById('jogos-grid-container').scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            applyFilters();
        }
    </script>
</body>
</html>
