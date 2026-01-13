<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Jogo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mx-auto p-4">
                        @if ($errors->any())
                            <div class="bg-red-500 text-white p-2 my-4 rounded">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="game-edit-form" action="{{ route('jogos.update', $jogo->ID) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Numero do Jogo -->
                                <div class="mb-4">
                                    <label for="event_number" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Nº Jogo *:</label>
                                    <input type="number" name="event_number" id="event_number" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded" value="{{ old('event_number', $eventNumber) }}" required>
                                </div>

                                <!-- Campeonato -->
                                <div class="mb-4">
                                    <label for="campeonato_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Campeonato *:</label>
                                    <select name="campeonato_id" id="campeonato_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded" required>
                                        <option value="">Selecione o Campeonato</option>
                                        @foreach ($campeonatos as $camp)
                                            <option value="{{ $camp->cpo_id }}" {{ old('campeonato_id', $selectedCampeonatoId) == $camp->cpo_id ? 'selected' : '' }}>
                                                {{ $camp->cpo_nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Mandante -->
                                <div class="mb-4">
                                    <label for="mandante_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Mandante *:</label>
                                    <select name="mandante_id" id="mandante_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded" required>
                                        <option value="">Selecione o campeonato primeiro</option>
                                    </select>
                                </div>

                                <!-- Visitante -->
                                <div class="mb-4">
                                    <label for="visitante_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Visitante *:</label>
                                    <select name="visitante_id" id="visitante_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded" required>
                                        <option value="">Selecione o campeonato primeiro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Categoria -->
                                <div class="mb-4">
                                    <label for="categoria_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Categoria *:</label>
                                    <select name="categoria_id" id="categoria_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded" required>
                                        <option value="">Selecione a Categoria</option>
                                        @foreach ($categorias as $cat)
                                            <option value="{{ $cat->cto_id }}" {{ old('categoria_id', $selectedCategoriaId) == $cat->cto_id ? 'selected' : '' }}>
                                                {{ $cat->cto_nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Local (Ginasio) -->
                                <div class="mb-4">
                                    <label for="ginasio_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Local do Jogo *:</label>
                                    <select name="ginasio_id" id="ginasio_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded" required>
                                        <option value="">Selecione o Local</option>
                                        @foreach ($ginasios as $gin)
                                            <option value="{{ $gin->gin_id }}" {{ old('ginasio_id', $ginasioId) == $gin->gin_id ? 'selected' : '' }}>
                                                {{ $gin->gin_nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Data -->
                                <div class="mb-4">
                                    <label for="data_jogo" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Data *:</label>
                                    <input type="date" name="data_jogo" id="data_jogo" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded" value="{{ old('data_jogo', $dataJogo) }}" required>
                                </div>

                                <!-- Hora -->
                                <div class="mb-4">
                                    <label for="hora_jogo" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Horário *:</label>
                                    <input type="time" name="hora_jogo" id="hora_jogo" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded" value="{{ old('hora_jogo', $horaJogo) }}" required>
                                </div>
                            </div>

                            <!-- Arbitragem -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="mb-4">
                                    <label for="juiz_principal" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Árbitro Principal:</label>
                                    <select name="juiz_principal" id="juiz_principal" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded">
                                        <option value="">Selecione...</option>
                                        @foreach($juizes as $juiz)
                                            <option value="{{ $juiz->id }}" {{ old('juiz_principal', $juizPrincipalId) == $juiz->id ? 'selected' : '' }}>
                                                {{ $juiz->name }} ({{$juiz->apelido}})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="juiz_linha1" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Árbitro Secundário:</label>
                                    <select name="juiz_linha1" id="juiz_linha1" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded">
                                        <option value="">Selecione...</option>
                                        @foreach($juizes as $juiz)
                                            <option value="{{ $juiz->id }}" {{ old('juiz_linha1', $juizLinha1Id) == $juiz->id ? 'selected' : '' }}>
                                                {{ $juiz->name }} ({{$juiz->apelido}})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="juiz_linha2" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Apontador:</label>
                                    <select name="juiz_linha2" id="juiz_linha2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded">
                                        <option value="">Selecione...</option>
                                        @foreach($juizes as $juiz)
                                            <option value="{{ $juiz->id }}" {{ old('juiz_linha2', $juizLinha2Id) == $juiz->id ? 'selected' : '' }}>
                                                {{ $juiz->name }} ({{$juiz->apelido}})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-between mt-6">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                    Salvar Alterações
                                </button>
                                <a href="{{ route('jogos.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition">
                                    Cancelar
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para carregar equipes dinamicamente -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const campeonatoSelect = document.getElementById('campeonato_id');
            const mandanteSelect = document.getElementById('mandante_id');
            const visitanteSelect = document.getElementById('visitante_id');
            
            // Valores salvos ou anteriores
            const savedMandanteId = "{{ old('mandante_id', $mandanteId) }}";
            const savedVisitanteId = "{{ old('visitante_id', $visitanteId) }}";

            function loadTeams(campeonatoId, callback = null) {
                // Limpa selects e mostra carregando
                mandanteSelect.innerHTML = '<option value="">Carregando...</option>';
                visitanteSelect.innerHTML = '<option value="">Carregando...</option>';
                mandanteSelect.disabled = true;
                visitanteSelect.disabled = true;

                if (campeonatoId) {
                    fetch(`/api/campeonatos/${campeonatoId}/equipes`)
                        .then(response => response.json())
                        .then(data => {
                            let options = '<option value="">Selecione a equipe</option>';
                            data.forEach(equipe => {
                                options += `<option value="${equipe.id}">${equipe.nome}</option>`;
                            });

                            mandanteSelect.innerHTML = options;
                            visitanteSelect.innerHTML = options;
                            mandanteSelect.disabled = false;
                            visitanteSelect.disabled = false;
                            
                            if (callback) callback();
                        })
                        .catch(error => {
                            console.error('Erro ao buscar equipes:', error);
                            mandanteSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                            visitanteSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                        });
                } else {
                    mandanteSelect.innerHTML = '<option value="">Selecione primeiro o campeonato</option>';
                    visitanteSelect.innerHTML = '<option value="">Selecione primeiro o campeonato</option>';
                }
            }

            campeonatoSelect.addEventListener('change', function() {
                loadTeams(this.value);
            });

            // Carregamento inicial se houver campeonato selecionado (Edição)
            if (campeonatoSelect.value) {
                loadTeams(campeonatoSelect.value, function() {
                    // Restaura seleção se houver
                    if (savedMandanteId) mandanteSelect.value = savedMandanteId;
                    if (savedVisitanteId) visitanteSelect.value = savedVisitanteId;
                });
            }
        });
    </script>
</x-app-layout>
