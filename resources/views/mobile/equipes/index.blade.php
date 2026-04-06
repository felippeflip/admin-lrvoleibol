@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header Title --}}
    <div class="mb-4 px-1">
        <div class="flex justify-between items-end">
            <div class="flex-1">
                <h2 class="text-2xl font-black text-gray-900 dark:text-gray-100 tracking-tight leading-tight">
                    {{ isset($time) ? 'Equipes: ' . $time->tim_nome : 'Equipes da Liga' }}
                </h2>
                <p class="text-[12px] text-gray-500 font-medium dark:text-gray-400 mt-0.5 uppercase tracking-wider">
                    Categorias & Elencos
                </p>
            </div>
            @if(!auth()->user()->hasRole('ComissaoTecnica'))
            <a href="{{ isset($time) ? route('equipes.create', ['time_id' => $time->tim_id]) : route('equipes.create') }}" class="shrink-0 bg-orange-600 hover:bg-orange-700 text-white text-[11px] font-black px-3 py-2.5 rounded-xl shadow-lg active:scale-95 transition-transform flex items-center gap-1.5 uppercase">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                NOVA
            </a>
            @endif
        </div>
    </div>

    {{-- Algis Erros / Mensagens Globais --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 font-bold py-3 px-4 rounded-xl shadow-sm mb-4 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(isset($time))
        {{-- Breadcrumb fake ou Status do filtro --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/30 rounded-xl p-3 mb-4 flex items-center gap-3">
            <div class="w-10 h-10 shrink-0 bg-white dark:bg-gray-800 rounded-lg border border-blue-100 dark:border-blue-700 p-1 flex items-center justify-center">
                <img src="{{ $time->tim_logo_url }}" class="max-h-full max-w-full object-contain overflow-hidden rounded-md">
            </div>
            <div>
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest leading-none">Filtrado por Clube</p>
                <p class="text-xs font-black text-blue-900 dark:text-blue-300 leading-tight mt-0.5">{{ $time->tim_nome }}</p>
            </div>
            <a href="{{ route('equipes.index') }}" class="ml-auto text-[10px] font-black text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-800 px-2 py-1.5 rounded-lg border border-blue-200 dark:border-blue-700 uppercase">Ver Tudo</a>
        </div>
    @endif

    {{-- Filtros Sanfona (Apenas na Index Geral) --}}
    @if(!isset($time))
    <div x-data="{ openFilters: false }" class="mb-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <button @click="openFilters = !openFilters" class="w-full px-4 py-3.5 flex items-center justify-between text-gray-700 dark:text-gray-300 text-xs font-bold leading-none bg-gray-50/50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700">
            <span class="flex items-center gap-2">
                <svg class="w-4.5 h-4.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> 
                FILTRAR EQUIPES
            </span>
            <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-180': openFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div x-show="openFilters" x-collapse>
            <form method="GET" action="{{ route('equipes.index') }}" class="p-4 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1">PESQUISAR</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome da equipe ou copa..." class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm rounded-xl p-3 outline-none focus:ring-2 focus:ring-orange-500 dark:text-white">
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1">CATEGORIA</label>
                        <select name="categoria" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm rounded-xl p-2.5 dark:text-white appearance-none outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Todas</option>
                            @foreach($categorias ?? [] as $cat)
                                <option value="{{ $cat->cto_id }}" {{ request('categoria') == $cat->cto_id ? 'selected' : '' }}>{{ $cat->cto_nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 bg-gray-900 dark:bg-gray-700 text-white font-black text-xs py-3.5 rounded-xl shadow-lg active:scale-95 transition-transform uppercase">Aplicar</button>
                    <a href="{{ route('equipes.index') }}" class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold text-xs py-3.5 rounded-xl text-center shadow-sm active:scale-95 transition-transform uppercase">Limpar</a>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Grid de Cards Equipes --}}
    <div class="space-y-4 mb-20 px-0.5">
        @forelse ($equipes as $equipe)
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" x-data="{ openOptions: false }">
                {{-- INFO PRINCIPAL --}}
                <div class="p-4">
                    <div class="flex items-start gap-4">
                        {{-- Badge Categoria Círculo --}}
                        <div class="w-14 h-14 shrink-0 flex flex-col items-center justify-center rounded-2xl bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800/30">
                            <span class="text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-tighter leading-none mb-1">Cat.</span>
                            <span class="text-sm font-black text-orange-900 dark:text-orange-200 leading-none">
                                {{ preg_replace('/[^0-9]/', '', $equipe->categoria->cto_nome ?? '??') ?: '..' }}
                            </span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <h3 class="text-base font-black text-gray-900 dark:text-white leading-tight truncate">
                                    {{ $equipe->eqp_nome_detalhado }}
                                </h3>
                                {{-- Actions Trigger --}}
                                <button @click="openOptions = !openOptions" class="p-1.5 bg-gray-50 dark:bg-gray-900 rounded-lg text-gray-400 hover:text-orange-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                                </button>
                            </div>
                            
                            <div class="mt-2 space-y-1.5">
                                {{-- Clube (Apenas se não estiver filtrado) --}}
                                @if(!isset($time))
                                <div class="flex items-center gap-1.5 opacity-80">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.382 0z"></path></svg>
                                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tight">{{ $equipe->time->tim_nome ?? 'Clube Independente' }}</span>
                                </div>
                                @endif

                                {{-- Treinador --}}
                                <div class="flex items-center gap-1.5 opacity-80">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                    <span class="text-[11px] font-bold text-gray-600 dark:text-gray-300">{{ $equipe->eqp_nome_treinador ?: 'Treinador não definido' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Campeonatos Pills --}}
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        @forelse($equipe->campeonatos as $campeonato)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800/30 rounded-lg text-[10px] font-black text-blue-700 dark:text-blue-300 uppercase tracking-tight">
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                {{ $campeonato->cpo_nome }}
                            </span>
                        @empty
                            <span class="text-[10px] font-bold text-gray-400 bg-gray-50 dark:bg-gray-900 px-2 py-1 rounded-lg border border-dashed border-gray-200 dark:border-gray-700 italic">Sem Inscrições Ativas</span>
                        @endforelse
                    </div>
                </div>

                {{-- GAVETA DE OPÇÕES (Alpine.js) --}}
                <div x-show="openOptions" x-collapse>
                    <div class="px-4 pb-4 pt-1 bg-gray-50 dark:bg-gray-900 gap-3 grid grid-cols-2">
                        @if(!auth()->user()->hasRole('ComissaoTecnica'))
                        <a href="{{ route('equipes.edit', $equipe->eqp_id) }}" class="flex items-center justify-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-3 rounded-2xl text-[11px] font-black uppercase text-gray-700 dark:text-gray-200 shadow-sm transition active:scale-95">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Editar Equipe
                        </a>
                        <form action="{{ route('equipes.destroy', $equipe->eqp_id) }}" method="POST" class="col-span-1" onsubmit="return confirm('Deseja realmente remover esta equipe permanentemente?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/40 py-3 rounded-2xl text-[11px] font-black uppercase text-red-600 dark:text-red-400 shadow-sm transition active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Excluir
                            </button>
                        </form>
                        @endif
                        
                        {{-- Botão de Ver Elenco/Jogadores se existir atalho futuro --}}
                        <div class="col-span-2 mt-1">
                            <button @click="alert('Gerenciamento de elenca em breve no mobile!')" class="w-full bg-gray-900 dark:bg-gray-700 py-3 rounded-2xl text-[11px] font-black uppercase text-white shadow-lg active:scale-[0.98] transition">
                                Gerenciar Elenco da Equipe
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center p-14 bg-white dark:bg-gray-800 rounded-[2.5rem] border-2 border-dashed border-gray-100 dark:border-gray-700 mt-6">
                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-800">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-gray-900 dark:text-white font-black text-sm">Nenhuma Equipe</h3>
                <p class="text-gray-400 text-xs font-bold mt-1 px-4 leading-relaxed">Não encontramos equipes cadastradas para este critério.</p>
            </div>
        @endforelse

        {{-- Paginação Customizada Mobile --}}
        <div class="mt-6 px-1 mb-10">
            {{ $equipes->links() }}
        </div>
    </div>
</div>
@endsection
