<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Adicionar Novo Time') }}
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
                        <form action="{{ route('times.store') }}" method="POST" enctype="multipart/form-data" id="timeForm">
                            @csrf

                            <div class="mb-4">
                                <label for="tim_logo" class="block text-gray-700 dark:text-gray-300 mb-2">Logo do Time</label> {{-- Adicionado mb-2 --}}
                                <input name="tim_logo" id="tim_logo" type="file" accept="image/jpeg, image/png" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" aria-describedby="user_avatar_help">
                            </div>

                           <div class="mb-4">
                                <label for="tim_user_id" class="block text-gray-700 dark:text-gray-300 mb-2">Usuário Responsável do Time:</label> {{-- Adicionado mb-2 --}}
                                <select name="tim_user_id" id="tim_user_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"> {{-- Adicionado focus styles --}}
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

                            <div class="mb-4">
                                <label for="tim_nome" class="block text-gray-700 dark:text-gray-300 mb-2">Nome do Time *:</label> {{-- Adicionado mb-2 --}}
                                <input type="text" name="tim_nome" id="tim_nome" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_nome') }}" required> {{-- Adicionado focus styles --}}
                            </div>

                            <div class="mb-4">
                                <label for="tim_nome_abre" class="block text-gray-700 dark:text-gray-300 mb-2">Nome do Time Abreviado:</label> {{-- Adicionado mb-2 --}}
                                <input type="text" name="tim_nome_abre" id="tim_nome_abre" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_nome_abre') }}"> {{-- Adicionado focus styles --}}
                            </div>

                            <div class="mb-4">
                                <label for="tim_email" class="block text-gray-700 dark:text-gray-300 mb-2">E-mail:</label> {{-- Adicionado mb-2 --}}
                                <input type="email" name="tim_email" id="tim_email" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_email') }}"> {{-- Adicionado focus styles --}}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="tim_celular" class="block text-gray-700 dark:text-gray-300 mb-2">Celular:</label> {{-- Adicionado mb-2 --}}
                                    <input type="text" name="tim_celular" id="tim_celular" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_celular') }}"> {{-- Adicionado focus styles --}}
                                </div>
                                <div>
                                    <label for="tim_telefone" class="block text-gray-700 dark:text-gray-300 mb-2">Telefone:</label> {{-- Adicionado mb-2 --}}
                                    <input type="text" name="tim_telefone" id="tim_telefone" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_telefone') }}"> {{-- Adicionado focus styles --}}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="tim_cnpj" class="block text-gray-700 dark:text-gray-300 mb-2">CNPJ:</label> {{-- Adicionado mb-2 --}}
                                <input type="text" name="tim_cnpj" id="tim_cnpj" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_cnpj') }}"> {{-- Adicionado focus styles --}}
                            </div>

                            <div class="mb-4">
                                <label for="tim_cep" class="block text-gray-700 dark:text-gray-300 mb-2">CEP:</label> {{-- Adicionado mb-2 --}}
                                <input type="text" name="tim_cep" id="tim_cep" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_cep') }}"> {{-- Adicionado focus styles --}}
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300" id="user_avatar_help">Informe o CEP para realizar a pesquisa do endereço</p> {{-- Alterado para 'p' e ajustado margem --}}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-3">
                                    <label for="tim_endereco" class="block text-gray-700 dark:text-gray-300 mb-2">Endereço:</label> {{-- Adicionado mb-2 --}}
                                    <input type="text" name="tim_endereco" id="tim_endereco" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_endereco') }}"> {{-- Adicionado focus styles --}}
                                </div>
                                <div class="md:col-span-1">
                                    <label for="tim_numero" class="block text-gray-700 dark:text-gray-300 mb-2">Número:</label> {{-- Adicionado mb-2 --}}
                                    <input type="text" name="tim_numero" id="tim_numero" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_numero') }}"> {{-- Adicionado focus styles --}}
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="tim_bairro" class="block text-gray-700 dark:text-gray-300 mb-2">Bairro:</label> {{-- Adicionado mb-2 --}}
                                <input type="text" name="tim_bairro" id="tim_bairro" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_bairro') }}"> {{-- Adicionado focus styles --}}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-3">
                                    <label for="tim_cidade" class="block text-gray-700 dark:text-gray-300 mb-2">Cidade:</label> {{-- Adicionado mb-2 --}}
                                    <input type="text" name="tim_cidade" id="tim_cidade" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_cidade') }}"> {{-- Adicionado focus styles --}}
                                </div>
                                <div class="md:col-span-1">
                                    <label for="tim_uf" class="block text-gray-700 dark:text-gray-300 mb-2">UF:</label> {{-- Adicionado mb-2 --}}
                                    <input type="text" name="tim_uf" id="tim_uf" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('tim_uf') }}" maxlength="2"> {{-- Adicionado focus styles --}}
                                </div>
                            </div>

                            <div class="flex justify-start mt-6 space-x-4"> {{-- Alterado para 'justify-start' e adicionado 'space-x-4' --}}
                                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Salvar</button> {{-- Ajustado texto e classes --}}
                                <a href="{{ route('times.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Voltar</a> {{-- Ajustado texto e classes --}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>