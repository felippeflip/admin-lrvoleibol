<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Categorias') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between">
                        <a href="{{ route('categorias.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">NOVO</a>
                    </div>

                    <!-- Mensagem de sucesso -->
                    @if (session('success'))
                        <div class="bg-green-500 text-white font-bold py-2 px-4 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Mensagem de erro -->
                    @if ($errors->any())
                        <div class="bg-red-500 text-white p-2 my-4 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Table List -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">CATEGORIA</th>
                                    <th scope="col" class="px-6 py-3">DESCRIÇÃO</th>
                                    <th scope="col" class="px-6 py-3">SLUG</th>
                                    <th scope="col" class="px-6 py-3">AÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categorias as $categoria)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4">{{ $categoria->cto_nome }}</th>
                                        <td class="px-6 py-4">{{ $categoria->cto_descricao }}</td>
                                        <td class="px-6 py-4">{{ $categoria->cto_slug }}</td>
                                        <td class="px-6 py-4 flex space-x-2">
                                            <a href="{{ route('categorias.edit', $categoria->cto_id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Editar</a>
                                            <form action="{{ route('categorias.destroy', $categoria->cto_id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja remover esta categoria?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Deletar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
