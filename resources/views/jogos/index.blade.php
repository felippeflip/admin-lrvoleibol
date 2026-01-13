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

                    {{-- Actions --}}
                    <div class="flex justify-between items-center mb-6">
                        <div class="space-x-2">
                            <a href="{{ route('jogos.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">NOVO JOGO</a>
                            <a href="{{ route('jogos.showImportForm') }}" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">IMPORTAR</a>
                        </div>
                    </div>

                    {{-- Filter Form --}}
                    <form method="GET" action="{{ route('jogos.index') }}" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            
                            <!-- Título -->
                            <div>
                                <label for="titulo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título</label>
                                <input type="text" id="titulo" name="titulo" value="{{ request('titulo') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Ex: Time A x Time B">
                            </div>

                            <!-- Campeonato -->
                            <div>
                                <label for="campeonato_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Campeonato</label>
                                <select id="campeonato_id" name="campeonato_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                    <option value="">Todos</option>
                                    @foreach($campeonatos as $camp)
                                        <option value="{{ $camp->cpo_id }}" {{ request('campeonato_id') == $camp->cpo_id ? 'selected' : '' }}>{{ $camp->cpo_nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Local (Ginásio) -->
                            <div>
                                <label for="ginasio_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Local</label>
                                <select id="ginasio_id" name="ginasio_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                    <option value="">Todos</option>
                                    @foreach($ginasios as $gin)
                                        <option value="{{ $gin->gin_id }}" {{ request('ginasio_id') == $gin->gin_id ? 'selected' : '' }}>{{ $gin->gin_nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Data (Período) -->
                            <div class="flex space-x-2">
                                <div class="w-1/2">
                                    <label for="data_inicio" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">De</label>
                                    <input type="date" id="data_inicio" name="data_inicio" value="{{ request('data_inicio') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                </div>
                                <div class="w-1/2">
                                    <label for="data_fim" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Até</label>
                                    <input type="date" id="data_fim" name="data_fim" value="{{ request('data_fim') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                    <option value="">Todos</option>
                                    <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                             <a href="{{ route('jogos.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Limpar</a>
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Filtrar</button>
                        </div>
                    </form>

                    {{-- Messages --}}
                    @if (session('success'))
                        <div id="success-message" class="bg-green-500 text-white font-bold py-2 px-4 rounded mb-4 flash-message">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Table --}}
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
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
                                @forelse ($jogos as $jogo)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4">{{ $jogo->meta['_event_number']->meta_value ?? 'N/A' }}</th>
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $jogo->meta['_event_title']->meta_value ?? $jogo->post_title }}</th>
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
                                        <td class="px-6 py-4">
                                            @if($jogo->jgo_status == 'ativo')
                                                <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Ativo</span>
                                            @elseif($jogo->jgo_status == 'inativo')
                                                <span class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Inativo</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($jogo->jgo_status ?? 'Desconhecido') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            @php
                                                $resStatus = $jogo->jgo_res_status ?? 'nao_informado';
                                            @endphp
                                            
                                            {{-- Result Status Badge --}}
                                            <div class="mb-2">
                                                 @if($resStatus == 'aprovado')
                                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Res. Aprovado</span>
                                                 @elseif($resStatus == 'pendente')
                                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Res. Pendente</span>
                                                 @endif
                                            </div>

                                            <div class="flex item-center justify-center">
                                                <!-- Results (Insert/Edit) -->
                                                <a href="{{ route('resultados.create', $jogo->ID) }}" class="mr-2 transform hover:text-blue-500 hover:scale-110" title="Informar Resultado">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                    </svg>
                                                </a>

                                                <!-- Approve (Admin Only - Logic in View for now, Controller validates) -->
                                                @if($resStatus == 'pendente' && auth()->user()->can('manage team')) 
                                                    {{-- Note: 'manage team' is placeholder permission for Admin/Manager --}}
                                                    <form action="{{ route('resultados.approve', $jogo->local_id ?? 0) }}" method="POST" class="mr-2 transform hover:text-green-500 hover:scale-110" onsubmit="return confirm('Aprovar este resultado?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" title="Aprovar Resultado">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Edit Jogo -->
                                                <a href="{{ route('jogos.edit', $jogo->ID) }}" class="mr-2 transform hover:text-purple-500 hover:scale-110" title="Editar Jogo">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </a>

                                                <!-- Delete Jogo -->
                                                <form action="{{ route('jogos.destroy', $jogo->ID) }}" method="POST" class="mr-2 transform hover:text-purple-500 hover:scale-110" onsubmit="return confirm('Tem certeza que deseja excluir este jogo?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Excluir">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Nenhum jogo encontrado com os filtros selecionados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $jogos->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>