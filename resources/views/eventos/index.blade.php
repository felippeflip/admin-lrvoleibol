<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Eventos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">


                    <div class="flex justify-between">
                        <a href="{{ route('eventos.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">NOVO</a>
                    </div>
                   <!-- Table List -->

                   

<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    @if ($errors->any())
        <div class="bg-red-500 text-white p-2 my-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    EVENTO
                </th>
                <th scope="col" class="px-6 py-3">
                    DESCRIÇÃO
                </th>
                <th scope="col" class="px-6 py-3">
                    SLUG
                </th>
            </tr>
        </thead>
        <tbody>

            @foreach ($eventos as $evento)

            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <th scope="row" class="px-6 py-4">
                    {{ $evento->term->name}}
                </th>
                <td class="px-6 py-4">
                    {{ $evento->description }}
                </td>
                <td class="px-6 py-4">
                    {{ $evento->term->slug }}
                </td>
                <td class="px-6 py-4 flex space-x-2">
                    <a href="{{ route('eventos.edit', $evento->term->term_id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Editar</a>
                    <form action="{{ route('eventos.destroy', $evento->term->term_id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Deletar</button>
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
