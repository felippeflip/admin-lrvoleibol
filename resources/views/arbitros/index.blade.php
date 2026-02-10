<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Juízes e Apontadores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('arbitros.index') }}" class="mb-4">
                        <div class="flex items-center">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Buscar por Nome, Apelido ou Telefone..."
                                class="w-full md:w-1/3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <button type="submit"
                                class="ml-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Pesquisar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('arbitros.index') }}"
                                    class="ml-2 px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Limpar
                                </a>
                            @endif
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto relative">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">Foto</th>
                                    <th scope="col" class="py-3 px-6">Nome</th>
                                    <th scope="col" class="py-3 px-6">Apelido</th>
                                    <th scope="col" class="py-3 px-6">Telefone</th>
                                    <th scope="col" class="py-3 px-6">E-mail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($arbitros as $arbitro)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="py-4 px-6">
                                            @if($arbitro->foto)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $arbitro->foto_url }}" alt="{{ $arbitro->name }}">
                                            @else
                                                <svg class="h-10 w-10 text-gray-400 rounded-full bg-gray-100 p-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $arbitro->name }}
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ $arbitro->apelido ?? '-' }}
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ $arbitro->telefone ?? '-' }}
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ $arbitro->email }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="6" class="py-4 px-6 text-center">Nenhum registro encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $arbitros->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
