<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Adicionar Novo Usuário') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mx-auto p-4">
                        @if ($errors->any())
                            <div class="bg-red-500 text-white p-2 my-4">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="flex items-center mb-4">
                                <!-- Hidden input para garantir que o campo seja enviado mesmo se desmarcado -->
                                <input type="hidden" name="is_arbitro" value="0">
                                <input id="is_arbitro" name="is_arbitro" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded mr-4 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" value="1" {{ old('is_arbitro') ? 'checked' : '' }}>
                                <label for="is_arbitro" class="text-sm font-medium text-gray-900 dark:text-gray-300">É árbitro?</label>
                            </div>
                            <div class="mb-4">
                                <label for="apelido" class="block text-gray-700">APELIDO:</label>
                                <input type="text" name="apelido" id="apelido" class="w-full border border-gray-300 p-2 rounded" value="{{ old('apelido') }}">
                            </div>
                            <div class="mb-4">
                                <label for="name" class="block text-gray-700">NOME COMPLETO:</label>
                                <input type="text" name="name" id="name" class="w-full border border-gray-300 p-2 rounded" value="{{ old('name') }}">
                            </div>
                            <div class="mb-4">
                                <label for="email" class="block text-gray-700">E-MAIL:</label>
                                <input type="email" name="email" id="email" class="w-full border border-gray-300 p-2 rounded" value="{{ old('email') }}" >
                            </div>
                            <div class="mb-4">
                                <label for="password" class="block text-gray-700">SENHA:</label>
                                <input type="password" name="password" id="password" class="w-full border border-gray-300 p-2 rounded">
                            </div>
                            <div class="mb-4">
                                <label for="password_confirmation" class="block text-gray-700">CONFIRMAR SENHA:</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 p-2 rounded">
                            </div>
                            <div class="mb-4">
                                <label for="telefone" class="block text-gray-700">TELEFONE:</label>
                                <input type="text" name="telefone" id="telefone" class="w-full border border-gray-300 p-2 rounded" value="{{ old('telefone') }}">
                            </div>

                            <div class="mb-4">
                                <label for="cpf" class="block text-gray-700">CPF:</label>
                                <input type="text" name="cpf" id="cpf" class="w-full border border-gray-300 p-2 rounded" value="{{ old('cpf') }}">
                            </div>

                            <div class="mb-4">
                                <label for="cref" class="block text-gray-700">REGISTRO:</label>
                                <input type="text" name="cref" id="cref" class="w-full border border-gray-300 p-2 rounded" value="{{ old('cref') }}">
                            </div>

                            <div class="mb-4">
                                <label for="tipo_arbitro" class="block text-gray-700">CATEGORIA:</label>
                                <input type="text" name="tipo_arbitro" id="tipo_arbitro" class="w-full border border-gray-300 p-2 rounded" value="{{ old('tipo_arbitro') }}">
                            </div>

                            <div class="mb-4">
                                <label for="cep" class="block text-gray-700">CEP:</label>
                                <input type="text" name="cep" id="cep" class="w-full border border-gray-300 p-2 rounded" value="{{ old('cep') }}">
                            </div>

                            <div class="mb-4">
                                <label for="endereco" class="block text-gray-700">ENDEREÇO:</label>
                                <input type="text" name="endereco" id="endereco" class="w-full border border-gray-300 p-2 rounded" value="{{ old('endereco') }}">
                            </div>

                            <div class="mb-4">
                                <label for="bairro" class="block text-gray-700">BAIRRO:</label>
                                <input type="text" name="bairro" id="bairro" class="w-full border border-gray-300 p-2 rounded" value="{{ old('bairro') }}">
                            </div>   
                                <div class="mb-4 group">
                                    <label for="cidade" class="block text-gray-700">CIDADE:</label>
                                    <input type="text" name="cidade" id="cidade" class="w-full border border-gray-300 p-2 rounded" value="{{ old('cidade') }}">
                                </div>
    
                                <div class="mb-4">
                                    <label for="cidade" class="block text-gray-700">ESTADO:</label>
                                    <input type="text" name="cidade" id="cidade" class="w-full border border-gray-300 p-2 rounded" value="{{ old('cidade') }}">
                                </div>
                            <div class="flex justify-between">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Cadastrar</button>
                                <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Voltar</a>
                            </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
