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
                        <table id="jogosTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Nº jogo
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Adversários
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Campeonato
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
                                        Juiz 1
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Juiz 2
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Apontador
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jogos as $jogo)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4">
                                            {{ $jogo->meta['_event_number']->meta_value ?? 'N/A' }}
                                        </th>
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
                                            {{ $jogo->referees['principal'] }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $jogo->referees['line1'] }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $jogo->referees['line2'] }}
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

<!-- DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#jogosTable').DataTable({
            "order": [[0, "asc"], [4, "asc"]],
            "language": {
                "decimal": "",
                "emptyTable": "Nenhum dado disponível na tabela",
                "info": "Mostrando _START_ até _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 até 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros no total)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Carregando...",
                "processing": "Processando...",
                "search": "Buscar:",
                "zeroRecords": "Nenhum registro correspondente encontrado",
                "paginate": {
                    "first": "Primeiro",
                    "last": "Último",
                    "next": "Próximo",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": ativar para classificar a coluna em ordem crescente",
                    "sortDescending": ": ativar para classificar a coluna em ordem decrescente"
                }
            },
            "initComplete": function () {
                // Estilizar o elemento select
                $('select[name="jogosTable_length"]').addClass('form-select form-select-sm');
            }
        });
    });
</script>
