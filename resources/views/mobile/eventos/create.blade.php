@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-lg mx-auto pb-24">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6 px-1">
        <a href="{{ route('eventos.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-95 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Novo Campeonato</h2>
    </div>

    {{-- Erros --}}
    @if ($errors->any())
        <div class="mb-5 mx-1 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
            <ul class="text-xs text-red-700 dark:text-red-400 list-disc list-inside space-y-1 font-bold">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('eventos.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Identificação do Campeonato --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
            <div class="flex items-center gap-2 mb-2 ml-1">
                <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Identificação Básica</span>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nome do Campeonato</label>
                <input type="text" name="cpo_nome" id="cpo_nome" value="{{ old('cpo_nome') }}" required placeholder="Ex: Copa Ouro 2024"
                       class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white focus:ring-4 focus:ring-orange-500/10 transition-all">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Slug (URL amigável)</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required placeholder="copa-ouro-2024"
                       class="w-full bg-gray-100 dark:bg-gray-900 border-none rounded-2xl p-4 font-bold text-xs text-gray-500 dark:text-gray-400 cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Ano da Temporada</label>
                <select name="cpo_ano" required class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white appearance-none">
                    <option value="">Selecione o ano...</option>
                    @for ($year = date('Y') + 1; $year >= 2020; $year--)
                        <option value="{{ $year }}" {{ old('cpo_ano', date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- Calendário do Evento --}}
        <div class="bg-blue-600 rounded-[2.5rem] p-8 text-white shadow-xl space-y-6 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            
            <h3 class="text-sm font-black uppercase tracking-widest opacity-80 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Período de Realização
            </h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-2 ml-1 opacity-60">Data Início</label>
                    <input type="date" name="cpo_dt_inicio" value="{{ old('cpo_dt_inicio') }}" required
                           class="w-full bg-white/10 border-none rounded-2xl p-4 font-bold text-sm text-white focus:bg-white/20 transition-all appearance-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-2 ml-1 opacity-60">Data Término</label>
                    <input type="date" name="cpo_dt_fim" value="{{ old('cpo_dt_fim') }}" required
                           class="w-full bg-white/10 border-none rounded-2xl p-4 font-bold text-sm text-white focus:bg-white/20 transition-all appearance-none">
                </div>
            </div>

            <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                <p class="text-[10px] leading-relaxed font-bold opacity-80">
                    O período define o tempo de exibição e validade dos jogos no sistema para este campeonato.
                </p>
            </div>
        </div>

        {{-- Prazos Administrativos --}}
        <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl space-y-6">
            <h3 class="text-sm font-black uppercase tracking-widest text-orange-500 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4h3m9-4a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Prazos Agendamentos
            </h3>

            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Fase Classificatória (Limite Sugestão)</label>
                <input type="date" name="cpo_dt_fim_classificacao" value="{{ old('cpo_dt_fim_classificacao') }}"
                       class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 font-bold text-sm text-white focus:border-orange-500/50 transition-all">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Finais / Semi Finais (Limite Sugestão)</label>
                <input type="date" name="cpo_dt_fim_finais" value="{{ old('cpo_dt_fim_finais') }}"
                       class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 font-bold text-sm text-white focus:border-orange-500/50 transition-all">
            </div>

            <p class="text-[9px] text-gray-500 uppercase font-black tracking-widest leading-none pt-2">* Data máxima que as equipes podem sugerir horários.</p>
        </div>

        {{-- Botões de Ação --}}
        <div class="flex flex-col gap-3 pt-4 px-1">
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-5 rounded-2xl shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Criar Campeonato
            </button>
            <a href="{{ route('eventos.index') }}" class="w-full text-gray-400 font-bold py-4 text-center">Cancelar e Sair</a>
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

<style>
    @keyframes fade-in-down {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down { animation: fade-in-down 0.4s ease-out forwards; }
</style>
@endsection
