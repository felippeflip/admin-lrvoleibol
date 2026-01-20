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
                                                        <a href="{{ route('jogos.index', ['campeonato_id' => $stat['id']]) }}" class="text-blue-600 hover:text-blue-900 hover:underline">
                                                            {{ $stat['campeonato'] }}
                                                        </a>
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
                                                <li class="py-4">
                                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start space-y-2 sm:space-y-0">
                                                        <!-- Game Info -->
                                                        <div class="flex flex-col">
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">
                                                                    #{{ $jogo->jgo_id }}
                                                                </span>
                                                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                                                    {{ $jogo->mandante && $jogo->mandante->campeonato ? $jogo->mandante->campeonato->cpo_nome : 'Campeonato Indefinido' }}
                                                                </span>
                                                            </div>
                                                            
                                                            <div class="flex items-center gap-2 text-md text-gray-700 dark:text-gray-300">
                                                                <span class="font-medium">
                                                                    {{ $jogo->mandante && $jogo->mandante->equipe ? $jogo->mandante->equipe->eqp_nome_detalhado : '?' }}
                                                                </span>
                                                                <span class="text-xs text-gray-400">vs</span>
                                                                <span class="font-medium">
                                                                    {{ $jogo->visitante && $jogo->visitante->equipe ? $jogo->visitante->equipe->eqp_nome_detalhado : '?' }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- Date & Location -->
                                                        <div class="flex flex-col sm:items-end text-sm text-gray-500 dark:text-gray-400">
                                                            <div class="flex items-center gap-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                                <span>{{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }}</span>
                                                                <span class="mx-1">•</span>
                                                                <span>{{ \Carbon\Carbon::parse($jogo->jgo_hora_jogo)->format('H:i') }}</span>
                                                            </div>
                                                            
                                                            @if($jogo->ginasio)
                                                                <a href="{{ $jogo->ginasio->google_maps_link }}" target="_blank" class="mt-1 flex items-center gap-1 text-blue-600 hover:text-blue-800 hover:underline">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                    {{ $jogo->ginasio->gin_nome }}
                                                                </a>
                                                            @else
                                                                <span class="mt-1 flex items-center gap-1">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                    Local não definido
                                                                </span>
                                                            @endif
                                                        </div>
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
                                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-6">
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

                                    <!-- Filters -->
                                    <div class="mb-4">
                                        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row gap-4">
                                            <div class="w-full sm:w-1/3">
                                                <label for="search" class="sr-only">Buscar</label>
                                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                                    placeholder="Buscar por ID ou Campeonato..."
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>
                                            <div class="w-full sm:w-1/3">
                                                <label for="status" class="sr-only">Status</label>
                                                <select name="status" id="status"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    <option value="ativo" {{ request('status', 'ativo') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                                    <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                                    <option value="todos" {{ request('status') == 'todos' ? 'selected' : '' }}>Todos</option>
                                                </select>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    Filtrar
                                                </button>
                                                <a href="{{ route('dashboard') }}"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                                    Limpar
                                                </a>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Games List -->
                                    @if(isset($timeJogos) && $timeJogos->count() > 0)
                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3">Número</th>
                                                        <th scope="col" class="px-6 py-3">Campeonato</th>
                                                        <th scope="col" class="px-6 py-3">Equipes</th>
                                                        <th scope="col" class="px-6 py-3">Local</th>
                                                        <th scope="col" class="px-6 py-3">Data/Hora</th>
                                                        <th scope="col" class="px-6 py-3">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($timeJogos as $jogo)
                                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                #{{ $jogo->jgo_id }}
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                {{ $jogo->mandante && $jogo->mandante->campeonato ? $jogo->mandante->campeonato->cpo_nome : '-' }}
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                <div class="flex flex-col">
                                                                    <span class="font-bold text-gray-900 dark:text-white">
                                                                        {{ $jogo->mandante && $jogo->mandante->equipe ? $jogo->mandante->equipe->eqp_nome_detalhado : '?' }}
                                                                    </span>
                                                                    <span class="text-xs text-center text-gray-400">vs</span>
                                                                    <span class="font-bold text-gray-900 dark:text-white">
                                                                        {{ $jogo->visitante && $jogo->visitante->equipe ? $jogo->visitante->equipe->eqp_nome_detalhado : '?' }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                @if($jogo->ginasio)
                                                                    <a href="{{ $jogo->ginasio->google_maps_link }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                        {{ $jogo->ginasio->gin_nome }}
                                                                    </a>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }} <br>
                                                                {{ substr($jogo->jgo_hora_jogo, 0, 5) }}
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                @if($jogo->jgo_status == 'ativo')
                                                                    <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Ativo</span>
                                                                @else
                                                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">Inativo</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="mt-4 px-2">
                                                {{ $timeJogos->links() }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-md">
                                            <p class="text-yellow-700 dark:text-yellow-300">Nenhum jogo encontrado com os filtros selecionados.</p>
                                        </div>
                                    @endif
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