@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-24">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6 px-1">
        <a href="{{ route('users.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-95 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Editar Usuário</h2>
    </div>

    {{-- Erros --}}
    @if ($errors->any())
        <div class="mb-5 mx-1 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
            <ul class="text-xs text-red-700 dark:text-red-400 list-disc list-inside space-y-1 font-bold">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form x-data="{ role: '{{ old('role', $user->roles->first()?->name) }}' }" action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Seção: Perfil & Foto --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center">
            <div class="relative group mb-6">
                <div class="w-28 h-36 rounded-2xl overflow-hidden border-2 border-dashed border-indigo-200 dark:border-indigo-900/30 bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                    <img id="fotoPreview" src="{{ ($user->foto && \Storage::disk('user_fotos')->exists($user->foto)) ? asset('storage/user_fotos/' . $user->foto) : asset('images/placeholder-atleta.png') }}" class="w-full h-full object-cover">
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
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Campos Dinâmicos por Role --}}
        <div x-show="role === 'Juiz' || role === 'ResponsavelTime' || role === 'ComissaoTecnica'" 
             class="bg-indigo-600 rounded-3xl p-6 text-white shadow-xl space-y-4 animate-fade-in-down">
            <h3 class="text-sm font-black uppercase tracking-widest text-indigo-200">Dados do Vínculo</h3>
            
            <div x-show="role === 'Juiz'">
                 <div class="mb-4">
                    <label class="block text-[10px] font-black text-white/50 uppercase tracking-widest mb-1 ml-1 tracking-tighter">Reg. Liga Volley</label>
                    <input type="text" value="{{ $user->lrv }}" readonly
                           class="w-full bg-white/5 border-none rounded-2xl p-4 font-black text-sm text-white/80 cursor-not-allowed">
                 </div>
                 <div>
                    <label class="block text-[10px] font-black text-white/50 uppercase tracking-widest mb-1 ml-1 tracking-tighter">Categoria de Árbitro</label>
                    <input type="text" name="tipo_arbitro" value="{{ old('tipo_arbitro', $user->tipo_arbitro) }}"
                           class="w-full bg-white/10 border-none rounded-2xl p-4 font-bold text-sm text-white focus:bg-white/20">
                 </div>
            </div>

            <div>
                 <label class="block text-[10px] font-black text-white/50 uppercase tracking-widest mb-1 ml-1 tracking-tighter">Time / Entidade Vinculada</label>
                 @php $responsibleTime = $times->firstWhere('tim_user_id', $user->id); @endphp
                 <select name="time_id" class="w-full bg-white/10 border-none rounded-2xl p-4 font-bold text-sm text-white appearance-none focus:bg-white/20">
                     <option value="" class="text-gray-900">Selecione...</option>
                     @foreach($times as $time)
                         <option value="{{ $time->tim_id }}" {{ old('time_id', $user->time_id ?? ($responsibleTime ? $responsibleTime->tim_id : '')) == $time->tim_id ? 'selected' : '' }} class="text-gray-900">{{ $time->tim_nome }}</option>
                     @endforeach
                 </select>
            </div>
        </div>

        {{-- Seção: Informações de Cadastro --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">E-mail de Acesso *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Apelido</label>
                    <input type="text" name="apelido" value="{{ old('apelido', $user->apelido) }}"
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nome Completo *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">CPF</label>
                     <input type="text" name="cpf" value="{{ old('cpf', $user->cpf) }}"
                            class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white">
                </div>
                <div>
                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">RG</label>
                     <input type="text" name="rg" value="{{ old('rg', $user->rg) }}"
                            class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nascimento</label>
                     <input type="date" name="data_nascimento" value="{{ old('data_nascimento', $user->data_nascimento) }}"
                            class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white appearance-none">
                </div>
                <div>
                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Telefone</label>
                     <input type="text" name="telefone" value="{{ old('telefone', $user->telefone) }}"
                            class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white">
                </div>
            </div>
        </div>

        {{-- Localização --}}
        <details class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" {{ $user->cep ? 'open' : '' }}>
             <summary class="flex justify-between items-center p-6 cursor-pointer list-none">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Localização</span>
                <svg class="w-5 h-5 text-gray-300 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="p-6 pt-0 space-y-4 border-t border-gray-50 dark:border-gray-700/50">
                <div class="pt-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">CEP</label>
                    <input type="text" name="cep" id="cep_mobile" value="{{ old('cep', $user->cep) }}"
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                </div>
                <div>
                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Cidade / UF</label>
                     <div class="flex gap-2">
                         <input type="text" name="cidade" id="cidade_mobile" value="{{ old('cidade', $user->cidade) }}" class="flex-1 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                         <input type="text" name="estado" id="estado_mobile" value="{{ old('estado', $user->estado) }}" class="w-16 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white text-center">
                     </div>
                </div>
            </div>
        </details>

        <div class="flex flex-col gap-3 pt-4 px-1">
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-5 rounded-2xl shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Salvar Alterações
            </button>
        </div>
    </form>

    {{-- Redefinir Senha --}}
    <div class="mt-8 bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl space-y-6">
        <div class="flex items-center gap-2 mb-1">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            <h3 class="text-[10px] font-black uppercase tracking-widest text-red-500">Trocar Senha</h3>
        </div>

        <form action="{{ route('resetPasswordUser', $user->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Nova Senha</label>
                <input type="password" name="password" required
                       class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 font-bold text-sm text-white">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Confirmar Nova Senha</label>
                <input type="password" name="password_confirmation" required
                       class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 font-bold text-sm text-white">
            </div>
            <button type="submit" class="w-full bg-red-600/20 hover:bg-red-600/30 text-red-500 border border-red-500/30 font-black py-4 rounded-2xl transition-all">
                Redefinir Agora
            </button>
        </form>
    </div>

    <div class="pt-6 px-1">
        <a href="{{ route('users.index') }}" class="block w-full text-gray-400 font-bold py-4 text-center">Descartar e Voltar</a>
    </div>
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
    });
</script>
@endsection
