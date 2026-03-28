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
        @endphp

        <!-- EQUIPES PARTICIPANTES -->
        <div class="px-6 pt-6 pb-4 bg-white">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4 uppercase flex items-center">
                <svg class="w-5 h-5 mr-2 text-principal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Equipes Participantes
            </h3>
            <div class="flex flex-wrap gap-3">
                @forelse($equipesParticipantes as $eqp)
                    <div class="relative group cursor-pointer inline-block">
                        <span class="bg-blue-50 border border-blue-200 text-blue-800 text-sm font-semibold px-3 py-1.5 rounded-md transition-colors group-hover:bg-principal group-hover:text-white group-hover:border-principal shadow-sm whitespace-nowrap">
                            {{ $eqp->equipe->time->tim_nome ?? 'Time Desconhecido' }}
                        </span>
                        
                        <!-- Wrapper para manter o hover fluindo sem falhas visuais de gap -->
                        <div class="absolute z-50 left-0 top-full pt-2 hidden group-hover:block">
                            <!-- Tooltip com a agenda do Time -->
                            <div class="w-[280px] sm:w-[320px] bg-white rounded-xl shadow-2xl border border-gray-200 text-left overflow-hidden">
                                <div class="p-3 bg-gradient-to-r from-gray-100 to-gray-50 border-b border-gray-200">
                                    <h4 class="font-bold text-gray-800 text-xs">Agenda: {{ \Illuminate\Support\Str::limit($eqp->equipe->time->tim_nome ?? '', 30) }}</h4>
                                </div>
                                <div class="max-h-60 overflow-y-auto p-2 bg-gray-50/50 table-scroll">
                                    @php
                                        $meusJogos = collect($jogosTodos)->filter(function($j) use ($eqp) {
                                            return $j->jgo_eqp_cpo_mandante_id == $eqp->eqp_cpo_id || $j->jgo_eqp_cpo_visitante_id == $eqp->eqp_cpo_id;
                                        });
                                    @endphp
                                    @forelse($meusJogos as $mj)
                                        <div class="mb-2 pb-2 border-b border-gray-200 last:border-0 last:mb-0 last:pb-0 bg-white p-2 rounded shadow-sm">
                                            <div class="flex justify-between items-center text-[10px] text-gray-500 mb-1.5">
                                                <span class="font-bold text-principal bg-blue-50 px-1.5 py-0.5 rounded">{{ $mj->jgo_fase ?? 'Classificatória' }}</span>
                                                <span>
                                                    {{ $mj->jgo_dt_jogo ? \Carbon\Carbon::parse($mj->jgo_dt_jogo)->format('d/m') : 'A Def' }} 
                                                    {{ $mj->jgo_hora_jogo ? \Carbon\Carbon::parse($mj->jgo_hora_jogo)->format('H:i') : '' }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-[11px] font-bold truncate pr-1 {{ $mj->jgo_vencedor_mandante === 1 ? 'text-green-600' : 'text-gray-700' }}">
                                                        {{ $mj->mandante->equipe->time->tim_nome ?? 'A Def' }}
                                                    </span>
                                                    @if(in_array($mj->jgo_res_status, ['pendente', 'aprovado']))
                                                        <span class="text-[10px] font-black w-4 h-4 flex items-center justify-center rounded bg-gray-100 text-gray-800">
                                                            @php $setsM = 0; foreach($mj->resultadoSets as $set) { if(($set->set_pontos_mandante ?? 0) > ($set->set_pontos_visitante ?? 0)) $setsM++; } echo $setsM; @endphp
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-[11px] font-bold truncate pr-1 {{ $mj->jgo_vencedor_mandante === 0 ? 'text-green-600' : 'text-gray-700' }}">
                                                        {{ $mj->visitante->equipe->time->tim_nome ?? 'A Def' }}
                                                    </span>
                                                    @if(in_array($mj->jgo_res_status, ['pendente', 'aprovado']))
                                                        <span class="text-[10px] font-black w-4 h-4 flex items-center justify-center rounded bg-gray-100 text-gray-800">
                                                            @php $setsV = 0; foreach($mj->resultadoSets as $set) { if(($set->set_pontos_visitante ?? 0) > ($set->set_pontos_mandante ?? 0)) $setsV++; } echo $setsV; @endphp
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-xs text-gray-500 italic p-3 text-center">Nenhum jogo na agenda.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
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
                            return $fase ?? 'classificatoria';
                        })->unique()->filter()->sort();
                    @endphp
                    
                    @if($fasesUnicas->count() > 1)
                        <div class="flex items-center space-x-2 w-full sm:w-auto overflow-x-auto pb-1 table-scroll">
                            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Filtro:</span>
                            <button onclick="filtrarJogos(event, 'todos')" class="filter-jogo-btn active px-3 py-1.5 bg-principal text-white border border-principal text-xs font-bold rounded shadow-sm whitespace-nowrap hover:opacity-90 transition-opacity">Todos</button>
                            @foreach($fasesUnicas as $fu)
                                <button onclick="filtrarJogos(event, '{{ Str::slug($fu) }}')" class="filter-jogo-btn px-3 py-1.5 bg-white border border-gray-300 text-gray-600 hover:bg-gray-100 text-xs font-bold rounded shadow-sm whitespace-nowrap transition-colors">{{ $fu }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="jogos-grid-container">
                    @forelse($jogosTodos as $jogo)
                        <div class="jogo-card bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow p-3 relative overflow-hidden flex flex-col justify-between" data-fase="{{ Str::slug($jogo->jgo_fase ?? 'classificatoria') }}">
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
                                <div class="flex justify-between items-center bg-gray-50 rounded p-1.5 {{ $jogo->jgo_vencedor_mandante === 1 ? 'border border-green-300 ring-1 ring-green-300' : '' }}">
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
                                            <span class="text-xs font-black w-5 h-5 flex items-center justify-center {{ $jogo->jgo_vencedor_mandante === 1 ? 'bg-principal text-white rounded ml-1' : 'text-gray-400 ml-1 bg-white border border-gray-200' }}">{{ $setsM }}</span>
                                        </div>
                                    @endif
                                </div>
                                <!-- Vis -->
                                <div class="flex justify-between items-center bg-gray-50 rounded p-1.5 {{ $jogo->jgo_vencedor_mandante === 0 ? 'border border-green-300 ring-1 ring-green-300' : '' }}">
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
                                            <span class="text-xs font-black w-5 h-5 flex items-center justify-center {{ $jogo->jgo_vencedor_mandante === 0 ? 'bg-principal text-white rounded ml-1' : 'text-gray-400 ml-1 bg-white border border-gray-200' }}">{{ $setsV }}</span>
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
            <span><strong>PA:</strong> Ponto Average (PP/PC)</span>
            <span class="w-full mt-1 text-gray-400 italic">* Regra: 3x0 ou 3x1 (3 pts vencedor). 3x2 (2 pts vencedor, 1 pt perdedor). Empate desempata por SA e PA.</span>
        </div>

        <!-- Seção de Eliminatórias (Opcional - se hover jogos nas finais) -->
        @if(count($dados['finais']) > 0)
        <div class="border-t-4 border-secundario bg-white mt-4">
            <div class="p-6 bg-gradient-to-r from-gray-100 to-white">
                <h3 class="text-xl font-extrabold text-principal uppercase mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-secundario" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    Fases Finais Eliminatórias
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($dados['finais'] as $jogo)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow p-4 relative overflow-hidden">
                            <!-- Faixa decorativa com o tipo de fase -->
                            <div class="absolute top-0 right-0 bg-secundario text-white text-[10px] font-bold px-2 py-1 rounded-bl-lg uppercase">
                                {{ str_replace('_', ' ', $jogo->jgo_fase_tipo) }}
                            </div>
                            
                            <div class="text-xs text-gray-500 mb-3 mt-1 font-medium">
                                {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m') }} às {{ \Carbon\Carbon::parse($jogo->jgo_hora_jogo)->format('H:i') }}
                            </div>
                            
                            <div class="flex flex-col space-y-2">
                                <!-- Mandante -->
                                <div class="flex justify-between items-center bg-gray-50 rounded-lg p-2 {{ $jogo->jgo_vencedor_mandante === 1 ? 'border border-green-300' : '' }}">
                                    <span class="font-bold text-gray-800 text-sm truncate max-w-[70%]">
                                        {{ $jogo->mandante->equipe->time->tim_nome ?? 'A Definir' }}
                                    </span>
                                    @if(in_array($jogo->jgo_res_status, ['pendente', 'aprovado']))
                                        <div class="flex space-x-1">
                                            @php $setsM = 0; @endphp
                                            @foreach($jogo->resultadoSets as $set)
                                                @php if(($set->set_pontos_mandante ?? 0) > ($set->set_pontos_visitante ?? 0)) $setsM++; @endphp
                                                <span class="text-[10px] w-5 h-5 flex items-center justify-center bg-gray-200 text-gray-700 rounded-sm">{{ $set->set_pontos_mandante ?? 0 }}</span>
                                            @endforeach
                                            <span class="text-sm font-black w-6 h-6 flex items-center justify-center {{ $jogo->jgo_vencedor_mandante === 1 ? 'bg-principal text-white rounded-md ml-1' : 'text-gray-400 ml-1' }}">{{ $setsM }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Vs</span>
                                    @endif
                                </div>
                                
                                <!-- Visitante -->
                                <div class="flex justify-between items-center bg-gray-50 rounded-lg p-2 {{ $jogo->jgo_vencedor_mandante === 0 ? 'border border-green-300' : '' }}">
                                    <span class="font-bold text-gray-800 text-sm truncate max-w-[70%]">
                                        {{ $jogo->visitante->equipe->time->tim_nome ?? 'A Definir' }}
                                    </span>
                                    @if(in_array($jogo->jgo_res_status, ['pendente', 'aprovado']))
                                        <div class="flex space-x-1">
                                            @php $setsV = 0; @endphp
                                            @foreach($jogo->resultadoSets as $set)
                                                @php if(($set->set_pontos_visitante ?? 0) > ($set->set_pontos_mandante ?? 0)) $setsV++; @endphp
                                                <span class="text-[10px] w-5 h-5 flex items-center justify-center bg-gray-200 text-gray-700 rounded-sm">{{ $set->set_pontos_visitante ?? 0 }}</span>
                                            @endforeach
                                            <span class="text-sm font-black w-6 h-6 flex items-center justify-center {{ $jogo->jgo_vencedor_mandante === 0 ? 'bg-principal text-white rounded-md ml-1' : 'text-gray-400 ml-1' }}">{{ $setsV }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="bg-gray-800 text-center py-3 text-xs text-gray-400 rounded-b-2xl">
            Gerado automaticamente por Liga Regional de Voleibol Campinas em {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        </div>

    </div>

    <!-- JS Limitado Estrito para Tabs -->
    <script>
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

        // JS Filtragem de Jogos
        function filtrarJogos(event, fase) {
            // Update active state visual configs of buttons
            const btns = document.querySelectorAll('.filter-jogo-btn');
            btns.forEach(b => {
                b.classList.remove('bg-principal', 'text-white', 'border-principal');
                b.classList.add('bg-white', 'text-gray-600', 'border-gray-300');
            });
            event.currentTarget.classList.remove('bg-white', 'text-gray-600', 'border-gray-300');
            event.currentTarget.classList.add('bg-principal', 'text-white', 'border-principal');

            // Find all cards
            const cards = document.querySelectorAll('.jogo-card');
            let anyVisible = false;
            
            cards.forEach(c => {
                if(fase === 'todos' || c.getAttribute('data-fase') === fase) {
                    c.style.display = 'flex';
                    anyVisible = true;
                } else {
                    c.style.display = 'none';
                }
            });

            // Fallback message handling if all filtered out
            let fallbackMsg = document.getElementById('filt-fallback-msg');
            if(!anyVisible) {
                if(!fallbackMsg) {
                    fallbackMsg = document.createElement('div');
                    fallbackMsg.id = 'filt-fallback-msg';
                    fallbackMsg.className = 'col-span-full text-center text-gray-500 italic py-4 text-sm';
                    fallbackMsg.innerText = 'Nenhum jogo encontrado para este turno/fase.';
                    document.getElementById('jogos-grid-container').appendChild(fallbackMsg);
                }
                fallbackMsg.style.display = 'block';
            } else if(fallbackMsg) {
                fallbackMsg.style.display = 'none';
            }
        }
    </script>
</body>
</html>
