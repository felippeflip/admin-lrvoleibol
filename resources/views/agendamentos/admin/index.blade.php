<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Agendamentos Previos: {{ $cmp->cpo_nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    
                    @if (session('success'))
                        <div class="bg-green-500 text-white font-bold py-2 px-4 rounded mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="bg-red-500 text-white font-bold py-2 px-4 rounded mb-4" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <h3 class="text-lg font-bold mb-4">Gerar Novo Agendamento</h3>
                    <form action="{{ route('agendamentos.gerar', ['campeonato' => $cmp->cpo_id, 'categoria' => '__CAT__']) }}" method="POST" id="form-gerar" onsubmit="return handleGerarSubmit()">
                        @csrf
                        <div class="flex items-center gap-4 mb-6 p-4 bg-gray-100 rounded flex-wrap">
                            <label for="categoria_id" class="font-semibold">Categoria:</label>
                            <select id="categoria_id" class="border p-2 rounded w-64" onchange="verificarCategoria(this)">
                                <option value="" data-qtd="0">Selecione uma categoria...</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->cto_id }}" data-qtd="{{ $cat->qtd_equipes ?? 0 }}">
                                        {{ $cat->cto_nome }} ({{ $cat->qtd_equipes ?? 0 }} equipes)
                                    </option>
                                @endforeach
                            </select>
                            
                            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 font-bold rounded">
                                GERAR JOGOS
                            </button>
                        </div>
                    </form>

                    <h3 class="text-lg font-bold mb-4 mt-8">Jogos Gerados e Pendentes</h3>

                    <!-- Cards de Resumo -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <!-- Total -->
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow border-l-4 border-blue-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-500 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total de Jogos</p>
                                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['total'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pendente Preenchimento -->
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow border-l-4 border-gray-400">
                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                <div class="p-3 rounded-full bg-gray-100 dark:bg-gray-600 text-gray-500 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Pendente Preenchimento</p>
                                    <p class="text-2xl font-bold">{{ $stats['preenchimento'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Aguardando Aprovação -->
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow border-l-4 border-yellow-500">
                            <div class="flex items-center text-yellow-600">
                                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Aguardando Aprovação</p>
                                    <p class="text-2xl font-bold">{{ $stats['aprovacao'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Aprovado -->
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow border-l-4 border-green-500">
                            <div class="flex items-center text-green-600">
                                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Aprovado</p>
                                    <p class="text-2xl font-bold">{{ $stats['aprovado'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                        <form method="GET" action="{{ route('agendamentos.admin.index', $cmp->cpo_id) }}">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <!-- Categoria -->
                                <div>
                                    <label for="categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label>
                                    <select name="categoria" id="categoria" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                        <option value="">Todas</option>
                                        @foreach($categorias as $cat)
                                            <option value="{{ $cat->cto_id }}" {{ request('categoria') == $cat->cto_id ? 'selected' : '' }}>
                                                {{ $cat->cto_nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Fase -->
                                <div>
                                    <label for="fase" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fase</label>
                                    <select name="fase" id="fase" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                        <option value="">Todas</option>
                                        @foreach($fases as $fase)
                                            <option value="{{ $fase }}" {{ request('fase') == $fase ? 'selected' : '' }}>
                                                {{ $fase }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                        <option value="">Todos</option>
                                        <option value="pendente_preenchimento" {{ request('status') == 'pendente_preenchimento' ? 'selected' : '' }}>Pendente Preenchimento</option>
                                        <option value="pendente_aprovacao" {{ request('status') == 'pendente_aprovacao' ? 'selected' : '' }}>Aguardando Aprovação</option>
                                        <option value="aprovado" {{ request('status') == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                                    </select>
                                </div>
                                <!-- Equipe -->
                                <div class="md:col-span-2">
                                    <label for="equipe_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Equipe (Mandante ou Visitante)</label>
                                    <select name="equipe_id" id="equipe_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                        <option value="">Todas as Equipes</option>
                                        @foreach($equipesInscritas as $ei)
                                            <option value="{{ $ei->eqp_cpo_id }}" {{ request('equipe_id') == $ei->eqp_cpo_id ? 'selected' : '' }}>
                                                {{ ($ei->equipe->time->tim_nome ?? 'N/A') . ' - ' . ($ei->equipe->categoria->cto_nome ?? 'N/A') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <a href="{{ route('agendamentos.admin.index', $cmp->cpo_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">Limpar</a>
                                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Filtrar</button>
                            </div>
                        </form>
                    </div>                    
                    <div class="flex justify-start items-center space-x-2 mb-4">
                        <select id="bulk-action-select" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Selecione a Ação em Lote...</option>
                            <option value="aprovar">Aprovar Selecionados</option>
                            <option value="deletar">Deletar Selecionados</option>
                        </select>
                        <button type="button" onclick="executeBulkAction()" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded text-sm">
                            Executar
                        </button>
                    </div>

                    <form action="{{ route('agendamentos.deletarMassa') }}" method="POST" id="form-deletar-massa" class="hidden">
                        @csrf
                        <input type="hidden" name="jogos_ids" id="jogos_ids_input_del" value="">
                    </form>

                    <form action="{{ route('agendamentos.aprovarMassa') }}" method="POST" id="form-aprovar-massa" class="hidden">
                        @csrf
                        <input type="hidden" name="jogos_ids" id="jogos_ids_input_apv" value="">
                    </form>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-2"><input type="checkbox" id="select-all" onclick="toggleSelectAll(this)"></th>
                                    <th class="px-3 py-2">Nº Jogo</th>
                                    <th class="px-3 py-2">Categoria</th>
                                    <th class="px-3 py-2">Fase</th>
                                    <th class="px-3 py-2">Mandante</th>
                                    <th class="px-3 py-2">Visitante</th>
                                    <th class="px-3 py-2">Data/Hora/Local</th>
                                    <th class="px-3 py-2">Status</th>
                                    <th class="px-3 py-2">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jogos as $jogo)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                    <td class="px-3 py-2"><input type="checkbox" value="{{ $jogo->jgo_id }}" class="jogo-checkbox"></td>
                                    <td class="px-3 py-2 font-bold text-purple-700 dark:text-purple-400">
                                        {{ $jogo->jgo_numero_jogo ? '#'.$jogo->jgo_numero_jogo : '-' }}
                                    </td>
                                    <td class="px-3 py-2">{{ $jogo->mandante->equipe->categoria->cto_nome }}</td>
                                    <td class="px-3 py-2">{{ $jogo->jgo_fase }}</td>
                                    <td class="px-3 py-2 font-bold">{{ $jogo->mandante->equipe->eqp_nome_detalhado }}</td>
                                    <td class="px-3 py-2 font-bold">{{ $jogo->visitante->equipe->eqp_nome_detalhado }}</td>
                                    <td class="px-3 py-2 text-xs">
                                        @if($jogo->jgo_dt_jogo)
                                            {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }} às {{ $jogo->jgo_hora_jogo }}<br>
                                            {{ $jogo->ginasio->gin_nome ?? 'N/A' }}
                                        @else
                                            <span class="text-gray-400 italic">Pendente preenchimento</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($jogo->jgo_status_agendamento == 'pendente_preenchimento')
                                            <span class="text-gray-500">Pendente Preench.</span>
                                        @elseif($jogo->jgo_status_agendamento == 'pendente_aprovacao')
                                            <span class="text-yellow-600 font-bold">Aguardando Aprovação</span>
                                        @elseif($jogo->jgo_status_agendamento == 'aprovado')
                                            <span class="text-green-600">Aprovado</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 flex flex-col gap-1 justify-center">
                                        @if($jogo->jgo_status_agendamento == 'pendente_aprovacao')
                                            <form action="{{ route('agendamentos.aprovar', $jogo->jgo_id) }}" method="POST" onsubmit="return confirm('Deseja aprovar estas datas?')">
                                                @csrf
                                                <button class="bg-green-500 hover:bg-green-700 text-white px-2 py-1 text-xs rounded w-full mb-1">APROVAR</button>
                                            </form>
                                            <form action="{{ route('agendamentos.desbloquear', $jogo->jgo_id) }}" method="POST" onsubmit="return confirm('Isso removerá a data sugerida e permite tentar novamente. Deseja continuar?')">
                                                @csrf
                                                <button class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 text-xs rounded w-full mb-1">DESBLOQUEAR / CORRIGIR</button>
                                            </form>
                                        @endif

                                        @if($jogo->jgo_status_agendamento == 'aprovado')
                                            @hasrole('Administrador')
                                            <a href="{{ route('jogos.edit', $jogo->jgo_wp_id ?: $jogo->jgo_id) }}" class="bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 text-xs rounded w-full mb-1 text-center inline-block">EDITAR</a>
                                            @endhasrole
                                        @endif

                                        <form action="{{ route('agendamentos.deletar', $jogo->jgo_id) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir este agendamento/jogo definitivamente?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="bg-gray-700 hover:bg-red-900 text-white px-2 py-1 text-xs rounded w-full">DELETAR</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $jogos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function toggleSelectAll(source) {
        let checkboxes = document.querySelectorAll('.jogo-checkbox');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function executeBulkAction() {
        let action = document.getElementById('bulk-action-select').value;
        if (!action) {
            alert('Por favor, selecione uma ação na lista de opções antes de clicar em Executar.');
            return;
        }

        if (action === 'aprovar') {
            submitAprovarMassa();
        } else if (action === 'deletar') {
            submitDeleteMassa();
        }
    }

    function submitAprovarMassa() {
        let selectedIds = [];
        let checkboxes = document.querySelectorAll('.jogo-checkbox:checked');
        
        checkboxes.forEach((cb) => {
            selectedIds.push(cb.value);
        });

        if (selectedIds.length === 0) {
            alert('Nenhum agendamento selecionado.');
            return;
        }

        if (confirm('Aprovar TODOS os ' + selectedIds.length + ' agendamentos selecionados? Apenas jogos com status "Aguardando Aprovação" serão sincronizados com o site.')) {
            document.getElementById('jogos_ids_input_apv').value = selectedIds.join(',');
            document.getElementById('form-aprovar-massa').submit();
        }
    }

    function submitDeleteMassa() {
        let selectedIds = [];
        let checkboxes = document.querySelectorAll('.jogo-checkbox:checked');
        
        checkboxes.forEach((cb) => {
            selectedIds.push(cb.value);
        });

        if (selectedIds.length === 0) {
            alert('Nenhum agendamento selecionado.');
            return;
        }

        if (confirm('ATENÇÃO: Deseja realmente excluir TODOS os ' + selectedIds.length + ' agendamentos selecionados?')) {
            document.getElementById('jogos_ids_input_del').value = selectedIds.join(',');
            document.getElementById('form-deletar-massa').submit();
        }
    }

    function verificarCategoria(selectElement) {
        // Obsolete function but keeping it empty so no console errors if it's referenced
    }

    function handleGerarSubmit() {
        var cat_id = document.getElementById('categoria_id').value;
        if (!cat_id) {
            alert('Selecione uma categoria primeiro.');
            return false;
        }
        var option = document.querySelector('#categoria_id option:checked');
        var qtd = parseInt(option.getAttribute('data-qtd') || 0);

        var form = document.getElementById('form-gerar');
        form.action = form.action.replace('__CAT__', cat_id);
        
        if (qtd >= 16) {
            return confirm('Existem 16 ou mais equipes na categoria selecionada. Você será redirecionado para a tela de definição de Grupos. Deseja prosseguir?');
        } else {
            return confirm('Gerar os jogos todos contra todos para a categoria selecionada?');
        }
    }
    </script>
</x-app-layout>
