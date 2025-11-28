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
                                            <form action="{{ route('equipes.campeonato.destroy', ['campeonato' => $campeonato->cpo_id, 'equipe' => $equipe->eqp_id]) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja remover esta equipe do campeonato?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-xs">Remover</button>
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
