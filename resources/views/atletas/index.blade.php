<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lista de Atletas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mx-auto p-4">
                        @if (session('success'))
                            <div class="bg-green-500 text-white p-2 my-4 rounded flash-message" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="flex justify-end mb-4">
                            <a href="{{ route('atletas.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Adicionar Novo Atleta</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg shadow-md">
                                <thead>
                                    <tr class="bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-200 uppercase text-sm leading-normal">
                                        <th class="py-3 px-6 text-left">ID</th>
                                        <th class="py-3 px-6 text-left">Foto</th> {{-- Nova Coluna --}}
                                        <th class="py-3 px-6 text-left">Nome</th>
                                        <th class="py-3 px-6 text-left">CPF</th>
                                        <th class="py-3 px-6 text-left">Celular</th>
                                        <th class="py-3 px-6 text-left">Telefone</th>
                                        <th class="py-3 px-6 text-left">Cidade / UF</th>
                                        <th class="py-3 px-6 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 dark:text-gray-300 text-sm font-light">
                                    @foreach ($atletas as $atleta)
                                        <tr class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $atleta->atl_id }}</td>
                                            <td class="py-3 px-6 text-left">
                                                <img src="{{ $atleta->atl_foto_url }}" alt="Foto {{ $atleta->atl_nome }}" class="h-8 w-8 object-cover rounded-full inline-block">
                                            </td>
                                            <td class="py-3 px-6 text-left">{{ $atleta->atl_nome }}</td>
                                            <td class="py-3 px-6 text-left">{{ $atleta->atl_cpf_formatted }}</td>
                                            <td class="py-3 px-6 text-left">{{ $atleta->atl_cel_formatted }}</td>
                                            <td class="py-3 px-6 text-left">{{ $atleta->atl_tel_formatted }}</td>
                                            <td class="py-3 px-6 text-left">{{ $atleta->atl_cidade }} / {{ $atleta->atl_estado }}</td>
                                            <td class="py-3 px-6 text-center">
                                                <div class="flex item-center justify-center">
                                                    <a href="{{ route('atletas.show', $atleta->atl_id) }}" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('atletas.edit', $atleta->atl_id) }}" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('atletas.destroy', $atleta->atl_id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este atleta?');" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $atletas->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>