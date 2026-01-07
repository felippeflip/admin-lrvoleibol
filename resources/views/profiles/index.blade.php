<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Perfis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl mb-4">Perfis</h1>
                    <a href="{{ route('profiles.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Novo Perfil</a>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">Nome</th>
                                    <th class="py-2 px-4 border-b">Funções</th>
                                    <th class="py-2 px-4 border-b">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($profiles as $profile)
                                    <tr>
                                        <td class="py-2 px-4 border-b">{{ $profile->name }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @foreach($profile->roles as $role)
                                                <span class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-full px-2 py-1 text-xs font-semibold">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="flex space-x-2">
                                            <a href="{{ route('profiles.edit', $profile) }}" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" title="Editar Perfil">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('profiles.destroy', $profile) }}" method="POST" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" onsubmit="return confirm('Tem certeza que deseja excluir este perfil?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Excluir Perfil">
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
