<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lista de Times') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between">
                        <a href="{{ route('times.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">NOVO TIME</a>
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
                                    <th scope="col" class="px-6 py-3">ID</th>
                                    <th scope="col" class="px-6 py-3">Logo</th>
                                    <th scope="col" class="px-6 py-3">Nome</th>
                                    <th scope="col" class="px-6 py-3">Responsável</th>
                                    <th scope="col" class="px-6 py-3">Celular</th>
                                    <th scope="col" class="px-6 py-3">Telefone</th>
                                    <th scope="col" class="px-6 py-3 text-center">AÇÃO</th> {{-- Alinhe AÇÕES ao centro se preferir --}}
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
                                        <td class="px-6 py-4">{{ $time->tim_telefone_formatted }}</td>
                                        <td class="px-6 py-4 flex space-x-2 justify-center"> {{-- Centralizando os botões de ação --}}
                                            <a href="{{ route('times.edit', $time->tim_id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Editar</a>
                                            <form action="{{ route('times.destroy', $time->tim_id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este time?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Deletar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Manter a paginação se você a estiver usando --}}
                    <div class="mt-4">
                        {{ $times->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>