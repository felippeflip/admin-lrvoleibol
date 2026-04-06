@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header Title --}}
    <div class="flex justify-between items-end mb-4 px-1">
        <div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-gray-100 tracking-tight leading-none mb-1">
                Documentos
            </h2>
            <p class="text-[13px] text-gray-500 font-medium dark:text-gray-400 uppercase tracking-wider">Regulamentos, Atas e Arquivos.</p>
        </div>
        @hasrole('Administrador')
        <a href="{{ route('documentos.create') }}" class="flex items-center gap-1.5 bg-orange-600 hover:bg-orange-700 text-white text-[11px] font-black px-3.5 py-2.5 rounded-xl shadow-lg active:scale-95 transition-transform uppercase">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            NOVO
        </a>
        @endhasrole
    </div>

    {{-- Sucesso / Erros --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 font-bold py-3.5 px-4 rounded-xl shadow-sm mb-4 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414-1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Grid de Documentos --}}
    <div class="space-y-4 mb-24 px-0.5">
        @forelse ($documentos as $documento)
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" x-data="{ openAdmin: false }">
                <div class="p-4">
                    <div class="flex items-start gap-4">
                        {{-- Icone Tipo --}}
                        <div class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center {{ $documento->tipo == 'pdf' ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-500' }} dark:bg-opacity-10">
                            @if($documento->tipo == 'pdf')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <h3 class="text-base font-black text-gray-900 dark:text-white leading-tight pr-2">
                                    {{ $documento->titulo }}
                                </h3>
                                @hasrole('Administrador')
                                <button @click="openAdmin = !openAdmin" class="p-1.5 bg-gray-50 dark:bg-gray-900 rounded-lg text-gray-400 hover:text-orange-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                                </button>
                                @endhasrole
                            </div>
                            <p class="text-[11px] font-medium text-gray-500 dark:text-gray-400 mt-1 line-clamp-2 leading-relaxed">
                                {{ $documento->descricao }}
                            </p>
                            
                            <div class="flex flex-wrap gap-2 mt-3 text-[9px] font-black uppercase tracking-tighter">
                                <span class="px-2 py-0.5 rounded-md border {{ $documento->tipo == 'pdf' ? 'bg-red-50 border-red-100 text-red-600' : 'bg-blue-50 border-blue-100 text-blue-600' }} dark:bg-opacity-10">
                                    {{ strtoupper($documento->tipo) }}
                                </span>
                                <span class="px-2 py-0.5 rounded-md border bg-gray-50 border-gray-100 text-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                                    Púb: {{ $documento->permissao }}
                                </span>
                                @if(!$documento->ativo)
                                    <span class="px-2 py-0.5 rounded-md border bg-yellow-50 border-yellow-100 text-yellow-600">INATIVO</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex gap-3">
                        <a href="{{ $documento->url }}" target="_blank" class="flex-1 flex items-center justify-center gap-2 bg-gray-900 dark:bg-gray-700 text-white font-black text-xs py-3.5 rounded-2xl shadow-lg active:scale-[0.98] transition-transform uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Baixar Arquivo
                        </a>
                    </div>
                </div>

                {{-- Painel Admin (Alpine) --}}
                @hasrole('Administrador')
                <div x-show="openAdmin" x-collapse>
                    <div class="px-4 pb-4 pt-1 bg-gray-50 dark:bg-gray-900/50 flex gap-3">
                        <a href="{{ route('documentos.edit', $documento->id) }}" class="flex-1 flex items-center justify-center gap-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-3 rounded-2xl text-[10px] font-black uppercase text-gray-700 dark:text-gray-200 shadow-sm transition active:scale-95">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Editar
                        </a>
                        <form action="{{ route('documentos.destroy', $documento->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Excluir documento permanentemente?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/40 py-3 rounded-2xl text-[10px] font-black uppercase text-red-600 shadow-sm transition active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Excluir
                            </button>
                        </form>
                    </div>
                </div>
                @endhasrole
            </div>
        @empty
            <div class="text-center p-16 bg-white dark:bg-gray-800 rounded-[2.5rem] border-2 border-dashed border-gray-100 dark:border-gray-700 mt-6">
                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-800">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-gray-900 dark:text-white font-black text-sm uppercase">Nenhum Documento</h3>
                <p class="text-gray-400 text-xs font-bold mt-1 px-4 leading-relaxed italic">A liga ainda não publicou documentos oficiais.</p>
            </div>
        @endforelse

        {{-- Paginação --}}
        <div class="mt-6 px-1 mb-10">
            {{ $documentos->links() }}
        </div>
    </div>
</div>
@endsection
