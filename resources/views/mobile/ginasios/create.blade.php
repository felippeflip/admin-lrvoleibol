@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-24">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8 px-1">
        <a href="{{ route('ginasios.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-90 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Novo Ginásio</h2>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-2 border-red-100 dark:border-red-900/30 p-4 rounded-3xl animate-shake">
             <p class="text-xs font-black text-red-600 uppercase tracking-widest mb-1">Erro de Validação</p>
             <p class="text-[11px] font-bold text-red-500 leading-tight">Verifique os campos obrigatórios em vermelho.</p>
        </div>
    @endif

    <form action="{{ route('ginasios.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Seção: Identificação --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-2 h-6 bg-indigo-600 rounded-full"></div>
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Identificação</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Nome do Ginásio</label>
                    <input type="text" name="gin_nome" value="{{ old('gin_nome') }}" required placeholder="Ex: Ginásio Poly Esportivo..." class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    @error('gin_nome') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Time Responsável</label>
                    <select name="gin_tim_id" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black text-gray-700 dark:text-gray-300 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        <option value="">Selecione um Time (Opcional)</option>
                        @foreach($times as $time)
                            <option value="{{ $time->tim_id }}" {{ old('gin_tim_id') == $time->tim_id ? 'selected' : '' }}>{{ $time->tim_nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Seção: Localização --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-6 shadow-sm border border-gray-50 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-2 h-6 bg-indigo-600 rounded-full"></div>
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Endereço</h3>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">CEP</label>
                        <input type="text" name="gin_cep" value="{{ old('gin_cep') }}" required placeholder="00000-000" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                    
                    <div class="col-span-2">
                         <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Rua / Logradouro</label>
                         <input type="text" name="gin_endereco" value="{{ old('gin_endereco') }}" required placeholder="Ex: Av. Principal..." class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Nº</label>
                        <input type="text" name="gin_numero" value="{{ old('gin_numero') }}" required placeholder="123" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Bairro</label>
                        <input type="text" name="gin_bairro" value="{{ old('gin_bairro') }}" required placeholder="Ex: Centro..." class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div class="col-span-2 grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">Cidade</label>
                            <input type="text" name="gin_cidade" value="{{ old('gin_cidade') }}" required placeholder="Ex: Limeira..." class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-2 ml-1">UF</label>
                            <input type="text" name="gin_estado" value="{{ old('gin_estado') }}" required placeholder="SP" maxlength="2" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-4 text-sm font-black dark:text-white placeholder-gray-200 focus:ring-4 focus:ring-indigo-500/10 transition-all uppercase">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-8 space-y-4 px-1">
            <button type="submit" class="w-full bg-gray-900 dark:bg-gray-700 text-white font-black py-6 rounded-[2rem] shadow-xl active:scale-95 transition-all text-xs uppercase tracking-widest">
                Salvar Localização
            </button>
            <a href="{{ route('ginasios.index') }}" class="block w-full text-center text-gray-400 font-bold text-xs underline italic">Descartar e Voltar</a>
        </div>
    </form>
</div>
@endsection
