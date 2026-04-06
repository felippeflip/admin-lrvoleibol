<div class="space-y-4">
    @foreach ($jogos as $jogo)
        @php
            $dataStr = $jogo->meta['_event_start_date']->meta_value ?? $jogo->jgo_dt_jogo;
            $horaStr = $jogo->meta['_event_start_time']->meta_value ?? $jogo->jgo_hora_jogo ?? '00:00';
            $borderClass = 'border-gray-200 dark:border-gray-700'; // Default
            
            $statusApontamento = 'neutro';
            if ($dataStr) {
                try {
                    $dtOnly = \Carbon\Carbon::parse($dataStr)->format('Y-m-d');
                    $start = \Carbon\Carbon::parse($dtOnly . ' ' . $horaStr);
                    $now = \Carbon\Carbon::now();
                    $endWindow = $start->copy()->addHours(3);
                    $hasResult = in_array($jogo->jgo_res_status, ['pendente', 'aprovado']);

                    if ($now < $start) {
                        $statusApontamento = 'verde'; 
                        $borderClass = 'border-green-400 dark:border-green-600';
                    } elseif ($now >= $start && $now <= $endWindow) {
                        $statusApontamento = 'amarelo'; 
                        $borderClass = 'border-yellow-400 dark:border-yellow-600 border-l-4';
                    } else {
                        if (!$hasResult) {
                            $statusApontamento = 'vermelho'; 
                            $borderClass = 'border-red-500 dark:border-red-600 border-l-4';
                        } else {
                            $statusApontamento = 'concluido';
                            $borderClass = 'border-gray-300 dark:border-gray-600';
                        }
                    }
                } catch (\Exception $e) {}
            }
            
            $solAlteracao = $jogo->solicitacoesAlteracao ? current(array_filter($jogo->solicitacoesAlteracao->all(), fn($s) => $s->status == 'pendente')) : null;

            // Nomes Protegidos
            $campeonatoNome = $jogo->mandante && $jogo->mandante->campeonato ? $jogo->mandante->campeonato->cpo_nome : 'Campeonato N/A';
            $mandanteNome = $jogo->mandante && $jogo->mandante->equipe ? $jogo->mandante->equipe->eqp_nome_detalhado : 'Mandante N/A';
            $visitanteNome = $jogo->visitante && $jogo->visitante->equipe ? $jogo->visitante->equipe->eqp_nome_detalhado : 'Visitante N/A';
            $categoriaNome = $jogo->mandante && $jogo->mandante->equipe && $jogo->mandante->equipe->categoria ? $jogo->mandante->equipe->categoria->cto_nome : null;
            $turnoNome = $jogo->jgo_fase ?? null;
        @endphp

        <div x-data="{ openDetails: false }" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $borderClass }} overflow-hidden relative">
            
            @if($solAlteracao)
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border-b border-yellow-200 dark:border-yellow-800/50 px-4 py-2 flex items-start gap-2">
                    <svg class="w-4 h-4 text-yellow-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div class="text-[11px] text-yellow-800 dark:text-yellow-200 leading-tight">
                        <span class="font-bold block">Alteração Solicitada</span>
                        Motivo: {{ Str::limit($solAlteracao->motivo, 50) }}
                    </div>
                </div>
            @endif

            <div class="p-3 bg-gray-50/50 dark:bg-gray-800/80 border-b border-gray-100 dark:border-gray-700 px-4">
                <div class="flex items-center gap-2 overflow-hidden mb-1.5">
                    @if($jogo->jgo_numero_jogo)
                        <span class="bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 text-[11px] font-bold px-2 py-0.5 rounded border border-indigo-200 dark:border-indigo-800/50 shrink-0">
                            #{{ $jogo->jgo_numero_jogo }}
                        </span>
                    @endif
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest truncate">
                        {{ $campeonatoNome }}
                    </span>
                </div>
                <!-- Badges extras (Categoria e Turno) -->
                <div class="flex flex-wrap gap-1.5">
                    @if($categoriaNome)
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400">
                            {{ $categoriaNome }}
                        </span>
                    @endif
                    @if($turnoNome)
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400">
                            {{ $turnoNome }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-4">
                <div class="flex items-center justify-between mb-4 bg-gray-50 dark:bg-gray-700/30 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col items-center w-[45%] text-center">
                        <span class="text-[13px] font-black leading-tight text-gray-900 dark:text-white">{{ $mandanteNome }}</span>
                    </div>
                    <div class="text-[10px] font-black text-gray-400 bg-white dark:bg-gray-800 px-2 py-1 rounded-full shadow-sm border border-gray-100">VS</div>
                    <div class="flex flex-col items-center w-[45%] text-center">
                        <span class="text-[13px] font-black leading-tight text-gray-900 dark:text-white">{{ $visitanteNome }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-2 gap-x-4 mb-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <div>
                            <p class="text-[11px] font-bold text-gray-900 dark:text-gray-200 leading-none">
                                {{ \Carbon\Carbon::parse($dataStr)->format('d/m/y') }} às {{ substr($horaStr, 0, 5) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-1 items-center">
                        @if($jogo->jgo_status == 'ativo')
                            <span class="bg-green-100 text-green-700 text-[9px] font-bold px-2 py-0.5 rounded">ATIVO</span>
                        @else
                            <span class="bg-gray-200 text-gray-600 text-[9px] font-bold px-2 py-0.5 rounded">ENCERRADO</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-start gap-2 mb-4">
                    <svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 leading-tight">
                        {{ $jogo->ginasio ? $jogo->ginasio->gin_nome : 'Local não definido' }}
                    </p>
                </div>
                
                {{-- Ações Rápidas --}}
                <div class="flex gap-2 border-t border-gray-100 dark:border-gray-700 pt-3">
                    @if(Auth::id() == $jogo->jgo_apontador)
                        <a href="{{ route('resultados.create', $jogo->jgo_id) }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center text-xs font-bold py-2 rounded-lg transition-colors">
                            Informar Placar
                        </a>
                    @else
                        @php $resStatus = $jogo->jgo_res_status ?? 'nao_informado'; @endphp
                        @if($resStatus == 'aprovado')
                            <div class="flex-1 bg-green-50 dark:bg-green-900/30 border border-green-200 text-green-700 flex justify-center items-center gap-1 text-xs font-bold py-2 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Resultado Aprovado
                            </div>
                        @elseif($resStatus == 'pendente')
                            <div class="flex-1 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 text-yellow-700 flex justify-center items-center gap-1 text-xs font-bold py-2 rounded-lg">
                                Pendente Aprovação
                            </div>
                        @else
                            <div class="flex-1 bg-gray-50 dark:bg-gray-700 border border-gray-200 text-gray-500 font-semibold text-center flex justify-center items-center text-[11px] py-2 rounded-lg">
                                Aguardando Jogo / Placar
                            </div>
                        @endif
                    @endif

                    @if(Auth::user()->hasRole(['ResponsavelTime', 'ComissaoTecnica']) && $jogo->jgo_status == 'ativo' && $now < $start && !$solAlteracao)
                        <button type="button" @click="$dispatch('open-solicitar', { id: {{ $jogo->jgo_id }} })" class="w-10 flex border items-center justify-center border-gray-200 text-yellow-600 bg-white hover:bg-yellow-50 rounded-lg shrink-0" title="Solicitar Alteração">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </button>
                    @endif
                </div>
            </div>

            @if(Auth::user()->hasRole(['Administrador', 'Juiz']) || Auth::user()->is_arbitro)
            <div class="border-t border-gray-100 dark:border-gray-700">
                <button @click="openDetails = !openDetails" class="w-full text-center py-2 text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-gray-600 focus:outline-none dark:bg-gray-800/80 transition-colors">
                    <span x-text="openDetails ? 'Ocultar Oficiais' : 'Ver Oficiais'"></span>
                </button>
                <div x-show="openDetails" x-collapse>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 text-xs pt-2 font-medium">
                        @if(!$jogo->arbitroPrincipal && !$jogo->arbitroSecundario && !$jogo->apontador)
                            <span class="text-gray-400 italic">Nenhum diretor/árbitro alocado.</span>
                        @else
                            <div class="grid grid-cols-1 gap-2">
                                @if($jogo->arbitroPrincipal)
                                    <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1"><span class="text-gray-500">Principal</span> <span class="text-gray-800 dark:text-gray-200">{{ $jogo->arbitroPrincipal->name }}</span></div>
                                @endif
                                @if($jogo->arbitroSecundario)
                                    <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1"><span class="text-gray-500">Secundário</span> <span class="text-gray-800 dark:text-gray-200">{{ $jogo->arbitroSecundario->name }}</span></div>
                                @endif
                                @if($jogo->apontador)
                                    <div class="flex justify-between"><span class="text-gray-500">Apontador</span> <span class="text-gray-800 dark:text-gray-200">{{ $jogo->apontador->name }}</span></div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    @endforeach
</div>
