<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Perfis de Usuários') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mx-auto p-4">
                        <h1 class="text-2xl mb-4">Perfis e Usuários</h1>

                        @if (session('success'))
                            <div class="bg-green-500 text-white font-bold py-2 px-4 rounded mb-4 flash-message"
                                role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="mb-6 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                            <form method="GET" action="{{ request()->url() }}">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Usuários -->
                                    <div>
                                        <label for="user"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Usuários</label>
                                        <input type="text" name="user" id="user" value="{{ request('user') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400"
                                            placeholder="Nome ou Email">
                                    </div>

                                    <!-- Perfil -->
                                    <div>
                                        <label for="profile"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Perfil</label>
                                        <select name="profile" id="profile"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                            <option value="">Todos</option>
                                            @foreach($profiles as $profile)
                                                <option value="{{ $profile->id }}" {{ request('profile') == $profile->id ? 'selected' : '' }}>{{ $profile->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Botões -->
                                    <div class="flex items-end space-x-2">
                                        <button type="submit"
                                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">Filtrar</button>
                                        <a href="{{ route('profile_user.index') }}"
                                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded w-full text-center">Limpar</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="flex justify-start mb-4">
                            <a href="{{ route('profile_user.create') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Associar
                                Perfil a Usuário</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg shadow-md">
                                <thead>
                                    <tr
                                        class="bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-200 uppercase text-sm leading-normal">
                                        <th class="py-3 px-6 text-left">Usuário</th>
                                        <th class="py-3 px-6 text-left">Perfil</th>
                                        <th class="py-3 px-6 text-left">Roles (Permissões)</th>
                                        <th class="py-3 px-6 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 dark:text-gray-300 text-sm font-light">
                                    @foreach($users as $user)
                                        <tr
                                            class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="py-3 px-6 text-left">{{ $user->name }} <br> <span
                                                    class="text-xs text-gray-500">{{ $user->email }}</span></td>
                                            <td class="py-3 px-6 text-left">
                                                {{ $user->profile->name ?? 'Nenhum Perfil Associado' }}
                                            </td>
                                            <td class="py-3 px-6 text-left">
                                                @foreach($user->roles as $role)
                                                    <span
                                                        class="inline-block bg-gray-200 dark:bg-gray-600 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 dark:text-gray-300 mr-2 mb-2">{{ $role->name }}</span>
                                                @endforeach
                                            </td>
                                            <td class="py-3 px-6 text-center">
                                                <div class="flex item-center justify-center">
                                                    <a href="{{ route('profile_user.edit', $user->id) }}"
                                                        class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110"
                                                        title="Editar Associação">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('profile_user.destroy', $user->id) }}"
                                                        method="POST"
                                                        class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110"
                                                        onsubmit="return confirmDelete(event)">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Remover Associação">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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

    <!-- Script para confirmação -->
    <script>
        function confirmDelete(event) {
            if (!confirm('Tem certeza que deseja remover este usuário?')) {
                event.preventDefault(); // Impede o envio do formulário se o usuário cancelar
            }
        }
    </script>
</x-app-layout>