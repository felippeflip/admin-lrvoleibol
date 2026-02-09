<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Membro da Comissão Técnica') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form id="comissaoTecnicaEditForm" method="POST" action="{{ route('comissao-tecnica.update', $comissaoTecnica->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Time (Se Admin) -->
                         @if (auth()->user()->hasRole('Administrador'))
                            <div class="mb-4">
                                <label for="time_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time</label>
                                <select name="time_id" id="time_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">Selecione um Time</option>
                                    @foreach ($times as $time)
                                        <option value="{{ $time->tim_id }}" {{ old('time_id', $comissaoTecnica->time_id) == $time->tim_id ? 'selected' : '' }}>
                                            {{ $time->tim_nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('time_id')" class="mt-2" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <!-- Foto -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto 3x4</label>
                                <div class="mb-2">
                                    @if($comissaoTecnica->foto)
                                        <img src="{{ $comissaoTecnica->foto_url }}" alt="Foto Atual" class="h-24 w-24 object-cover rounded-full">
                                    @else
                                        <p class="text-sm text-gray-500">Sem foto atual.</p>
                                    @endif
                                </div>
                                <input type="file" name="foto" id="foto" accept="image/*" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">Alterar Foto (JPG, PNG ou JPEG - Max. 5MB).</p>
                                <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                            </div>

                            <!-- Nome -->
                            <div>
                                <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome Completo *</label>
                                <input type="text" name="nome" id="nome" value="{{ old('nome', $comissaoTecnica->nome) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                            </div>

                            <!-- CPF -->
                            <div>
                                <label for="cpf" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CPF *</label>
                                <input type="text" name="cpf" id="cpf" value="{{ old('cpf', $comissaoTecnica->cpf) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300" placeholder="000.000.000-00">
                                <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
                            </div>

                            <!-- RG -->
                            <div>
                                <label for="rg" class="block text-sm font-medium text-gray-700 dark:text-gray-300">RG</label>
                                <input type="text" name="rg" id="rg" value="{{ old('rg', $comissaoTecnica->rg) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                <x-input-error :messages="$errors->get('rg')" class="mt-2" />
                            </div>

                            <!-- Função -->
                            <div>
                                <label for="funcao" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Função *</label>
                                <select name="funcao" id="funcao" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">Selecione</option>
                                    @foreach ($funcoes as $funcao)
                                        <option value="{{ $funcao }}" {{ old('funcao', $comissaoTecnica->funcao) == $funcao ? 'selected' : '' }}>{{ $funcao }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('funcao')" class="mt-2" />
                            </div>

                            <!-- Documento Registro -->
                            <div>
                                <label for="documento_registro" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registro (CREF, CRM, etc) *</label>
                                <input type="text" name="documento_registro" id="documento_registro" value="{{ old('documento_registro', $comissaoTecnica->documento_registro) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                <x-input-error :messages="$errors->get('documento_registro')" class="mt-2" />
                            </div>
                            
                             <!-- Comprovante Documento -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="comprovante_documento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comprovante do Registro/Diploma</label>
                                 <div class="mb-2">
                                    @if($comissaoTecnica->comprovante_documento)
                                       <a href="{{ $comissaoTecnica->comprovante_url }}" target="_blank" class="text-blue-500 hover:text-blue-700 underline">Ver Documento Atual</a>
                                    @else
                                        <p class="text-sm text-gray-500">Sem documento enviado.</p>
                                    @endif
                                </div>
                                <input type="file" name="comprovante_documento" id="comprovante_documento" accept=".pdf,image/*" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">Alterar Documento (PDF ou Imagem - Max. 5MB).</p>
                                <x-input-error :messages="$errors->get('comprovante_documento')" class="mt-2" />
                            </div>

                            <!-- Celular -->
                            <div>
                                <label for="celular" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Celular</label>
                                <input type="text" name="celular" id="celular" value="{{ old('celular', $comissaoTecnica->celular) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300" placeholder="(00) 00000-0000">
                                <x-input-error :messages="$errors->get('celular')" class="mt-2" />
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label for="telefone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label>
                                <input type="text" name="telefone" id="telefone" value="{{ old('telefone', $comissaoTecnica->telefone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300" placeholder="(00) 0000-0000">
                                <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                            </div>

                            <!-- E-mail -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $comissaoTecnica->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                         <div class="mt-6 border-t border-gray-200 pt-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Endereço</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- CEP -->
                                <div>
                                    <label for="cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP</label>
                                    <input type="text" name="cep" id="cep" value="{{ old('cep', $comissaoTecnica->cep) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300" placeholder="00000-000">
                                     <p class="mt-1 text-sm text-gray-500">Informe o CEP para preenchimento automático do endereço.</p>
                                    <x-input-error :messages="$errors->get('cep')" class="mt-2" />
                                </div>

                                <!-- Endereço -->
                                <div>
                                    <label for="endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Endereço</label>
                                    <input type="text" name="endereco" id="endereco" value="{{ old('endereco', $comissaoTecnica->endereco) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                                </div>

                                <!-- Número -->
                                <div>
                                    <label for="numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                                    <input type="text" name="numero" id="numero" value="{{ old('numero', $comissaoTecnica->numero) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                                </div>

                                <!-- Bairro -->
                                <div>
                                    <label for="bairro" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bairro</label>
                                    <input type="text" name="bairro" id="bairro" value="{{ old('bairro', $comissaoTecnica->bairro) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
                                </div>

                                <!-- Cidade -->
                                <div>
                                    <label for="cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade</label>
                                    <input type="text" name="cidade" id="cidade" value="{{ old('cidade', $comissaoTecnica->cidade) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
                                </div>

                                 <!-- Estado -->
                                <div>
                                    <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                    <input type="text" name="estado" id="estado" value="{{ old('estado', $comissaoTecnica->estado) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300">
                                    <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                                </div>
                            </div>
                        </div>


                        @if(auth()->user()->hasRole('Administrador'))
                            <div class="mt-6 border-t border-gray-200 pt-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Cartão da Liga</h3>
                                <div class="flex items-center justify-between">
                                    <label for="cartao_impresso_ano_atual" class="inline-flex items-center">
                                        <input id="cartao_impresso_ano_atual" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="cartao_impresso_ano_atual" value="1" {{ $comissaoTecnica->cartaoImpresso() ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Cartão Impresso ({{ date('Y') }})</span>
                                    </label>
                                    
                                    @if($comissaoTecnica->cartoes->where('impresso', true)->count() > 0)
                                        <div class="text-sm text-gray-500">
                                            <span class="font-bold">Histórico:</span>
                                            @foreach($comissaoTecnica->cartoes->where('impresso', true)->sortByDesc('ano') as $cartao)
                                                <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">
                                                    {{ $cartao->ano }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('comissao-tecnica.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Atualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
