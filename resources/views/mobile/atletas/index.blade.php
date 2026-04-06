@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header & Ações --}}
    <div class="mb-4 flex flex-col gap-2">
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-gray-100 flex items-center justify-between">
            <span>Guarnição / Atletas</span>
            <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs py-1 px-2.5 rounded-full font-bold">
                {{ $atletas->total() }} registros
            </span>
        </h2>
    </div>

    {{-- Ações Administrativas (Horizontal Scroll) --}}
    <div class="flex overflow-x-auto gap-2 pb-2 mb-4 scrollbar-hide">
        <a href="{{ route('atletas.create') }}" class="whitespace-nowrap shrink-0 flex items-center justify-center gap-1 text-white bg-blue-600 hover:bg-blue-700 font-semibold rounded-lg text-sm px-4 py-3 shadow-sm transition-colors">
            <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Novo Atleta
        </a>
    </div>

    {{-- Mensagens de Alerta --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 font-semibold py-3 px-4 rounded-lg shadow-sm mb-4 text-sm animate-fade-in-down">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-800 font-semibold py-3 px-4 rounded-lg shadow-sm mb-4 text-sm animate-fade-in-down">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filtros Retráteis no Mobile --}}
    <div x-data="{ openFilters: {{ request()->hasAny(['nome', 'cpf', 'time_id', 'categoria', 'sexo']) ? 'true' : 'false' }} }" class="mb-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <button @click="openFilters = !openFilters" class="w-full px-4 py-3 flex items-center justify-between text-gray-700 dark:text-gray-300 font-medium bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filtros Dinâmicos
                @if(request()->except('page'))
                    <span class="w-2 h-2 rounded-full md:w-3 md:h-3 bg-red-500 ml-1"></span>
                @endif
            </span>
            <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': openFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>

        <div x-show="openFilters" x-collapse>
            <form method="GET" action="{{ route('atletas.index') }}" class="p-4 space-y-4 text-left">
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Nome</label>
                        <input type="text" name="nome" value="{{ request('nome') }}" placeholder="Nome do Atleta" class="w-full bg-gray-50 border border-gray-300 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Status</label>
                        <select name="ativo" class="w-full bg-gray-50 border border-gray-300 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="1" {{ request('ativo', '1') == '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ request('ativo') == '0' ? 'selected' : '' }}>Inativo</option>
                            <option value="todos" {{ request('ativo') == 'todos' ? 'selected' : '' }}>Todos</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">CPF</label>
                        <input type="text" name="cpf" value="{{ request('cpf') }}" placeholder="Busca por formatação" class="w-full bg-gray-50 border border-gray-300 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    @role('Administrador')
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Time (Admin)</label>
                        <select name="time_id" class="w-full bg-gray-50 border border-gray-300 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Qualquer Time</option>
                            @foreach($times as $time)
                                <option value="{{ $time->tim_id }}" {{ request('time_id') == $time->tim_id ? 'selected' : '' }}>{{ \Illuminate\Support\Str::limit($time->tim_nome, 35) }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endrole

                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Categoria</label>
                        <select name="categoria" class="w-full bg-gray-50 border border-gray-300 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Todas</option>
                            @foreach($categorias ?? [] as $cat)
                                <option value="{{ $cat->cto_id }}" {{ request('categoria') == $cat->cto_id ? 'selected' : '' }}>{{ $cat->cto_nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700 mt-4">
                    <a href="{{ route('atletas.index', ['clear' => 1]) }}" class="w-1/3 flex justify-center items-center py-2.5 px-4 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600">
                        Limpar
                    </a>
                    <button type="submit" class="w-2/3 flex justify-center items-center py-2.5 px-4 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm">
                        Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista de Cards Dinâmicos (Touch Target Friendly) --}}
    <div class="space-y-4 pb-8">
        @forelse ($atletas as $atleta)
            @php
                $borderStatus = $atleta->atl_ativo ? 'border-gray-200 dark:border-gray-700' : 'border-red-400 dark:border-red-600 border-l-4 opacity-75';
            @endphp
            
            <div x-data="{ openActions: false }" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $borderStatus }} overflow-hidden">
                <div class="p-4 flex gap-4">
                    {{-- Foto Perfil Esquerda --}}
                    <div class="shrink-0 relative">
                        <img src="{{ $atleta->atl_foto_url }}" alt="Foto Atleta" class="h-16 w-16 object-cover rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                        @if($atleta->cartaoImpresso())
                            <span class="absolute -top-1.5 -right-1.5 h-5 w-5 bg-green-500 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center text-white" title="Cartão {{ date('Y') }} Impresso">
                                <svg class="w-3 h-3 font-bold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                        @endif
                    </div>

                    {{-- Info Principal --}}
                    <div class="flex-1 min-w-0 flex flex-col justify-center">
                        <div class="flex justify-between items-start gap-1">
                            <h3 class="text-[15px] font-bold text-gray-900 dark:text-white truncate leading-tight mt-0.5">
                                {{ $atleta->atl_nome }}
                            </h3>
                        </div>
                        
                        <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide truncate">
                            {{ $atleta->time->tim_nome ?? 'Sem Time Vinculado' }}
                        </p>
                        
                        <div class="flex items-center gap-1.5 mt-2 overflow-x-auto scrollbar-hide pb-1">
                            <span class="inline-block whitespace-nowrap bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px] font-medium px-2 py-0.5 rounded">
                                Reg. LRV: {{ $atleta->atl_resg ?: 'N/A' }}
                            </span>
                            <span class="inline-block whitespace-nowrap bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-[10px] font-medium px-2 py-0.5 rounded">
                                {{ $atleta->categoria->cto_nome ?? 'Categoria -' }}
                            </span>
                            @if(!$atleta->atl_ativo)
                                <span class="inline-block whitespace-nowrap bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400 text-[10px] font-bold px-2 py-0.5 rounded">INATIVO</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Barra de Detalhes Expandida --}}
                <div class="grid grid-cols-2 bg-gray-50/50 dark:bg-gray-900/40 px-4 py-3 border-t border-gray-100 dark:border-gray-700 text-xs gap-y-2">
                    <div>
                        <span class="block text-gray-500 dark:text-gray-400 text-[10px] font-semibold uppercase">CPF</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $atleta->atl_cpf_formatted }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 dark:text-gray-400 text-[10px] font-semibold uppercase">Nascimento</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $atleta->atl_dt_nasc ? \Carbon\Carbon::parse($atleta->atl_dt_nasc)->format('d/m/Y') : 'N/A' }} ({{ $atleta->atl_dt_nasc ? \Carbon\Carbon::parse($atleta->atl_dt_nasc)->age : '-' }} anos)</span>
                    </div>
                </div>

                {{-- Toggle Operador Ações (Gatilho Maior para Thumb) --}}
                <button @click="openActions = !openActions" class="w-full flex items-center justify-center p-3 text-xs bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 text-blue-600 hover:text-blue-800 font-semibold transition-colors">
                    <span x-text="openActions ? 'FECHAR MENU DE OPERAÇÕES' : 'OPERAÇÕES DO ATLETA'"></span>
                </button>

                {{-- Modulo de Ações Grid --}}
                <div x-show="openActions" x-collapse>
                    <div class="grid grid-cols-4 divide-y divide-x divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                        
                        {{-- Visualizar --}}
                        <a href="{{ route('atletas.show', $atleta->atl_id) }}" class="flex flex-col items-center justify-center py-4 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="h-5 w-5 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span class="text-[9px] font-bold uppercase">Ver</span>
                        </a>

                        {{-- Editar --}}
                        <a href="{{ route('atletas.edit', $atleta->atl_id) }}" class="flex flex-col items-center justify-center py-4 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="h-5 w-5 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span class="text-[9px] font-bold uppercase">Editar</span>
                        </a>

                        {{-- Ativar/Desativar --}}
                        <form action="{{ route('atletas.inactivate', $atleta->atl_id) }}" method="POST" class="w-full" onsubmit="return confirm('Tem certeza que deseja {{ $atleta->atl_ativo ? 'desativar' : 'ativar' }} este atleta?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full h-full flex flex-col items-center justify-center py-4 bg-gray-50/50 hover:bg-gray-100 dark:bg-gray-900/30 dark:hover:bg-gray-700 {{ $atleta->atl_ativo ? 'text-red-500' : 'text-green-600' }}">
                                @if($atleta->atl_ativo)
                                    <svg class="h-5 w-5 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                    <span class="text-[9px] font-bold uppercase text-gray-500">Desativar</span>
                                @else
                                    <svg class="h-5 w-5 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span class="text-[9px] font-bold uppercase text-gray-500">Reativar</span>
                                @endif
                            </button>
                        </form>

                        {{-- Deletar --}}
                        <form action="{{ route('atletas.destroy', $atleta->atl_id) }}" method="POST" class="w-full" onsubmit="return confirm('Atenção: Tem certeza que deseja excluir o atleta? Essa ação não desfaz!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full h-full flex flex-col items-center justify-center py-4 text-red-500 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                                <svg class="h-5 w-5 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                <span class="text-[9px] font-bold uppercase">Apagar</span>
                            </button>
                        </form>

                        {{-- Row 2 (Se houver documento ou permissões de Admin) --}}
                        {{-- Colapsado em divs para simétrica em col-span --}}
                        
                        @if($atleta->atl_documento)
                            <a href="{{ $atleta->atl_documento_url }}" target="_blank" class="flex flex-col items-center justify-center py-4 bg-gray-50/20 text-gray-600 border-t border-gray-100 dark:text-gray-400 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <svg class="h-5 w-5 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                <span class="text-[9px] font-bold uppercase">Anexo.</span>
                            </a>
                        @endif

                        @hasrole('Administrador')
                            @if(!$atleta->cartaoImpresso())
                                <form action="{{ route('atletas.markPrinted', $atleta->atl_id) }}" method="POST" class="w-full {{ $atleta->atl_documento ? '' : 'col-start-1 border-l-0' }} col-span-1 border-t border-gray-100 dark:border-gray-700" onsubmit="return confirm('Confirmar carteirinha impressa?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full h-full flex flex-col items-center justify-center py-4 text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20">
                                        <svg class="h-5 w-5 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span class="text-[9px] font-bold uppercase">Impresso</span>
                                    </button>
                                </form>
                            @endif

                             <a href="{{ route('atletas.transferencia-direta', $atleta->atl_id) }}" class="flex flex-col items-center justify-center py-4 {{ (!$atleta->cartaoImpresso() && !$atleta->atl_documento) ? 'col-start-1 border-l-0' : '' }} border-t border-gray-100 text-blue-600 dark:text-blue-400 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                <svg class="h-5 w-5 mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                <span class="text-[9px] font-bold uppercase">Transferir</span>
                            </a>
                        @endhasrole
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-8 text-center dark:bg-gray-800/50 dark:border-gray-600">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nenhum atleta encontrado.</p>
            </div>
        @endforelse

        {{-- Paginação --}}
        <div class="mt-8 mb-16">
            {{ $atletas->links() }}
        </div>
    </div>
</div>
@endsection
