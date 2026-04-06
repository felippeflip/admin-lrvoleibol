@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-24">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-4 px-1">
        <a href="{{ route('ginasios.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-90 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-none">Editar Ginásio</h2>
    </div>
    
    {{-- Status Toggle Quick Access --}}
    <div class="mb-8 px-1">
        <form action="{{ route('ginasios.toggleStatus', $ginasio->gin_id) }}" method="POST" class="w-full">
            @csrf
            @method('PATCH')
            <button type="submit" class="w-full flex items-center justify-between p-5 rounded-[2rem] border-2 {{ $ginasio->gin_status ? 'bg-green-50/50 border-green-200 text-green-700' : 'bg-red-50/50 border-red-200 text-red-700' }} active:scale-95 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full {{ $ginasio->gin_status ? 'bg-green-500' : 'bg-red-500' }} animate-pulse"></div>
                    <span class="text-xs font-black uppercase tracking-widest leading-none">{{ $ginasio->gin_status ? 'Este Local está Ativo' : 'Este Local está Inativo' }}</span>
                </div>
                <div class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
            </button>
        </form>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-2 border-red-100 dark:border-red-900/30 p-4 rounded-3xl animate-shake">
             <p class="text-xs font-black text-red-600 uppercase tracking-widest mb-1">Erro de Validação</p>
             <p class="text-[11px] font-bold text-red-500 leading-tight">Verifique os campos obrigatórios em vermelho.</p>
        </div>
    @endif

    <form action="{{ route('ginasios.update', $ginasio->gin_id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Seção: Identificação --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-2 h-6 bg-indigo-600 rounded-full"></div>
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Dados Principais</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Nome do Ginásio</label>
                    <input type="text" name="gin_nome" value="{{ old('gin_nome', $ginasio->gin_nome) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Time Responsável</label>
                    <select name="gin_tim_id" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black text-gray-700 dark:text-gray-300 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        <option value="">Selecione um Time (Opcional)</option>
                        @foreach($times as $time)
                            <option value="{{ $time->tim_id }}" {{ old('gin_tim_id', $ginasio->gin_tim_id) == $time->tim_id ? 'selected' : '' }}>{{ $time->tim_nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Seção: Localização --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-2 h-6 bg-indigo-600 rounded-full"></div>
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Endereço Atualizado</h3>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">CEP</label>
                        <input type="text" name="gin_cep" value="{{ old('gin_cep', $ginasio->gin_cep) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                    
                    <div class="col-span-2">
                         <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Rua / Logradouro</label>
                         <input type="text" name="gin_endereco" value="{{ old('gin_endereco', $ginasio->gin_endereco) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Nº</label>
                        <input type="text" name="gin_numero" value="{{ old('gin_numero', $ginasio->gin_numero) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Bairro</label>
                        <input type="text" name="gin_bairro" value="{{ old('gin_bairro', $ginasio->gin_bairro) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div class="col-span-2 grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Cidade</label>
                            <input type="text" name="gin_cidade" value="{{ old('gin_cidade', $ginasio->gin_cidade) }}" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">UF</label>
                            <input type="text" name="gin_estado" value="{{ old('gin_estado', $ginasio->gin_estado) }}" required maxlength="2" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all uppercase">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-8 space-y-4 px-1">
            <button type="submit" class="w-full bg-gray-900 dark:bg-gray-700 text-white font-black py-6 rounded-[2rem] shadow-xl active:scale-95 transition-all text-xs uppercase tracking-widest">
                Salvar Alterações
            </button>
            <a href="{{ route('ginasios.index') }}" class="block w-full text-center text-gray-400 font-bold text-xs underline italic">Cancelar Edição</a>
        </div>
    </form>
</div>
@endsection
