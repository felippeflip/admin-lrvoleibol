@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header Title --}}
    <div class="mb-6 px-1">
        <h2 class="text-2xl font-black text-gray-900 dark:text-gray-100 tracking-tight leading-none mb-1">
            Tabelas Geradas
        </h2>
        <p class="text-[13px] text-gray-500 font-medium dark:text-gray-400 uppercase tracking-wider leading-tight">Arquivos estáticos para o site.</p>
    </div>

    <div class="space-y-4 mb-24 px-0.5">
        @forelse($arquivos as $arquivo)
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex items-center p-4 gap-4">
                {{-- Icone Arquivo --}}
                <div class="w-12 h-12 shrink-0 bg-green-50 dark:bg-green-900/20 rounded-2xl flex items-center justify-center text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="text-xs font-black text-gray-800 dark:text-gray-200 truncate uppercase">{{ $arquivo['nome'] }}</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[9px] font-bold text-gray-400 uppercase">{{ $arquivo['data'] }}</span>
                        <span class="text-[9px] font-bold text-gray-300">|</span>
                        <span class="text-[9px] font-bold text-gray-400 uppercase">{{ $arquivo['tamanho'] }}</span>
                    </div>
                </div>

                <div class="shrink-0 flex gap-2">
                    <a href="{{ $arquivo['url'] }}" target="_blank" class="p-2.5 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600 text-gray-500 dark:text-gray-300 active:scale-90 transition">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center p-16 bg-white dark:bg-gray-800 rounded-[2.5rem] border-2 border-dashed border-gray-100 dark:border-gray-700 mt-6 italic text-gray-400 text-xs">
                Nenhuma tabela foi gerada ainda.
            </div>
        @endforelse
    </div>
</div>
@endsection
