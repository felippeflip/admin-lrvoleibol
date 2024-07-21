<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Associar Perfil a Usuário') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl mb-4">Associar Perfil a Usuário</h1>
                    
                    <form action="{{ route('profile_user.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="user_id" class="block text-gray-700">Usuário:</label>
                            <select name="user_id" id="user_id" class="w-full border border-gray-300 p-2 rounded">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="profile_id" class="block text-gray-700">Perfil:</label>
                            <select name="profile_id" id="profile_id" class="w-full border border-gray-300 p-2 rounded">
                                @foreach($profiles as $profile)
                                    <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Salvar</button>
                            <a href="{{ route('profile_user.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
