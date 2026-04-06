@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header Title --}}
    <div class="flex justify-between items-end mb-4 px-1">
        <div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-gray-100 tracking-tight leading-none mb-1">
                Comissão Técnica
            </h2>
            <p class="text-[13px] text-gray-500 font-medium dark:text-gray-400 uppercase tracking-wider">Gestão do staff das equipes.</p>
        </div>
        <a href="{{ route('comissao-tecnica.create') }}" class="flex items-center gap-1.5 bg-orange-600 hover:bg-orange-700 text-white text-[11px] font-black px-3.5 py-2.5 rounded-xl shadow-lg active:scale-95 transition-transform uppercase">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            NOVO
        </a>
    </div>

    {{-- Sucesso / Erros --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 font-bold py-3.5 px-4 rounded-xl shadow-sm mb-4 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filtros Sanfona (Alpine.js) --}}
    <div x-data="{ openFilters: false }" class="mb-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <button @click="openFilters = !openFilters" class="w-full px-4 py-3.5 flex items-center justify-between text-gray-700 dark:text-gray-300 text-xs font-bold bg-gray-50/50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700">
            <span class="flex items-center gap-2">
                <svg class="w-4.5 h-4.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> 
                FILTRAR STAFF
            </span>
            <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-180': openFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div x-show="openFilters" x-collapse>
            <form method="GET" action="{{ route('comissao-tecnica.index') }}" class="p-4 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 ml-1">NOME / CPF</label>
                    <input type="text" name="nome" value="{{ request('nome') }}" placeholder="Nome do membro..." class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm rounded-xl p-3 outline-none focus:ring-2 focus:ring-orange-500 dark:text-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 ml-1">FUNÇÃO</label>
                        <select name="funcao" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-xs rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Todas</option>
                            @foreach ($funcoes as $funcao)
                                <option value="{{ $funcao }}" {{ request('funcao') == $funcao ? 'selected' : '' }}>{{ $funcao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 ml-1">STATUS</label>
                        <select name="status" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-xs rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Todos</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativos</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>
                </div>
                @if(auth()->user()->hasRole('Administrador'))
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 ml-1">TIME</label>
                    <select name="time_id" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-xs rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Todos os Clubes</option>
                        @foreach ($times as $time)
                            <option value="{{ $time->tim_id }}" {{ request('time_id') == $time->tim_id ? 'selected' : '' }}>{{ $time->tim_nome }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 bg-gray-900 dark:bg-gray-700 text-white font-black text-xs py-3.5 rounded-xl uppercase tracking-wider transition active:scale-95">Filtrar</button>
                    <a href="{{ route('comissao-tecnica.index', ['clear' => 1]) }}" class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold text-xs py-3.5 rounded-xl text-center uppercase tracking-wider transition active:scale-95">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Cards (Comissão Técnica) --}}
    <div class="space-y-4 mb-24 px-0.5">
        @forelse ($comissao as $membro)
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" x-data="{ openMenu: false }">
                {{-- Principal Info --}}
                <div class="p-4 flex items-center gap-4">
                    {{-- Foto Avatar --}}
                    <div class="relative">
                        <div class="w-16 h-16 shrink-0 rounded-2xl overflow-hidden border-2 border-gray-100 dark:border-gray-700 shadow-sm bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                            @if($membro->foto)
                                <img src="{{ $membro->foto_url }}" alt="Foto" class="w-full h-full object-cover">
                            @else
                                <svg class="w-8 h-8 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                            @endif
                        </div>
                        {{-- Status Dot --}}
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center {{ $membro->status ? 'bg-green-500' : 'bg-red-500' }}">
                            @if($membro->status)
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-base font-black text-gray-900 dark:text-white leading-tight truncate pr-1">
                                    {{ $membro->nome }}
                                </h3>
                                <p class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest mt-0.5">
                                    {{ $membro->funcao }}
                                </p>
                            </div>
                            <button @click="openMenu = !openMenu" class="p-1.5 bg-gray-50 dark:bg-gray-900 rounded-lg text-gray-400 hover:text-orange-500 transition-colors">
                                <svg class="w-5 h-5 transition-transform duration-300" :class="{'rotate-90': openMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                            </button>
                        </div>
                        
                        <div class="flex flex-wrap gap-x-3 gap-y-1 mt-2">
                             <div class="flex items-center gap-1">
                                <span class="text-[9px] font-bold text-gray-400 uppercase">Reg. LRV:</span>
                                <span class="text-[10px] font-black text-gray-700 dark:text-gray-300">#{{ $membro->registro_lrv ?: '---' }}</span>
                            </div>
                            @if($membro->cartaoImpresso())
                                <div class="flex items-center gap-1 bg-green-50 dark:bg-green-900/20 px-1.5 py-0.5 rounded border border-green-100 dark:border-green-800/30">
                                    <svg class="w-2.5 h-2.5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-[8px] font-black text-green-700 dark:text-green-400 uppercase tracking-tighter">Cartão OK</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Détails Secundários (Time e Documento) --}}
                <div class="px-4 py-2.5 bg-gray-50/50 dark:bg-gray-900/20 border-y border-gray-100 dark:border-gray-700 grid grid-cols-2">
                    <div class="min-w-0">
                        <span class="block text-[8px] font-bold text-gray-400 uppercase tracking-widest">CLUBE</span>
                        <span class="text-[10px] font-black text-gray-600 dark:text-gray-400 truncate block">{{ $membro->time->tim_nome ?? 'N/A' }}</span>
                    </div>
                    <div class="text-right">
                        <span class="block text-[8px] font-bold text-gray-400 uppercase tracking-widest">REGISTRO (PROF)</span>
                        <span class="text-[10px] font-black text-gray-600 dark:text-gray-400 truncate block">{{ $membro->documento_registro ?: 'N/A' }}</span>
                    </div>
                </div>

                {{-- Action Panel (Alpine) --}}
                <div x-show="openMenu" x-collapse>
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Visualizar Detalhes --}}
                            <a href="{{ route('comissao-tecnica.show', $membro->id) }}" class="flex items-center justify-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-3 rounded-2xl text-[10px] font-black uppercase text-gray-700 dark:text-gray-200 shadow-sm active:scale-95 transition-transform">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                PERFIL COMPLETO
                            </a>

                            {{-- Editar --}}
                            <a href="{{ route('comissao-tecnica.edit', $membro->id) }}" class="flex items-center justify-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-3 rounded-2xl text-[10px] font-black uppercase text-gray-700 dark:text-gray-200 shadow-sm active:scale-95 transition-transform">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                EDITAR DADOS
                            </a>

                            {{-- Toggle Status --}}
                            <form action="{{ route('comissao-tecnica.toggleStatus', $membro->id) }}" method="POST" class="col-span-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full flex items-center justify-center gap-2 {{ $membro->status ? 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-900/40 text-red-600' : 'bg-green-50 dark:bg-green-900/20 border-green-100 dark:border-green-900/40 text-green-600' }} border py-3 rounded-2xl text-[10px] font-black uppercase shadow-sm active:scale-95 transition-transform">
                                    @if($membro->status)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        DESATIVAR
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        ATIVAR
                                    @endif
                                </button>
                            </form>

                            {{-- Imprimir (Admin Only) --}}
                            @if(auth()->user()->hasRole('Administrador') && !$membro->cartaoImpresso())
                            <form action="{{ route('comissao-tecnica.markPrinted', $membro->id) }}" method="POST" class="col-span-1" onsubmit="return confirm('Confirmar impressão do cartão?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-indigo-50 dark:bg-indigo-900/20 border-indigo-100 dark:border-indigo-900/40 border py-3 rounded-2xl text-[10px] font-black uppercase text-indigo-700 shadow-sm active:scale-95 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    CRIAR CARTÃO
                                </button>
                            </form>
                            @endif

                            {{-- Transferir (Admin Only) --}}
                            @if(auth()->user()->hasRole('Administrador'))
                            <div class="col-span-2 mt-1">
                                <a href="{{ route('comissao-tecnica.transferencia-direta', $membro->id) }}" class="w-full flex items-center justify-center gap-2 bg-gray-900 text-white py-3.5 rounded-2xl text-[10px] font-black uppercase shadow-lg active:scale-[0.98] transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    TRANSFERIR DE EQUIPE
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center p-16 bg-white dark:bg-gray-800 rounded-[2.5rem] border-2 border-dashed border-gray-100 dark:border-gray-700 mt-6">
                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-800">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354l1.1 2.226 2.456.357-1.777 1.732.42 2.446L12 9.97l-2.199 1.156.42-2.446-1.777-1.732 2.456-.357L12 4.354zM16 11.37C17.5 11.37 19 12.5 19 14.5s-1.5 3.13-3 3.13-3-1.13-3-3.13 1.5-3.13 3-3.13zM4 11.37C5.5 11.37 7 12.5 7 14.5s-1.5 3.13-3 3.13-3-1.13-3-3.13 1.5-3.13 3-3.13z"></path></svg>
                </div>
                <h3 class="text-gray-900 dark:text-white font-black text-sm uppercase">Nenhum Registro</h3>
                <p class="text-gray-400 text-xs font-bold mt-1 px-4 leading-relaxed italic">Não encontramos membros desta comissão técnica.</p>
            </div>
        @endforelse

        {{-- Paginação Customizada Mobile --}}
        <div class="mt-6 px-1 mb-10">
            {{ $comissao->links() }}
        </div>
    </div>
</div>
@endsection
