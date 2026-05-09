@extends('mobile.layouts.app')

@section('content')
<div class="w-full pb-20">
    <div class="mb-4 px-1">
        <h2 class="text-2xl font-black text-gray-900 dark:text-gray-100 tracking-tight leading-none mb-1">
            Relatório
        </h2>
        <p class="text-[13px] text-gray-500 font-medium dark:text-gray-400 uppercase tracking-wider">Jogos por Árbitro/Apontador</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-5">
        <form method="GET" action="{{ route('relatorios.jogos-por-arbitro') }}" class="p-4 space-y-4">
            <div>
                <label for="arbitro_id" class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Árbitro / Apontador</label>
                <select name="arbitro_id" id="arbitro_id" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione...</option>
                    @foreach($arbitros as $arbitro)
                        <option value="{{ $arbitro->id }}" {{ request('arbitro_id') == $arbitro->id ? 'selected' : '' }}>
                            {{ $arbitro->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="ano" class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Ano</label>
                <input type="number" name="ano" id="ano" value="{{ $ano }}" min="2000" max="2100" required class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 text-white font-black text-xs uppercase tracking-wider shadow-lg active:scale-95 transition-transform">
                GERAR RELATÓRIO
            </button>
        </form>
    </div>

    @if($arbitroSelecionado)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-5 p-4 text-center">
            <h3 class="text-lg font-black text-gray-900 dark:text-white">{{ $arbitroSelecionado->name }}</h3>
            <div class="mt-2 flex flex-col items-center">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total no ano de {{ $ano }}</span>
                <span class="text-3xl font-black text-blue-600">{{ $totalJogos }}</span>
            </div>
            
            @if($totalJogos > 0)
                <a href="{{ route('relatorios.jogos-por-arbitro.print', ['arbitro_id' => $arbitroSelecionado->id, 'ano' => $ano]) }}" target="_blank" class="mt-4 flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-gray-900 dark:bg-gray-700 text-white font-bold text-xs uppercase tracking-wider active:scale-95 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Gerar PDF (Imprimir)
                </a>
            @endif
        </div>

        @if($totalJogos > 0)
            <div class="space-y-4">
                @foreach($jogos as $jogo)
                    @php
                        $funcao = [];
                        if ($jogo->jgo_arbitro_principal == $arbitroSelecionado->id) $funcao[] = 'Principal';
                        if ($jogo->jgo_arbitro_secundario == $arbitroSelecionado->id) $funcao[] = 'Secundário';
                        if ($jogo->jgo_apontador == $arbitroSelecionado->id) $funcao[] = 'Apontador';
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-start mb-3 border-b border-gray-100 dark:border-gray-700 pb-2">
                            <div>
                                <span class="block text-xs font-black text-gray-900 dark:text-white">{{ $jogo->jgo_dt_jogo ? \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') : 'N/A' }}</span>
                                <span class="text-[10px] text-gray-500">{{ $jogo->jgo_hora_jogo ? \Carbon\Carbon::parse($jogo->jgo_hora_jogo)->format('H:i') : 'N/A' }}</span>
                            </div>
                            <div class="text-right">
                                @foreach($funcao as $f)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-[9px] font-black px-2 py-0.5 rounded uppercase mb-0.5">{{ $f }}</span><br>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3 text-center">
                            <span class="block text-[10px] font-bold text-orange-500 uppercase">{{ $jogo->mandante->campeonato->cpo_nome ?? 'N/A' }}</span>
                            <span class="block text-xs font-bold text-gray-700 dark:text-gray-300">{{ $jogo->mandante->equipe->categoria->cto_nome ?? 'N/A' }} - {{ $jogo->jgo_fase }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-2 px-2 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                            <div class="flex-1 min-w-0 text-center">
                                <span class="block text-[9px] font-black text-gray-400 uppercase mb-1">Mandante</span>
                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200 line-clamp-2 leading-tight">{{ $jogo->mandante->equipe->eqp_nome_detalhado ?? 'N/A' }}</span>
                            </div>
                            <span class="text-lg font-black text-gray-300 mx-1">×</span>
                            <div class="flex-1 min-w-0 text-center">
                                <span class="block text-[9px] font-black text-gray-400 uppercase mb-1">Visitante</span>
                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200 line-clamp-2 leading-tight">{{ $jogo->visitante->equipe->eqp_nome_detalhado ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <span class="text-[10px] font-bold text-gray-500">📍 {{ $jogo->ginasio->gin_nome ?? 'N/A' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center p-10 bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-100 dark:border-gray-700">
                <span class="text-gray-400 text-sm font-bold">Nenhum jogo encontrado para este árbitro no ano de {{ $ano }}.</span>
            </div>
        @endif
    @endif
</div>
@endsection
