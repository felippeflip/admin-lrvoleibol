<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Jogos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- ALTERAÇÃO AQUI: de 'max-w-7xl' para 'w-full' --}}
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between inline-flex space-x-2 mb-4">
                        <a href="{{ route('jogos.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">NOVO</a>
                        <a href="{{ route('jogos.showImportForm') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">IMPORTAR JOGOS</a>
                    </div>

                    @if (session('success'))
                        <div id="success-message" class="bg-green-500 text-white font-bold py-2 px-4 rounded mb-4 flash-message" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

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
                        <table id="jogos-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nº Jogo</th>
                                    <th scope="col" class="px-6 py-3">Título</th>
                                    <th scope="col" class="px-6 py-3">Tipo</th>
                                    <th scope="col" class="px-6 py-3">Local</th>
                                    <th scope="col" class="px-6 py-3">Data</th>
                                    <th scope="col" class="px-6 py-3">Hora</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jogos as $jogo)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4">{{ $jogo->meta['_event_number']->meta_value ?? 'N/A' }}</th>
                                        <th scope="row" class="px-6 py-4">{{ $jogo->meta['_event_title']->meta_value ?? 'N/A' }}</th>
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
                                        <td class="px-6 py-4">{{ $jogo->meta['_event_location']->meta_value ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $startDate = isset($jogo->meta['_event_start_date']) ? Carbon\Carbon::parse($jogo->meta['_event_start_date']->meta_value)->format('d/m/Y') : 'N/A';
                                            @endphp
                                            {{ $startDate }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $startTime = isset($jogo->meta['_event_start_time']) ? Carbon\Carbon::parse($jogo->meta['_event_start_time']->meta_value)->format('H:i') : 'N/A';
                                            @endphp
                                            {{ $startTime }}
                                        </td>
                                        <td class="px-6 py-4">{{ $jogo->post_status }}</td>
                                        <td class="px-6 py-4 flex space-x-2">
                                            <a href="{{ route('jogos.edit', $jogo->ID) }}" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" title="Editar Jogo">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('jogos.destroy', $jogo->ID) }}" method="POST" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" onsubmit="return confirmDelete()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Excluir Jogo">
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

    <script>
        function confirmDelete() {
            return confirm('Tem certeza que deseja remover este jogo?');
        }
    </script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#jogos-table').DataTable({
                "order": [[3, "desc"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese-Brasil.json"
                },
                "columnDefs": [
                    {
                        "targets": 3, // Index of the 'Data' column
                        "type": "date-eu" // Define the date format for sorting
                    }
                ]
            });
        });
    </script>
</x-app-layout>