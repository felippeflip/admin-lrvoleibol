@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header --}}
    <div class="flex flex-col mb-8 px-1">
        <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter leading-tight">Painel de Arbitragem</h2>
        <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mt-1">Juízes e Apontadores da Liga</p>
    </div>

    {{-- Busca Rápida --}}
    <form method="GET" action="{{ route('arbitros.index') }}" class="mb-6 mx-1">
        <div class="relative group">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, apelido..." 
                   class="w-full bg-white dark:bg-gray-800 border-none rounded-2xl py-4 pl-12 pr-4 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 transition-all dark:text-white">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            @if(request('search'))
                <a href="{{ route('arbitros.index') }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            @endif
        </div>
    </form>

    {{-- Grid de Juízes --}}
    <div class="grid grid-cols-2 gap-4">
        @forelse($arbitros as $arbitro)
            <a href="{{ route('arbitros.show', $arbitro->id) }}" class="bg-white dark:bg-gray-800 rounded-[2rem] p-5 shadow-sm border border-gray-50 dark:border-gray-700 active:scale-95 transition-all text-center group">
                <div class="relative mb-4 mx-auto w-20 h-20">
                     <div class="absolute inset-0 bg-indigo-500 rounded-3xl blur-xl opacity-0 group-hover:opacity-20 transition-opacity"></div>
                     <div class="relative w-20 h-20 rounded-[1.5rem] overflow-hidden border-2 border-indigo-100 dark:border-indigo-900/30 bg-gray-50 dark:bg-gray-900">
                        @if($arbitro->foto)
                            <img src="{{ $arbitro->foto_url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/20">
                                <span class="text-2xl font-black text-indigo-400 uppercase">{{ substr($arbitro->name, 0, 1) }}</span>
                            </div>
                        @endif
                     </div>
                </div>
                
                <h3 class="text-xs font-black text-gray-900 dark:text-white leading-tight mb-1 truncate">{{ $arbitro->name }}</h3>
                <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3">{{ $arbitro->apelido ?? 'Juiz' }}</p>
                
                <div class="pt-3 border-t border-gray-100 dark:border-gray-700/50 flex flex-col items-center gap-1">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">Registro</span>
                    <span class="text-[11px] font-black text-gray-900 dark:text-white">{{ $arbitro->lrv ?? 'Pendente' }}</span>
                </div>
            </a>
        @empty
            <div class="col-span-2 text-center py-12 bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Nenhum oficial encontrado</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8 mb-24 px-2">
        {{ $arbitros->links() }}
    </div>
</div>
@endsection
