@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header --}}
    <div class="flex flex-col mb-8 px-1">
        <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter leading-tight">Clubes e Equipes</h2>
        <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mt-1">Gestão de filiados</p>
    </div>

    {{-- Ações Admin --}}
    @hasrole('Administrador')
    <div class="mb-6 px-1">
        <a href="{{ route('times.create') }}" class="flex items-center justify-center gap-2 w-full bg-indigo-600 text-white font-black py-4 rounded-2xl shadow-xl shadow-indigo-100 dark:shadow-none active:scale-95 transition-all text-xs uppercase tracking-widest leading-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            Novo Clube Filiado
        </a>
    </div>
    @endhasrole

    {{-- Filtros Rápido --}}
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700 mb-8 mx-1">
        <form method="GET" action="{{ route('times.index') }}" class="space-y-4">
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Nome do Clube</p>
                <input type="text" name="tim_nome" value="{{ request('tim_nome') }}" placeholder="Ex: Vôlei Futuro..." class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl py-3 px-4 text-xs font-bold dark:text-white">
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 bg-gray-900 dark:bg-gray-700 text-white font-black py-3 rounded-xl text-[10px] uppercase tracking-widest active:scale-95 transition-all">Filtrar</button>
                <a href="{{ route('times.index') }}" class="px-4 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-xl flex items-center justify-center active:scale-95 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Lista de Times --}}
    <div class="space-y-4">
        @forelse($times as $time)
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700">
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 flex-shrink-0">
                        @if($time->tim_logo)
                            <img src="{{ Storage::disk('times_logos')->url($time->tim_logo) }}" class="w-full h-full object-contain p-1">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-indigo-400 font-black uppercase text-xl">{{ substr($time->tim_nome, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight mb-1 truncate">{{ $time->tim_nome }}</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest">{{ $time->tim_sigla ?? 'LRV' }}</span>
                            <div class="w-1 h-1 bg-gray-200 rounded-full"></div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate">{{ $time->tim_cidade ?? 'Região LRV' }}</span>
                        </div>
                    </div>
                    <span class="px-2 py-1 {{ $time->tim_status ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }} rounded-lg text-[8px] font-black uppercase tracking-widest">
                        {{ $time->tim_status ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-6">
                    <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-2xl border border-gray-100 dark:border-gray-800">
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter leading-none mb-1">Responsável</p>
                        <p class="text-[10px] font-bold text-gray-600 dark:text-gray-300 truncate">{{ $time->user->name ?? 'Não Vinculado' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-2xl border border-gray-100 dark:border-gray-800">
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-tighter leading-none mb-1">Atletas</p>
                        <p class="text-[10px] font-bold text-gray-600 dark:text-gray-300">{{ $time->atletas_count ?? \App\Models\Atleta::where('atl_tim_id', $time->tim_id)->count() }} Inscritos</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('times.show', $time->tim_id) }}" class="flex-1 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 font-black py-4 rounded-xl text-[10px] uppercase tracking-widest text-center active:scale-95 transition-all">
                        Ver Perfil
                    </a>
                    @if(auth()->user()->hasRole('Administrador') || $time->tim_user_id == auth()->id())
                        <a href="{{ route('times.edit', $time->tim_id) }}" class="w-12 h-12 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl flex items-center justify-center text-gray-400 active:scale-90 transition-all">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-12 bg-gray-50 dark:bg-gray-900/50 rounded-[2.5rem] border border-dashed border-gray-200 dark:border-gray-700 text-center">
                 <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nenhum clube encontrado</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8 mb-24">
        {{ $times->links() }}
    </div>
</div>
@endsection
