@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-24" x-data="{ 
    sets: @js(range(1, 5)).map(num => ({
        num: num,
        mandante: {{ old('sets.1.mandante') ?? (isset($sets[1]) ? $sets[1]->set_pontos_mandante : 'null') }},
        visitante: {{ old('sets.1.visitante') ?? (isset($sets[1]) ? $sets[1]->set_pontos_visitante : 'null') }}
    })) 
}">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8 px-1">
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-90 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Súmula Eletrônica</h2>
    </div>

    {{-- Scoreboard View --}}
    <div class="bg-gray-900 rounded-[2.5rem] p-6 text-white mb-8 shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-transparent"></div>
        
        <div class="flex justify-between items-center relative z-10">
            <div class="flex-1 text-center">
                <div class="w-16 h-16 bg-white/10 rounded-2xl mx-auto mb-3 flex items-center justify-center">
                    <span class="text-2xl font-black text-white/40 uppercase">{{ substr($mandante, 0, 1) }}</span>
                </div>
                <h3 class="text-[10px] font-black uppercase tracking-widest text-indigo-300 leading-tight">{{ explode(' ', $mandante)[0] }}</h3>
            </div>
            
            <div class="px-4 text-center">
                <span class="text-3xl font-black italic text-orange-500 opacity-50">VS</span>
            </div>

            <div class="flex-1 text-center">
                 <div class="w-16 h-16 bg-white/10 rounded-2xl mx-auto mb-3 flex items-center justify-center">
                    <span class="text-2xl font-black text-white/40 uppercase">{{ substr($visitante, 0, 1) }}</span>
                </div>
                <h3 class="text-[10px] font-black uppercase tracking-widest text-orange-300 leading-tight">{{ explode(' ', $visitante)[0] }}</h3>
            </div>
        </div>
    </div>

    @if ($errors->any() || session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-2 border-red-100 dark:border-red-900/30 p-4 rounded-3xl animate-shake">
             <p class="text-xs font-black text-red-600 uppercase tracking-widest mb-1">Atenção no Preenchimento</p>
             <p class="text-[11px] font-bold text-red-500 leading-tight">
                {{ session('error') ?? $errors->first() }}
             </p>
        </div>
    @endif

    <form action="{{ route('resultados.store', $jogo->jgo_id) }}" method="POST">
        @csrf
        <div class="space-y-4">
            @for ($i = 1; $i <= 5; $i++)
                @php
                    $mVal = old("sets.$i.mandante", isset($sets[$i]) ? $sets[$i]->set_pontos_mandante : '');
                    $vVal = old("sets.$i.visitante", isset($sets[$i]) ? $sets[$i]->set_pontos_visitante : '');
                    $isLast = ($i == 5);
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] p-5 shadow-sm border border-gray-50 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            {{ $isLast ? 'Tie Break' : 'Set ' . $i }}
                        </span>
                        <div class="h-px bg-gray-100 dark:bg-gray-700 flex-1 mx-4"></div>
                        <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">
                            {{ $isLast ? '15 pts' : '25 pts' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Mandante Input --}}
                        <div class="flex-1 flex flex-col gap-2">
                             <input type="number" 
                                   name="sets[{{ $i }}][mandante]" 
                                   id="set_{{ $i }}_m"
                                   value="{{ $mVal }}"
                                   placeholder="00"
                                   class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-6 text-center text-3xl font-black text-gray-900 dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                             <div class="flex gap-1">
                                <button type="button" @click="let el = document.getElementById('set_{{ $i }}_m'); el.value = Math.max(0, parseInt(el.value || 0) + 1)" class="flex-1 h-10 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 rounded-xl flex items-center justify-center active:scale-95 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                                <button type="button" @click="let el = document.getElementById('set_{{ $i }}_m'); el.value = Math.max(0, parseInt(el.value || 0) - 1)" class="flex-1 h-10 bg-gray-50 dark:bg-gray-700 text-gray-400 rounded-xl flex items-center justify-center active:scale-95 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                                </button>
                             </div>
                        </div>

                        <div class="text-gray-300 font-black italic">X</div>

                        {{-- Visitante Input --}}
                        <div class="flex-1 flex flex-col gap-2">
                             <input type="number" 
                                   name="sets[{{ $i }}][visitante]" 
                                   id="set_{{ $i }}_v"
                                   value="{{ $vVal }}"
                                   placeholder="00"
                                   class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-6 text-center text-3xl font-black text-gray-900 dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-orange-500/10 transition-all appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                             <div class="flex gap-1">
                                <button type="button" @click="let el = document.getElementById('set_{{ $i }}_v'); el.value = Math.max(0, parseInt(el.value || 0) + 1)" class="flex-1 h-10 bg-orange-50 dark:bg-orange-900/20 text-orange-600 rounded-xl flex items-center justify-center active:scale-95 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                                <button type="button" @click="let el = document.getElementById('set_{{ $i }}_v'); el.value = Math.max(0, parseInt(el.value || 0) - 1)" class="flex-1 h-10 bg-gray-50 dark:bg-gray-700 text-gray-400 rounded-xl flex items-center justify-center active:scale-95 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                                </button>
                             </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        @if($isPlayoff)
            @php
                $mValGS = old("sets.6.mandante", isset($sets[6]) ? $sets[6]->set_pontos_mandante : '');
                $vValGS = old("sets.6.visitante", isset($sets[6]) ? $sets[6]->set_pontos_visitante : '');
                $activeGS = old('tem_golden_set', $hasGoldenSet);
            @endphp
            <div x-data="{ goldenAtivo: {{ $activeGS ? 'true' : 'false' }} }" class="bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-950/30 dark:to-yellow-950/20 rounded-[2rem] p-5 shadow-sm border-2 border-amber-300 dark:border-amber-700/60 mt-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🏆</span>
                        <span class="text-[11px] font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider">
                            Golden Set
                        </span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="tem_golden_set" value="1" x-model="goldenAtivo" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                    </label>
                </div>
                <p class="text-[10px] text-amber-700 dark:text-amber-400 font-medium mb-4">
                    Desempate em caso de 1x1 na série eliminatória. Alvo: <strong>25 pts (diferença de 2)</strong>.
                </p>

                <div x-show="goldenAtivo" x-cloak class="space-y-3 pt-2 border-t border-amber-200 dark:border-amber-800/40">
                    <div class="flex items-center gap-4">
                        {{-- Mandante Input --}}
                        <div class="flex-1 flex flex-col gap-2">
                            <span class="text-[10px] font-black text-amber-900 dark:text-amber-300 text-center uppercase truncate">
                                {{ explode(' ', $mandante)[0] }}
                            </span>
                            <input type="number" 
                                   name="sets[6][mandante]" 
                                   id="set_6_m"
                                   value="{{ $mValGS }}"
                                   placeholder="25"
                                   class="w-full bg-white dark:bg-gray-900 border-2 border-amber-300 dark:border-amber-600 rounded-2xl py-5 text-center text-3xl font-black text-amber-900 dark:text-amber-300 placeholder-amber-200 focus:ring-4 focus:ring-amber-500/20 transition-all appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <div class="flex gap-1">
                                <button type="button" @click="let el = document.getElementById('set_6_m'); el.value = Math.max(0, parseInt(el.value || 0) + 1)" class="flex-1 h-9 bg-amber-200/60 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 rounded-xl flex items-center justify-center active:scale-95 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                                <button type="button" @click="let el = document.getElementById('set_6_m'); el.value = Math.max(0, parseInt(el.value || 0) - 1)" class="flex-1 h-9 bg-white/80 dark:bg-gray-700 text-gray-500 rounded-xl flex items-center justify-center active:scale-95 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div class="text-amber-500 font-black italic">X</div>

                        {{-- Visitante Input --}}
                        <div class="flex-1 flex flex-col gap-2">
                            <span class="text-[10px] font-black text-amber-900 dark:text-amber-300 text-center uppercase truncate">
                                {{ explode(' ', $visitante)[0] }}
                            </span>
                            <input type="number" 
                                   name="sets[6][visitante]" 
                                   id="set_6_v"
                                   value="{{ $vValGS }}"
                                   placeholder="23"
                                   class="w-full bg-white dark:bg-gray-900 border-2 border-amber-300 dark:border-amber-600 rounded-2xl py-5 text-center text-3xl font-black text-amber-900 dark:text-amber-300 placeholder-amber-200 focus:ring-4 focus:ring-amber-500/20 transition-all appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <div class="flex gap-1">
                                <button type="button" @click="let el = document.getElementById('set_6_v'); el.value = Math.max(0, parseInt(el.value || 0) + 1)" class="flex-1 h-9 bg-amber-200/60 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 rounded-xl flex items-center justify-center active:scale-95 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                                <button type="button" @click="let el = document.getElementById('set_6_v'); el.value = Math.max(0, parseInt(el.value || 0) - 1)" class="flex-1 h-9 bg-white/80 dark:bg-gray-700 text-gray-500 rounded-xl flex items-center justify-center active:scale-95 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-12 space-y-4 px-1">
            <button type="submit" class="w-full bg-indigo-600 text-white font-black py-6 rounded-[2rem] shadow-xl shadow-indigo-100 dark:shadow-none active:scale-95 transition-all text-sm uppercase tracking-widest">
                Finalizar e Enviar Súmula
            </button>
            <a href="{{ route('dashboard') }}" class="block w-full text-center text-gray-400 font-bold text-xs underline italic">Cancelar preenchimento</a>
        </div>
    </form>
</div>

<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }
    .animate-shake { animation: shake 0.3s ease-in-out; }
</style>
@endsection
