<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lista de Usuários') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mx-auto p-4">
                        @if (session('success'))
                            <div class="bg-green-500 text-white p-2 my-4 rounded flash-message" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="mb-6 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                            <form method="GET" action="{{ route('users.index') }}">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                            <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Ativo</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                            <option value="todos" {{ request('status') == 'todos' ? 'selected' : '' }}>Todos</option>
                                        </select>
                                    </div>

                                    <!-- Perfil -->
                                    <div>
                                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Perfil</label>
                                        <select name="role" id="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                            <option value="">Todos</option>
                                            <option value="Administrador" {{ request('role') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                                            <option value="Juiz" {{ request('role') == 'Juiz' ? 'selected' : '' }}>Juiz</option>
                                            <option value="ResponsavelTime" {{ request('role') == 'ResponsavelTime' ? 'selected' : '' }}>Responsável pelo TIME</option>
                                        </select>
                                    </div>

                                    <!-- Botões -->
                                    <div class="flex items-end space-x-2">
                                        <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">Filtrar</button>
                                        <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded w-full text-center">Limpar</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="flex justify-start mb-4">
                            <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Novo Usuário</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg shadow-md">
                                <thead>
                                    <tr class="bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-200 uppercase text-sm leading-normal">
                                        <th class="py-3 px-6 text-left">Apelido</th>
                                        <th class="py-3 px-6 text-left">Nome Completo</th>
                                        <th class="py-3 px-6 text-left">Registro</th>
                                        <th class="py-3 px-6 text-left">Categoria</th>
                                        <th class="py-3 px-6 text-left">Telefone</th>
                                        <th class="py-3 px-6 text-center">Status</th>
                                        <th class="py-3 px-6 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 dark:text-gray-300 text-sm font-light">
                                    @foreach ($users as $user)
                                        <tr class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="py-3 px-6 text-left">{{ $user->apelido }}</td>
                                            <td class="py-3 px-6 text-left">{{ $user->name }}</td>
                                            <td class="py-3 px-6 text-left">{{ $user->cref }}</td>
                                            <td class="py-3 px-6 text-left">{{ $user->tipo_arbitro }}</td>
                                            <td class="py-3 px-6 text-left">{{ $user->telefone }}</td>
                                            <td class="py-3 px-6 text-center">
                                                <span class="{{ $user->active ? 'bg-green-200 text-green-600' : 'bg-red-200 text-red-600' }} py-1 px-3 rounded-full text-xs">
                                                    {{ $user->active ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-6 text-center">
                                                <div class="flex item-center justify-center space-x-2">
                                                    <a href="{{ route('users.edit', $user->id) }}" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" title="Editar Usuário">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </a>
                                                    
                                                    <form action="{{ route('users.toggleStatus', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja {{ $user->active ? 'desativar' : 'ativar' }} este usuário?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" title="{{ $user->active ? 'Desativar Usuário' : 'Ativar Usuário' }}">
                                                            @if($user->active)
                                                                <!-- Ícone para Desativar (Ex: Bloqueio ou Check) -->
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                                </svg>
                                                            @else
                                                                <!-- Ícone para Ativar (Ex: Check) -->
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            @endif
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" onsubmit="return confirm('Tem certeza que deseja remover este usuário?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Excluir Usuário">
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
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
