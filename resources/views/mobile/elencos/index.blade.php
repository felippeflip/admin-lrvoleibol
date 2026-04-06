@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header Title --}}
    <div class="flex justify-between items-end mb-4 px-1">
        <div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-gray-100 tracking-tight leading-none mb-1">
                Gestão de Elencos
            </h2>
            <p class="text-[13px] text-gray-500 font-medium dark:text-gray-400 uppercase tracking-wider">Inscrição de atletas por copa.</p>
        </div>
    </div>

    {{-- Algis Erros / Mensagens Globais --}}
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-800 font-bold py-3.5 px-4 rounded-xl shadow-sm mb-4 text-sm flex items-center gap-2">
             <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filtros Sanfona (Alpine.js) --}}
    <div x-data="{ openFilters: false }" class="mb-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <button @click="openFilters = !openFilters" class="w-full px-4 py-3.5 flex items-center justify-between text-gray-700 dark:text-gray-300 text-xs font-bold bg-gray-50/50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700">
            <span class="flex items-center gap-2">
                <svg class="w-4.5 h-4.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> 
                BUSCAR PARTICIPAÇÕES
            </span>
            <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-180': openFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div x-show="openFilters" x-collapse>
            <form method="GET" action="{{ route('elenco.list') }}" class="p-4 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1">CAMPEONATO</label>
                    <select name="campeonato_id" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-xs rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-orange-500 appearance-none">
                        <option value="">Todos os Ativos</option>
                        @foreach($campeonatos as $camp)
                            <option value="{{ $camp->cpo_id }}" {{ request('campeonato_id') == $camp->cpo_id ? 'selected' : '' }}>{{ $camp->cpo_nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1">EQUIPE</label>
                    <select name="equipe_id" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-xs rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-orange-500 appearance-none">
                        <option value="">Todas</option>
                        @foreach($equipes as $eqp)
                             <option value="{{ $eqp->eqp_id }}" {{ request('equipe_id') == $eqp->eqp_id ? 'selected' : '' }}>
                                {{ $eqp->eqp_nome_detalhado ?? $eqp->time->tim_nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1">CATEGORIA</label>
                    <select name="categoria_id" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-xs rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-orange-500 appearance-none">
                        <option value="">Todas</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->cto_id }}" {{ request('categoria_id') == $cat->cto_id ? 'selected' : '' }}>{{ $cat->cto_nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 bg-gray-900 dark:bg-gray-700 text-white font-black text-xs py-3.5 rounded-xl uppercase tracking-wider transition active:scale-95 shadow-lg">Filtrar</button>
                    <a href="{{ route('elenco.list') }}" class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold text-xs py-3.5 rounded-xl text-center uppercase tracking-wider transition active:scale-95">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Cards (Participações) --}}
    <div class="space-y-4 mb-24 px-0.5">
        @forelse ($participacoes as $participacao)
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden group">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                         <div class="flex-1 min-w-0 pr-2">
                            <h3 class="text-base font-black text-gray-900 dark:text-white leading-tight truncate">
                                {{ $participacao->equipe->eqp_nome_detalhado ?? $participacao->equipe->time->tim_nome }}
                            </h3>
                            <p class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest mt-1">
                                Categoria: {{ $participacao->equipe->categoria->cto_nome }}
                            </p>
                        </div>
                        <div class="shrink-0 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded-lg border border-blue-100 dark:border-blue-800/30">
                            <span class="text-[9px] font-black text-blue-700 dark:text-blue-300 uppercase tracking-tighter">🏆 INSCRITO</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-2xl border border-gray-100 dark:border-gray-700 mb-4">
                        <div class="w-8 h-8 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700 shrink-0">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="block text-[8px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-0.5">Torneio / Temporada</span>
                            <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate block leading-tight">{{ $participacao->campeonato->cpo_nome }}</span>
                        </div>
                    </div>

                    <a href="{{ route('elenco.index', ['campeonato' => $participacao->cpo_fk_id, 'equipe_campeonato' => $participacao->eqp_cpo_id]) }}" class="w-full flex items-center justify-center gap-2 bg-gray-900 dark:bg-gray-700 text-white font-black text-xs py-4 rounded-2xl shadow-lg active:scale-[0.98] transition-transform uppercase tracking-wider group-hover:bg-orange-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Gerenciar Elenco
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center p-16 bg-white dark:bg-gray-800 rounded-[2.5rem] border-2 border-dashed border-gray-100 dark:border-gray-700 mt-6">
                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-800">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h3 class="text-gray-900 dark:text-white font-black text-sm uppercase">Sem Inscrições</h3>
                <p class="text-gray-400 text-xs font-bold mt-1 px-4 leading-relaxed italic">Sua equipe precisa estar inscrita em uma copa ativa para gerenciar o elenco.</p>
            </div>
        @endforelse

        {{-- Paginação Customizada Mobile --}}
        <div class="mt-6 px-1 mb-10">
            {{ $participacoes->links() }}
        </div>
    </div>
</div>
@endsection
