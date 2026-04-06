@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header & Ações --}}
    <div class="mb-4 flex flex-col gap-2">
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-gray-100 flex items-center justify-between">
            <span>Partidas</span>
            <span class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs py-1 px-2.5 rounded-full font-bold">
                {{ $jogos->total() }} registros
            </span>
        </h2>
    </div>

    {{-- Ações Administrativas (Horizontal Scroll Mínimo) --}}
    @hasrole('Administrador')
    <div class="flex overflow-x-auto gap-2 pb-2 mb-4 scrollbar-hide">
        <a href="{{ route('jogos.create') }}" class="whitespace-nowrap shrink-0 flex items-center justify-center gap-1 text-white bg-blue-700 hover:bg-blue-800 font-semibold rounded-lg text-sm px-4 py-3 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Novo
        </a>
        <button type="button" onclick="abrirModalClassificacao()" class="whitespace-nowrap shrink-0 flex items-center justify-center gap-1 text-gray-900 bg-yellow-400 hover:bg-yellow-500 font-bold rounded-lg text-sm px-4 py-3 shadow-sm">
            🏆 Tabela
        </button>
        <button type="button" onclick="abrirModalNumeracao()" class="whitespace-nowrap shrink-0 flex items-center justify-center gap-1 text-white bg-purple-700 hover:bg-purple-800 font-semibold rounded-lg text-sm px-4 py-3 shadow-sm">
             Numerar
        </button>
    </div>
    @endhasrole

    {{-- Mensagens de Alerta (Flash) --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 font-semibold py-3 px-4 rounded-lg shadow-sm mb-4 text-sm animate-fade-in-down">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filtros Retráteis no Mobile --}}
    <div x-data="{ openFilters: {{ request()->hasAny(['titulo', 'campeonato_id', 'categoria_id']) ? 'true' : 'false' }} }" class="mb-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <button @click="openFilters = !openFilters" class="w-full px-4 py-3 flex items-center justify-between text-gray-700 dark:text-gray-300 font-medium bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filtros Dinâmicos
                @if(request()->except('page'))
                    <span class="w-2 h-2 rounded-full md:w-3 md:h-3 bg-red-500 ml-1"></span>
                @endif
            </span>
            <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': openFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>

        <div x-show="openFilters" x-collapse>
            <form method="GET" action="{{ route('jogos.index') }}" class="p-4 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Partida (Título)</label>
                    <input type="text" name="titulo" value="{{ request('titulo') }}" class="w-full bg-gray-50 border border-gray-300 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Ex: Time A vs Time B">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Campeonato</label>
                    <select name="campeonato_id" class="w-full bg-gray-50 border border-gray-300 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white truncate">
                        <option value="">Qualquer Campeonato</option>
                        @foreach($campeonatos as $camp)
                            <option value="{{ $camp->cpo_id }}" {{ request('campeonato_id') == $camp->cpo_id ? 'selected' : '' }}>{{ \Illuminate\Support\Str::limit($camp->cpo_nome, 40) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">De (Data)</label>
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="w-full bg-gray-50 border border-gray-300 text-sm rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Até (Data)</label>
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="w-full bg-gray-50 border border-gray-300 text-sm rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700 mt-4">
                    <a href="{{ route('jogos.index') }}" class="w-1/3 flex justify-center items-center py-2.5 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600">
                        Limpar
                    </a>
                    <button type="submit" class="w-2/3 flex justify-center py-2.5 px-4 text-sm font-semibold text-white focus:outline-none bg-orange-600 hover:bg-orange-700 rounded-lg shadow-sm">
                        Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista Vertical de Cards de Jogos --}}
    <div class="space-y-4 pb-8">
        @forelse ($jogos as $jogo)
            @php
                // Cálculo de cores da borda baseada no status de apontamento (herdado de sua lógica visual original)
                $dataStr = $jogo->meta['_event_start_date']->meta_value ?? null;
                $horaStr = $jogo->meta['_event_start_time']->meta_value ?? '00:00';
                $borderClass = 'border-gray-200 dark:border-gray-700'; // Default Neutro
                
                $statusApontamento = 'neutro';
                if ($dataStr) {
                    try {
                        $dtOnly = \Carbon\Carbon::parse($dataStr)->format('Y-m-d');
                        $start = \Carbon\Carbon::parse($dtOnly . ' ' . $horaStr);
                        $now = \Carbon\Carbon::now();
                        $endWindow = $start->copy()->addHours(3);
                        $hasResult = in_array($jogo->jgo_res_status, ['pendente', 'aprovado']);

                        if ($now < $start) {
                            $statusApontamento = 'verde'; // Vai acontecer
                            $borderClass = 'border-green-400 dark:border-green-600';
                        } elseif ($now >= $start && $now <= $endWindow) {
                            $statusApontamento = 'amarelo'; // No momento
                            $borderClass = 'border-yellow-400 dark:border-yellow-600 border-l-4';
                        } else {
                            if (!$hasResult) {
                                $statusApontamento = 'vermelho'; // Atrasado
                                $borderClass = 'border-red-500 dark:border-red-600 border-l-4';
                            } else {
                                $statusApontamento = 'concluido';
                                $borderClass = 'border-gray-300 dark:border-gray-600';
                            }
                        }
                    } catch (\Exception $e) {}
                }
                
                $solAlteracao = $jogo->solicitacoesAlteracao ? current(array_filter($jogo->solicitacoesAlteracao->all(), fn($s) => $s->status == 'pendente')) : null;
            @endphp

            <div x-data="{ openDetails: false }" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $borderClass }} overflow-hidden">
                {{-- Cabeçalho do Card --}}
                <div class="p-3 bg-gray-50/50 dark:bg-gray-800/80 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center px-4">
                    <div class="flex items-center gap-2">
                        @if($jogo->jgo_numero_jogo)
                            <span class="bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 text-[11px] font-bold px-2 py-0.5 rounded border border-indigo-200 dark:border-indigo-800/50">
                                #{{ $jogo->jgo_numero_jogo }}
                            </span>
                        @endif
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest truncate max-w-[200px]">
                            {{ $jogo->mandante->equipe->categoria->cto_nome ?? 'Categoria -' }}
                        </span>
                    </div>
                </div>

                {{-- Corpo Principal --}}
                <div class="p-4 relative">
                    {{-- Alert Overlay para Solicitacao de alt --}}
                    @if($solAlteracao)
                        <div class="absolute -mx-4 -mt-4 mb-3 w-[calc(100%+2rem)] bg-yellow-50 dark:bg-yellow-900/30 border-b border-yellow-200 dark:border-yellow-800/50 px-4 py-2 flex items-start gap-2">
                            <svg class="w-4 h-4 text-yellow-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div class="text-xs text-yellow-800 dark:text-yellow-200 leading-tight">
                                <span class="font-bold block">Alteração Pendente</span>
                                Solicitado por {{ $solAlteracao->user->name ?? 'N/A' }} 
                            </div>
                        </div>
                        <div class="h-8"></div> {{-- Spacer --}}
                    @endif

                    <h3 class="text-base font-extrabold text-gray-900 dark:text-white leading-tight mb-1 pr-8">
                        {{ $jogo->meta['_event_title']->meta_value ?? $jogo->post_title }}
                    </h3>
                    @if($jogo->jgo_fase)
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 font-bold uppercase mb-3">{{ $jogo->jgo_fase }}</p>
                    @endif

                    {{-- Data e Local Box --}}
                    <div class="grid grid-cols-2 gap-y-2 gap-x-4 mb-4 bg-gray-50 dark:bg-gray-900/40 p-3 rounded-lg border border-gray-100 dark:border-gray-700/60">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <div>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium leading-none mb-0.5">Data</p>
                                <p class="text-xs font-bold text-gray-900 dark:text-gray-200 leading-none">
                                    {{ isset($jogo->meta['_event_start_date']) ? Carbon\Carbon::parse($jogo->meta['_event_start_date']->meta_value)->format('d/m/Y') : '--/--/----' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium leading-none mb-0.5">Horário</p>
                                <p class="text-xs font-bold text-gray-900 dark:text-gray-200 leading-none">
                                    {{ isset($jogo->meta['_event_start_time']) ? Carbon\Carbon::parse($jogo->meta['_event_start_time']->meta_value)->format('H:i') : '--:--' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-span-2 flex items-start gap-2 mt-1">
                            <svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <div>
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300 leading-tight pr-2">
                                    {{ $jogo->meta['_event_location']->meta_value ?? 'Local não definido' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Badges de Status Inferiores --}}
                    <div class="flex flex-wrap gap-1.5 items-center justify-between">
                        <div class="flex gap-1.5">
                            @if($jogo->jgo_status == 'ativo')
                                <span class="bg-green-100/80 text-green-700 text-[10px] font-bold px-2 py-1 rounded dark:bg-green-900/40 dark:text-green-400">Ativo</span>
                            @else
                                <span class="bg-red-100/80 text-red-700 text-[10px] font-bold px-2 py-1 rounded dark:bg-red-900/40 dark:text-red-400">Inativo</span>
                            @endif

                            @php $resStatus = $jogo->jgo_res_status ?? 'nao_informado'; @endphp
                            @if($resStatus == 'aprovado')
                                <span class="bg-green-100/80 border border-green-200 text-green-700 text-[10px] font-bold px-2 py-1 rounded dark:bg-green-900/50 dark:border-green-800/50 dark:text-green-400">Res. Aprovado</span>
                            @elseif($resStatus == 'pendente')
                                <span class="bg-yellow-100 border border-yellow-200 text-yellow-800 text-[10px] font-bold px-2 py-1 rounded dark:bg-yellow-900/60 dark:border-yellow-700/60 dark:text-yellow-400">Res. Pendente</span>
                            @endif

                            {{-- Icone de Apontamento Vermelho (Só mostra se for critico) --}}
                            @if($statusApontamento == 'vermelho')
                                <span class="text-[10px] bg-red-100 text-red-700 font-bold px-2 py-1 rounded animate-pulse shadow-sm flex items-center gap-1 dark:bg-red-900/50 dark:text-red-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Apontar
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Collapsible Arbitragem --}}
                @hasrole('Administrador')
                <div class="border-t border-gray-100 dark:border-gray-700">
                    <button @click="openDetails = !openDetails" class="w-full text-center py-2 text-xs font-semibold text-gray-500 hover:bg-gray-50 focus:outline-none dark:text-gray-400 dark:hover:bg-gray-800 transition-colors bg-gray-50/50 dark:bg-gray-800/80">
                        <span x-text="openDetails ? 'Ocultar Equipe de Arbitragem' : 'Ver Equipe de Arbitragem'"></span>
                    </button>
                    <div x-show="openDetails" x-collapse>
                        <div class="p-4 bg-gray-50/80 dark:bg-gray-900/40 text-[11px] sm:text-xs">
                            @if(!$jogo->arbitro_principal_nome && !$jogo->arbitro_secundario_nome && !$jogo->apontador_nome)
                                <span class="text-gray-400 italic">Nenhum oficial designado.</span>
                            @else
                                <div class="space-y-2">
                                    @if($jogo->arbitro_principal_nome)
                                        <div class="flex justify-between items-center"><span class="font-bold text-gray-600 dark:text-gray-400">Principal</span> <span class="text-gray-800 dark:text-gray-200">{{ $jogo->arbitro_principal_nome }}</span></div>
                                    @endif
                                    @if($jogo->arbitro_secundario_nome)
                                        <div class="flex justify-between items-center"><span class="font-bold text-gray-600 dark:text-gray-400">Secundário</span> <span class="text-gray-800 dark:text-gray-200">{{ $jogo->arbitro_secundario_nome }}</span></div>
                                    @endif
                                    @if($jogo->apontador_nome)
                                        <div class="flex justify-between items-center"><span class="font-bold text-gray-600 dark:text-gray-400">Apontador</span> <span class="text-gray-800 dark:text-gray-200">{{ $jogo->apontador_nome }}</span></div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endhasrole

                {{-- Barra Rápida de Ações do Card (Full width buttons para o polegar) --}}
                <div class="grid grid-cols-4 border-t border-gray-100 dark:border-gray-700 divide-x divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    
                    {{-- Informar Resultado (Destacado) --}}
                    @if(auth()->user()->hasRole('Administrador') || (isset($jogo->jgo_apontador) && auth()->user()->id == $jogo->jgo_apontador))
                        <a href="{{ route('resultados.create', $jogo->ID) }}" class="flex flex-col items-center justify-center p-3 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 active:bg-blue-100 transition-colors">
                            <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                            <span class="text-[9px] font-bold uppercase tracking-wide">Placar</span>
                        </a>
                    @else
                        <div class="flex items-center justify-center p-3 text-gray-300 dark:text-gray-600"><span class="text-[9px]">-</span></div>
                    @endif

                    {{-- Aprovar Pendente --}}
                    @if($resStatus == 'pendente' && auth()->user()->hasRole('Administrador'))
                        <form action="{{ route('resultados.approve', $jogo->local_id ?? 0) }}" method="POST" class="w-full flex items-center justify-center" onsubmit="return confirm('Aprovar este resultado oficial?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full h-full flex flex-col items-center justify-center p-3 text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/20 active:bg-green-100 transition-colors bg-green-50/30">
                                <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-[9px] font-bold uppercase tracking-wide">Aprovar</span>
                            </button>
                        </form>
                    @else
                         <div class="flex items-center justify-center p-3 text-gray-300 dark:text-gray-600"><span class="text-[9px]">-</span></div>
                    @endif

                    {{-- Editar Admin --}}
                    @hasrole('Administrador')
                        <a href="{{ route('jogos.edit', $jogo->ID) }}" class="flex flex-col items-center justify-center p-3 text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700 active:bg-gray-200 transition-colors">
                            <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            <span class="text-[9px] font-bold uppercase tracking-wide">Editar</span>
                        </a>
                    @else
                         <div class="flex items-center justify-center p-3 text-gray-300 dark:text-gray-600"><span class="text-[9px]">-</span></div>
                    @endhasrole

                    {{-- Excluir Admin --}}
                    @hasrole('Administrador')
                        <form action="{{ route('jogos.destroy', $jogo->ID) }}" method="POST" class="w-full flex items-center justify-center" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full h-full flex flex-col items-center justify-center p-3 text-red-500 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 active:bg-red-100 transition-colors">
                                <svg class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                <span class="text-[9px] font-bold uppercase tracking-wide">Excluir</span>
                            </button>
                        </form>
                    @else
                         <div class="flex items-center justify-center p-3 text-gray-300 dark:text-gray-600"><span class="text-[9px]">-</span></div>
                    @endhasrole
                </div>
            </div>
        @empty
            <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-8 text-center dark:bg-gray-800/50 dark:border-gray-600">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nenhum jogo encontrado.</p>
            </div>
        @endforelse

        {{-- Paginação Simplificada Mod --}}
        <div class="mt-8 mb-16">
            {{ $jogos->links() }}
        </div>
    </div>
</div>

{{-- Modais Nativos herdados da View Original copiados sob AlpineJS ou JS Puro Oculto --}}
{{-- Modal Numerar Jogos --}}
@hasrole('Administrador')
<div id="modal-numeracao" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm mx-auto overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 font-bold text-lg">🔢 Numerar Jogos</div>
        <div class="p-5">
            <p class="text-xs text-gray-500 mb-4 font-medium leading-relaxed">Selecione Campeonato e Categoria para numerar automaticamente todos os jogos por ordem cronológica.</p>
            <div class="space-y-4">
                <div>
                    <select id="modal-campeonato" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 rounded-xl text-sm font-medium focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Selecione o Campeonato...</option>
                        @foreach($campeonatos as $camp)
                            <option value="{{ $camp->cpo_id }}">{{ \Illuminate\Support\Str::limit($camp->cpo_nome, 30) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select id="modal-categoria" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 rounded-xl text-sm font-medium focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Selecione a Categoria...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->cto_id }}">{{ $cat->cto_nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="modal-resultado" class="hidden mt-4 text-sm font-bold p-3 rounded-xl"></div>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700 flex gap-2">
            <button type="button" onclick="fecharModalNumeracao()" class="flex-1 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-bold shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">Cancelar</button>
            <button type="button" id="btn-confirmar-numeracao" onclick="confirmarNumeracao()" class="flex-1 py-3 bg-purple-700 border border-purple-700 text-white rounded-xl text-sm font-bold shadow-sm">Confirmar</button>
        </div>
    </div>
</div>

<div id="modal-classificacao" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm mx-auto overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 font-bold text-lg text-yellow-500 flex items-center gap-2">🏆 Prévia da Tabela</div>
        <div class="p-5">
            <div class="space-y-4">
                <div>
                    <select id="modal-class-campeonato" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 rounded-xl text-sm font-medium focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Selecione o Campeonato...</option>
                        @foreach($campeonatos as $camp)
                            <option value="{{ $camp->cpo_id }}">{{ \Illuminate\Support\Str::limit($camp->cpo_nome, 30) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select id="modal-class-categoria" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-3 rounded-xl text-sm font-medium focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Selecione a Categoria...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->cto_id }}">{{ $cat->cto_nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="modal-class-resultado" class="hidden mt-4 text-sm font-bold p-3 rounded-xl text-red-600 bg-red-100 border border-red-200"></div>
        </div>
        <div class="p-4 bg-yellow-50 dark:bg-gray-900 border-t border-yellow-100 dark:border-gray-700 flex gap-2">
            <button type="button" onclick="fecharModalClassificacao()" class="flex-1 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-bold shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">Voltar</button>
            <button type="button" onclick="confirmarClassificacao()" id="btn-confirmar-classificacao" class="flex-1 py-3 bg-yellow-400 text-gray-900 border border-yellow-500 rounded-xl text-sm font-extrabold shadow-sm active:bg-yellow-500 text-center flex items-center justify-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg> Visualizar</button>
        </div>
    </div>
</div>

<script>
    function abrirModalNumeracao() {
        if('{{ request("campeonato_id") }}') document.getElementById('modal-campeonato').value = '{{ request("campeonato_id") }}';
        if('{{ request("categoria_id") }}') document.getElementById('modal-categoria').value = '{{ request("categoria_id") }}';
        document.getElementById('modal-resultado').classList.add('hidden');
        document.getElementById('modal-numeracao').classList.remove('hidden');
    }
    function fecharModalNumeracao() { document.getElementById('modal-numeracao').classList.add('hidden'); }
    function confirmarNumeracao() {
        const camp = document.getElementById('modal-campeonato').value, cat = document.getElementById('modal-categoria').value, res = document.getElementById('modal-resultado');
        if (!camp || !cat) {
            res.classList.remove('hidden'); res.className = 'mt-4 text-sm font-bold p-3 rounded-xl bg-red-100 text-red-700 border border-red-200'; res.textContent = 'Indique Camp. e Cat.';
            return;
        }
        const btn = document.getElementById('btn-confirmar-numeracao'); btn.disabled = true; btn.textContent = '...';
        fetch('{{ route("jogos.numerar") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ campeonato_id: camp, categoria_id: cat }) })
        .then(r => r.json()).then(d => {
            btn.disabled = false; btn.textContent = 'Confirmar';
            res.classList.remove('hidden');
            if (d.success) { res.className = 'mt-4 text-sm font-bold p-3 rounded-xl bg-green-100 text-green-800 border border-green-200'; res.textContent = d.message; setTimeout(() => window.location.reload(), 1500); }
            else { res.className = 'mt-4 text-sm font-bold p-3 rounded-xl bg-red-100 text-red-700 border border-red-200'; res.textContent = d.error || 'Erro'; }
        }).catch(() => { btn.disabled = false; btn.textContent = 'Confirmar'; res.classList.remove('hidden'); res.textContent = 'Falha na conexão.'; });
    }

    function abrirModalClassificacao() {
        if('{{ request("campeonato_id") }}') document.getElementById('modal-class-campeonato').value = '{{ request("campeonato_id") }}';
        if('{{ request("categoria_id") }}') document.getElementById('modal-class-categoria').value = '{{ request("categoria_id") }}';
        document.getElementById('modal-class-resultado').classList.add('hidden');
        document.getElementById('modal-classificacao').classList.remove('hidden');
    }
    function fecharModalClassificacao() { document.getElementById('modal-classificacao').classList.add('hidden'); }
    function confirmarClassificacao() {
        const camp = document.getElementById('modal-class-campeonato').value, cat = document.getElementById('modal-class-categoria').value, res = document.getElementById('modal-class-resultado');
        if (!camp || !cat) { res.classList.remove('hidden'); res.textContent = 'Indique Camp. e Cat.'; return; }
        document.getElementById('btn-confirmar-classificacao').disabled = true;
        document.getElementById('btn-confirmar-classificacao').innerHTML = '<span class="animate-pulse">Aguarde...</span>';
        window.location.href = `/classificacao/preview/${camp}/${cat}`;
    }
</script>
@endhasrole
@endsection
