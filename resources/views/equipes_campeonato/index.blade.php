<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Equipes do Campeonato: ') . $campeonato->cpo_nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-start mb-4 gap-2">
                        <a href="{{ route('equipes.campeonato.create', $campeonato->cpo_id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">ADICIONAR EQUIPE</a>
                        <a href="{{ route('eventos.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">VOLTAR</a>
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
                                            {{-- Botão de remover a equipe do campeonato --}}
                                            <form action="{{ route('equipes.campeonato.destroy', ['campeonato' => $campeonato->cpo_id, 'equipe' => $equipe->eqp_id]) }}" method="POST" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" onsubmit="return confirm('Tem certeza que deseja remover esta equipe do campeonato?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Remover Equipe do Campeonato">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
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
