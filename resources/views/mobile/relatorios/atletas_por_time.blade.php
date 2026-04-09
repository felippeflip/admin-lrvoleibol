@extends('mobile.layouts.app')

@section('content')
<div class="w-full pb-24">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8 px-1">
        <a href="{{ route('relatorios.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-90 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-none">Inscritos por Time</h2>
    </div>

    {{-- Filtro Rápido --}}
    <div class="bg-white dark:bg-gray-800 rounded-[2rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700 mb-8">
        <form method="GET" action="{{ route('relatorios.atletas-por-time') }}">
             <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-4">Filtrar por Equipe</p>
             <select name="time_id" onchange="this.form.submit()" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black text-gray-700 dark:text-gray-300 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                <option value="">Todas as Equipes</option>
                @foreach($timesList as $time)
                    <option value="{{ $time->tim_id }}" {{ request('time_id') == $time->tim_id ? 'selected' : '' }}>{{ $time->tim_nome }}</option>
                @endforeach
             </select>
        </form>
    </div>

    {{-- Listagem Agrupada --}}
    <div class="space-y-10">
            @php 
                $allIds = $grupoAtletas->pluck('atl_id')->toArray();
                $timeNome = $grupoAtletas->first()->time->tim_nome ?? 'Sem Time'; 
            @endphp
            <div x-data="{ 
                    selectedIds: {{ json_encode($allIds) }},
                    toggleAll() {
                        if (this.selectedIds.length === {{ count($allIds) }}) {
                            this.selectedIds = [];
                        } else {
                            this.selectedIds = {{ json_encode($allIds) }};
                        }
                    }
                }">
                <div class="flex items-center justify-between mb-5 ml-1">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-1 bg-indigo-600 rounded-full"></div>
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-[0.2em] leading-none flex items-center">
                            {{ $timeNome }}
                            <span class="print-only ml-2 text-[10px] font-bold text-gray-400 normal-case tracking-normal">
                                (<span x-text="selectedIds.length">{{ count($allIds) }}</span>/{{ count($allIds) }})
                            </span>
                        </h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 rounded-lg">
                            <span x-text="selectedIds.length">{{ count($allIds) }}</span>/{{ count($allIds) }}
                        </span>
                        <button @click="toggleAll()" class="text-[10px] font-black uppercase text-gray-400 active:text-indigo-600 transition-colors">
                            <span x-text="selectedIds.length === {{ count($allIds) }} ? 'Desmarcar' : 'Marcar'">Alternar</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    @foreach($grupoAtletas as $atleta)
                        <div :class="!selectedIds.includes({{ $atleta->atl_id }}) ? 'opacity-40 grayscale-[0.5]' : ''" 
                             class="bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-sm border border-gray-50 dark:border-gray-700 flex items-center gap-4 transition-all">
                            
                            <div class="flex-shrink-0">
                                <input type="checkbox" 
                                       value="{{ $atleta->atl_id }}" 
                                       x-model.number="selectedIds"
                                       class="w-5 h-5 rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-500/20 transition-all">
                            </div>

                            <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 flex-shrink-0">
                                @if($atleta->atl_foto)
                                    <img src="{{ $atleta->atl_foto_url }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-indigo-400 font-bold uppercase text-xs">{{ substr($atleta->atl_nome, 0, 1) }}</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-black text-gray-900 dark:text-white leading-tight mb-1 truncate">{{ $atleta->atl_nome }}</p>
                                <div class="flex items-center gap-3">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest italic">{{ $atleta->atl_resg ?? 'Sem Ref' }}</span>
                                    <div class="w-1 h-1 rounded-full bg-gray-200"></div>
                                    <span class="text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">{{ $atleta->categoria->cto_nome ?? 'Geral' }}</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                @if($atleta->cartaoImpresso())
                                    <div class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center text-white">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    <div class="w-5 h-5 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="py-12 bg-gray-50 dark:bg-gray-900/50 rounded-[2.5rem] border border-dashed border-gray-200 dark:border-gray-700 text-center">
                 <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nenhum atleta encontrado</p>
            </div>
        @endforelse
    </div>

    {{-- Export Rápido --}}
    <div class="mt-12 px-1">
        <a href="{{ route('relatorios.export-atletas-por-time', ['time_id' => request('time_id')]) }}" class="block w-full bg-gray-900 dark:bg-gray-800 text-white font-black py-6 rounded-[2rem] text-center shadow-xl active:scale-95 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-3">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Exportar Protocolo (CSV)
        </a>
    </div>
</div>
@endsection
