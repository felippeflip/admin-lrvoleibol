@extends('mobile.layouts.app')

@section('content')
<div class="w-full pb-24 px-1">
    {{-- Header Personalizado --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter leading-none mb-1">Olá, {{ explode(' ', Auth::user()->name)[0] }}!</h2>
            <p class="text-xs font-bold text-gray-400">Aqui está o resumo da sua competição.</p>
        </div>
        <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-100 dark:shadow-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
    </div>

    {{-- Widget: Próximos Jogos (Admin ou Global) --}}
    @if(isset($adminJogos) && count($adminJogos) > 0)
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4 ml-1">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Próximos Confrontos</h3>
                <a href="{{ route('jogos.index') }}" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest cursor-pointer active:scale-95 transition-all">Ver Todos</a>
            </div>
            
            <div class="flex gap-4 overflow-x-auto pb-4 no-scrollbar -mx-4 px-4">
                @foreach($adminJogos->take(5) as $jogo)
                    <div class="shrink-0 w-[240px] bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700 relative overflow-hidden group">
                        <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-50 dark:bg-indigo-900/10 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-[8px] font-black uppercase tracking-widest px-2 py-1 bg-gray-50 dark:bg-gray-700 text-gray-400 rounded-lg">{{ $jogo->mandante->campeonato->cpo_nome }}</span>
                            <div class="flex items-center gap-1 text-gray-900 dark:text-white font-black text-[10px]">
                                <svg class="w-3 h-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ substr($jogo->jgo_hora_jogo, 0, 5) }}
                            </div>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-indigo-600"></div>
                                <span class="text-xs font-black text-gray-700 dark:text-gray-300 truncate">{{ $jogo->mandante->equipe->eqp_nome_detalhado }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                                <span class="text-xs font-black text-gray-700 dark:text-gray-300 truncate">{{ $jogo->visitante->equipe->eqp_nome_detalhado }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-4 border-t border-gray-50 dark:border-gray-700/50">
                            <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter truncate">{{ $jogo->ginasio->gin_nome ?? 'Local a definir' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Resumo da Temporada (Admin) --}}
    @if(isset($adminStats))
        <div class="mb-8">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-4 ml-1">Monitoramento Administrativo</h3>
            <div class="grid grid-cols-2 gap-4">
                 <div class="bg-indigo-600 rounded-[2rem] p-6 text-white shadow-xl shadow-indigo-100 dark:shadow-none">
                    <p class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-1">Novos Jogos</p>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-black">{{ $adminStats[0]['novos'] ?? '0' }}</span>
                        <span class="text-[10px] font-bold opacity-60 pb-1">Esta semana</span>
                    </div>
                 </div>
                 <div class="bg-gray-900 rounded-[2rem] p-6 text-white shadow-xl shadow-gray-200 dark:shadow-none relative overflow-hidden">
                    <div class="absolute -right-2 -bottom-2 w-16 h-16 bg-red-500 rounded-full blur-[20px] opacity-20"></div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-red-400 mb-1 leading-tight">Pendente<br>Resultado</p>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-black text-white">{{ $adminStats[0]['sem_apontamento'] ?? '0' }}</span>
                        <span class="text-[10px] font-bold text-red-400 pb-1 animate-pulse">ALERTA</span>
                    </div>
                 </div>
            </div>
        </div>
    @endif

    {{-- Acesso Rápido --}}
    <div class="mb-8">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-4 ml-1">Acesso Rápido</h3>
        <div class="grid grid-cols-4 gap-3 px-1">
            <a href="{{ route('atletas.index') }}" class="flex flex-col items-center gap-2">
                <div class="w-full aspect-square bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center shadow-sm border border-gray-50 dark:border-gray-700 active:scale-90 transition-all text-orange-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <span class="text-[8px] font-black uppercase text-gray-400">Atletas</span>
            </a>
            <a href="{{ route('equipes.index') }}" class="flex flex-col items-center gap-2">
                <div class="w-full aspect-square bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center shadow-sm border border-gray-50 dark:border-gray-700 active:scale-90 transition-all text-indigo-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <span class="text-[8px] font-black uppercase text-gray-400">Equipes</span>
            </a>
            <a href="{{ route('ginasios.index') }}" class="flex flex-col items-center gap-2">
                <div class="w-full aspect-square bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center shadow-sm border border-gray-50 dark:border-gray-700 active:scale-90 transition-all text-green-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <span class="text-[8px] font-black uppercase text-gray-400">Ginásios</span>
            </a>
            <a href="{{ route('arbitros.index') }}" class="flex flex-col items-center gap-2">
                <div class="w-full aspect-square bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center shadow-sm border border-gray-50 dark:border-gray-700 active:scale-90 transition-all text-yellow-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <span class="text-[8px] font-black uppercase text-gray-400">Oficiais</span>
            </a>
        </div>
    </div>

    {{-- Notificações / Feed Recente (Se houver solicitações) --}}
    @php
        $solicitacoesRecentes = \App\Models\JogoSolicitacao::where('status', 'pendente')->with('user', 'jogo.mandante.equipe', 'jogo.visitante.equipe')->latest()->take(3)->get();
    @endphp
    
    @if($solicitacoesRecentes->count() > 0)
        <div class="mb-12">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-4 ml-1">Alertas do Sistema</h3>
            <div class="space-y-3">
                @foreach($solicitacoesRecentes as $sol)
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-4 shadow-sm border border-gray-50 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center text-yellow-500 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Mudar Data: {{ $sol->jogo->mandante->equipe->eqp_nome }}</p>
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $sol->motivo }}</p>
                        </div>
                        <a href="{{ route('jogos.edit', $sol->jogo->jgo_id) }}" class="w-8 h-8 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-indigo-600 active:scale-90 transition-all">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
