@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header Title --}}
    <div class="flex justify-between items-end mb-4">
        <div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-gray-100 tracking-tight leading-none mb-1">
                Copas & Torneios
            </h2>
            <p class="text-[13px] text-gray-500 font-medium dark:text-gray-400">Gerencie todos os eventos.</p>
        </div>
        <a href="{{ route('eventos.create') }}" class="flex items-center gap-1 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-3 py-2 rounded-xl shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            NOVO
        </a>
    </div>

    {{-- Algis Erros / Mensagens Globais --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 font-semibold py-3 px-4 rounded-lg shadow-sm mb-4 text-sm animate-fade-in-down">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-800 font-semibold py-3 px-4 rounded-lg shadow-sm mb-4 text-sm animate-fade-in-down">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filtros Sanfona (Alpine.js) --}}
    <div x-data="{ openFilters: false }" class="mb-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <button @click="openFilters = !openFilters" class="w-full px-4 py-3 flex items-center justify-between text-gray-700 dark:text-gray-300 text-xs font-bold bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> 
                FILTROS DE BUSCA
            </span>
            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': openFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div x-show="openFilters" x-collapse>
            <form method="GET" action="{{ route('eventos.index') }}" class="p-4 space-y-3">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Nome</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ex: Taça Ouro" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500 dark:text-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Ano</label>
                        <select name="ano" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500 dark:text-white">
                            <option value="">Todos</option>
                            @for ($year = date('Y') + 1; $year >= 2000; $year--)
                                <option value="{{ $year }}" {{ request('ano') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Status</label>
                        <select name="ativo" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-orange-500 dark:text-white">
                            <option value="">Todos</option>
                            <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Em Aberto</option>
                            <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Fechado</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-gray-900 dark:bg-gray-700 text-white font-bold text-xs py-2.5 rounded-lg shadow-sm">Buscar</button>
                    <a href="{{ route('eventos.index') }}" class="flex-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold text-xs py-2.5 rounded-lg text-center hover:bg-gray-50 dark:hover:bg-gray-700">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Cards (Campeonatos) --}}
    <div class="space-y-4 relative pb-6">
        @forelse ($campeonatos as $campeonato)
            @php
                $isActive = $campeonato->cpo_ativo;
                $borderClass = $isActive ? 'border-orange-400 border-l-4' : 'border-gray-300 dark:border-gray-600 border-l-4 opacity-75';
                $badgeColors = $isActive ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                $statusText = $isActive ? 'ATIVO' : 'INATIVO';
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $borderClass }} overflow-hidden">
                {{-- CABEÇALHO DO CARD --}}
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1 pr-2">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight">
                                {{ $campeonato->cpo_nome }}
                            </h3>
                        </div>
                        <span class="{{ $badgeColors }} text-[10px] font-black px-2 py-1 rounded inline-flex shrink-0">
                            {{ $statusText }}
                        </span>
                    </div>

                    {{-- Data e Ano Pílulas --}}
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-[11px] font-bold text-gray-700 dark:text-gray-300">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Ano: {{ $campeonato->cpo_ano }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-[11px] font-bold">
                            Início: {{ \Carbon\Carbon::parse($campeonato->cpo_dt_inicio)->format('d/m/y') }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded text-[11px] font-bold">
                            Fim: {{ \Carbon\Carbon::parse($campeonato->cpo_dt_fim)->format('d/m/y') }}
                        </span>
                    </div>
                </div>

                {{-- GRADES DE AÇÕES RÁPIDAS --}}
                <div x-data="{ openOptions: false }" class="bg-gray-50 dark:bg-gray-900/50">
                    <div class="grid grid-cols-4 divide-x divide-gray-200 dark:divide-gray-700 border-b border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                        
                        {{-- 1. Equipes do Torneio --}}
                        <a href="{{ route('equipes.campeonato.index', ['campeonato' => $campeonato->cpo_id]) }}" class="flex flex-col items-center justify-center p-3 hover:bg-orange-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-5 h-5 mb-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="text-[9px] font-bold uppercase text-center w-full truncate px-1">Equipes</span>
                        </a>

                        {{-- 2. Adicionar Equipe --}}
                        <a href="{{ route('equipes.campeonato.create', ['campeonato' => $campeonato->cpo_id]) }}" class="flex flex-col items-center justify-center p-3 hover:bg-orange-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-5 h-5 mb-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            <span class="text-[9px] font-bold uppercase text-center w-full truncate px-1">Add Time</span>
                        </a>
                        
                        {{-- 3. Agendamentos --}}
                        <a href="{{ route('agendamentos.admin.index', ['campeonato' => $campeonato->cpo_id]) }}" class="flex flex-col items-center justify-center p-3 hover:bg-orange-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-5 h-5 mb-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-[9px] font-bold uppercase text-center w-full truncate px-1">Agenda</span>
                        </a>
                        
                        {{-- 4. Mais Ações (Menu sanfona secundário) --}}
                        <button @click="openOptions = !openOptions" class="flex flex-col items-center justify-center p-3 hover:bg-gray-200 dark:hover:bg-gray-700 transition focus:outline-none">
                            <svg class="w-5 h-5 mb-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                            <span class="text-[9px] font-bold uppercase text-center w-full truncate px-1" x-text="openOptions ? 'FECHAR' : 'MAIS'"></span>
                        </button>

                    </div>

                    {{-- Gaveta de Exclusão e Edição --}}
                    <div x-show="openOptions" x-collapse>
                        <div class="p-3 bg-gray-100 dark:bg-gray-800 flex justify-between gap-3">
                            <a href="{{ route('eventos.edit', $campeonato->cpo_id) }}" class="flex-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 font-bold text-[11px] py-2 rounded-lg text-center flex justify-center items-center gap-1 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Editar Copa
                            </a>
                            <form action="{{ route('eventos.destroy', $campeonato->cpo_id) }}" method="POST" class="flex-1" x-data="{ confirming: false }">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="confirming = true" x-show="!confirming" class="w-full bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 font-bold text-[11px] py-2 rounded-lg text-center flex justify-center items-center gap-1 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Excluir Definitivo
                                </button>
                                <button type="submit" x-show="confirming" class="w-full bg-red-600 hover:bg-red-700 text-white font-black text-[11px] py-2 rounded-lg text-center flex justify-center items-center gap-1 shadow-sm animate-pulse">
                                    CERTEZA?
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="text-center p-8 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 mt-4">
                <span class="text-3xl block mb-2">🏆</span>
                <p class="text-gray-500 dark:text-gray-400 font-medium text-sm">Nenhum campeonato encontrado.</p>
            </div>
        @endforelse

        {{-- Paginação --}}
        <div class="mt-6 mb-16 px-1">
            {{ $campeonatos->links() }}
        </div>
    </div>
</div>
@endsection
