<!-- filepath: /var/www/html/admin-lrvoleibol/resources/views/users/edit.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Usuário') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="bg-red-500 text-white p-2 my-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Card/Container para o formulário de atualização de usuário -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Atualizar Informações do Usuário</h3>
                    <form id="userEditForm" action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div x-data="{ role: '{{ old('role', $user->roles->first()?->name) }}' }"
                            class="mb-4 border p-4 rounded border-gray-200 dark:border-gray-700">
                            <div class="mb-4">
                                <label for="role" class="block text-gray-700 dark:text-gray-300 mb-1">Perfil
                                    (Função):</label>
                                <select name="role" id="role" x-model="role"
                                    class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                    <option value="">Selecione um perfil...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="role === 'Juiz'">
                                <div class="mb-4">
                                    <label for="cref" class="block text-gray-700 dark:text-gray-300">REGISTRO
                                        (CREF):</label>
                                    <input type="text" name="cref" id="cref"
                                        class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                        value="{{ $user->cref }}">
                                </div>
                                <div class="mb-4">
                                    <label for="tipo_arbitro"
                                        class="block text-gray-700 dark:text-gray-300">CATEGORIA:</label>
                                    <input type="text" name="tipo_arbitro" id="tipo_arbitro"
                                        class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                        value="{{ $user->tipo_arbitro }}">
                                </div>
                            </div>

                            <div x-show="role === 'ResponsavelTime'">
                                <label for="time_id" class="block text-gray-700 dark:text-gray-300 mb-1">Selecione o
                                    Time:</label>
                                @php
                                    $responsibleTime = $times->firstWhere('tim_user_id', $user->id);
                                @endphp
                                <select name="time_id" id="time_id"
                                    class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                    <option value="">Selecione...</option>
                                    @foreach($times as $time)
                                        <option value="{{ $time->tim_id }}" {{ old('time_id', $responsibleTime ? $responsibleTime->tim_id : '') == $time->tim_id ? 'selected' : '' }}>
                                            {{ $time->tim_nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="apelido" class="block text-gray-700">APELIDO:</label>
                            <input type="text" name="apelido" id="apelido"
                                class="w-full border border-gray-300 p-2 rounded" value="{{ $user->apelido }}">
                        </div>
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700">NOME COMPLETO:</label>
                            <input type="text" name="name" id="name" class="w-full border border-gray-300 p-2 rounded"
                                value="{{ $user->name }}">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700">E-MAIL:</label>
                            <input type="email" name="email" id="email"
                                class="w-full border border-gray-300 p-2 rounded" value="{{ $user->email }}">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="telefone" class="block text-gray-700 dark:text-gray-300">TELEFONE:</label>
                                <input type="text" name="telefone" id="telefone"
                                    class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                    value="{{ $user->telefone }}">
                            </div>
                            <div>
                                <label for="cpf" class="block text-gray-700 dark:text-gray-300">CPF:</label>
                                <input type="text" name="cpf" id="cpf"
                                    class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                    value="{{ $user->cpf }}">
                            </div>
                        </div>





                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                            <div class="col-span-12 md:col-span-3">
                                <label for="cep" class="block text-gray-700 dark:text-gray-300">CEP:</label>
                                <input type="text" name="cep" id="cep"
                                    class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                    value="{{ $user->cep }}">
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Informe o CEP para
                                    preenchimento automático do endereço.</p>
                            </div>
                            <div class="col-span-12 md:col-span-7">
                                <label for="endereco" class="block text-gray-700 dark:text-gray-300">ENDEREÇO:</label>
                                <input type="text" name="endereco" id="endereco"
                                    class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                    value="{{ $user->endereco }}">
                            </div>
                            <div class="col-span-12 md:col-span-2">
                                <label for="numero" class="block text-gray-700 dark:text-gray-300">NÚMERO:</label>
                                <input type="text" name="numero" id="numero"
                                    class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                    value="{{ $user->numero }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="bairro" class="block text-gray-700 dark:text-gray-300">BAIRRO:</label>
                                <input type="text" name="bairro" id="bairro"
                                    class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                    value="{{ $user->bairro }}">
                            </div>
                            <div>
                                <label for="cidade" class="block text-gray-700 dark:text-gray-300">CIDADE:</label>
                                <input type="text" name="cidade" id="cidade"
                                    class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                    value="{{ $user->cidade }}">
                            </div>
                            <div>
                                <label for="estado" class="block text-gray-700 dark:text-gray-300">ESTADO:</label>
                                <input type="text" name="estado" id="estado"
                                    class="w-full border border-gray-300 p-2 rounded dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                    value="{{ $user->estado }}">
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Atualizar</button>
                            <a href="{{ route('users.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Voltar</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card/Container para o formulário de redefinição de senha -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Redefinir Senha</h3>
                    <form action="{{ route('resetPasswordUser', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="password" class="block text-gray-700">NOVA SENHA:</label>
                            <input type="password" name="password" id="password"
                                class="w-full border border-gray-300 p-2 rounded">
                        </div>
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-gray-700">CONFIRMAR NOVA SENHA:</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="w-full border border-gray-300 p-2 rounded">
                        </div>
                        <div class="flex justify-between">
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Redefinir
                                Senha</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>