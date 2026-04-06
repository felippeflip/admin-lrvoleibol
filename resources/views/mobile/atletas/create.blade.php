@extends('mobile.layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto pb-24">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6 px-1">
        <a href="{{ route('atletas.index') }}" class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 active:scale-90 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">Novo Atleta</h2>
    </div>

    {{-- Erros --}}
    @if ($errors->any())
        <div class="mb-5 mx-1 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
            <ul class="text-xs text-red-700 dark:text-red-400 list-disc list-inside space-y-1 font-bold">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('atletas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Seção: Perfil & Foto --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center">
            <div class="relative group mb-6">
                <div class="w-32 h-44 rounded-2xl overflow-hidden border-2 border-dashed border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center justify-center group-active:scale-95 transition-all">
                    <img id="fotoPreview" src="{{ asset('images/placeholder-atleta.png') }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                </div>
                <input type="file" name="atl_foto" id="atl_foto" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
            </div>
            
            <div class="w-full space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nome Completo *</label>
                    <input type="text" name="atl_nome" value="{{ old('atl_nome') }}" required 
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all">
                </div>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nascimento</label>
                        <input type="date" name="atl_dt_nasc" value="{{ old('atl_dt_nasc') }}"
                               class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white">
                    </div>
                    <div class="w-24">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Sexo</label>
                        <select name="atl_sexo" class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white appearance-none">
                            <option value="M" {{ old('atl_sexo') == 'M' ? 'selected' : '' }}>Masc</option>
                            <option value="F" {{ old('atl_sexo') == 'F' ? 'selected' : '' }}>Fem</option>
                            <option value="O" {{ old('atl_sexo') == 'O' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Seção: Identificação --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
            <div class="flex items-center justify-between ml-1">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Identificação</span>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="atl_estrangeiro_mobile" name="atl_estrangeiro" value="1" {{ old('atl_estrangeiro') ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Estrangeiro</span>
                </label>
            </div>

            <div id="div_cpf_mobile">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">CPF</label>
                <input type="text" name="atl_cpf" id="atl_cpf_mobile" value="{{ old('atl_cpf') }}" maxlength="14" placeholder="000.000.000-00"
                       class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white focus:ring-4 focus:ring-blue-500/10">
            </div>

            <div id="div_passaporte_mobile" class="hidden">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Passaporte / Doc</label>
                <input type="text" name="atl_passaporte" value="{{ old('atl_passaporte') }}" maxlength="50"
                       class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">RG</label>
                <input type="text" name="atl_rg" value="{{ old('atl_rg') }}" maxlength="12"
                       class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
            </div>
        </div>

        {{-- Seção: Contato --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-5">
             <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">E-mail</label>
                <input type="email" name="atl_email" value="{{ old('atl_email') }}"
                       class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Celular</label>
                    <input type="text" name="atl_celular" value="{{ old('atl_celular') }}" maxlength="15" placeholder="(00) 00000-0000"
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Telefone</label>
                    <input type="text" name="atl_telefone" value="{{ old('atl_telefone') }}" maxlength="14"
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                </div>
            </div>
        </div>

        {{-- Seção: Endereço (Colapsado para limpar UI se não quiser preencher) --}}
        <details class="group bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <summary class="flex justify-between items-center p-6 cursor-pointer list-none">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Endereço (Opcional)</span>
                <svg class="w-5 h-5 text-gray-300 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="p-6 pt-0 space-y-4 border-t border-gray-50 dark:border-gray-700/50">
                <div class="pt-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">CEP</label>
                    <input type="text" name="atl_cep" id="atl_cep_mobile" value="{{ old('atl_cep') }}" maxlength="9" placeholder="00000-000"
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white focus:ring-4 focus:ring-blue-500/10">
                </div>
                <div class="grid grid-cols-4 gap-2">
                    <div class="col-span-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Logradouro</label>
                        <input type="text" name="atl_endereco" id="atl_endereco_mobile" value="{{ old('atl_endereco') }}"
                               class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nº</label>
                        <input type="text" name="atl_numero" value="{{ old('atl_numero') }}"
                               class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Bairro</label>
                    <input type="text" name="atl_bairro" id="atl_bairro_mobile" value="{{ old('atl_bairro') }}"
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                </div>
                <div class="grid grid-cols-4 gap-2">
                    <div class="col-span-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Cidade</label>
                        <input type="text" name="atl_cidade" id="atl_cidade_mobile" value="{{ old('atl_cidade') }}"
                               class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-sm dark:text-white">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">UF</label>
                        <input type="text" name="atl_estado" id="atl_estado_mobile" value="{{ old('atl_estado') }}" maxlength="2"
                               class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 font-bold text-xs dark:text-white">
                    </div>
                </div>
            </div>
        </details>

        {{-- Seção: Categoria & Time --}}
        <div class="bg-blue-600 rounded-[3rem] p-8 text-white shadow-xl space-y-6">
            <h3 class="text-lg font-black uppercase tracking-tighter">Dados de Jogo</h3>
            
            @if(isset($times) && count($times) > 0)
            <div>
                <label class="block text-[10px] font-black opacity-60 uppercase tracking-widest mb-2 ml-1 text-white">Time Vinculado *</label>
                <select name="atl_tim_id" required class="w-full bg-white/10 border-none rounded-2xl p-4 font-bold text-sm text-white appearance-none focus:bg-white/20">
                    <option value="" class="text-gray-900">Selecione o Time...</option>
                    @foreach($times as $time)
                        <option value="{{ $time->tim_id }}" {{ old('atl_tim_id') == $time->tim_id ? 'selected' : '' }} class="text-gray-900">{{ $time->tim_nome }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-[10px] font-black opacity-60 uppercase tracking-widest mb-2 ml-1 text-white">Categoria Competição</label>
                <select name="atl_categoria" required class="w-full bg-white/10 border-none rounded-2xl p-4 font-bold text-sm text-white appearance-none focus:bg-white/20">
                    <option value="" class="text-gray-900">Selecione a Categoria...</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->cto_id }}" {{ old('atl_categoria') == $categoria->cto_id ? 'selected' : '' }} class="text-gray-900">{{ $categoria->cto_nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 items-end">
                <div>
                    <label class="block text-[10px] font-black opacity-60 uppercase tracking-widest mb-2 ml-1 text-white">Ano Inscrição</label>
                    <select name="atl_ano_insc" class="w-full bg-white/10 border-none rounded-2xl p-4 font-bold text-sm text-white appearance-none">
                        @php $currentYear = date('Y'); $years = range($currentYear + 1, $currentYear - 2); @endphp
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ old('atl_ano_insc', $currentYear) == $year ? 'selected' : '' }} class="text-gray-900">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pb-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="cartao_impresso_ano_atual" value="1" {{ !auth()->user()->hasRole('Administrador') ? 'disabled' : '' }}
                               class="w-5 h-5 rounded-lg text-blue-500 bg-white/20 border-white/20 focus:ring-blue-400">
                        <span class="text-[9px] font-black uppercase leading-tight">Cartão {{ date('Y') }} Impresso?</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Seção: Comprovação --}}
        <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden">
             <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-600/20 rounded-full blur-3xl"></div>
             <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4 ml-1">Documento de Comprovação (PDF/Foto)</label>
                <div class="flex items-center gap-4">
                    <div class="relative w-full">
                        <input type="file" name="atl_documento" id="atl_documento" accept=".pdf,image/*" 
                               class="absolute inset-0 opacity-0 z-10 cursor-pointer">
                        <div class="w-full bg-white/5 border border-dashed border-white/20 rounded-2xl p-6 flex flex-col items-center gap-2">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <span class="text-[10px] font-bold text-gray-400 uppercase" id="filename-preview">Fazer Upload</span>
                        </div>
                    </div>
                </div>
                <p class="text-[9px] text-gray-500 mt-4 leading-relaxed">* Envie RG, CNH ou Certidão para validar a inscrição do atleta na Liga.</p>
            </div>
        </div>

        {{-- Ações --}}
        <div class="flex flex-col gap-3 pt-6 px-1">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Finalizar Cadastro
            </button>
            <a href="{{ route('atletas.index') }}" class="w-full text-gray-400 font-bold py-4 text-center">Cancelar</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Foto Preview
        const fotoInput = document.getElementById('atl_foto');
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

        // Filename Preview
        const docInput = document.getElementById('atl_documento');
        const fileSpan = document.getElementById('filename-preview');
        if (docInput) {
            docInput.addEventListener('change', function(e) {
                if (this.files[0]) fileSpan.innerText = this.files[0].name;
            });
        }

        // Estrangeiro Toggle
        const eCheckbox = document.getElementById('atl_estrangeiro_mobile');
        const divCpf = document.getElementById('div_cpf_mobile');
        const divPass = document.getElementById('div_passaporte_mobile');
        if (eCheckbox) {
            eCheckbox.addEventListener('change', function() {
                if(this.checked) {
                    divCpf.classList.add('hidden');
                    divPass.classList.remove('hidden');
                } else {
                    divCpf.classList.remove('hidden');
                    divPass.classList.add('hidden');
                }
            });
        }

        // Busca CEP (Otimizado Mobile)
        const cepInput = document.getElementById('atl_cep_mobile');
        if (cepInput) {
            cepInput.addEventListener('blur', function() {
                const cep = this.value.replace(/\D/g, '');
                if (cep.length === 8) {
                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(r => r.json())
                        .then(data => {
                            if (!data.erro) {
                                document.getElementById('atl_endereco_mobile').value = data.logradouro;
                                document.getElementById('atl_bairro_mobile').value = data.bairro;
                                document.getElementById('atl_cidade_mobile').value = data.localidade;
                                document.getElementById('atl_estado_mobile').value = data.uf;
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
