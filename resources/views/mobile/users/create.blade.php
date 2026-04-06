@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-24">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6 px-1">
        <a href="{{ route('users.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-95 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Novo Usuário</h2>
    </div>

    {{-- Erros --}}
    @if ($errors->any())
        <div class="mb-5 mx-1 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
            <ul class="text-xs text-red-700 dark:text-red-400 list-disc list-inside space-y-1 font-bold">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form x-data="{ role: '{{ old('role') }}' }" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Seção: Perfil & Foto --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center">
            <div class="relative group mb-6">
                <div class="w-28 h-36 rounded-2xl overflow-hidden border-2 border-dashed border-indigo-200 dark:border-indigo-900/30 bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                    <img id="fotoPreview" src="{{ asset('images/placeholder-atleta.png') }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                    </div>
                </div>
                <input type="file" name="foto" id="foto" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
            </div>

            <div class="w-full">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Perfil (Função) *</label>
                <select name="role" x-model="role" required class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white appearance-none transition-all focus:ring-4 focus:ring-indigo-500/10">
                    <option value="">Selecione o perfil...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Campos Dinâmicos por Role --}}
        <div x-show="role === 'Juiz' || role === 'ResponsavelTime' || role === 'ComissaoTecnica'" 
             class="bg-indigo-600 rounded-3xl p-6 text-white shadow-xl space-y-4 animate-fade-in-down">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-[10px] font-black uppercase tracking-widest text-white/70">Dados de Atribuição</span>
            </div>

            <div x-show="role === 'Juiz'">
                 <label class="block text-[10px] font-black text-white/50 uppercase tracking-widest mb-2 ml-1">Categoria de Árbitro</label>
                 <input type="text" name="tipo_arbitro" value="{{ old('tipo_arbitro') }}" placeholder="Ex: Nacional A"
                        class="w-full bg-white/10 border-none rounded-2xl p-4 font-bold text-sm text-white focus:bg-white/20 appearance-none">
            </div>

            <div>
                 <label class="block text-[10px] font-black text-white/50 uppercase tracking-widest mb-2 ml-1">Time / Entidade Vinculada</label>
                 <select name="time_id" class="w-full bg-white/10 border-none rounded-2xl p-4 font-bold text-sm text-white appearance-none focus:bg-white/20">
                     <option value="" class="text-gray-900">Selecione...</option>
                     @foreach($times as $time)
                         <option value="{{ $time->tim_id }}" {{ old('time_id') == $time->tim_id ? 'selected' : '' }} class="text-gray-900">{{ $time->tim_nome }}</option>
                     @endforeach
                 </select>
            </div>
        </div>

        {{-- Seção: Acesso --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
             <div class="flex items-center gap-2 mb-1 ml-1 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span class="text-[10px] font-black uppercase tracking-widest">Segurança & Acesso</span>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">E-mail de Acesso *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@exemplo.com"
                       class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Senha *</label>
                    <input type="password" name="password" required
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Confirmar *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                </div>
            </div>
        </div>

        {{-- Seção: Informações Pessoais --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
            <div class="flex items-center gap-2 mb-1 ml-1 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="text-[10px] font-black uppercase tracking-widest">Dados Pessoais</span>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Apelido</label>
                    <input type="text" name="apelido" value="{{ old('apelido') }}" placeholder="Nick"
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nome Completo *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">CPF</label>
                     <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00"
                            class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white">
                </div>
                <div>
                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">RG</label>
                     <input type="text" name="rg" value="{{ old('rg') }}"
                            class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nascimento</label>
                     <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}"
                            class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white appearance-none">
                </div>
                <div>
                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Telefone</label>
                     <input type="text" name="telefone" value="{{ old('telefone') }}" placeholder="(00) 00000-0000"
                            class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white">
                </div>
            </div>
        </div>

        {{-- Endereço (Colapsado) --}}
        <details class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
             <summary class="flex justify-between items-center p-6 cursor-pointer list-none">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Endereço (Opcional)</span>
                <svg class="w-5 h-5 text-gray-300 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="p-6 pt-0 space-y-4 border-t border-gray-50 dark:border-gray-700/50">
                <div class="pt-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">CEP</label>
                    <input type="text" name="cep" id="cep_mobile" value="{{ old('cep') }}" placeholder="00000-000"
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                </div>
                <div class="grid grid-cols-4 gap-2">
                    <div class="col-span-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Logradouro</label>
                        <input type="text" name="endereco" id="endereco_mobile" value="{{ old('endereco') }}"
                               class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nº</label>
                        <input type="text" name="numero" value="{{ old('numero') }}"
                               class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white">
                    </div>
                </div>
            </div>
        </details>

        <div class="flex flex-col gap-3 pt-6 px-1">
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-5 rounded-2xl shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Cadastrar Usuário
            </button>
            <a href="{{ route('users.index') }}" class="w-full text-gray-400 font-bold py-4 text-center">Cancelar</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fotoInput = document.getElementById('foto');
        const fotoPreview = document.getElementById('fotoPreview');
        if (fotoInput && fotoPreview) {
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = f => fotoPreview.src = f.target.result;
                    reader.readAsDataURL(file);
                }
            });
        }

        const cepInput = document.getElementById('cep_mobile');
        if (cepInput) {
            cepInput.addEventListener('blur', function() {
                const cep = this.value.replace(/\D/g, '');
                if (cep.length === 8) {
                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(r => r.json())
                        .then(data => {
                            if (!data.erro) {
                                document.getElementById('endereco_mobile').value = data.logradouro;
                                // Adicionar outros campos se necessário
                            }
                        });
                }
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
