<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Adicionar Nova Equipe') }}
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
                        <form action="{{ route('equipes.store') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="eqp_time_id"
                                    class="block text-gray-700 dark:text-gray-300 mb-2">Time:</label>
                                <select name="eqp_time_id" id="eqp_time_id"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    @if($times->count() > 1)
                                        <option value="">Selecione um Time</option>
                                    @endif
                                    @foreach ($times as $time)
                                        <option value="{{ $time->tim_id }}" {{ (old('eqp_time_id') == $time->tim_id || $times->count() == 1) ? 'selected' : '' }}>
                                            {{ $time->tim_nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($times->isEmpty())
                                    <p class="text-red-500 text-xs mt-1">Nenhum time vinculado ao seu perfil encontrado.</p>
                                @endif
                                @error('eqp_time_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="eqp_categoria_id"
                                    class="block text-gray-700 dark:text-gray-300 mb-2">Categoria:</label>
                                <select name="eqp_categoria_id" id="eqp_categoria_id"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                    <option value="">Selecione uma Categoria</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->cto_id }}" {{ old('eqp_categoria_id') == $categoria->cto_id ? 'selected' : '' }}>
                                            {{ $categoria->cto_nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('eqp_categoria_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="eqp_nome_detalhado" class="block text-gray-700 dark:text-gray-300 mb-2">Nome
                                    Detalhado da Equipe:</label>
                                <input type="text" name="eqp_nome_detalhado" id="eqp_nome_detalhado"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                    value="{{ old('eqp_nome_detalhado') }}" required>
                                @error('eqp_nome_detalhado')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="eqp_nome_treinador" class="block text-gray-700 dark:text-gray-300 mb-2">Nome
                                    do Treinador:</label>
                                <input type="text" name="eqp_nome_treinador" id="eqp_nome_treinador"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500"
                                    value="{{ old('eqp_nome_treinador') }}">
                                @error('eqp_nome_treinador')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="campeonato_id" class="block text-gray-700 dark:text-gray-300 mb-2">Inscrever
                                    em Campeonato (Opcional):</label>
                                <select name="campeonato_id" id="campeonato_id"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Nenhum (Apenas criar equipe)</option>
                                    @foreach ($campeonatos as $campeonato)
                                        <option value="{{ $campeonato->cpo_id }}" {{ old('campeonato_id') == $campeonato->cpo_id ? 'selected' : '' }}>
                                            {{ $campeonato->cpo_nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-gray-500 text-xs mt-1">Se selecionado, a equipe será criada e
                                    automaticamente inscrita neste campeonato.</p>
                            </div>

                            <div class="flex justify-start mt-6 space-x-4">
                                <button type="submit"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Salvar</button>
                                <a href="{{ route('equipes.index') }}"
                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>