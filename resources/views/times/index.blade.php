<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lista de Times') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="container mx-auto p-4">
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

                        {{-- Search Form --}}
                        <div class="mb-6 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                            <form method="GET" action="{{ route('times.index') }}">
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                            <option value="1" {{ request('status', '1') == '1' ? 'selected' : '' }}>Ativo</option>
                                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inativo</option>
                                            <option value="todos" {{ request('status') == 'todos' ? 'selected' : '' }}>Todos</option>
                                        </select>
                                    </div>

                                    <!-- Nome -->
                                    <div class="col-span-2">
                                        <label for="tim_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                                        <input type="text" name="tim_nome" id="tim_nome" value="{{ request('tim_nome') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400" placeholder="Nome do Time">
                                    </div>

                                    <!-- Responsável -->
                                    <div class="col-span-2">
                                        <label for="responsavel" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Responsável</label>
                                        <input type="text" name="responsavel" id="responsavel" value="{{ request('responsavel') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400" placeholder="Nome do Responsável">
                                    </div>

                                    <!-- Botões -->
                                    <div class="flex items-end space-x-2">
                                        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">Filtrar</button>
                                        <a href="{{ route('times.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded w-full text-center">Limpar</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="flex justify-start mb-4">
                            <a href="{{ route('times.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">NOVO TIME</a>
                        </div>

                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">ID</th>
                                        <th scope="col" class="px-6 py-3">Logo</th>
                                        <th scope="col" class="px-6 py-3">Nome</th>
                                        <th scope="col" class="px-6 py-3">Responsável</th>
                                        <th scope="col" class="px-6 py-3">Celular</th>
                                        <th scope="col" class="px-6 py-3">E-mail</th>
                                        <th scope="col" class="px-6 py-3 text-center">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($times as $time)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $time->tim_id }}</th>
                                            <td class="px-6 py-4">
                                                <img src="{{ $time->tim_logo_url }}" alt="Logo {{ $time->tim_nome }}" class="h-10 w-13 object-contain inline-block">
                                            </td>
                                            <td class="px-6 py-4">{{ $time->tim_nome }}</td>
                                            <td class="px-6 py-4">{{ $time->user ? $time->user->name : 'N/A' }}</td>
                                            <td class="px-6 py-4">{{ $time->tim_celular_formatted }}</td>
                                            <td class="px-6 py-4">{{ $time->tim_email }}</td>
                                            <td class="px-6 py-4 flex flex-col space-y-2 justify-center items-center">
                                                {{-- Botões de Ação para o Time --}}
                                                <div class="flex space-x-2 w-full justify-center items-center">
                                                    <a href="{{ route('times.edit', $time->tim_id) }}" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" title="Editar Time">
                                                        {{-- Ícone de Editar --}}
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </a>
                                                    
                                                     {{-- Botão de Ativar/Desativar --}}
                                                    <form action="{{ route('times.inactivate', $time->tim_id) }}" method="POST" class="inline-flex w-4 mr-2 transform hover:text-purple-500 hover:scale-110" onsubmit="return confirm('Tem certeza que deseja {{ $time->tim_status ? 'desativar' : 'ativar' }} este time?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-full h-full" title="{{ $time->tim_status ? 'Desativar' : 'Ativar' }} Time">
                                                            @if($time->tim_status)
                                                                {{-- Ícone de Desativar (Block/Ban) ou similar --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                                </svg>
                                                            @else
                                                                {{-- Ícone de Ativar (Check/Play) --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            @endif
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('times.destroy', $time->tim_id) }}" method="POST" class="inline-flex w-4 mr-2 transform hover:text-purple-500 hover:scale-110" onsubmit="return confirm('Tem certeza que deseja excluir este time?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full h-full" title="Excluir Time">
                                                            {{-- Ícone de Deletar/Lixeira --}}
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                                {{-- Botões de Ação para Equipes --}}
                                                <div class="flex space-x-2 w-full justify-center items-center">
                                                    <a href="{{ route('equipes.create', ['time_id' => $time->tim_id]) }}" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" title="Adicionar Equipe">
                                                        {{-- Ícone de Adicionar Equipe --}}
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('equipes.index.for.time', $time->tim_id) }}" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" title="Visualizar Equipes">
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

                        <div class="mt-4">
                            {{ $times->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
