<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Jogos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Dashboard Content -->
                    <div class="space-y-6">

                        {{-- 1. SECTION: ADMINISTRATOR --}}
                        @if(isset($adminStats))
                            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border-l-4 border-blue-500">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">Painel do
                                    Administrador</h3>

                                <div class="relative overflow-x-auto">
                                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">Campeonato</th>
                                                <th scope="col" class="px-6 py-3 text-center">Jogos Novos</th>
                                                <th scope="col" class="px-6 py-3 text-center">Finalizados</th>
                                                <th scope="col" class="px-6 py-3 text-center">Com Apontamento</th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-center bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400">
                                                    Pendente Apontamento</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($adminStats as $stat)
                                                <tr
                                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <th scope="row"
                                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <a href="{{ route('jogos.index', ['campeonato_id' => $stat['id']]) }}" class="text-blue-600 hover:text-blue-900 hover:underline">
                                                            {{ $stat['campeonato'] }}
                                                        </a>
                                                    </th>
                                                    <td class="px-6 py-4 text-center font-bold text-blue-600">
                                                        {{ $stat['novos'] }}
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        {{ $stat['finalizados'] }}
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-green-600">
                                                        {{ $stat['com_apontamento'] }}
                                                    </td>
                                                    <td
                                                        class="px-6 py-4 text-center font-bold text-red-600 bg-red-50 dark:bg-red-900/10">
                                                        {{ $stat['sem_apontamento'] }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-6 py-4 text-center">Nenhum campeonato ativo
                                                        encontrada.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- 1.1 WEEKLY GAMES CARDS (ADMIN) --}}
                        @if(isset($adminJogos) && count($adminJogos) > 0)
                            <div class="mt-8">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">
                                    Jogos da Semana (Recentes e Próximos)
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                    @foreach($adminJogos as $jogo)
                                        @php
                                            // Define border color based on status
                                            $borderColor = 'border-gray-200';
                                            $statusColor = 'text-gray-600';
                                            
                                            if ($jogo->jgo_status == 'ativo') {
                                                $borderColor = 'border-blue-400';
                                                $statusColor = 'text-blue-600';
                                            } elseif ($jogo->jgo_status == 'inativo') {
                                                $borderColor = 'border-red-400';
                                                $statusColor = 'text-red-600';
                                            }
                                            
                                            // Result status logic
                                            $resStatus = $jogo->jgo_res_status ?? 'nao_informado';
                                            $resBadgeClass = 'bg-gray-100 text-gray-800';
                                            $resLabel = 'Não Informado';

                                            if ($resStatus == 'aprovado') {
                                                $resBadgeClass = 'bg-green-100 text-green-800';
                                                $resLabel = 'Aprovado';
                                            } elseif ($resStatus == 'pendente') {
                                                $resBadgeClass = 'bg-yellow-100 text-yellow-800';
                                                $resLabel = 'Pendente';
                                            }
                                        @endphp
                                    
                                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-l-4 {{ $borderColor }} p-4 flex flex-col justify-between hover:shadow-lg transition-shadow duration-200">
                                            
                                            <!-- Validar Objetos Relacionados -->
                                            @php
                                                $campeonatoNome = $jogo->mandante && $jogo->mandante->campeonato ? $jogo->mandante->campeonato->cpo_nome : 'Camp. N/A';
                                                $mandanteNome = $jogo->mandante && $jogo->mandante->equipe ? $jogo->mandante->equipe->eqp_nome_detalhado : 'Mandante N/A';
                                                $visitanteNome = $jogo->visitante && $jogo->visitante->equipe ? $jogo->visitante->equipe->eqp_nome_detalhado : 'Visitante N/A';
                                                $localNome = $jogo->ginasio ? $jogo->ginasio->gin_nome : 'Local não definido';
                                                $categoriaNome = $jogo->mandante && $jogo->mandante->equipe && $jogo->mandante->equipe->categoria ? $jogo->mandante->equipe->categoria->cto_nome : null;
                                                $turnoNome = $jogo->jgo_fase ?? null;
                                            @endphp

                                            <!-- Header: Date & Camp -->
                                            <div class="mb-3">
                                                 <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wider bg-gray-100 text-gray-600 mb-1">
                                                    {{ $campeonatoNome }}
                                                </span>
                                                <!-- Badges: Categoria e Turno -->
                                                <div class="flex flex-wrap gap-1 mb-1">
                                                    @if($categoriaNome)
                                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                                                            {{ $categoriaNome }}
                                                         </span>
                                                    @endif
                                                    @if($turnoNome)
                                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">
                                                            {{ $turnoNome }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm font-bold text-gray-900 dark:text-white flex items-center mt-1">
                                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }} 
                                                    <span class="mx-2 text-gray-300">|</span>
                                                    {{ substr($jogo->jgo_hora_jogo, 0, 5) }}
                                                </div>
                                            </div>
                                            
                                            <!-- Teams -->
                                            <div class="flex items-center justify-between mb-4 bg-gray-50 dark:bg-gray-700/50 p-2 rounded">
                                                <div class="flex flex-col items-center w-[45%]">
                                                    <div class="font-bold text-sm text-gray-900 dark:text-white text-center break-words leading-tight">
                                                        {{ $mandanteNome }}
                                                    </div>
                                                </div>
                                                <div class="text-xs font-bold text-gray-400">VS</div>
                                                <div class="flex flex-col items-center w-[45%]">
                                                    <div class="font-bold text-sm text-gray-900 dark:text-white text-center break-words leading-tight">
                                                        {{ $visitanteNome }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Location -->
                                            <div class="mb-3 flex items-start space-x-2 text-xs text-gray-600 dark:text-gray-300">
                                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                <span class="mt-0.5">{{ $localNome }}</span>
                                            </div>
                                            
                                            <!-- Separator -->
                                            <hr class="border-gray-100 dark:border-gray-700 mb-3">
                                            
                                            <!-- Referees -->
                                            <div class="text-xs space-y-1 mb-3 bg-white dark:bg-gray-800">
                                                 <div class="font-semibold text-gray-400 text-[10px] uppercase mb-1">Arbitragem</div>
                                                 @if($jogo->arbitroPrincipal)
                                                    <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                        <span class="w-5 font-bold text-blue-500">P:</span>
                                                        <span class="truncate">{{ $jogo->arbitroPrincipal->name }}</span>
                                                    </div>
                                                 @endif
                                                 @if($jogo->arbitroSecundario)
                                                     <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                        <span class="w-5 font-bold text-blue-500">S:</span>
                                                        <span class="truncate">{{ $jogo->arbitroSecundario->name }}</span>
                                                    </div>
                                                 @endif
                                                 @if($jogo->apontador)
                                                     <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                        <span class="w-5 font-bold text-blue-500">A:</span>
                                                        <span class="truncate">{{ $jogo->apontador->name }}</span>
                                                    </div>
                                                 @endif
                                                 @if(!$jogo->arbitroPrincipal && !$jogo->arbitroSecundario && !$jogo->apontador)
                                                     <div class="text-gray-400 italic pl-1">Não definida</div>
                                                 @endif
                                            </div>
                                            
                                            @php
                                                $solicitacaoPendente = current(array_filter($jogo->solicitacoesAlteracao->all(), fn($s) => $s->status == 'pendente'));
                                            @endphp
                                            <!-- Footer Status -->
                                            <div class="flex flex-col gap-2 mt-auto pt-3 border-t border-gray-100 dark:border-gray-700">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-xs font-medium {{ $resBadgeClass }} px-2 py-0.5 rounded border border-transparent">
                                                        {{ $resLabel }}
                                                    </span>
                                                    <span class="text-xs font-bold {{ $statusColor }} uppercase tracking-wide">
                                                        {{ ucfirst($jogo->jgo_status) }}
                                                    </span>
                                                </div>
                                                @if($solicitacaoPendente)
                                                     <div class="flex items-center justify-between mt-2 px-2 py-1 bg-yellow-100 border border-yellow-300 text-yellow-800 text-xs font-bold rounded">
                                                         <div class="truncate mr-2" title="Solicitado por: {{ $solicitacaoPendente->user->name ?? 'N/A' }} - Motivo: {{ $solicitacaoPendente->motivo }}">
                                                             ⚠ Alt: {{ Str::limit($solicitacaoPendente->motivo, 15) }}
                                                         </div>
                                                         <a href="{{ route('jogos.edit', $jogo->jgo_id) }}" class="underline text-yellow-900 hover:text-yellow-700 shrink-0">
                                                             Editar Jogo
                                                         </a>
                                                     </div>
                                                @endif
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 2. SECTION: JUIZ/ARBITRO --}}
                        @if(isset($juizStats))
                            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border-l-4 border-yellow-500">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">Painel do
                                    Árbitro / Apontador </h3>

                                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                                    <div
                                        class="px-4 py-5 bg-gray-50 dark:bg-gray-700 shadow rounded-lg overflow-hidden sm:p-6">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total de
                                            Escalações</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                                            {{ $juizStats['total_participacao'] }}</dd>
                                    </div>
                                    <div
                                        class="px-4 py-5 bg-blue-50 dark:bg-blue-900/20 shadow rounded-lg overflow-hidden sm:p-6">
                                        <dt class="text-sm font-medium text-blue-600 dark:text-blue-400 truncate">Jogos
                                            Futuros</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-blue-900 dark:text-blue-100">
                                            {{ $juizStats['novos'] }}</dd>
                                    </div>
                                    <div
                                        class="px-4 py-5 bg-green-50 dark:bg-green-900/20 shadow rounded-lg overflow-hidden sm:p-6">
                                        <dt class="text-sm font-medium text-green-600 dark:text-green-400 truncate">Jogos
                                            Realizados</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-green-900 dark:text-green-100">
                                            {{ $juizStats['realizados'] }}</dd>
                                    </div>
                                </dl>

                                {{-- Filters for Juiz --}}
                                <div class="mb-4 mt-6">
                                    <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row gap-4">
                                        <div class="w-full sm:w-1/3">
                                            <label for="search" class="sr-only">Buscar</label>
                                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                                placeholder="Buscar por ID ou Campeonato..."
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div class="w-full sm:w-1/3">
                                            <label for="status" class="sr-only">Status</label>
                                            <select name="status" id="status"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                <option value="ativo" {{ request('status', 'ativo') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                                <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                                <option value="todos" {{ request('status') == 'todos' ? 'selected' : '' }}>Todos</option>
                                            </select>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                Filtrar
                                            </button>
                                            <a href="{{ route('dashboard') }}"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                                Limpar
                                            </a>
                                        </div>
                                    </form>
                                </div>

                                {{-- List Games for Juiz (Cards) --}}
                                @if(isset($juizJogos) && count($juizJogos) > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                        @foreach($juizJogos as $jogo)
                                            @php
                                                // Define border color based on status
                                                $borderColor = 'border-gray-200';
                                                $statusColor = 'text-gray-600';
                                                
                                                if ($jogo->jgo_status == 'ativo') {
                                                    $borderColor = 'border-yellow-400';
                                                    $statusColor = 'text-yellow-600';
                                                } elseif ($jogo->jgo_status == 'inativo') {
                                                    $borderColor = 'border-red-400';
                                                    $statusColor = 'text-red-600';
                                                }
                                                
                                                // Result status logic
                                                $resStatus = $jogo->jgo_res_status ?? 'nao_informado';
                                                $resBadgeClass = 'bg-gray-100 text-gray-800';
                                                $resLabel = 'Não Informado';

                                                if ($resStatus == 'aprovado') {
                                                    $resBadgeClass = 'bg-green-100 text-green-800';
                                                    $resLabel = 'Aprovado';
                                                } elseif ($resStatus == 'pendente') {
                                                    $resBadgeClass = 'bg-yellow-100 text-yellow-800';
                                                    $resLabel = 'Pendente';
                                                }
                                                
                                                // Validar Objetos Relacionados
                                                $campeonatoNome = $jogo->mandante && $jogo->mandante->campeonato ? $jogo->mandante->campeonato->cpo_nome : 'Camp. N/A';
                                                $mandanteNome = $jogo->mandante && $jogo->mandante->equipe ? $jogo->mandante->equipe->eqp_nome_detalhado : 'Mandante N/A';
                                                $visitanteNome = $jogo->visitante && $jogo->visitante->equipe ? $jogo->visitante->equipe->eqp_nome_detalhado : 'Visitante N/A';
                                                $localNome = $jogo->ginasio ? $jogo->ginasio->gin_nome : 'Local não definido';
                                                $categoriaNome = $jogo->mandante && $jogo->mandante->equipe && $jogo->mandante->equipe->categoria ? $jogo->mandante->equipe->categoria->cto_nome : null;
                                                $turnoNome = $jogo->jgo_fase ?? null;
                                            @endphp
                                        
                                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-l-4 {{ $borderColor }} p-4 flex flex-col justify-between hover:shadow-lg transition-shadow duration-200">
                                                
                                                <!-- Header: Date & Camp -->
                                                <div class="mb-3">
                                                     <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wider bg-gray-100 text-gray-600 mb-1">
                                                        {{ $campeonatoNome }}
                                                    </span>
                                                    <!-- Badges: Categoria e Turno -->
                                                    <div class="flex flex-wrap gap-1 mb-1">
                                                        @if($categoriaNome)
                                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                                                                {{ $categoriaNome }}
                                                             </span>
                                                        @endif
                                                        @if($turnoNome)
                                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">
                                                                {{ $turnoNome }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm font-bold text-gray-900 dark:text-white flex items-center mt-1">
                                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }} 
                                                        <span class="mx-2 text-gray-300">|</span>
                                                        {{ substr($jogo->jgo_hora_jogo, 0, 5) }}
                                                    </div>
                                                </div>
                                                
                                                <!-- Teams -->
                                                <div class="flex items-center justify-between mb-4 bg-gray-50 dark:bg-gray-700/50 p-2 rounded">
                                                    <div class="flex flex-col items-center w-[45%]">
                                                        <div class="font-bold text-sm text-gray-900 dark:text-white text-center break-words leading-tight">
                                                            {{ $mandanteNome }}
                                                        </div>
                                                    </div>
                                                    <div class="text-xs font-bold text-gray-400">VS</div>
                                                    <div class="flex flex-col items-center w-[45%]">
                                                        <div class="font-bold text-sm text-gray-900 dark:text-white text-center break-words leading-tight">
                                                            {{ $visitanteNome }}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Location -->
                                                <div class="mb-3 flex items-start space-x-2 text-xs text-gray-600 dark:text-gray-300">
                                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    <span class="mt-0.5 text-blue-600 hover:underline">
                                                        @if($jogo->ginasio)
                                                            <a href="{{ $jogo->ginasio->google_maps_link }}" target="_blank">
                                                                {{ $localNome }}
                                                            </a>
                                                        @else
                                                            {{ $localNome }}
                                                        @endif
                                                    </span>
                                                </div>
                                                
                                                <!-- Separator -->
                                                <hr class="border-gray-100 dark:border-gray-700 mb-3">
                                                
                                                <!-- Referees -->
                                                <div class="text-xs space-y-1 mb-3 bg-white dark:bg-gray-800">
                                                     <div class="font-semibold text-gray-400 text-[10px] uppercase mb-1">Arbitragem</div>
                                                     @if($jogo->arbitroPrincipal)
                                                        <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                            <span class="w-5 font-bold text-blue-500">P:</span>
                                                            <span class="truncate">{{ $jogo->arbitroPrincipal->name }}</span>
                                                        </div>
                                                     @endif
                                                     @if($jogo->arbitroSecundario)
                                                         <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                            <span class="w-5 font-bold text-blue-500">S:</span>
                                                            <span class="truncate">{{ $jogo->arbitroSecundario->name }}</span>
                                                        </div>
                                                     @endif
                                                     @if($jogo->apontador)
                                                         <div class="flex items-center text-gray-700 dark:text-gray-300">
                                                            <span class="w-5 font-bold text-blue-500">A:</span>
                                                            <span class="truncate">{{ $jogo->apontador->name }}</span>
                                                        </div>
                                                     @endif
                                                     @if(!$jogo->arbitroPrincipal && !$jogo->arbitroSecundario && !$jogo->apontador)
                                                         <div class="text-gray-400 italic pl-1">Não definida</div>
                                                     @endif
                                                </div>
                                                
                                                <!-- Footer Status -->
                                                <div class="flex justify-between items-center mt-auto pt-3 border-t border-gray-100 dark:border-gray-700 font-sans">
                                                    @if(Auth::id() == $jogo->jgo_apontador)
                                                        <a href="{{ route('resultados.create', $jogo->jgo_id) }}" class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded transition-colors duration-200">
                                                            Informar Resultado
                                                        </a>
                                                    @else
                                                        <span class="text-xs font-medium {{ $resBadgeClass }} px-2 py-0.5 rounded border border-transparent">
                                                            {{ $resLabel }}
                                                        </span>
                                                    @endif
                                                    
                                                    <span class="text-xs font-bold {{ $statusColor }} uppercase tracking-wide">
                                                        {{ ucfirst($jogo->jgo_status) }}
                                                    </span>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-md">
                                        <p class="text-yellow-700 dark:text-yellow-300">Nenhum jogo encontrado com os filtros selecionados.</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- 3. SECTION: RESPONSAVEL PELO TIME --}}
                        @if(isset($timeStats))
                            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border-l-4 border-indigo-500">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">Painel do
                                    Time</h3>

                                @if($timeStats)
                                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-6">
                                        <div
                                            class="px-4 py-5 bg-gray-50 dark:bg-gray-700 shadow rounded-lg overflow-hidden sm:p-6">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total de
                                                Jogos no Campeonato</dt>
                                            <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                                                {{ $timeStats['escalado_total'] }}</dd>
                                        </div>
                                        <div
                                            class="px-4 py-5 bg-green-50 dark:bg-green-900/20 shadow rounded-lg overflow-hidden sm:p-6">
                                            <dt class="text-sm font-medium text-green-600 dark:text-green-400 truncate">Jogos
                                                Concluídos</dt>
                                            <dd class="mt-1 text-3xl font-semibold text-green-900 dark:text-green-100">
                                                {{ $timeStats['concluidos'] }}</dd>
                                        </div>
                                        <div
                                            class="px-4 py-5 bg-indigo-50 dark:bg-indigo-900/20 shadow rounded-lg overflow-hidden sm:p-6">
                                            <dt class="text-sm font-medium text-indigo-600 dark:text-indigo-400 truncate">Jogos
                                                Agendados</dt>
                                            <dd class="mt-1 text-3xl font-semibold text-indigo-900 dark:text-indigo-100">
                                                {{ $timeStats['proximos'] }}</dd>
                                        </div>
                                    </dl>

                                    <!-- Filters -->
                                    <div class="mb-4">
                                        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row flex-wrap gap-3">
                                            <div class="w-full sm:w-auto flex-1">
                                                <label for="search" class="sr-only">Buscar</label>
                                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                                    placeholder="Buscar por ID ou Campeonato..."
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>
                                            <div class="w-full sm:w-auto">
                                                <label for="status" class="sr-only">Status</label>
                                                <select name="status" id="status"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    <option value="ativo" {{ request('status', 'ativo') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                                    <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                                    <option value="todos" {{ request('status') == 'todos' ? 'selected' : '' }}>Todos</option>
                                                </select>
                                            </div>
                                            @if(isset($timeCategorias) && $timeCategorias->count())
                                            <div class="w-full sm:w-auto">
                                                <label for="categoria_id" class="sr-only">Categoria</label>
                                                <select name="categoria_id" id="categoria_id"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    <option value="">Todas as Categorias</option>
                                                    @foreach($timeCategorias as $cat)
                                                        <option value="{{ $cat->cto_id }}" {{ request('categoria_id') == $cat->cto_id ? 'selected' : '' }}>
                                                            {{ $cat->cto_nome }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endif
                                            @if(isset($timeTurnos) && $timeTurnos->count())
                                            <div class="w-full sm:w-auto">
                                                <label for="turno" class="sr-only">Turno</label>
                                                <select name="turno" id="turno"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    <option value="">Todos os Turnos</option>
                                                    @foreach($timeTurnos as $tn)
                                                        <option value="{{ $tn }}" {{ request('turno') == $tn ? 'selected' : '' }}>{{ $tn }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endif
                                            <div class="flex gap-2 items-start">
                                                <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    Filtrar
                                                </button>
                                                <a href="{{ route('dashboard') }}"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                                    Limpar
                                                </a>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Games List (Cards) -->
                                    @if(isset($timeJogos) && count($timeJogos) > 0)
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                            @foreach($timeJogos as $jogo)
                                                @php
                                                    // Define border color based on status
                                                    $borderColor = 'border-gray-200';
                                                    $statusColor = 'text-gray-600';
                                                    
                                                    if ($jogo->jgo_status == 'ativo') {
                                                        $borderColor = 'border-indigo-400';
                                                        $statusColor = 'text-indigo-600';
                                                    } elseif ($jogo->jgo_status == 'inativo') {
                                                        $borderColor = 'border-red-400';
                                                        $statusColor = 'text-red-600';
                                                    }
                                                    
                                                    // Result status logic
                                                    $resStatus = $jogo->jgo_res_status ?? 'nao_informado';
                                                    $resBadgeClass = 'bg-gray-100 text-gray-800';
                                                    $resLabel = 'Não Informado';

                                                    if ($resStatus == 'aprovado') {
                                                        $resBadgeClass = 'bg-green-100 text-green-800';
                                                        $resLabel = 'Aprovado';
                                                    } elseif ($resStatus == 'pendente') {
                                                        $resBadgeClass = 'bg-yellow-100 text-yellow-800';
                                                        $resLabel = 'Pendente';
                                                    }
                                                    
                                                    // Validar Objetos Relacionados
                                                    $campeonatoNome = $jogo->mandante && $jogo->mandante->campeonato ? $jogo->mandante->campeonato->cpo_nome : 'Camp. N/A';
                                                    $mandanteNome = $jogo->mandante && $jogo->mandante->equipe ? $jogo->mandante->equipe->eqp_nome_detalhado : 'Mandante N/A';
                                                    $visitanteNome = $jogo->visitante && $jogo->visitante->equipe ? $jogo->visitante->equipe->eqp_nome_detalhado : 'Visitante N/A';
                                                    $localNome = $jogo->ginasio ? $jogo->ginasio->gin_nome : 'Local não definido';
                                                    $categoriaNome = $jogo->mandante && $jogo->mandante->equipe && $jogo->mandante->equipe->categoria ? $jogo->mandante->equipe->categoria->cto_nome : null;
                                                    $turnoNome = $jogo->jgo_fase ?? null;
                                                @endphp
                                            
                                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-l-4 {{ $borderColor }} p-4 flex flex-col justify-between hover:shadow-lg transition-shadow duration-200">
                                                    
                                                    <!-- Header: Date & Camp -->
                                                    <div class="mb-3">
                                                         <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wider bg-gray-100 text-gray-600 mb-1">
                                                            {{ $campeonatoNome }}
                                                        </span>
                                                        <!-- Badges: Categoria e Turno -->
                                                        <div class="flex flex-wrap gap-1 mb-1">
                                                            @if($categoriaNome)
                                                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                                                                    {{ $categoriaNome }}
                                                                </span>
                                                            @endif
                                                            @if($turnoNome)
                                                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">
                                                                    {{ $turnoNome }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-sm font-bold text-gray-900 dark:text-white flex items-center mt-1">
                                                            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                            {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }} 
                                                            <span class="mx-2 text-gray-300">|</span>
                                                            {{ substr($jogo->jgo_hora_jogo, 0, 5) }}
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Teams -->
                                                    <div class="flex items-center justify-between mb-4 bg-gray-50 dark:bg-gray-700/50 p-2 rounded">
                                                        <div class="flex flex-col items-center w-[45%]">
                                                            <div class="font-bold text-sm text-gray-900 dark:text-white text-center break-words leading-tight">
                                                                {{ $mandanteNome }}
                                                            </div>
                                                        </div>
                                                        <div class="text-xs font-bold text-gray-400">VS</div>
                                                        <div class="flex flex-col items-center w-[45%]">
                                                            <div class="font-bold text-sm text-gray-900 dark:text-white text-center break-words leading-tight">
                                                                {{ $visitanteNome }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Location -->
                                                    <div class="mb-3 flex items-start space-x-2 text-xs text-gray-600 dark:text-gray-300">
                                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                        <span class="mt-0.5 text-blue-600 hover:underline">
                                                            @if($jogo->ginasio)
                                                                <a href="{{ $jogo->ginasio->google_maps_link }}" target="_blank">
                                                                    {{ $localNome }}
                                                                </a>
                                                            @else
                                                                {{ $localNome }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Separator -->
                                                    <hr class="border-gray-100 dark:border-gray-700 mb-3">
                                                    
                                                    @php
                                                        $solicitacaoPendente = current(array_filter($jogo->solicitacoesAlteracao->all(), fn($s) => $s->status == 'pendente'));
                                                    @endphp
                                                    <!-- Footer Status -->
                                                    <div class="flex flex-col gap-2 mt-auto pt-3 border-t border-gray-100 dark:border-gray-700">
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-xs font-medium {{ $resBadgeClass }} px-2 py-0.5 rounded border border-transparent">
                                                                {{ $resLabel }}
                                                            </span>
                                                            <span class="text-xs font-bold {{ $statusColor }} uppercase tracking-wide">
                                                                {{ ucfirst($jogo->jgo_status) }}
                                                            </span>
                                                        </div>
                                                        @if($jogo->jgo_status == 'ativo' && $jogo->jgo_dt_jogo >= now()->format('Y-m-d'))
                                                            @if($solicitacaoPendente)
                                                                <div class="text-center mt-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded cursor-help" title="Solicitado por: {{ $solicitacaoPendente->user->name ?? 'N/A' }} - Motivo: {{ $solicitacaoPendente->motivo }}">
                                                                    Alteração Pendente
                                                                </div>
                                                            @else
                                                                <button type="button" onclick="abrirModalSolicitacao({{ $jogo->jgo_id }})" class="mt-2 w-full text-center border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 font-bold py-1.5 px-3 rounded text-xs transition duration-150">
                                                                    Solicitar Alteração
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </div>

                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-md">
                                            <p class="text-yellow-700 dark:text-yellow-300">Nenhum jogo encontrado com os filtros selecionados.</p>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">Você tem o perfil de Responsável de Time ou Comissão Técnica, mas
                                        nenhum time está vinculado ao seu usuário.</p>
                                @endif
                            </div>
                        @endif

                        @if(!isset($adminStats) && !isset($juizStats) && !isset($timeStats))
                            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                                <p>Bem-vindo ao sistema Admin LRVoleibol.</p>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE SOLICITAÇÃO DE ALTERAÇÃO -->
    <div id="modalSolicitacao" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Solicitar Alteração de Jogo</h3>
                <button onclick="fecharModalSolicitacao()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="formSolicitacao" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="motivo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motivo da Alteração (Data, Local, etc) <span class="text-red-500">*</span></label>
                    <textarea name="motivo" id="motivo" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Ex: Precisamos adiar o jogo em 1 hora e o ginásio fechou."></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="fecharModalSolicitacao()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Enviar Solicitação</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalSolicitacao(jogoId) {
            let form = document.getElementById('formSolicitacao');
            form.action = `/jogos/${jogoId}/solicitar-alteracao`;
            document.getElementById('modalSolicitacao').classList.remove('hidden');
        }

        function fecharModalSolicitacao() {
            document.getElementById('modalSolicitacao').classList.add('hidden');
            document.getElementById('formSolicitacao').reset();
        }
    </script>
</x-app-layout>

<!-- DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#jogosTable').DataTable({
            "order": [[0, "asc"], [4, "asc"]],
            "language": {
                "decimal": "",
                "emptyTable": "Nenhum dado disponível na tabela",
                "info": "Mostrando _START_ até _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 até 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros no total)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Carregando...",
                "processing": "Processando...",
                "search": "Buscar:",
                "zeroRecords": "Nenhum registro correspondente encontrado",
                "paginate": {
                    "first": "Primeiro",
                    "last": "Último",
                    "next": "Próximo",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": ativar para classificar a coluna em ordem crescente",
                    "sortDescending": ": ativar para classificar a coluna em ordem decrescente"
                }
            },
            "initComplete": function () {
                // Estilizar o elemento select
                $('select[name="jogosTable_length"]').addClass('form-select form-select-sm');
            }
        });
    });
</script>