<div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow p-3 relative overflow-hidden flex flex-col justify-between">
    <!-- Tipo de Fase e Turno -->
    <div class="flex items-center justify-between mb-2">
        <span class="text-[10px] font-bold uppercase text-gray-500 bg-gray-100 px-2 py-0.5 rounded border border-gray-200">
            {{ $jogo->jgo_fase ?: 'TURNO ÚNICO' }}
        </span>
        <div class="text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider {{ in_array($jogo->jgo_res_status, ['pendente', 'aprovado']) ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
            {{ in_array($jogo->jgo_res_status, ['pendente', 'aprovado']) ? 'REALIZADO' : 'A REALIZAR' }}
        </div>
    </div>

    <!-- Data e Horário -->
    <div class="text-[11px] text-gray-500 mb-2 font-medium">
        {{ $jogo->jgo_dt_jogo ? \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') : 'Data a definir' }}
        {{ $jogo->jgo_hora_jogo ? ' às ' . \Carbon\Carbon::parse($jogo->jgo_hora_jogo)->format('H:i') : '' }}
    </div>

    <!-- Placar / Times -->
    @php
        $setsRegulares = $jogo->resultadoSets ? $jogo->resultadoSets->where('set_numero', '<=', 5) : collect();
        $goldenSet = $jogo->resultadoSets ? $jogo->resultadoSets->firstWhere('set_numero', 6) : null;
    @endphp
    <div class="flex flex-col space-y-1 relative mb-2">
        <!-- Mandante -->
        <div class="flex justify-between items-center bg-gray-50 rounded p-2 {{ (int)$jogo->jgo_vencedor_mandante === 1 ? 'border border-green-300 ring-1 ring-green-300' : '' }}">
            <span class="font-bold text-gray-800 text-xs truncate max-w-[70%]" title="{{ $jogo->mandante->equipe->time->tim_nome ?? 'A Definir' }}">
                {{ $jogo->mandante->equipe->time->tim_nome ?? 'A Definir' }}
            </span>
            @if(in_array($jogo->jgo_res_status, ['pendente', 'aprovado']))
                <div class="flex items-center space-x-1">
                    @php $setsM = 0; @endphp
                    @foreach($setsRegulares as $set)
                        @php if(($set->set_pontos_mandante ?? 0) > ($set->set_pontos_visitante ?? 0)) $setsM++; @endphp
                        <span class="text-[9px] w-4 h-4 flex items-center justify-center bg-gray-200 text-gray-700 rounded-sm">{{ $set->set_pontos_mandante ?? 0 }}</span>
                    @endforeach
                    <span class="text-xs font-black w-5 h-5 flex items-center justify-center {{ (int)$jogo->jgo_vencedor_mandante === 1 ? 'bg-principal text-white rounded ml-1' : 'text-gray-400 ml-1 bg-white border border-gray-200' }}">{{ $setsM }}</span>
                </div>
            @endif
        </div>
        <!-- Visitante -->
        <div class="flex justify-between items-center bg-gray-50 rounded p-2 {{ (int)$jogo->jgo_vencedor_mandante === 0 ? 'border border-green-300 ring-1 ring-green-300' : '' }}">
            <span class="font-bold text-gray-800 text-xs truncate max-w-[70%]" title="{{ $jogo->visitante->equipe->time->tim_nome ?? 'A Definir' }}">
                {{ $jogo->visitante->equipe->time->tim_nome ?? 'A Definir' }}
            </span>
            @if(in_array($jogo->jgo_res_status, ['pendente', 'aprovado']))
                <div class="flex items-center space-x-1">
                    @php $setsV = 0; @endphp
                    @foreach($setsRegulares as $set)
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

    <!-- Destaque Golden Set (quando houver) -->
    @if($goldenSet && in_array($jogo->jgo_res_status, ['pendente', 'aprovado']))
        <div class="mt-1 mb-2 py-1 px-2.5 bg-gradient-to-r from-amber-100 via-yellow-100 to-amber-100 border border-amber-300 rounded-lg flex items-center justify-between text-[11px] text-amber-950 font-bold shadow-xs">
            <span class="flex items-center gap-1">
                <span>🏆</span> <strong class="tracking-wide">GOLDEN SET:</strong>
            </span>
            <span class="font-black bg-amber-200/90 text-amber-950 px-2 py-0.5 rounded shadow-inner text-xs">
                {{ $goldenSet->set_pontos_mandante }} x {{ $goldenSet->set_pontos_visitante }}
            </span>
        </div>
    @endif

    <!-- Local -->
    @if($jogo->ginasio)
    <div class="text-[10px] text-gray-400 truncate flex items-center pt-1 border-t border-gray-100 mt-auto" title="{{ $jogo->ginasio->gin_nome }}">
        <svg class="w-3 h-3 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        <span class="truncate">{{ $jogo->ginasio->gin_nome }}</span>
    </div>
    @endif
</div>
