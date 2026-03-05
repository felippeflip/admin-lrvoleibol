<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Importar Arquivo HTML - Torneio Início') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="bg-red-500 text-white p-2 my-4 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-500 text-white p-2 my-4 rounded mb-4 flash-message" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Import Form -->
                    <form action="{{ route('torneio-inicio.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <label for="categoria_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoria <span class="text-red-500">*</span></label>
                                <select id="categoria_id" name="categoria_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                    <option value="">Selecione a Categoria</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->cto_id }}">{{ $cat->cto_nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="arquivo">Arquivo HTML (.html) <span class="text-red-500">*</span></label>
                                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="arquivo" name="arquivo" type="file" accept=".html" required>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">O arquivo será renomeado automaticamente com o padrão da categoria escolhida (Ex: sub13-feminino.html).</div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-4">
                            <a href="{{ route('torneio-inicio.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">VOLTAR</a>
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">IMPORTAR</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
