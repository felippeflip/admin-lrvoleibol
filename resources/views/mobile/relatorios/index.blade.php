@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header --}}
    <div class="flex flex-col mb-8 px-1">
        <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter leading-tight">Central de Inteligência</h2>
        <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mt-1">Relatórios e Consultas da Liga</p>
    </div>

    {{-- Grid de Relatórios --}}
    <div class="space-y-4">
        {{-- Relatório: Atletas por Time --}}
        <a href="{{ route('relatorios.atletas-por-time') }}" class="group block bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700 active:scale-95 transition-all">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center text-blue-600 group-hover:rotate-6 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight mb-1">Inscritos por Time</h3>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Protocolo de Carteirinhas</p>
                </div>
                <div class="text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>
        </a>

        {{-- Relatório: Tabelas Geradas --}}
        <a href="{{ route('relatorios.tabelas-geradas') }}" class="group block bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700 active:scale-95 transition-all">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-green-50 dark:bg-green-900/20 rounded-2xl flex items-center justify-center text-green-600 group-hover:rotate-6 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight mb-1">Arquivos Estáticos</h3>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Classificação HTML</p>
                </div>
                <div class="text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>
        </a>

        {{-- Em Breve --}}
        <div class="bg-gray-50 dark:bg-gray-900 rounded-[2.5rem] p-6 border border-dashed border-gray-200 dark:border-gray-700 opacity-60">
            <div class="flex items-center gap-5 grayscale">
                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-gray-400 leading-tight mb-1">Estatísticas PRO</h3>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Breve Disponibilidade</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
