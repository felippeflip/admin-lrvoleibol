<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Time') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mx-auto p-4">
                        @if ($errors->any())
                            <div class="bg-red-500 text-white p-2 my-4 rounded">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('times.update', $time->tim_id) }}" method="POST" enctype="multipart/form-data" id="timeEditForm">
                            @csrf
                            @method('PUT') {{-- Importante para o método PUT/PATCH --}}

                            <div class="mb-6"> {{-- Aumentando a margem para o bloco do logo --}}
                                <label for="tim_logo" class="block text-gray-700 dark:text-gray-300 mb-2">Logo do Time</label>
                                @if ($time->tim_logo_url)
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Logo Atual:</p>
                                        <img id="current-logo-preview" src="{{ $time->tim_logo_url }}" alt="Logo Atual" class="max-w-xs h-auto object-contain border border-gray-300 dark:border-gray-600 p-2 rounded-lg">
                                    </div>
                                @endif
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Selecione um novo logo (ou visualize abaixo):</p>
                                <input name="tim_logo" id="tim_logo" type="file" accept="image/jpeg, image/png" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" aria-describedby="user_avatar_help">
                                <div class="mt-4 border border-dashed border-gray-400 dark:border-gray-600 p-4 flex justify-center items-center min-h-[100px] rounded-lg"> {{-- Área para pré-visualização --}}
                                    <img id="new-logo-preview" src="#" alt="Pré-visualização do Novo Logo" class="hidden max-w-full h-auto object-contain" style="max-height: 200px;"> {{-- max-h para limitar altura --}}
                                    <span id="no-new-logo-text" class="text-gray-500 dark:text-gray-400">Nenhum logo selecionado para pré-visualização.</span>
                                </div>
                            </div>

                           <div class="mb-4">
                                <label for="tim_user_id" class="block text-gray-700 dark:text-gray-300 mb-2">Usuário Responsável do Time:</label>
                                <select name="tim_user_id" id="tim_user_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecione um usuário</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('tim_user_id', $time->tim_user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tim_user_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="tim_nome" class="block text-gray-700 dark:text-gray-300 mb-2">Nome do Time *:</label>
                                <input type="text" name="tim_nome" id="tim_nome" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_nome', $time->tim_nome) }}" required>
                            </div>

                            <div class="mb-4">
                                <label for="tim_nome_abre" class="block text-gray-700 dark:text-gray-300 mb-2">Nome do Time Abreviado:</label>
                                <input type="text" name="tim_nome_abre" id="tim_nome_abre" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_nome_abre', $time->tim_nome_abre) }}">
                            </div>

                            <div class="mb-4">
                                <label for="tim_email" class="block text-gray-700 dark:text-gray-300 mb-2">E-mail:</label>
                                <input type="email" name="tim_email" id="tim_email" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_email', $time->tim_email) }}">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="tim_celular" class="block text-gray-700 dark:text-gray-300 mb-2">Celular:</label>
                                    <input type="text" name="tim_celular" id="tim_celular" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_celular', $time->tim_celular) }}">
                                </div>
                                <div>
                                    <label for="tim_telefone" class="block text-gray-700 dark:text-gray-300 mb-2">Telefone:</label>
                                    <input type="text" name="tim_telefone" id="tim_telefone" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_telefone', $time->tim_telefone) }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="tim_cnpj" class="block text-gray-700 dark:text-gray-300 mb-2">CNPJ:</label>
                                <input type="text" name="tim_cnpj" id="tim_cnpj" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_cnpj', $time->tim_cnpj) }}">
                            </div>

                            <div class="mb-4">
                                <label for="tim_cep" class="block text-gray-700 dark:text-gray-300 mb-2">CEP:</label>
                                <input type="text" name="tim_cep" id="tim_cep" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_cep', $time->tim_cep) }}">
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300" id="user_avatar_help">Informe o CEP para realizar a pesquisa do endereço</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-3">
                                    <label for="tim_endereco" class="block text-gray-700 dark:text-gray-300 mb-2">Endereço:</label>
                                    <input type="text" name="tim_endereco" id="tim_endereco" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_endereco', $time->tim_endereco) }}">
                                </div>
                                <div class="md:col-span-1">
                                    <label for="tim_numero" class="block text-gray-700 dark:text-gray-300 mb-2">Número:</label>
                                    <input type="text" name="tim_numero" id="tim_numero" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_numero', $time->tim_numero) }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="tim_bairro" class="block text-gray-700 dark:text-gray-300 mb-2">Bairro:</label>
                                <input type="text" name="tim_bairro" id="tim_bairro" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_bairro', $time->tim_bairro) }}">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-3">
                                    <label for="tim_cidade" class="block text-gray-700 dark:text-gray-300 mb-2">Cidade:</label>
                                    <input type="text" name="tim_cidade" id="tim_cidade" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_cidade', $time->tim_cidade) }}">
                                </div>
                                <div class="md:col-span-1">
                                    <label for="tim_uf" class="block text-gray-700 dark:text-gray-300 mb-2">UF:</label>
                                    <input type="text" name="tim_uf" id="tim_uf" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_uf', $time->tim_uf) }}" maxlength="2">
                                </div>
                            </div>

                            <div class="flex justify-start mt-6 space-x-4">
                                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Atualizar</button> {{-- Alterado texto do botão --}}
                                <a href="{{ route('times.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script para pré-visualizar o logo selecionado
        document.addEventListener('DOMContentLoaded', function() {
            const logoInput = document.getElementById('tim_logo');
            const newLogoPreview = document.getElementById('new-logo-preview');
            const noNewLogoText = document.getElementById('no-new-logo-text');
            const currentLogoPreview = document.getElementById('current-logo-preview');

            logoInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        newLogoPreview.src = e.target.result;
                        newLogoPreview.classList.remove('hidden');
                        noNewLogoText.classList.add('hidden');
                        // Esconde o logo atual se um novo for selecionado
                        if (currentLogoPreview) {
                            currentLogoPreview.classList.add('hidden');
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    newLogoPreview.classList.add('hidden');
                    noNewLogoText.classList.remove('hidden');
                    // Mostra o logo atual novamente se o novo for desmarcado
                    if (currentLogoPreview) {
                        currentLogoPreview.classList.remove('hidden');
                    }
                }
            });
        });
    </script>
</x-app-layout>