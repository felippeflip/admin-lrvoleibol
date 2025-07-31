<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Funções e Permissões') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mx-auto p-4">
                        <h1 class="text-2xl font-bold mb-4">Funções e Permissões</h1>

                        <!-- Mensagem de sucesso -->
                        @if (session('success'))
                            <div class="bg-green-500 text-white p-2 mb-4 rounded flash-message" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Mensagem de erro -->
                        @if ($errors->any())
                            <div class="bg-red-500 text-white p-2 mb-4 rounded">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-4">
                            <a href="{{ route('role-permission.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Criar Nova Função
                            </a>
                        </div>
                        
                        @foreach($roles as $role)
                            <div class="mb-6 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-md">
                                <div class="flex justify-between items-center mb-4">
                                    <strong class="text-lg font-semibold">{{ $role->name }}</strong>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('role-permission.edit', $role) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Editar
                                        </a>
                                        <form action="{{ route('role-permission.destroy', $role) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h3 class="text-md font-semibold mb-2">Permissões:</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ implode(' | ', $role->permissions->pluck('name')->toArray()) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
