<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($time) ? __('Equipes do Time: ') . $time->tim_nome : __('Lista de Equipes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-start mb-4">
                        {{-- Ação para criar uma nova equipe, com o time pré-selecionado se aplicável --}}
                        <a href="{{ isset($time) ? route('equipes.create', ['time_id' => $time->tim_id]) : route('equipes.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">NOVA EQUIPE</a>
                    </div>
                    
                    @if (session('success'))
                        <div class="bg-green-500 text-white font-bold py-2 px-4 rounded mb-4 flash-message" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-500 text-white p-2 my-4 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">NOME EQUIPE</th>
                                    <th scope="col" class="px-6 py-3">TIME</th>
                                    <th scope="col" class="px-6 py-3">CATEGORIA</th>
                                    <th scope="col" class="px-6 py-3">TREINADOR</th>
                                    <th scope="col" class="px-6 py-3 text-center">AÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($equipes as $equipe)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $equipe->eqp_nome_detalhado }}</th>
                                        <td class="px-6 py-4">{{ $equipe->time->tim_nome ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $equipe->categoria->cto_nome ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $equipe->eqp_nome_treinador ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 flex space-x-2 justify-center">
                                            <a href="{{ route('equipes.edit', $equipe->eqp_id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-xs">Editar</a>
                                            <form action="{{ route('equipes.destroy', $equipe->eqp_id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja remover esta equipe?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-xs">Deletar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $equipes->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
