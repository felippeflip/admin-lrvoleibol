<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Torneio Início - Arquivos Importados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between">
                        <a href="{{ route('torneio-inicio.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">NOVA IMPORTAÇÃO</a>
                    </div>

                    <!-- Mensagem de sucesso -->
                    @if (session('success'))
                        <div class="bg-green-500 text-white font-bold py-2 px-4 rounded mb-4 flash-message" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Mensagem de erro -->
                    @if (session('error'))
                        <div class="bg-red-500 text-white p-2 my-4 rounded mb-4 flash-message" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Table List -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">CATEGORIA</th>
                                    <th scope="col" class="px-6 py-3">ARQUIVO</th>
                                    <th scope="col" class="px-6 py-3">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($arquivosImportados as $categoria)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $categoria->cto_nome }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ $categoria->file_name }}
                                        </td>
                                        <td class="px-6 py-4 flex space-x-3 items-center">
                                            <!-- Visualizar -->
                                            <a href="{{ $categoria->file_url }}" target="_blank" class="text-blue-600 dark:text-blue-500 hover:underline flex items-center" title="Visualizar">
                                                <svg class="w-5 h-5 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <!-- Editar/Substituir -->
                                            <a href="{{ route('torneio-inicio.edit', $categoria->cto_id) }}" class="text-yellow-600 dark:text-yellow-500 hover:underline flex items-center" title="Substituir Arquivo">
                                                <svg class="w-5 h-5 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <!-- Remover -->
                                            <form action="{{ route('torneio-inicio.destroy', $categoria->cto_id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja remover o arquivo desta categoria?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-500 hover:underline flex items-center" title="Excluir Arquivo">
                                                    <svg class="w-5 h-5 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center">Nenhum arquivo importado ainda.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
