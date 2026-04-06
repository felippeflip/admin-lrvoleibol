@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header --}}
    <div class="flex flex-col mb-8 px-1">
        <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter leading-tight">Ginásios e Sedes</h2>
        <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mt-1">Locais oficiais de competição</p>
    </div>

    {{-- Ações Admin --}}
    @hasrole('Administrador')
    <div class="mb-6 px-1">
        <a href="{{ route('ginasios.create') }}" class="flex items-center justify-center gap-2 w-full bg-indigo-600 text-white font-black py-4 rounded-2xl shadow-xl shadow-indigo-100 dark:shadow-none active:scale-95 transition-all text-xs uppercase tracking-widest leading-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            Cadastrar Novo Local
        </a>
    </div>
    @endhasrole

    {{-- Filtros Rápido --}}
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700 mb-8 mx-1">
        <form method="GET" action="{{ route('ginasios.index') }}" class="space-y-4">
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Buscar por Nome</p>
                <input type="text" name="nome" value="{{ request('nome') }}" placeholder="Ex: Ginásio Municipal..." class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl py-3 px-4 text-xs font-bold dark:text-white">
            </div>
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Cidade</p>
                <input type="text" name="cidade" value="{{ request('cidade') }}" placeholder="Ex: Limeira..." class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl py-3 px-4 text-xs font-bold dark:text-white">
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 bg-gray-900 dark:bg-gray-700 text-white font-black py-3 rounded-xl text-[10px] uppercase tracking-widest active:scale-95 transition-all">Filtrar</button>
                <a href="{{ route('ginasios.index') }}" class="px-4 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-xl flex items-center justify-center active:scale-95 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Lista de Ginásios --}}
    <div class="space-y-4">
        @forelse($ginasios as $ginasio)
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight mb-1 truncate">{{ $ginasio->gin_nome }}</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">{{ $ginasio->gin_cidade }} / {{ $ginasio->gin_estado }}</p>
                    </div>
                    <span class="px-2 py-1 {{ $ginasio->gin_status ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }} rounded-lg text-[8px] font-black uppercase tracking-widest">
                        {{ $ginasio->gin_status ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>

                <div class="flex items-center gap-3 mb-6 bg-gray-50 dark:bg-gray-900 p-3 rounded-2xl border border-gray-100 dark:border-gray-800">
                    <div class="w-10 h-10 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center text-indigo-500 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-tighter leading-none mb-1">Endereço</p>
                        <p class="text-[11px] font-bold text-gray-600 dark:text-gray-300 truncate">{{ $ginasio->gin_endereco }}, {{ $ginasio->gin_numero }}</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ $ginasio->google_maps_link }}" target="_blank" class="flex-1 bg-green-500 text-white font-black py-4 rounded-xl text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A2 2 0 013 15.485V6a2 2 0 011.236-1.846l7-3.5a2 2 0 011.528 0l7 3.5A2 2 0 0121 6v9.485a2 2 0 01-1.553 1.943L14 20l-2 1-2-1z"></path></svg>
                        Maps
                    </a>
                    <a href="{{ $ginasio->waze_link }}" target="_blank" class="flex-1 bg-blue-400 text-white font-black py-4 rounded-xl text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Waze
                    </a>
                    @hasrole('Administrador')
                        <a href="{{ route('ginasios.edit', $ginasio->gin_id) }}" class="w-12 h-12 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl flex items-center justify-center text-gray-400 active:scale-90 transition-all">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </a>
                    @endhasrole
                </div>
            </div>
        @empty
            <div class="py-12 bg-gray-50 dark:bg-gray-900/50 rounded-[2.5rem] border border-dashed border-gray-200 dark:border-gray-700 text-center">
                 <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nenhum ginásio encontrado</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8 mb-24">
        {{ $ginasios->links() }}
    </div>
</div>
@endsection
