@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-20">
    {{-- Header com botão Voltar --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('jogos.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-90 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">Novo Jogo</h2>
    </div>

    {{-- Erros de Validação --}}
    @if ($errors->any())
        <div class="mb-5 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fade-in-down">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <span class="text-sm font-bold text-red-800 dark:text-red-300">Verifique os campos abaixo:</span>
            </div>
            <ul class="text-xs text-red-700 dark:text-red-400 list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="game-create-form" action="{{ route('jogos.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Seção: Identificação --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 space-y-4">
            <h3 class="text-xs font-black text-orange-500 uppercase tracking-widest flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                Identificação
            </h3>

            <div>
                <label for="event_number" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Número do Jogo *</label>
                <input type="number" name="event_number" id="event_number" value="{{ old('event_number') }}" required 
                       class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white placeholder-gray-400" 
                       placeholder="Ex: 101">
            </div>

            <div>
                <label for="campeonato_id" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Campeonato *</label>
                <select name="campeonato_id" id="campeonato_id" required
                        class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white appearance-none">
                    <option value="">Selecione o Campeonato</option>
                    @foreach ($campeonatos as $camp)
                        <option value="{{ $camp->cpo_id }}" {{ old('campeonato_id') == $camp->cpo_id ? 'selected' : '' }}>
                            {{ $camp->cpo_nome }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Seção: Fase e Grupo --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 space-y-4">
            <h3 class="text-xs font-black text-orange-500 uppercase tracking-widest flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Fase do Torneio
            </h3>

            <div>
                <label for="jgo_fase_tipo" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Tipo de Fase *</label>
                <select name="jgo_fase_tipo" id="jgo_fase_tipo" required
                        class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="classificatoria" {{ old('jgo_fase_tipo') == 'classificatoria' ? 'selected' : '' }}>Fase Classificatória / Grupos</option>
                    <option value="quartas_de_final" {{ old('jgo_fase_tipo') == 'quartas_de_final' ? 'selected' : '' }}>Quartas de Final</option>
                    <option value="semi_final_ouro" {{ old('jgo_fase_tipo') == 'semi_final_ouro' ? 'selected' : '' }}>Semi-Final (Ouro)</option>
                    <option value="semi_final_prata" {{ old('jgo_fase_tipo') == 'semi_final_prata' ? 'selected' : '' }}>Semi-Final (Prata)</option>
                    <option value="semi_final_bronze" {{ old('jgo_fase_tipo') == 'semi_final_bronze' ? 'selected' : '' }}>Semi-Final (Bronze)</option>
                    <option value="final_ouro" {{ old('jgo_fase_tipo') == 'final_ouro' ? 'selected' : '' }}>Final (Série Ouro)</option>
                    <option value="final_prata" {{ old('jgo_fase_tipo') == 'final_prata' ? 'selected' : '' }}>Final (Série Prata)</option>
                    <option value="final_bronze" {{ old('jgo_fase_tipo') == 'final_bronze' ? 'selected' : '' }}>Final (Série Bronze)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="grupo" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Grupo</label>
                    <input type="text" name="grupo" id="grupo" value="{{ old('grupo') }}" 
                           class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Ex: A">
                </div>
                <div>
                    <label for="turno" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Turno</label>
                    <input type="text" name="turno" id="turno" value="{{ old('turno') }}" 
                           class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Ex: 1º">
                </div>
            </div>
        </div>

        {{-- Seção: Equipes --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 space-y-4">
            <h3 class="text-xs font-black text-orange-500 uppercase tracking-widest flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Equipes
            </h3>

            <div>
                <label for="mandante_id" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Mandante *</label>
                <select name="mandante_id" id="mandante_id" required disabled
                        class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-opacity disabled:opacity-50">
                    <option value="">Selecione primeiro o campeonato</option>
                </select>
            </div>

            <div>
                <label for="visitante_id" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Visitante *</label>
                <select name="visitante_id" id="visitante_id" required disabled
                        class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-opacity disabled:opacity-50">
                    <option value="">Selecione primeiro o campeonato</option>
                </select>
            </div>
        </div>

        {{-- Seção: Categoria, Local e Data --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 space-y-4">
            <h3 class="text-xs font-black text-orange-500 uppercase tracking-widest flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Detalhes da Partida
            </h3>

            <div>
                <label for="categoria_id" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Categoria *</label>
                <select name="categoria_id" id="categoria_id" required
                        class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Selecione a Categoria</option>
                    @foreach ($categorias as $cat)
                        <option value="{{ $cat->cto_id }}" {{ old('categoria_id') == $cat->cto_id ? 'selected' : '' }}>
                            {{ $cat->cto_nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="ginasio_id" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Local (Ginásio) *</label>
                <select name="ginasio_id" id="ginasio_id" required
                        class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Selecione o Local</option>
                    @foreach ($ginasios as $gin)
                        <option value="{{ $gin->gin_id }}" {{ old('ginasio_id') == $gin->gin_id ? 'selected' : '' }}>
                            {{ $gin->gin_nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="data_jogo" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Data *</label>
                    <input type="date" name="data_jogo" id="data_jogo" value="{{ old('data_jogo') }}" required 
                           class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="hora_jogo" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Horário *</label>
                    <input type="time" name="hora_jogo" id="hora_jogo" value="{{ old('hora_jogo') }}" required 
                           class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>
        </div>

        {{-- Seção: Arbitragem --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 space-y-4">
            <h3 class="text-xs font-black text-orange-500 uppercase tracking-widest flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Equipe de Arbitragem
            </h3>

            <div>
                <label for="juiz_principal" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Árbitro Principal</label>
                <select name="juiz_principal" id="juiz_principal" 
                        class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Selecione...</option>
                    @foreach($juizes as $juiz)
                        <option value="{{ $juiz->id }}" {{ old('juiz_principal') == $juiz->id ? 'selected' : '' }}>
                            {{ $juiz->name }} ({{$juiz->apelido}})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="juiz_linha1" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Árbitro Secundário</label>
                <select name="juiz_linha1" id="juiz_linha1" 
                        class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Selecione...</option>
                    @foreach($juizes as $juiz)
                        <option value="{{ $juiz->id }}" {{ old('juiz_linha1') == $juiz->id ? 'selected' : '' }}>
                            {{ $juiz->name }} ({{$juiz->apelido}})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="juiz_linha2" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-tight">Apontador</label>
                <select name="juiz_linha2" id="juiz_linha2" 
                        class="w-full bg-gray-50 border border-gray-300 text-sm rounded-xl p-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Selecione...</option>
                    @foreach($juizes as $juiz)
                        <option value="{{ $juiz->id }}" {{ old('juiz_linha2') == $juiz->id ? 'selected' : '' }}>
                            {{ $juiz->name }} ({{$juiz->apelido}})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Ações --}}
        <div class="flex flex-col gap-3 pt-4">
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-extrabold py-4 rounded-2xl shadow-lg active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Criar Jogo
            </button>
            <a href="{{ route('jogos.index') }}" class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold py-4 rounded-2xl text-center active:scale-[0.98] transition-all">
                Cancelar
            </a>
        </div>
    </form>
</div>

{{-- Script para carregar equipes dinamicamente --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const campeonatoSelect = document.getElementById('campeonato_id');
        const mandanteSelect = document.getElementById('mandante_id');
        const visitanteSelect = document.getElementById('visitante_id');

        function loadEquipes(campeonatoId) {
            // Limpa selects e mostra estado de carregamento
            mandanteSelect.innerHTML = '<option value="">Carregando equipes...</option>';
            visitanteSelect.innerHTML = '<option value="">Carregando equipes...</option>';
            mandanteSelect.disabled = true;
            visitanteSelect.disabled = true;

            if (campeonatoId) {
                fetch(`/api/campeonatos/${campeonatoId}/equipes`)
                    .then(response => response.json())
                    .then(data => {
                        let options = '<option value="">Selecione a equipe</option>';
                        data.forEach(equipe => {
                            options += `<option value="${equipe.id}">${equipe.nome}</option>`;
                        });

                        mandanteSelect.innerHTML = options;
                        visitanteSelect.innerHTML = options;
                        mandanteSelect.disabled = false;
                        visitanteSelect.disabled = false;

                        // Se houver valor antigo do Laravel (old), tenta selecionar
                        @if(old('mandante_id'))
                            mandanteSelect.value = "{{ old('mandante_id') }}";
                        @endif
                        @if(old('visitante_id'))
                            visitanteSelect.value = "{{ old('visitante_id') }}";
                        @endif
                    })
                    .catch(error => {
                        console.error('Erro ao buscar equipes:', error);
                        mandanteSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                        visitanteSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                    });
            } else {
                mandanteSelect.innerHTML = '<option value="">Selecione primeiro o campeonato</option>';
                visitanteSelect.innerHTML = '<option value="">Selecione primeiro o campeonato</option>';
            }
        }

        campeonatoSelect.addEventListener('change', function() {
            loadEquipes(this.value);
        });

        // Se já tiver campeonato selecionado (ex: erro de validação), carrega as equipes
        if (campeonatoSelect.value) {
            loadEquipes(campeonatoSelect.value);
        }
    });
</script>
@endsection
