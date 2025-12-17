<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Criar Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mx-auto p-4">
                        @if ($errors->any())
                            <div class="bg-red-500 text-white p-2 my-4">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <h1 class="text-2xl mb-4">Criar Nova Função</h1>
                        <form action="{{ route('role-permission.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="role_name" class="block text-gray-700">Nome da Função</label>
                                <input type="text" name="role_name" id="role_name" class="w-full border border-gray-300 p-2 rounded" required>
                            </div>
                            <div class="mb-4">
                                <label for="permissions" class="block text-gray-700">Permissões</label>
                                <select name="permissions[]" id="permissions" class="w-full border border-gray-300 p-2 rounded" multiple>
                                    @foreach($permissions as $permission)
                                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex justify-between">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Criar</button>
                                <a href="{{ route('role-permission.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Voltar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
