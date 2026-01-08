<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Eventos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-start mb-4">
                        <a href="{{ route('eventos.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">NOVO</a>
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

                    <!-- Filtros -->
                    <div class="mb-6 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                        <form method="GET" action="{{ route('eventos.index') }}">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Busca por Nome -->
                                <div>
                                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Campeonato</label>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white placeholder-gray-400" placeholder="Nome do campeonato">
                                </div>

                                <!-- Ano -->
                                <div>
                                    <label for="ano" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ano</label>
                                    <select name="ano" id="ano" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white">
                                        <option value="">Todos</option>
                                        @for ($year = date('Y') + 1; $year >= 2000; $year--)
                                            <option value="{{ $year }}" {{ request('ano') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="ativo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select name="ativo" id="ativo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white">
                                        <option value="">Todos</option>
                                        <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Ativo</option>
                                        <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Inativo</option>
                                    </select>
                                </div>

                                <!-- Botões -->
                                <div class="flex items-end space-x-2">
                                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">Filtrar</button>
                                    <a href="{{ route('eventos.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded w-full text-center">Limpar</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">CAMPEONATO</th>
                                    <th scope="col" class="px-6 py-3">ANO</th>
                                    <th scope="col" class="px-6 py-3">DATA INICIO</th>
                                    <th scope="col" class="px-6 py-3">DATA FIM</th>
                                    <th scope="col" class="px-6 py-3 text-center">STATUS</th>
                                    <th scope="col" class="px-6 py-3 text-center">AÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campeonatos as $campeonato)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $campeonato->cpo_nome }}</th>
                                        <td class="px-6 py-4">{{ $campeonato->cpo_ano }}</td>
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($campeonato->cpo_dt_inicio)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($campeonato->cpo_dt_fim)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if($campeonato->cpo_ativo)
                                                <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Ativo</span>
                                            @else
                                                <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Inativo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 flex flex-col space-y-2 justify-center items-center">
                                            {{-- Botões de Ação do Campeonato --}}
                                            <div class="flex space-x-2 w-full justify-center items-center">
                                                <a href="{{ route('eventos.edit', $campeonato->cpo_id) }}" title="Editar" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                    {{-- Ícone de Editar --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('eventos.destroy', $campeonato->cpo_id) }}" method="POST" title="Excluir" class="inline-flex w-4 mr-2 transform hover:text-purple-500 hover:scale-110" onsubmit="return confirm('Tem certeza que deseja excluir este evento?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full h-full">
                                                        {{-- Ícone de Deletar/Lixeira --}}
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            {{-- Novos botões para Equipes --}}
                                            <div class="flex space-x-2 w-full justify-center items-center">
                                                <a href="{{ route('equipes.campeonato.create', ['campeonato' => $campeonato->cpo_id]) }}" title="Adicionar" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                    {{-- Ícone de Adicionar Equipe --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('equipes.campeonato.index', ['campeonato' => $campeonato->cpo_id]) }}" title="Listar Times" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                    {{-- Ícone de Visualizar Equipes --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    </div>

                    <div class="mt-4">
                        {{ $campeonatos->links() }}
                    </div>

            </div>
        </div>
    </div>
</x-app-layout>
