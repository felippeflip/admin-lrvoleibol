@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-24">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6 px-1">
        <a href="{{ route('agendamentos.admin.index', $cmp->cpo_id) }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h2 class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight">Definir Grupos</h2>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $cmp->cpo_nome }} · {{ $cat->cto_nome }}</p>
        </div>
    </div>

    {{-- Instruções --}}
    <div class="bg-blue-50 dark:bg-blue-900/10 rounded-3xl p-5 border border-blue-100 dark:border-blue-900/40 mb-8 mx-1">
        <div class="flex gap-3 mb-2">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-[10px] font-bold text-blue-800 dark:text-blue-300 uppercase tracking-widest leading-relaxed">Multi-equipe: Divida as equipes manuamente nos grupos A, B, C...</p>
        </div>
        <p class="text-[9px] text-blue-600/80 dark:text-blue-300/60 font-medium">O sistema irá gerar jogos com Turno A e Turno B para cada grupo formado.</p>
    </div>

    {{-- Form --}}
    <form action="{{ route('agendamentos.gerar', ['campeonato' => $cmp->cpo_id, 'categoria' => $cat->cto_id]) }}" method="POST" class="space-y-6">
        @csrf
        <div class="space-y-4 px-1">
            @foreach($equipes as $pivot)
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col gap-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 dark:text-white leading-tight mb-1">{{ $pivot->equipe->eqp_nome_detalhado }}</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $pivot->equipe->time->tim_nome ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-50 dark:border-gray-700">
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Vincular Equipe ao:</label>
                    <div class="relative">
                        <select name="grupos[{{ $pivot->eqp_cpo_id }}]" required 
                                class="w-full bg-gray-50 dark:bg-gray-900 border-none dark:text-white text-sm rounded-2xl p-4 font-bold appearance-none focus:ring-4 focus:ring-blue-500/10">
                            <option value="">Selecione o Grupo...</option>
                            @foreach(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $letter)
                                <option value="Grupo {{ $letter }}">Grupo {{ $letter }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Ações fixas no rodapé --}}
        <div class="fixed bottom-6 left-6 right-6 z-50">
            <button type="submit" class="w-full bg-blue-600 text-white font-extrabold py-5 rounded-2xl shadow-2xl active:scale-[0.98] transition-all flex items-center justify-center gap-3 border-t border-white/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                GERAR AGENDAMENTOS
            </button>
        </div>
    </form>
</div>
@endsection
