@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-24">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6 px-1">
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h2 class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight">Agendamentos</h2>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $cmp->cpo_nome }}</p>
        </div>
    </div>

    {{-- Feedback --}}
    @if (session('success'))
        <div class="mb-5 mx-1 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
             <p class="text-xs font-bold text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-5 mx-1 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
             <p class="text-xs font-bold text-red-800 dark:text-red-300">{{ $errors->first() }}</p>
        </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 gap-3 mb-8 px-1">
        <div class="bg-blue-600 rounded-3xl p-4 text-white shadow-lg">
            <p class="text-[10px] font-black uppercase opacity-60 mb-1">Total</p>
            <p class="text-2xl font-black">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-orange-500 rounded-3xl p-4 text-white shadow-lg">
            <p class="text-[10px] font-black uppercase opacity-60 mb-1">Aprovação</p>
            <p class="text-2xl font-black">{{ $stats['aprovacao'] }}</p>
        </div>
    </div>

    {{-- Ações Rápidas (Accordion) --}}
    <div class="space-y-3 mb-8 px-1">
        <details class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <summary class="flex items-center justify-between p-5 cursor-pointer list-none">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <span class="font-black text-xs uppercase tracking-widest text-gray-700 dark:text-white">Gerar Agendamento</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="p-5 pt-0 border-t border-gray-50 dark:border-gray-700">
                <form action="{{ route('agendamentos.gerar', ['campeonato' => $cmp->cpo_id, 'categoria' => '__CAT__']) }}" method="POST" id="form-gerar-mobile" onsubmit="return handleGerarSubmitMobile()">
                    @csrf
                    <div class="space-y-4 pt-4">
                        <select id="categoria_id_mobile" class="w-full bg-gray-50 dark:bg-gray-700 border-none dark:text-white text-sm rounded-2xl p-4 font-bold appearance-none">
                            <option value="" data-qtd="0">Selecione a Categoria...</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->cto_id }}" data-qtd="{{ $cat->qtd_equipes ?? 0 }}">
                                    {{ $cat->cto_nome }} ({{ $cat->qtd_equipes ?? 0 }} eqp)
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl shadow-lg active:scale-95 transition-all text-xs uppercase tracking-widest">
                            Gerar Jogos Agora
                        </button>
                    </div>
                </form>
            </div>
        </details>

        <details class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <summary class="flex items-center justify-between p-5 cursor-pointer list-none">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <span class="font-black text-xs uppercase tracking-widest text-gray-700 dark:text-white">Adicionar Equipe</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="p-5 pt-0 border-t border-gray-50 dark:border-gray-700">
                <form action="{{ route('agendamentos.adicionar-equipe', $cmp->cpo_id) }}" method="POST" class="space-y-4 pt-4">
                    @csrf
                    <select name="eqp_cpo_id" required class="w-full bg-gray-50 dark:bg-gray-700 border-none dark:text-white text-sm rounded-2xl p-4 font-bold appearance-none">
                        <option value="">Equipe tardia...</option>
                        @foreach($equipesSemJogos as $ei)
                            <option value="{{ $ei->eqp_cpo_id }}">
                                {{ ($ei->equipe->time->tim_nome ?? 'N/A') . ' - ' . ($ei->equipe->categoria->cto_nome ?? 'N/A') }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="grupo_nome" class="w-full bg-gray-50 dark:bg-gray-700 border-none dark:text-white text-sm rounded-2xl p-4 font-bold" placeholder="Grupo (se >= 16)">
                    <button type="submit" class="w-full bg-purple-600 text-white font-black py-4 rounded-2xl shadow-lg active:scale-95 transition-all text-xs uppercase tracking-widest">
                        Adicionar à Tabela
                    </button>
                </form>
            </div>
        </details>
    </div>

    {{-- Filtros --}}
    <div class="mb-6 px-1 flex gap-2">
        <button onclick="toggleFilters()" class="flex-grow bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                 <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                 <span class="text-xs font-black text-gray-700 dark:text-white uppercase tracking-widest">Filtrar Jogos</span>
            </div>
            @if(request()->anyFilled(['categoria', 'fase', 'status', 'equipe_id']))
                <span class="w-2 h-2 rounded-full bg-blue-600 shadow-blue-500/50 shadow-sm animate-pulse"></span>
            @endif
        </button>
        <button onclick="toggleBulk()" class="bg-gray-900 text-white p-4 rounded-2xl shadow-lg flex items-center gap-2">
             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
             <span class="text-[10px] font-black uppercase">Lote</span>
        </button>
    </div>

    {{-- Modal de Filtros (Overlay Simples) --}}
    <div id="filter-overlay" class="hidden fixed inset-0 z-[60] bg-black/60 backdrop-blur-sm p-6 flex flex-col justify-end transition-opacity duration-300 opacity-0">
        <div class="bg-white dark:bg-gray-900 rounded-[3rem] p-8 space-y-6 shadow-2xl translate-y-10 transition-transform duration-300" id="filter-content">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-xl font-black text-gray-900 dark:text-white">Filtros</h3>
                <button onclick="toggleFilters()" class="text-gray-400 active:rotate-90 transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="GET" action="{{ route('agendamentos.admin.index', $cmp->cpo_id) }}" class="space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Categoria</label>
                    <select name="categoria" class="w-full bg-gray-50 dark:bg-gray-800 border-none rounded-2xl p-4 font-bold dark:text-white appearance-none">
                        <option value="">Todas</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->cto_id }}" {{ request('categoria') == $cat->cto_id ? 'selected' : '' }}>{{ $cat->cto_nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Fase</label>
                    <select name="fase" class="w-full bg-gray-50 dark:bg-gray-800 border-none rounded-2xl p-4 font-bold dark:text-white appearance-none">
                        <option value="">Todas</option>
                        @foreach($fases as $fase)
                            <option value="{{ $fase }}" {{ request('fase') == $fase ? 'selected' : '' }}>{{ $fase }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Status</label>
                    <select name="status" class="w-full bg-gray-50 dark:bg-gray-800 border-none rounded-2xl p-4 font-bold dark:text-white appearance-none">
                        <option value="">Todos</option>
                        <option value="pendente_preenchimento" {{ request('status') == 'pendente_preenchimento' ? 'selected' : '' }}>Pendente</option>
                        <option value="pendente_aprovacao" {{ request('status') == 'pendente_aprovacao' ? 'selected' : '' }}>Aguardando</option>
                        <option value="aprovado" {{ request('status') == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-4">
                    <a href="{{ route('agendamentos.admin.index', $cmp->cpo_id) }}" class="flex-1 bg-gray-100 dark:bg-gray-800 text-gray-500 font-bold py-4 rounded-2xl text-center">Limpar</a>
                    <button type="submit" class="flex-2 bg-blue-600 text-white font-black py-4 px-10 rounded-2xl shadow-lg">Ver Resultados</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Listagem de Jogos --}}
    <div class="px-1 space-y-4" id="jogos-container">
        @foreach($jogos as $jogo)
            @php
                $solAlteracao = $jogo->solicitacoesAlteracao ? current(array_filter($jogo->solicitacoesAlteracao->all(), fn($s) => $s->status == 'pendente')) : null;
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
                {{-- Checkbox de Lote (Apenas visível se bulk ativo) --}}
                <div class="absolute top-6 left-6 bulk-select hidden">
                    <input type="checkbox" value="{{ $jogo->jgo_id }}" class="jogo-checkbox-mobile w-6 h-6 rounded-lg text-blue-600 border-gray-200">
                </div>

                {{-- Card Header --}}
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400 px-2 py-1 rounded-full uppercase tracking-widest">
                            #{{ $jogo->jgo_numero_jogo ?: 'TBD' }}
                        </span>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $jogo->jgo_fase }}</span>
                    </div>
                    @if($jogo->jgo_status_agendamento == 'pendente_preenchimento')
                        <span class="w-3 h-3 rounded-full bg-gray-300"></span>
                    @elseif($jogo->jgo_status_agendamento == 'pendente_aprovacao')
                        <span class="w-3 h-3 rounded-full bg-orange-500 shadow-orange-500/50 shadow-lg animate-pulse"></span>
                    @elseif($jogo->jgo_status_agendamento == 'aprovado')
                        <span class="w-3 h-3 rounded-full bg-green-500 shadow-green-500/50 shadow-lg"></span>
                    @endif
                </div>

                {{-- Teams Comparison --}}
                <div class="flex items-center justify-between mb-6 gap-2">
                    <div class="flex-1 text-center">
                        <p class="text-xs font-black text-gray-900 dark:text-white leading-tight mb-1">{{ $jogo->mandante->equipe->eqp_nome_detalhado }}</p>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Mandante</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-lg font-black italic text-gray-300 dark:text-gray-700">VS</span>
                    </div>
                    <div class="flex-1 text-center">
                        <p class="text-xs font-black text-gray-900 dark:text-white leading-tight mb-1">{{ $jogo->visitante->equipe->eqp_nome_detalhado }}</p>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Visitante</span>
                    </div>
                </div>

                {{-- Data/Local --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 mb-6 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 flex items-center justify-center text-blue-600 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        @if($jogo->jgo_dt_jogo)
                            <p class="text-xs font-black text-gray-900 dark:text-white leading-none mb-1">
                                {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }} às {{ $jogo->jgo_hora_jogo }}
                            </p>
                            <p class="text-[10px] font-bold text-gray-400 truncate max-w-[180px]">{{ $jogo->ginasio->gin_nome ?? 'Sem local definido' }}</p>
                        @else
                            <p class="text-xs font-bold text-gray-400 italic">Pendente preenchimento</p>
                        @endif
                    </div>
                </div>

                @if($solAlteracao)
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800 rounded-2xl p-4 mb-6">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">Alteração Solicitada</span>
                        </div>
                        <p class="text-[10px] text-orange-800 dark:text-orange-300 font-medium italic">"{{ $solAlteracao->motivo }}"</p>
                    </div>
                @endif

                {{-- Action Menu --}}
                <div class="flex gap-2">
                    @if($jogo->jgo_status_agendamento == 'pendente_aprovacao')
                        <form action="{{ route('agendamentos.aprovar', $jogo->jgo_id) }}" method="POST" class="flex-1">
                            @csrf
                            <button class="w-full bg-green-600 text-white text-[10px] font-black py-3 rounded-xl shadow-lg active:scale-95 transition-all">APROVAR</button>
                        </form>
                        <form action="{{ route('agendamentos.desbloquear', $jogo->jgo_id) }}" method="POST" class="flex-1">
                            @csrf
                            <button class="w-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-[10px] font-black py-3 rounded-xl active:scale-95 transition-all">CORRIGIR</button>
                        </form>
                    @endif

                    @if($jogo->jgo_status_agendamento == 'aprovado')
                        <a href="{{ route('jogos.edit', $jogo->jgo_wp_id ?: $jogo->jgo_id) }}" class="flex-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-black py-3 px-2 rounded-xl text-center active:scale-95 transition-all">EDITAR</a>
                    @endif

                    <form action="{{ route('agendamentos.deletar', $jogo->jgo_id) }}" method="POST" class="w-12 h-12" onsubmit="return confirm('Excluir este jogo definitivamente?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full h-full bg-gray-50 dark:bg-gray-900/50 rounded-xl flex items-center justify-center text-gray-300 dark:text-gray-600 active:scale-95 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Paginação --}}
    <div class="mt-8 px-1">
        {{ $jogos->links('vendor.pagination.mobile') }}
    </div>
</div>

{{-- Bulk Actions Bottom Sheet --}}
<div id="bulk-sheet" class="fixed bottom-0 left-0 right-0 z-[70] bg-gray-900 rounded-t-[3rem] p-8 shadow-2xl transition-transform duration-300 translate-y-full">
    <div class="w-12 h-1.5 bg-gray-700 rounded-full mx-auto mb-6"></div>
    <div class="flex justify-between items-center mb-6">
         <h3 class="text-white text-xl font-black">Ação em Lote</h3>
         <span id="selected-count" class="bg-blue-600 text-white text-[10px] font-black px-2 py-1 rounded-full">0 selecionados</span>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <button onclick="executeBulkMobile('aprovar')" class="bg-green-600 text-white font-black py-5 rounded-2xl flex flex-col items-center gap-2 active:scale-95 transition">
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
             <span class="text-[10px] uppercase tracking-widest">Aprovar</span>
        </button>
        <button onclick="executeBulkMobile('deletar')" class="bg-red-600 text-white font-black py-5 rounded-2xl flex flex-col items-center gap-2 active:scale-95 transition">
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
             <span class="text-[10px] uppercase tracking-widest">Deletar</span>
        </button>
    </div>
    <button onclick="toggleBulk()" class="w-full text-gray-500 font-bold py-6">Cancelar</button>
</div>

<form action="{{ route('agendamentos.deletarMassa') }}" method="POST" id="form-deletar-massa-mobile" class="hidden">@csrf<input type="hidden" name="jogos_ids" id="jogos_ids_input_del_mobile"></form>
<form action="{{ route('agendamentos.aprovarMassa') }}" method="POST" id="form-aprovar-massa-mobile" class="hidden">@csrf<input type="hidden" name="jogos_ids" id="jogos_ids_input_apv_mobile"></form>

<script>
    function toggleFilters() {
        const overlay = document.getElementById('filter-overlay');
        const content = document.getElementById('filter-content');
        if (overlay.classList.contains('hidden')) {
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.add('opacity-100');
                content.classList.remove('translate-y-10');
            }, 10);
        } else {
            overlay.classList.remove('opacity-100');
            content.classList.add('translate-y-10');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }

    function toggleBulk() {
        const sheet = document.getElementById('bulk-sheet');
        const checkboxes = document.querySelectorAll('.bulk-select');
        const isOpening = sheet.classList.contains('translate-y-full');
        
        if (isOpening) {
            sheet.classList.remove('translate-y-full');
            checkboxes.forEach(c => c.classList.remove('hidden'));
            document.body.style.overflow = 'hidden';
            updateCount();
        } else {
            sheet.classList.add('translate-y-full');
            checkboxes.forEach(c => c.classList.add('hidden'));
            document.body.style.overflow = '';
        }
    }

    function updateCount() {
        const count = document.querySelectorAll('.jogo-checkbox-mobile:checked').length;
        document.getElementById('selected-count').innerText = count + ' selecionados';
    }

    document.querySelectorAll('.jogo-checkbox-mobile').forEach(c => {
        c.addEventListener('change', updateCount);
    });

    function executeBulkMobile(action) {
        let selectedIds = [];
        document.querySelectorAll('.jogo-checkbox-mobile:checked').forEach(cb => selectedIds.push(cb.value));

        if (selectedIds.length === 0) {
            alert('Nenhum agendamento selecionado.');
            return;
        }

        if (action === 'aprovar') {
            if (confirm('Aprovar TODOS os ' + selectedIds.length + ' agendamentos selecionados?')) {
                document.getElementById('jogos_ids_input_apv_mobile').value = selectedIds.join(',');
                document.getElementById('form-aprovar-massa-mobile').submit();
            }
        } else {
             if (confirm('ATENÇÃO: Deseja realmente excluir TODOS os ' + selectedIds.length + ' agendamentos?')) {
                document.getElementById('jogos_ids_input_del_mobile').value = selectedIds.join(',');
                document.getElementById('form-deletar-massa-mobile').submit();
            }
        }
    }

    function handleGerarSubmitMobile() {
        const cat_id = document.getElementById('categoria_id_mobile').value;
        if (!cat_id) { alert('Selecione uma categoria.'); return false; }
        const option = document.querySelector('#categoria_id_mobile option:checked');
        const qtd = parseInt(option.getAttribute('data-qtd') || 0);
        const form = document.getElementById('form-gerar-mobile');
        form.action = form.action.replace('__CAT__', cat_id);
        
        if (qtd >= 16) return confirm('16+ equipes: você irá para a tela de Grupos. Continuar?');
        return confirm('Gerar jogos (Todos x Todos) para esta categoria?');
    }
</script>

<style>
    @keyframes fade-in-down {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down { animation: fade-in-down 0.4s ease-out forwards; }
</style>
@endsection
