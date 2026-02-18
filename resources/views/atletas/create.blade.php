<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Adicionar Novo Atleta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
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
                        <form action="{{ route('atletas.store') }}" method="POST" enctype="multipart/form-data"
                            id="atletaForm">
                            @csrf

                            @if(isset($times) && count($times) > 0)
                                <div class="mb-4">
                                    <label for="atl_tim_id" class="block text-gray-700 dark:text-gray-300 mb-2">Time/Equipe
                                        *:</label>
                                    <select name="atl_tim_id" id="atl_tim_id"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                        <option value="">Selecione um Time</option>
                                        @foreach($times as $time)
                                            <option value="{{ $time->tim_id }}" {{ old('atl_tim_id') == $time->tim_id ? 'selected' : '' }}>{{ $time->tim_nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-1 flex flex-col items-center justify-center">
                                    <label for="atl_foto" class="block text-gray-700 dark:text-gray-300 mb-2">Foto do
                                        Atleta</label>
                                    <div
                                        class="w-36 h-48 border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden flex items-center justify-center mb-2">
                                        <img id="fotoPreview" src="{{ asset('images/placeholder-atleta.png') }}"
                                            alt="Pré-visualização da foto" class="w-full h-full object-cover">
                                    </div>
                                    <input name="atl_foto" id="atl_foto" type="file"
                                        accept="image/jpeg, image/png, image/jpg"
                                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                        aria-describedby="foto_help">
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-300" id="foto_help">Tamanho
                                        ideal: 3x4 (proporção)</p>
                                </div>
                                <div class="md:col-span-3">
                                    <div class="mb-4">
                                        <label for="atl_nome" class="block text-gray-700 dark:text-gray-300 mb-2">Nome
                                            Completo *:</label>
                                        <input type="text" name="atl_nome" id="atl_nome"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                            value="{{ old('atl_nome') }}" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="atl_resg" class="block text-gray-700 dark:text-gray-300 mb-2">Registro
                                            LRV:</label>
                                        <input type="text" name="atl_resg" id="atl_resg"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                            value="{{ old('atl_resg') }}">
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label for="atl_cpf"
                                                class="block text-gray-700 dark:text-gray-300 mb-2">CPF:</label>
                                            <input type="text" name="atl_cpf" id="atl_cpf"
                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                                value="{{ old('atl_cpf') }}" maxlength="14">
                                        </div>
                                        <div>
                                            <label for="atl_rg"
                                                class="block text-gray-700 dark:text-gray-300 mb-2">RG:</label>
                                            <input type="text" name="atl_rg" id="atl_rg"
                                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                                value="{{ old('atl_rg') }}" maxlength="12">
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="atl_email"
                                            class="block text-gray-700 dark:text-gray-300 mb-2">E-mail:</label>
                                        <input type="email" name="atl_email" id="atl_email"
                                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                            value="{{ old('atl_email') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label for="atl_celular"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Celular:</label>
                                    <input type="text" name="atl_celular" id="atl_celular"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('atl_celular') }}" maxlength="15">
                                </div>
                                <div>
                                    <label for="atl_telefone"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Telefone:</label>
                                    <input type="text" name="atl_telefone" id="atl_telefone"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('atl_telefone') }}" maxlength="14">
                                </div>
                                <div>
                                    <label for="atl_sexo"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Sexo:</label>
                                    <select name="atl_sexo" id="atl_sexo"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Selecione</option>
                                        <option value="M" {{ old('atl_sexo') == 'M' ? 'selected' : '' }}>Masculino
                                        </option>
                                        <option value="F" {{ old('atl_sexo') == 'F' ? 'selected' : '' }}>Feminino</option>
                                        <option value="O" {{ old('atl_sexo') == 'O' ? 'selected' : '' }}>Outro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="atl_dt_nasc" class="block text-gray-700 dark:text-gray-300 mb-2">Data de
                                        Nascimento:</label>
                                    <input type="date" name="atl_dt_nasc" id="atl_dt_nasc"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('atl_dt_nasc') }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="atl_cep" class="block text-gray-700 dark:text-gray-300 mb-2">CEP:</label>
                                <input type="text" name="atl_cep" id="atl_cep"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                    value="{{ old('atl_cep') }}" maxlength="9">
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Informe o CEP para
                                    preenchimento automático do endereço.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-3">
                                    <label for="atl_endereco"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Endereço:</label>
                                    <input type="text" name="atl_endereco" id="atl_endereco"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('atl_endereco') }}">
                                </div>
                                <div class="md:col-span-1">
                                    <label for="atl_numero"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Número:</label>
                                    <input type="text" name="atl_numero" id="atl_numero"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('atl_numero') }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="atl_bairro"
                                    class="block text-gray-700 dark:text-gray-300 mb-2">Bairro:</label>
                                <input type="text" name="atl_bairro" id="atl_bairro"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                    value="{{ old('atl_bairro') }}">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-3">
                                    <label for="atl_cidade"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Cidade:</label>
                                    <input type="text" name="atl_cidade" id="atl_cidade"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('atl_cidade') }}">
                                </div>
                                <div class="md:col-span-1">
                                    <label for="atl_estado"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">UF:</label>
                                    <input type="text" name="atl_estado" id="atl_estado"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('atl_estado') }}" maxlength="2">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="atl_categoria"
                                        class="block text-gray-700 dark:text-gray-300 mb-2">Categoria:</label>
                                    <select name="atl_categoria" id="atl_categoria"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Selecione a Categoria</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->cto_id }}" {{ old('atl_categoria') == $categoria->cto_id ? 'selected' : '' }}>
                                                {{ $categoria->cto_nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <div class="flex items-center h-10">
                                        <input id="cartao_impresso_ano_atual" name="cartao_impresso_ano_atual"
                                            type="checkbox" value="1"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                            }}
                                            {{ !auth()->user()->hasRole('Administrador') ? 'disabled' : '' }}>
                                        <label for="cartao_impresso_ano_atual"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Cartão da
                                            Liga {{ date('Y') }} Impresso?</label>
                                    </div>
                                </div>
                                <div>
                                    <label for="atl_ano_insc" class="block text-gray-700 dark:text-gray-300 mb-2">Ano
                                        Inscrição:</label>
                                    <select name="atl_ano_insc" id="atl_ano_insc"
                                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Selecione o Ano</option>
                                        @php
                                            $currentYear = date('Y');
                                            $years = range($currentYear + 3, $currentYear - 3);
                                        @endphp
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ old('atl_ano_insc', $currentYear) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-start mt-6 space-x-4">
                                <button type="submit"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Salvar</button>
                                <a href="{{ route('atletas.index') }}"
                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fotoInput = document.getElementById('atl_foto');
            const fotoPreview = document.getElementById('fotoPreview');

            if (fotoInput && fotoPreview) {
                fotoInput.addEventListener('change', function (event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            fotoPreview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        fotoPreview.src = "{{ asset('images/placeholder-atleta.png') }}";
                    }
                });
            }
        });
    </script>
</x-app-layout>