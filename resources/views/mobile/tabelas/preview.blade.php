@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-lg mx-auto pb-20">
    {{-- Header & Ações --}}
    <div class="mb-4 flex flex-col gap-2 px-1">
        <div class="flex items-center gap-3">
            <a href="{{ route('jogos.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-extrabold text-gray-900 dark:text-gray-100 leading-tight">
                Preview Classificação
            </h2>
        </div>
        <p class="text-xs text-gray-500 font-medium px-1 mt-1 truncate">
            {{ $campeonato->cpo_nome }} / {{ $categoria->cto_nome }}
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 mx-1 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
            <p class="text-xs font-bold text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Iframe de Preview (Simulando mobile look do que será gerado) --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-6 mx-1">
        <div class="bg-gray-50 dark:bg-gray-900 px-4 py-2 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Visualização Real
            </span>
            <div class="flex gap-1">
                <div class="w-2 h-2 rounded-full bg-red-400"></div>
                <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                <div class="w-2 h-2 rounded-full bg-green-400"></div>
            </div>
        </div>
        
        {{-- Iframe com o conteúdo estático que será publicado --}}
        <div class="relative w-full" style="height: 120vh; max-height: 800px;">
            <iframe id="preview-iframe-mobile" 
                    class="w-full h-full border-0" 
                    srcdoc="{{ view('tabelas.tabela_publica', compact('campeonato', 'categoria', 'dados'))->render() }}">
            </iframe>
        </div>
    </div>

    {{-- Botão de Publicação Fixo no Rodapé Desktop mas aqui visível --}}
    <div class="px-1 mb-8">
        <form action="{{ route('classificacao.publicar', [$campeonato->cpo_id, $categoria->cto_id]) }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-extrabold py-4 rounded-2xl shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Aprovar & Publicar Tabela
            </button>
        </form>
    </div>
</div>
@endsection
