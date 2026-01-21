<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Adicionar Novo Time') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mx-auto p-4">
                        @if ($errors->any())
                            <div class="bg-red-500 text-white p-2 my-4 rounded flash-message" role="alert">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('times.store') }}" method="POST" enctype="multipart/form-data"
                            id="timeForm">
                            @csrf

                            {{-- Estrutura de grid para logo e campos principais --}}
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                {{-- Coluna para a Logo --}}
                                <div class="md:col-span-1 flex flex-col items-center justify-center">
                                    <label for="tim_logo" class="block text-gray-700 dark:text-gray-300 mb-2">Logo do
                                        Time</label>
                                    <div
                                        class="w-48 h-36 border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden flex items-center justify-center mb-2">
                                        {{-- Pré-visualização da Logo --}}
                                        <img id="logoPreview" src="{{ asset('images/placeholder-logo.png') }}"
                                            alt="Pré-visualização da Logo" class="w-full h-full object-contain">
                                    </div>
                                    <input name="tim_logo" id="tim_logo" type="file"
                                        accept="image/jpeg, image/png, image/jpg"
                                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                        aria-describedby="logo_help">
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-300" id="logo_help">Tamanho
                                        ideal: retangular (e.g., 4:3 ou 16:9)</p>
                                </div>

                                {{-- Coluna para campos principais (Nome, Responsável, E-mail) --}}
                                <div class="md:col-span-3">
                                    <div class="mb-4">
                                        <label for="tim_user_id"
                                            class="block text-gray-700 dark:text-gray-300 mb-2">Usuário Responsável do
                                            Time:</label>
                                        <select name="tim_user_id" id="tim_user_id"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Selecione um usuário</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{ old('tim_user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tim_user_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-4 border-t border-gray-200 dark:border-gray-600 pt-4 mt-4">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Ou
                                            Cadastre um Novo Usuário Responsável</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label for="new_user_name"
                                                    class="block text-gray-700 dark:text-gray-300 mb-2">Nome:</label>
                                                <input type="text" name="new_user_name" id="new_user_name"
                                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                                    value="{{ old('new_user_name') }}">
                                            </div>
                                            <div>
                                                <label for="new_user_email"
                                                    class="block text-gray-700 dark:text-gray-300 mb-2">Email:</label>
                                                <input type="email" name="new_user_email" id="new_user_email"
                                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                                    value="{{ old('new_user_email') }}">
                                            </div>
                                            <div>
                                                <label for="new_user_password"
                                                    class="block text-gray-700 dark:text-gray-300 mb-2">Senha:</label>
                                                <input type="password" name="new_user_password" id="new_user_password"
                                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Preencha estes campos
                                            APENAS se quiser criar um novo usuário para este time.</p>
                                    </div>

                                    <div class="mb-4">
                                        <label for="tim_nome" class="block text-gray-700 dark:text-gray-300 mb-2">Nome
                                            do Time *:</label>
                                        <input type="text" name="tim_nome" id="tim_nome"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                            value="{{ old('tim_nome') }}" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="tim_nome_abre"
                                            class="block text-gray-700 dark:text-gray-300 mb-2">Nome do Time
                                            Abreviado:</label>
                                        <input type="text" name="tim_nome_abre" id="tim_nome_abre"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                            value="{{ old('tim_nome_abre') }}">
                                    </div>
                                    <div class="mb-4">
                                        <label for="tim_email"
                                            class="block text-gray-700 dark:text-gray-300 mb-2">E-mail:</label>
                                        <input type="email" name="tim_email" id="tim_email"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                            value="{{ old('tim_email') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Demais campos seguem o layout existente --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="tim_celular"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Celular:</label>
                                    <input type="text" name="tim_celular" id="tim_celular"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('tim_celular') }}" maxlength="15">
                                </div>
                                <div>
                                    <label for="tim_telefone"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Telefone:</label>
                                    <input type="text" name="tim_telefone" id="tim_telefone"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('tim_telefone') }}" maxlength="14">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="tim_cnpj" class="block text-gray-700 dark:text-gray-300 mb-2">CNPJ:</label>
                                <input type="text" name="tim_cnpj" id="tim_cnpj"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                    value="{{ old('tim_cnpj') }}" maxlength="18">
                            </div>

                            <div class="mb-4">
                                <label for="tim_cep" class="block text-gray-700 dark:text-gray-300 mb-2">CEP:</label>
                                <input type="text" name="tim_cep" id="tim_cep"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                    value="{{ old('tim_cep') }}" maxlength="9">
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300" id="user_avatar_help">Informe o
                                    CEP para preenchimento automático do endereço.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-3">
                                    <label for="tim_endereco"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Endereço:</label>
                                    <input type="text" name="tim_endereco" id="tim_endereco"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('tim_endereco') }}">
                                </div>
                                <div class="md:col-span-1">
                                    <label for="tim_numero"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Número:</label>
                                    <input type="text" name="tim_numero" id="tim_numero"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('tim_numero') }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="tim_bairro"
                                    class="block text-gray-700 dark:text-gray-300 mb-2">Bairro:</label>
                                <input type="text" name="tim_bairro" id="tim_bairro"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                    value="{{ old('tim_bairro') }}">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-3">
                                    <label for="tim_cidade"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Cidade:</label>
                                    <input type="text" name="tim_cidade" id="tim_cidade"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('tim_cidade') }}">
                                </div>
                                <div class="md:col-span-1">
                                    <label for="tim_uf" class="block text-gray-700 dark:text-gray-300 mb-2">UF:</label>
                                    <input type="text" name="tim_uf" id="tim_uf"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('tim_uf') }}" maxlength="2">
                                </div>
                            </div>

                            <div class="flex justify-start mt-6 space-x-4">
                                <button type="submit"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Salvar</button>
                                <a href="{{ route('times.index') }}"
                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Voltar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const logoInput = document.getElementById('tim_logo');
            const logoPreview = document.getElementById('logoPreview'); // ID do elemento da imagem

            if (logoInput && logoPreview) {
                logoInput.addEventListener('change', function (event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            logoPreview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // Se nenhum arquivo for selecionado, volta para o placeholder
                        logoPreview.src = "{{ asset('images/placeholder-logo.png') }}";
                    }
                });
            }
        });
    </script>
</x-app-layout>