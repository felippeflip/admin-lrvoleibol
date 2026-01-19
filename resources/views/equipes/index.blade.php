<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($time) ? __('Equipes do Time: ') . $time->tim_nome : __('Lista de Equipes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-start mb-4">
                        {{-- Ação para criar uma nova equipe, com o time pré-selecionado se aplicável --}}
                        <a href="{{ isset($time) ? route('equipes.create', ['time_id' => $time->tim_id]) : route('equipes.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">NOVA EQUIPE</a>
                    </div>
                    
                    <div class="mb-6 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                        <form method="GET" action="{{ route('equipes.index') }}">
                             {{-- Se estiver filtrando por um time específico vindo de outra página, mantém o ID no form hidden se não quiser mostrar o dropdown ou ajusta o dropdown --}}
                            @if(isset($time) && !request()->has('time_id')) 
                                <input type="hidden" name="time_id" value="{{ $time->tim_id }}">
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <!-- Busca por Nome -->
                                <div>
                                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome da Equipe</label>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white placeholder-gray-400" placeholder="Ex: Sub-17, Série A...">
                                </div>

                                <!-- Categoria -->
                                <div>
                                    <label for="categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label>
                                    <select name="categoria" id="categoria" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white">
                                        <option value="">Todas</option>
                                        @foreach($categorias ?? [] as $cat)
                                            <option value="{{ $cat->cto_id }}" {{ request('categoria') == $cat->cto_id ? 'selected' : '' }}>{{ $cat->cto_nome }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Campeonato -->
                                <div>
                                    <label for="campeonato_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Campeonato</label>
                                    <select name="campeonato_id" id="campeonato_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white">
                                        <option value="">Todos</option>
                                        @foreach($campeonatos ?? [] as $camp)
                                            <option value="{{ $camp->cpo_id }}" {{ request('campeonato_id') == $camp->cpo_id ? 'selected' : '' }}>{{ $camp->cpo_nome }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Time -->
                                <div>
                                    <label for="time_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time</label>
                                    <select name="time_id" id="time_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white">
                                        <option value="">Todos</option>
                                        @foreach($times ?? [] as $t)
                                            <option value="{{ $t->tim_id }}" {{ (request('time_id') == $t->tim_id || (isset($time) && $time->tim_id == $t->tim_id)) ? 'selected' : '' }}>{{ $t->tim_nome }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Botões -->
                                <div class="flex items-end space-x-2">
                                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">Filtrar</button>
                                    <a href="{{ route('equipes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded w-full text-center">Limpar</a>
                                </div>
                            </div>
                        </form>
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
                                    <th scope="col" class="px-6 py-3">NOME EQUIPE</th>
                                    <th scope="col" class="px-6 py-3">TIME</th>
                                    <th scope="col" class="px-6 py-3">CAMPEONATOS</th>
                                    <th scope="col" class="px-6 py-3">CATEGORIA</th>
                                    <th scope="col" class="px-6 py-3">TREINADOR</th>
                                    <th scope="col" class="px-6 py-3 text-center">AÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($equipes as $equipe)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $equipe->eqp_nome_detalhado }}</th>
                                        <td class="px-6 py-4">{{ $equipe->time->tim_nome ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                @forelse($equipe->campeonatos as $campeonato)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                        {{ $campeonato->cpo_nome }}
                                                    </span>
                                                @empty
                                                    <span class="text-gray-400 text-xs italic">Nenhum</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">{{ $equipe->categoria->cto_nome ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $equipe->eqp_nome_treinador ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 flex space-x-2 justify-center">
                                            <!-- Botão Ver Campeonatos -->
                                            <button type="button" 
                                                    onclick="openCampeonatosModal('{{ $equipe->eqp_nome_detalhado }}', {{ json_encode($equipe->campeonatos) }})"
                                                    class="w-4 mr-2 transform hover:text-yellow-500 hover:scale-110" 
                                                    title="Ver Campeonatos">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10a9 9 0 0118 0v1a1 1 0 01-1 1H2a1 1 0 01-1-1v-1a9 9 0 0118 0z" /> <!-- Ícone genérico, substituindo por trofeu abaixo -->
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                                </svg>
                                            </button>

                                            <a href="{{ route('equipes.edit', $equipe->eqp_id) }}" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" title="Editar Equipe">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('equipes.destroy', $equipe->eqp_id) }}" method="POST" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" onsubmit="return confirm('Tem certeza que deseja remover esta equipe?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Excluir Equipe">
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

                    <div class="mt-4">
                        {{ $equipes->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Campeonatos -->
    <div id="campeonatosModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button" onclick="closeCampeonatosModal()" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Fechar modal</span>
                </button>
                <div class="px-6 py-6 lg:px-8">
                    <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Campeonatos de <span id="modalEquipeNome" class="font-bold"></span></h3>
                    <ul id="modalCampeonatosList" class="space-y-4 text-gray-500 list-disc list-inside dark:text-gray-400">
                        <!-- Lista populated via JS -->
                    </ul>
                    <p id="modalNoCampeonatos" class="hidden text-sm text-gray-500 dark:text-gray-400">Esta equipe não está inscrita em nenhum campeonato.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openCampeonatosModal(nomeEquipe, campeonatos) {
            const modal = document.getElementById('campeonatosModal');
            const titulo = document.getElementById('modalEquipeNome');
            const lista = document.getElementById('modalCampeonatosList');
            const msgVazio = document.getElementById('modalNoCampeonatos');

            titulo.textContent = nomeEquipe;
            lista.innerHTML = '';

            if (campeonatos && campeonatos.length > 0) {
                msgVazio.classList.add('hidden');
                campeonatos.forEach(camp => {
                    const li = document.createElement('li');
                    // Ajuste conforme os campos do seu model Campeonato
                    li.textContent = `${camp.cpo_nome} (${new Date(camp.pivot.eqp_cpo_dt_inscricao).toLocaleDateString('pt-BR')})`; 
                    lista.appendChild(li);
                });
            } else {
                msgVazio.classList.remove('hidden');
            }

            modal.classList.remove('hidden');
        }

        function closeCampeonatosModal() {
            document.getElementById('campeonatosModal').classList.add('hidden');
        }
        
        // Fechar ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('campeonatosModal');
            if (event.target == modal) {
                closeCampeonatosModal();
            }
        }
    </script>
</x-app-layout>

