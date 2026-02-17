<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cadastrar Membro da Comissão Técnica') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form id="comissaoTecnicaForm" method="POST" action="{{ route('comissao-tecnica.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Time (Se Admin) -->
                        @if (auth()->user()->hasRole('Administrador'))
                            <div class="mb-4">
                                <label for="time_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time</label>
                                <select name="time_id" id="time_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">Selecione um Time</option>
                                    @foreach ($times as $time)
                                        <option value="{{ $time->tim_id }}" {{ old('time_id') == $time->tim_id ? 'selected' : '' }}>
                                            {{ $time->tim_nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('time_id')" class="mt-2" />
                            </div>
                        @endif

                        <!-- Main Layout Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            
                            <!-- Left Column: Photo -->
                            <div class="lg:col-span-1">
                                <div class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 h-full">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto 3x4 *</label>
                                    
                                    <!-- Preview Container -->
                                    <div class="mb-4 relative w-32 h-40 bg-gray-200 rounded-lg overflow-hidden shadow-md group">
                                        <img id="foto-preview" src="#" alt="Pré-visualização" class="hidden w-full h-full object-cover">
                                        
                                        <!-- Placeholder Icon -->
                                        <div id="foto-placeholder" class="flex flex-col items-center justify-center w-full h-full text-gray-400">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            <span class="text-xs mt-1">Sem foto</span>
                                        </div>
                                    </div>

                                    <label for="foto" class="cursor-pointer bg-white dark:bg-gray-800 text-indigo-600 hover:text-indigo-500 font-medium py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm hover:bg-gray-50 transition duration-150 ease-in-out w-full text-center">
                                        Selecionar
                                    </label>
                                    <input type="file" name="foto" id="foto" accept="image/*" required class="hidden" onchange="previewImage(this)">
                                    
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center" id="file_input_help">JPG, PNG, JPEG<br>(Max. 5MB)</p>
                                    <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Right Column: Personal & Professional Info -->
                            <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nome -->
                                <div class="md:col-span-2">
                                    <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome Completo *</label>
                                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                                </div>

                                <!-- Registro LRV -->
                                <div class="md:col-span-2">
                                    <label for="registro_lrv" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registro LRV</label>
                                    <input type="text" name="registro_lrv" id="registro_lrv" value="{{ old('registro_lrv') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('registro_lrv')" class="mt-2" />
                                </div>

                                <!-- CPF -->
                                <div>
                                    <label for="cpf" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CPF *</label>
                                    <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300" placeholder="000.000.000-00">
                                    <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
                                </div>

                                <!-- RG -->
                                <div>
                                    <label for="rg" class="block text-sm font-medium text-gray-700 dark:text-gray-300">RG</label>
                                    <input type="text" name="rg" id="rg" value="{{ old('rg') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('rg')" class="mt-2" />
                                </div>

                                <!-- Função -->
                                <div>
                                    <label for="funcao" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Função *</label>
                                    <select name="funcao" id="funcao" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="">Selecione</option>
                                        @foreach ($funcoes as $funcao)
                                            <option value="{{ $funcao }}" {{ old('funcao') == $funcao ? 'selected' : '' }}>{{ $funcao }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('funcao')" class="mt-2" />
                                </div>

                                <!-- Documento Registro -->
                                <div>
                                    <label for="documento_registro" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registro (CREF, CRM, etc)</label>
                                    <input type="text" name="documento_registro" id="documento_registro" value="{{ old('documento_registro') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('documento_registro')" class="mt-2" />
                                </div>

                                <!-- E-mail -->
                                <div class="md:col-span-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Full Width Section (Contacts & Docs) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            <!-- Celular -->
                            <div>
                                <label for="celular" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Celular</label>
                                <input type="text" name="celular" id="celular" value="{{ old('celular') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300" placeholder="(00) 00000-0000">
                                <x-input-error :messages="$errors->get('celular')" class="mt-2" />
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label for="telefone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label>
                                <input type="text" name="telefone" id="telefone" value="{{ old('telefone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300" placeholder="(00) 0000-0000">
                                <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                            </div>

                             <!-- Comprovante Documento -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="comprovante_documento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comprovante do Registro/Diploma</label>
                                <input type="file" name="comprovante_documento" id="comprovante_documento" accept=".pdf,image/*" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PDF ou Imagem (Max. 5MB).</p>
                                <x-input-error :messages="$errors->get('comprovante_documento')" class="mt-2" />
                            </div>
                        </div>

                        <script>
                            function previewImage(input) {
                                const previewImage = document.getElementById('foto-preview');
                                const placeholder = document.getElementById('foto-placeholder');

                                if (input.files && input.files[0]) {
                                    const reader = new FileReader();

                                    reader.onload = function(e) {
                                        previewImage.src = e.target.result;
                                        previewImage.classList.remove('hidden');
                                        placeholder.classList.add('hidden');
                                    }

                                    reader.readAsDataURL(input.files[0]);
                                } else {
                                    previewImage.src = '#';
                                    previewImage.classList.add('hidden');
                                    placeholder.classList.remove('hidden');
                                }
                            }
                        </script>

                         <div class="mt-6 border-t border-gray-200 pt-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Endereço</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- CEP -->
                                <div>
                                    <label for="cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP</label>
                                    <input type="text" name="cep" id="cep" value="{{ old('cep') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300" placeholder="00000-000">
                                     <p class="mt-1 text-sm text-gray-500">Informe o CEP para preenchimento automático do endereço.</p>
                                    <x-input-error :messages="$errors->get('cep')" class="mt-2" />
                                </div>

                                <!-- Endereço -->
                                <div>
                                    <label for="endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Endereço</label>
                                    <input type="text" name="endereco" id="endereco" value="{{ old('endereco') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                                </div>

                                <!-- Número -->
                                <div>
                                    <label for="numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                                    <input type="text" name="numero" id="numero" value="{{ old('numero') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                                </div>

                                <!-- Bairro -->
                                <div>
                                    <label for="bairro" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bairro</label>
                                    <input type="text" name="bairro" id="bairro" value="{{ old('bairro') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
                                </div>

                                <!-- Cidade -->
                                <div>
                                    <label for="cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade</label>
                                    <input type="text" name="cidade" id="cidade" value="{{ old('cidade') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
                                </div>

                                 <!-- Estado -->
                                <div>
                                    <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                    <input type="text" name="estado" id="estado" value="{{ old('estado') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                                </div>
                            </div>
                        </div>


                        @if(auth()->user()->hasRole('Administrador'))
                            <div class="mt-4">
                                <label for="cartao_impresso_ano_atual" class="inline-flex items-center">
                                    <input id="cartao_impresso_ano_atual" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="cartao_impresso_ano_atual" value="1">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Cartão Impresso ({{ date('Y') }})</span>
                                </label>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('comissao-tecnica.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Cadastrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
