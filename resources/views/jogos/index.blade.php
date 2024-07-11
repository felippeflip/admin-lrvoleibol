<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Jogos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between">
                        <a href="{{ route('jogos.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">NOVO</a>
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
                    Título
                </th>
                <th scope="col" class="px-6 py-3">
                    Tipo
                </th>
                <th scope="col" class="px-6 py-3">
                    Local
                </th>
                <th scope="col" class="px-6 py-3">
                    Data
                </th>
                <th scope="col" class="px-6 py-3">
                    Hora
                </th>
                <th scope="col" class="px-6 py-3">
                    Status
                </th>
                <th scope="col" class="px-6 py-3">
                    Ações
                </th>
            </tr>
        </thead>
        <tbody>

            @foreach ($jogos as $jogo)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4">
                        {{ $jogo->meta['_event_title']->meta_value ?? 'N/A' }}
                    </th>
                    <td class="px-6 py-4">
                        @php
                            $eventType = 'N/A';
                            if (!empty($jogo->term_relationships)) {
                                foreach ($jogo->term_relationships as $relationship) {
                                    if (isset($relationship->term_taxonomy) && $relationship->term_taxonomy->taxonomy == 'event_listing_type' && isset($relationship->term_taxonomy->term)) {
                                        $eventType = $relationship->term_taxonomy->term->name;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        {{ $eventType }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $jogo->meta['_event_location']->meta_value ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $startDate = isset($jogo->meta['_event_start_date']) ? Carbon\Carbon::parse($jogo->meta['_event_start_date']->meta_value)->format('d/m/Y') : 'N/A';
                        @endphp
                        {{ $startDate }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $jogo->meta['_event_start_time']->meta_value ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $jogo->post_status }}
                    </td>
                    <td class="px-6 py-4 flex space-x-2">
                        <a href="{{ route('jogos.edit', $jogo->ID) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Editar</a>
                        <form action="{{ route('jogos.destroy', $jogo->ID) }}" method="POST" class="inline">
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

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
