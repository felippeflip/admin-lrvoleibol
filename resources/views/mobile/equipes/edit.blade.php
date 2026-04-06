@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-20">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6 px-1">
        <a href="{{ route('equipes.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-90 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">Editar Equipe</h2>
    </div>

    {{-- Erros --}}
    @if ($errors->any())
        <div class="mb-5 mx-1 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
            <ul class="text-xs text-red-700 dark:text-red-400 list-disc list-inside space-y-1 font-bold">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('equipes.update', $equipe->eqp_id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Seção: Vínculo --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
            <div>
                <label for="eqp_time_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Time Responsável *</label>
                <select name="eqp_time_id" id="eqp_time_id" required 
                        class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-100 dark:border-gray-600 dark:text-white text-sm rounded-2xl p-4 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold">
                    @foreach ($times as $time)
                        <option value="{{ $time->tim_id }}" {{ old('eqp_time_id', $equipe->eqp_time_id) == $time->tim_id ? 'selected' : '' }}>
                            {{ $time->tim_nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="eqp_categoria_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Categoria *</label>
                <select name="eqp_categoria_id" id="eqp_categoria_id" required 
                        class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-100 dark:border-gray-600 dark:text-white text-sm rounded-2xl p-4 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-bold">
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->cto_id }}" {{ old('eqp_categoria_id', $equipe->eqp_categoria_id) == $categoria->cto_id ? 'selected' : '' }}>
                            {{ $categoria->cto_nome }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Seção: Detalhes --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
            <div>
                <label for="eqp_nome_detalhado" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nome Detalhado da Equipe *</label>
                <input type="text" name="eqp_nome_detalhado" id="eqp_nome_detalhado" value="{{ old('eqp_nome_detalhado', $equipe->eqp_nome_detalhado) }}" required 
                       class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-100 dark:border-gray-600 dark:text-white text-sm rounded-2xl p-4 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-bold placeholder-gray-300"
                       placeholder="Ex: LRV Masculino Sub-17 A">
            </div>

            <div>
                <label for="eqp_nome_treinador" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Técnico Principal</label>
                <select name="eqp_nome_treinador" id="eqp_nome_treinador" 
                        class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-100 dark:border-gray-600 dark:text-white text-sm rounded-2xl p-4 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-bold">
                    <option value="">Selecionar Técnico</option>
                    @foreach ($tecnicos as $tecnico)
                        <option value="{{ $tecnico->nome }}" {{ old('eqp_nome_treinador', $equipe->eqp_nome_treinador) == $tecnico->nome ? 'selected' : '' }}>
                            {{ $tecnico->nome }}
                            @if(auth()->user()->hasRole('Administrador') && $tecnico->time) ({{ $tecnico->time->tim_nome }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Ações --}}
        <div class="flex flex-col gap-3 pt-4 px-1">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-4 rounded-2xl shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Salvar Alterações
            </button>
            <a href="{{ route('equipes.index') }}" class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 font-bold py-4 rounded-2xl text-center active:scale-[0.98] transition-all">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
