@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-20">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-4 mb-6 px-1">
        <div class="flex items-center gap-3">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white">Categorias</h2>
            <span class="bg-blue-100 text-blue-600 text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-tighter">{{ count($categorias) }}</span>
        </div>
        <a href="{{ route('categorias.create') }}" class="flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-600 text-white shadow-lg active:scale-95 transition-all">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
        </a>
    </div>

    {{-- Sucesso --}}
    @if (session('success'))
        <div class="mb-5 mx-1 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
             <p class="text-xs font-bold text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Listagem em Cards --}}
    <div class="space-y-4 px-1">
        @forelse($categorias as $categoria)
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col gap-4 relative overflow-hidden group active:bg-gray-50 dark:active:bg-gray-900 transition-colors">
                
                {{-- Decoração --}}
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-110"></div>

                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight mb-1">{{ $categoria->cto_nome }}</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $categoria->cto_slug }}</p>
                    </div>
                    
                    {{-- Ações --}}
                    <div class="flex items-center gap-2">
                        <a href="{{ route('categorias.edit', $categoria->cto_id) }}" class="p-3 bg-gray-50 dark:bg-gray-700 rounded-2xl text-gray-500 dark:text-white active:scale-90 transition shadow-sm border border-gray-100 dark:border-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </a>
                        <form action="{{ route('categorias.destroy', $categoria->cto_id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta categoria?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-3 bg-red-50 dark:bg-red-900/30 rounded-2xl text-red-500 active:scale-90 transition shadow-sm border border-red-100 dark:border-red-900/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                @if($categoria->cto_descricao)
                    <div class="relative z-10 pt-4 border-t border-gray-50 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium line-clamp-2 italic">"{{ $categoria->cto_descricao }}"</p>
                    </div>
                @endif
                
                <div class="flex items-center gap-3 pt-1">
                     <span class="text-[9px] font-black text-gray-300 dark:text-gray-600 uppercase tracking-widest">ID: #{{ $categoria->cto_id }}</span>
                     @if($categoria->cto_idade_maxima)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 uppercase tracking-tighter">
                            Idade Máx: {{ $categoria->cto_idade_maxima }} anos
                        </span>
                     @endif
                </div>
            </div>
        @empty
            <div class="py-20 flex flex-col items-center justify-center text-center px-10">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-lg font-black text-gray-400 uppercase tracking-widest">Vazio</h3>
                <p class="text-xs text-gray-500 font-medium">Nenhuma categoria cadastrada no momento.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
