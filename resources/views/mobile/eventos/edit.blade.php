@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-lg mx-auto pb-24">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6 px-1">
        <a href="{{ route('eventos.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-95 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Editar Evento</h2>
    </div>

    {{-- Erros --}}
    @if ($errors->any())
        <div class="mb-5 mx-1 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
            <ul class="text-xs text-red-700 dark:text-red-400 list-disc list-inside space-y-1 font-bold">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('eventos.update', $campeonato->cpo_id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Status do Evento (Card Flutuante) --}}
        <div class="mx-1 mb-6 flex items-center justify-between bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Status Atual</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white" id="status-label">
                    {{ $campeonato->cpo_ativo ? 'Ativo e Visível' : 'Inativo / Arquivado' }}
                </p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="cpo_ativo" value="1" {{ $campeonato->cpo_ativo ? 'checked' : '' }} class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
            </label>
        </div>

        {{-- Identificação --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
            <div class="flex items-center gap-2 mb-2 ml-1">
                <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Informações Principais</span>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nome</label>
                <input type="text" name="cpo_nome" id="cpo_nome" value="{{ old('cpo_nome', $campeonato->cpo_nome) }}" required
                       class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white focus:ring-4 focus:ring-orange-500/10 transition-all">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Slug (URL amigável)</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $wpTerm->slug ?? '') }}" required
                       class="w-full bg-gray-100 dark:bg-gray-900 border-none rounded-2xl p-4 font-bold text-xs text-gray-500 dark:text-gray-400 cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Ano</label>
                <select name="cpo_ano" class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white appearance-none">
                    @for ($year = date('Y') + 1; $year >= 2020; $year--)
                        <option value="{{ $year }}" {{ old('cpo_ano', $campeonato->cpo_ano) == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- Período --}}
        <div class="bg-orange-600 rounded-[2.5rem] p-8 text-white shadow-xl space-y-6">
            <h3 class="text-sm font-black uppercase tracking-widest opacity-80 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Período Geral
            </h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-2 ml-1 opacity-60">Início</label>
                    <input type="date" name="cpo_dt_inicio" value="{{ old('cpo_dt_inicio', $campeonato->cpo_dt_inicio) }}" required
                           class="w-full bg-white/10 border-none rounded-2xl p-4 font-bold text-sm text-white focus:bg-white/20 transition-all appearance-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-2 ml-1 opacity-60">Final</label>
                    <input type="date" name="cpo_dt_fim" value="{{ old('cpo_dt_fim', $campeonato->cpo_dt_fim) }}" required
                           class="w-full bg-white/10 border-none rounded-2xl p-4 font-bold text-sm text-white focus:bg-white/20 transition-all appearance-none">
                </div>
            </div>
        </div>

        {{-- Prazos Agendamentos --}}
        <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl space-y-6 relative overflow-hidden">
             <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-orange-500/5 rounded-full blur-3xl"></div>
             
            <h3 class="text-sm font-black uppercase tracking-widest text-orange-500 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4h3m9-4a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Controle de Datas
            </h3>

            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Fase Classificatória (Limite)</label>
                <input type="date" name="cpo_dt_fim_classificacao" value="{{ old('cpo_dt_fim_classificacao', $campeonato->cpo_dt_fim_classificacao) }}"
                       class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 font-bold text-sm text-white focus:border-orange-500/50 transition-all">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Finais / Semi Finais (Limite)</label>
                <input type="date" name="cpo_dt_fim_finais" value="{{ old('cpo_dt_fim_finais', $campeonato->cpo_dt_fim_finais) }}"
                       class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 font-bold text-sm text-white focus:border-orange-500/50 transition-all">
            </div>

            <p class="text-[9px] text-gray-500 uppercase font-black tracking-widest pt-2">* Use estas datas para bloquear sugestões de jogos pelas equipes.</p>
        </div>

        {{-- Botões --}}
        <div class="flex flex-col gap-3 pt-4 px-1">
            <button type="submit" class="w-full bg-white dark:bg-gray-800 border border-orange-500/30 text-orange-600 font-black py-5 rounded-3xl shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                Atualizar Dados
            </button>
            <a href="{{ route('eventos.index') }}" class="w-full text-gray-400 font-bold py-4 text-center">Descartar alterações</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nomeInput = document.getElementById('cpo_nome');
        const slugInput = document.getElementById('slug');

        if (nomeInput && slugInput) {
            nomeInput.addEventListener('input', function() {
                const val = this.value
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/--+/g, '-')
                    .trim();
                slugInput.value = val;
            });
        }
    });
</script>
@endsection
