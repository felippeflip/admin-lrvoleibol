@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-24">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8 px-1">
        <a href="{{ route('arbitros.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-90 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Perfil de Oficial</h2>
    </div>

    {{-- Perfil Card --}}
    <div class="bg-indigo-600 rounded-[3rem] p-8 text-white shadow-2xl shadow-indigo-200 dark:shadow-none mb-6 relative overflow-hidden">
        {{-- Ornamentos --}}
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-black/10 rounded-full blur-3xl"></div>
        
        <div class="flex flex-col items-center text-center relative z-10">
            <div class="w-32 h-40 rounded-3xl overflow-hidden border-4 border-white/20 bg-white/10 mb-5 shadow-inner">
                @if($arbitro->foto)
                    <img src="{{ $arbitro->foto_url }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-5xl font-black text-white/20 uppercase">{{ substr($arbitro->name, 0, 1) }}</div>
                @endif
            </div>
            
            <h2 class="text-2xl font-black uppercase tracking-tighter mb-1">{{ $arbitro->name }}</h2>
            <div class="flex items-center gap-2 mb-6">
                <span class="px-3 py-1 bg-white/20 rounded-full text-[10px] font-black uppercase tracking-widest text-white shadow-sm border border-white/10">
                    {{ $arbitro->apelido ?? 'Juiz Oficial' }}
                </span>
            </div>

            <div class="flex gap-4 w-full pt-6 border-t border-white/10">
                <div class="flex-1 flex flex-col items-center">
                    <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none mb-1">Registro</span>
                    <span class="text-xl font-black text-white">{{ $arbitro->lrv ?? '---' }}</span>
                </div>
                <div class="w-px bg-white/10 h-10"></div>
                <div class="flex-1 flex flex-col items-center">
                    <span class="text-[10px] font-black text-white/50 uppercase tracking-widest leading-none mb-1">Categoria</span>
                    <span class="text-[15px] font-black text-white truncate w-full text-center">{{ $arbitro->tipo_arbitro ?? 'Ativo' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Detalhes Secundários --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-50 dark:border-gray-700 space-y-6">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 mb-2">Informações Adicionais</h3>
        
        <div class="grid grid-cols-1 gap-6">
            <div class="flex items-center gap-4 group">
                <div class="w-12 h-12 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center group-active:scale-95 transition-all text-indigo-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter leading-none mb-0.5">E-mail</p>
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $arbitro->email }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 group">
                 <div class="w-12 h-12 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center group-active:scale-95 transition-all text-indigo-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter leading-none mb-0.5">Telefone</p>
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $arbitro->telefone ?? '---' }}</p>
                </div>
            </div>

            @if($arbitro->cref)
            <div class="flex items-center gap-4 group">
                 <div class="w-12 h-12 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center group-active:scale-95 transition-all text-indigo-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-4 0a2 2 0 012-2h2a2 2 0 012 2v1m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter leading-none mb-0.5">CREF</p>
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $arbitro->cref }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Rodapé / Ações --}}
    <div class="flex flex-col gap-4 pt-8 pb-12 px-1">
        <a href="mailto:{{ $arbitro->email }}" class="w-full bg-gray-900 dark:bg-gray-800 text-white font-black py-5 rounded-2xl text-center shadow-xl active:scale-95 transition-all">
            Enviar Mensagem
        </a>
        <a href="{{ route('arbitros.index') }}" class="w-full text-gray-400 font-bold py-2 text-center underline italic text-sm">Voltar para listagem</a>
    </div>
</div>
@endsection
