<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Jogos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Dashboard Content -->
                    <div class="space-y-6">

                        {{-- 1. SECTION: ADMINISTRATOR --}}
                        @if(isset($adminStats))
                            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border-l-4 border-blue-500">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">Painel do
                                    Administrador</h3>

                                <div class="relative overflow-x-auto">
                                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">Campeonato</th>
                                                <th scope="col" class="px-6 py-3 text-center">Jogos Novos</th>
                                                <th scope="col" class="px-6 py-3 text-center">Finalizados</th>
                                                <th scope="col" class="px-6 py-3 text-center">Com Apontamento</th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-center bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400">
                                                    Pendente Apontamento</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($adminStats as $stat)
                                                <tr
                                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <th scope="row"
                                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{ $stat['campeonato'] }}
                                                    </th>
                                                    <td class="px-6 py-4 text-center font-bold text-blue-600">
                                                        {{ $stat['novos'] }}
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        {{ $stat['finalizados'] }}
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-green-600">
                                                        {{ $stat['com_apontamento'] }}
                                                    </td>
                                                    <td
                                                        class="px-6 py-4 text-center font-bold text-red-600 bg-red-50 dark:bg-red-900/10">
                                                        {{ $stat['sem_apontamento'] }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-6 py-4 text-center">Nenhum campeonato ativo
                                                        encontrada.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- 2. SECTION: JUIZ/ARBITRO --}}
                        @if(isset($juizStats))
                            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border-l-4 border-yellow-500">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">Painel do
                                    Árbitro / Mesário</h3>

                                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                                    <div
                                        class="px-4 py-5 bg-gray-50 dark:bg-gray-700 shadow rounded-lg overflow-hidden sm:p-6">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total de
                                            Escalações</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                                            {{ $juizStats['total_participacao'] }}</dd>
                                    </div>
                                    <div
                                        class="px-4 py-5 bg-blue-50 dark:bg-blue-900/20 shadow rounded-lg overflow-hidden sm:p-6">
                                        <dt class="text-sm font-medium text-blue-600 dark:text-blue-400 truncate">Jogos
                                            Futuros</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-blue-900 dark:text-blue-100">
                                            {{ $juizStats['novos'] }}</dd>
                                    </div>
                                    <div
                                        class="px-4 py-5 bg-green-50 dark:bg-green-900/20 shadow rounded-lg overflow-hidden sm:p-6">
                                        <dt class="text-sm font-medium text-green-600 dark:text-green-400 truncate">Jogos
                                            Realizados</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-green-900 dark:text-green-100">
                                            {{ $juizStats['realizados'] }}</dd>
                                    </div>
                                </dl>

                                {{-- Optional: List upcoming games for Juiz --}}
                                @if(isset($juizJogosFuturos) && $juizJogosFuturos->count() > 0)
                                    <div class="mt-6">
                                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-200 mb-2">Seus Próximos
                                            Jogos</h4>
                                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($juizJogosFuturos as $jogo)
                                                <li class="py-3 flex justify-between items-center">
                                                    <div class="flex flex-col">
                                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                            Jogo #{{ $jogo->jgo_id }}
                                                        </span>
                                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }} às
                                                            {{ $jogo->jgo_hora_jogo }}
                                                        </span>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        <!-- Location or other info -->
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- 3. SECTION: RESPONSAVEL PELO TIME --}}
                        @if(isset($timeStats))
                            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border-l-4 border-indigo-500">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">Painel do
                                    Time</h3>

                                @if($timeStats)
                                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                                        <div
                                            class="px-4 py-5 bg-gray-50 dark:bg-gray-700 shadow rounded-lg overflow-hidden sm:p-6">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total de
                                                Jogos no Campeonato</dt>
                                            <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                                                {{ $timeStats['escalado_total'] }}</dd>
                                        </div>
                                        <div
                                            class="px-4 py-5 bg-green-50 dark:bg-green-900/20 shadow rounded-lg overflow-hidden sm:p-6">
                                            <dt class="text-sm font-medium text-green-600 dark:text-green-400 truncate">Jogos
                                                Concluídos</dt>
                                            <dd class="mt-1 text-3xl font-semibold text-green-900 dark:text-green-100">
                                                {{ $timeStats['concluidos'] }}</dd>
                                        </div>
                                        <div
                                            class="px-4 py-5 bg-indigo-50 dark:bg-indigo-900/20 shadow rounded-lg overflow-hidden sm:p-6">
                                            <dt class="text-sm font-medium text-indigo-600 dark:text-indigo-400 truncate">Jogos
                                                Agendados</dt>
                                            <dd class="mt-1 text-3xl font-semibold text-indigo-900 dark:text-indigo-100">
                                                {{ $timeStats['proximos'] }}</dd>
                                        </div>
                                    </dl>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">Você tem o perfil de Responsável de Time, mas
                                        nenhum time está vinculado ao seu usuário.</p>
                                @endif
                            </div>
                        @endif

                        @if(!isset($adminStats) && !isset($juizStats) && !isset($timeStats))
                            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                                <p>Bem-vindo ao sistema Admin LRVoleibol.</p>
                            </div>
                        @endif

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
    $(document).ready(function () {
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