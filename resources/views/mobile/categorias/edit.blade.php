@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-20">
    {{-- Header com botão Voltar --}}
    <div class="flex items-center gap-4 mb-6 px-1">
        <a href="{{ route('categorias.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-90 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">Editar Categoria</h2>
    </div>

    {{-- Erros de Validação --}}
    @if ($errors->any())
        <div class="mb-5 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg shadow-sm animate-fade-in-down mx-1">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <span class="text-sm font-bold text-red-800 dark:text-red-300">Verifique os campos:</span>
            </div>
            <ul class="text-xs text-red-700 dark:text-red-400 list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categorias.update', $categoria->cto_id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Seção: Informações Básicas --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
            <div>
                <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nome da Categoria *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $categoria->cto_nome) }}" required 
                       class="w-full bg-gray-50 border border-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm rounded-2xl p-4 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-medium placeholder-gray-300"
                       placeholder="Ex: Sub-17 Feminino">
            </div>

            <div>
                <label for="slug" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Slug (Link Amigável) *</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $categoria->cto_slug) }}" required 
                       class="w-full bg-gray-50 border border-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm rounded-2xl p-4 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-medium placeholder-gray-300"
                       placeholder="Ex: sub-17-ferminino">
            </div>

            <div class="pt-2">
                <label for="cto_idade_maxima" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Idade Máxima Permitida</label>
                <div class="flex items-center gap-3">
                    <input type="number" name="cto_idade_maxima" id="cto_idade_maxima" value="{{ old('cto_idade_maxima', $categoria->cto_idade_maxima) }}"
                           class="w-32 bg-gray-50 border border-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-lg font-black rounded-2xl p-4 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all text-center"
                           placeholder="0">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">anos</span>
                </div>
                <p class="text-[9px] font-medium text-gray-400 mt-2 ml-1">* Deixe em branco se não houver limite.</p>
            </div>
        </div>

        {{-- Seção: Descrição --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-2">
            <label for="description" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Descrição Opcional</label>
            <textarea name="description" id="description" rows="4" 
                      class="w-full bg-gray-50 border border-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm rounded-2xl p-4 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-medium placeholder-gray-300"
                      placeholder="Alguma observação sobre esta categoria?">{{ old('description', $categoria->cto_descricao) }}</textarea>
        </div>

        {{-- Ações --}}
        <div class="flex flex-col gap-3 pt-4 px-1">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-4 rounded-2xl shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Salvar Alterações
            </button>
            <a href="{{ route('categorias.index') }}" class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold py-4 rounded-2xl text-center active:scale-[0.98] transition-all shadow-sm">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
