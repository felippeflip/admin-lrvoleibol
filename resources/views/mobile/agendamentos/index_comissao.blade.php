@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header Title --}}
    <div class="mb-4 px-1">
        <h2 class="text-2xl font-black text-gray-900 dark:text-gray-100 tracking-tight leading-none mb-1">
            Agendamentos
        </h2>
        <p class="text-[13px] text-gray-500 font-medium dark:text-gray-400 uppercase tracking-wider">Defina datas e locais para seus jogos.</p>
    </div>

    {{-- Feedback Mensagens --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 font-bold py-3.5 px-4 rounded-xl shadow-sm mb-4 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414-1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-800 font-bold py-3.5 px-4 rounded-xl shadow-sm mb-4 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Filtros Sanfona (Alpine.js) --}}
    <div x-data="{ openFilters: false }" class="mb-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <button @click="openFilters = !openFilters" class="w-full px-4 py-3.5 flex items-center justify-between text-gray-700 dark:text-gray-300 text-xs font-bold leading-none bg-gray-50/50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700">
            <span class="flex items-center gap-2">
                <svg class="w-4.5 h-4.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> 
                BUSCAR JOGOS
            </span>
            <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-180': openFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div x-show="openFilters" x-collapse>
            <form method="GET" action="{{ route('agendamentos.comissao.index') }}" class="p-4 space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1">CATEGORIA</label>
                    <select name="categoria_id" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-xs rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-orange-500 appearance-none">
                        <option value="">Todas</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->cto_id }}" {{ request('categoria_id') == $cat->cto_id ? 'selected' : '' }}>{{ $cat->cto_nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 ml-1">STATUS</label>
                    <select name="status" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-xs rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-orange-500 appearance-none">
                        <option value="">Todos</option>
                        <option value="pendente_preenchimento" {{ request('status') == 'pendente_preenchimento' ? 'selected' : '' }}>Livre (Disponível)</option>
                        <option value="pendente_aprovacao" {{ request('status') == 'pendente_aprovacao' ? 'selected' : '' }}>Bloqueado (Aguardando Admin)</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 bg-gray-900 dark:bg-gray-700 text-white font-black text-xs py-3.5 rounded-xl uppercase tracking-wider transition active:scale-95 shadow-lg">Aplicar</button>
                    <a href="{{ route('agendamentos.comissao.index') }}" class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold text-xs py-3.5 rounded-xl text-center uppercase tracking-wider transition active:scale-95">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Cards (Jogos Pendentes) --}}
    <div class="space-y-6 mb-24 px-0.5">
        @forelse ($jogos as $jogo)
            @php
                $isLocked = ($jogo->jgo_status_agendamento == 'pendente_aprovacao');
                $sugeridoPorMim = ($time_id && $jogo->jgo_sugerido_por_equipe_id == $time_id);
                $cmp = $jogo->mandante->campeonato;
                $cat = $jogo->mandante->equipe->categoria;
            @endphp
            
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative" x-data="{ openForm: false }">
                {{-- Badge Status Canto --}}
                <div class="absolute top-4 right-4 z-10">
                    @if($isLocked)
                        <span class="bg-yellow-100 text-yellow-700 text-[9px] font-black px-2 py-1 rounded-full border border-yellow-200 dark:bg-yellow-900/30 dark:border-yellow-800/40 uppercase tracking-tighter">🔒 Bloqueado</span>
                    @else
                        <span class="bg-blue-100 text-blue-700 text-[9px] font-black px-2 py-1 rounded-full border border-blue-200 dark:bg-blue-900/30 dark:border-blue-800/40 uppercase tracking-tighter">✨ Livre</span>
                    @endif
                </div>

                <div class="p-4 pt-10">
                    {{-- Header Contexto --}}
                    <div class="mb-5 text-center px-6">
                        <p class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest leading-none">{{ $cmp->cpo_nome ?? 'Copa Inválida' }}</p>
                        <h4 class="text-sm font-black text-gray-900 dark:text-white mt-1 uppercase">{{ $cat->cto_nome }}</h4>
                        <div class="mt-2 bg-gray-50 dark:bg-gray-900/50 rounded-lg py-1.5 px-3 inline-block border border-gray-100 dark:border-gray-700">
                             <span class="text-[9px] font-bold text-gray-400 uppercase">Fase: </span>
                             <span class="text-[10px] font-black text-gray-700 dark:text-gray-300 uppercase">{{ $jogo->jgo_fase }}</span>
                        </div>
                    </div>

                    {{-- Confronto Visual --}}
                    <div class="flex items-center justify-between mb-6 px-2 gap-4">
                        {{-- Mandante --}}
                        <div class="flex-1 min-w-0 text-center">
                            <div class="mb-1">
                                <span class="bg-blue-50 text-blue-500 text-[8px] font-black px-1.5 py-0.5 rounded-sm uppercase tracking-tighter">MANDANTE</span>
                            </div>
                            <h3 class="text-xs font-black text-gray-800 dark:text-gray-200 leading-tight line-clamp-2">
                                {{ $jogo->mandante->equipe->eqp_nome_detalhado }}
                            </h3>
                        </div>

                        {{-- VS --}}
                        <div class="shrink-0 flex flex-col items-center">
                            <span class="text-lg font-black text-gray-300 dark:text-gray-600 italic">VS</span>
                            @if(!$isLocked)
                                <form action="{{ route('agendamentos.trocar-mandante', $jogo->jgo_id) }}" method="POST" onsubmit="return confirm('Alternar mando de campo?');">
                                    @csrf
                                    <button type="submit" class="mt-1 p-1.5 bg-gray-50 dark:bg-gray-900 rounded-full border border-gray-100 dark:border-gray-700 text-gray-400 hover:text-indigo-600 active:scale-90 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    </button>
                                </form>
                            @endif
                        </div>

                        {{-- Visitante --}}
                        <div class="flex-1 min-w-0 text-center">
                            <div class="mb-1">
                                <span class="bg-gray-100 text-gray-400 text-[8px] font-black px-1.5 py-0.5 rounded-sm uppercase tracking-tighter">VISITANTE</span>
                            </div>
                            <h3 class="text-xs font-black text-gray-800 dark:text-gray-200 leading-tight line-clamp-2">
                                {{ $jogo->visitante->equipe->eqp_nome_detalhado }}
                            </h3>
                        </div>
                    </div>

                    {{-- Status / Info Agendamento --}}
                    @if($isLocked)
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 text-center">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 leading-none">Sugestão de Agendamento</p>
                            <div class="space-y-1">
                                <p class="text-sm font-black text-gray-800 dark:text-gray-200">📅 {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }} às {{ $jogo->jgo_hora_jogo }}h</p>
                                <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400">📍 {{ $jogo->ginasio->gin_nome ?? 'Local não definido' }}</p>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                                @if($sugeridoPorMim)
                                    <span class="text-[10px] font-bold text-green-600 flex items-center justify-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Aguardando aprovação do Admin
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-yellow-600 flex items-center justify-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        Sugerido por adversário
                                    </span>
                                @endif
                            </div>
                        </div>
                    @else
                        <button @click="openForm = !openForm" class="w-full py-4 rounded-2xl bg-gray-900 dark:bg-gray-700 text-white font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg active:scale-[0.98] transition-transform">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                             INFORMAR DATA E LOCAL
                        </button>
                    @endif
                </div>

                {{-- Formulário de Sugestão (Alpine) --}}
                @if(!$isLocked)
                    <div x-show="openForm" x-collapse>
                        <form action="{{ route('agendamentos.comissao.sugerir', $jogo->jgo_id) }}" method="POST" class="p-5 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1 leading-none">DATA</label>
                                    <input type="date" name="jgo_dt_jogo" required class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1 leading-none">HORÁRIO</label>
                                    <input type="time" name="jgo_hora_jogo" required class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1 leading-none">GINÁSIO / LOCAL</label>
                                <select name="jgo_local_jogo_id" required class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm rounded-xl p-3 dark:text-white outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                                    <option value="">Selecione o Ginásio...</option>
                                    @foreach($ginasios as $gin)
                                        <option value="{{ $gin->gin_id }}">{{ $gin->gin_nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full py-4 rounded-xl bg-blue-600 text-white font-black text-xs uppercase tracking-wider shadow-lg active:scale-95 transition-transform">
                                ENVIAR SUGESTÃO
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Footer do Card (Prazos) --}}
                <div class="px-5 py-3 bg-gray-50/50 dark:bg-gray-900/20 border-t border-gray-100 dark:border-gray-700 flex justify-between gap-4">
                    <div class="flex-1">
                        <span class="block text-[7px] font-black text-gray-400 uppercase tracking-tight">PRAZO CLASSIF.</span>
                        <span class="text-[9px] font-bold text-gray-600 dark:text-gray-400">{{ $cmp->cpo_dt_fim_classificacao ? \Carbon\Carbon::parse($cmp->cpo_dt_fim_classificacao)->format('d/m/Y') : '---' }}</span>
                    </div>
                    <div class="text-right flex-1">
                        <span class="block text-[7px] font-black text-gray-400 uppercase tracking-tight">PRAZO FINAIS</span>
                        <span class="text-[9px] font-bold text-gray-600 dark:text-gray-400">{{ $cmp->cpo_dt_fim_finais ? \Carbon\Carbon::parse($cmp->cpo_dt_fim_finais)->format('d/m/Y') : '---' }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center p-16 bg-white dark:bg-gray-800 rounded-[2.5rem] border-2 border-dashed border-gray-100 dark:border-gray-700 mt-6">
                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-800">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-gray-900 dark:text-white font-black text-sm uppercase">Tudo em dia!</h3>
                <p class="text-gray-400 text-xs font-bold mt-1 px-4 leading-relaxed italic">Nenhum agendamento pendente para sua equipe no momento.</p>
            </div>
        @endforelse

        {{-- Paginação --}}
        <div class="mt-6 px-1 mb-10">
            {{ $jogos->links() }}
        </div>
    </div>
</div>
@endsection
