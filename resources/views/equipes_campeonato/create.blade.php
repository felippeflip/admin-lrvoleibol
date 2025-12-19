<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gerenciar Equipes do Campeonato: ') . $campeonato->cpo_nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form action="{{ route('equipes.campeonato.store', $campeonato->cpo_id) }}" method="POST" id="equipesForm">
                        @csrf

                        @if ($errors->any())
                            <div class="bg-red-500 text-white p-4 mb-6 rounded-lg shadow-md">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto_1fr] gap-6 items-center">
                            
                            <!-- Coluna de Equipes Disponíveis -->
                            <div class="flex flex-col h-full">
                                <label for="equipesDisponiveis" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Equipes Disponíveis <span id="countDisponiveis" class="text-xs text-gray-500 ml-1">(0)</span>
                                </label>
                                <div class="relative mb-2">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                        </svg>
                                    </div>
                                    <input type="text" id="searchDisponiveis" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar equipe...">
                                </div>
                                <select multiple id="equipesDisponiveis" class="flex-1 w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500 h-96 text-sm">
                                    @foreach ($equipesDisponiveis as $equipe)
                                        <option value="{{ $equipe->eqp_id }}" data-search="{{ strtolower($equipe->eqp_nome_detalhado . ' ' . ($equipe->time->tim_nome ?? '')) }}">
                                            {{ $equipe->eqp_nome_detalhado }} (Time: {{ $equipe->time->tim_nome ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="flex flex-row lg:flex-col gap-2 justify-center items-center">
                                <button type="button" id="addAll" class="p-2 text-gray-500 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700" title="Adicionar Todos">
                                    <span class="hidden lg:inline">≫</span>
                                    <span class="lg:hidden">▼▼</span>
                                </button>
                                <button type="button" id="addSelected" class="p-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" title="Adicionar Selecionados">
                                    <span class="hidden lg:inline">ᐳ</span>
                                    <span class="lg:hidden">▼</span>
                                </button>
                                <button type="button" id="removeSelected" class="p-2 text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm dark:bg-red-500 dark:hover:bg-red-600 focus:outline-none dark:focus:ring-red-800" title="Remover Selecionados">
                                    <span class="hidden lg:inline">ᐸ</span>
                                    <span class="lg:hidden">▲</span>
                                </button>
                                <button type="button" id="removeAll" class="p-2 text-gray-500 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700" title="Remover Todos">
                                    <span class="hidden lg:inline">≪</span>
                                    <span class="lg:hidden">▲▲</span>
                                </button>
                            </div>

                            <!-- Coluna de Equipes Selecionadas -->
                            <div class="flex flex-col h-full">
                                <label for="equipesSelecionadas" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Equipes no Campeonato <span id="countSelecionadas" class="text-xs text-gray-500 ml-1">(0)</span>
                                </label>
                                <div class="relative mb-2">
                                     <!-- Placeholder visual para alinhar com a busca da esquerda, se desejar, ou apenas um espaço -->
                                     <div class="h-[42px] flex items-center justify-end">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Itens finais a salvar</span>
                                     </div>
                                </div>
                                <select multiple name="equipe_ids[]" id="equipesSelecionadas" class="flex-1 w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500 h-96 text-sm">
                                    @foreach ($equipesInscritas as $equipe)
                                        <option value="{{ $equipe->eqp_id }}" selected data-search="{{ strtolower($equipe->eqp_nome_detalhado . ' ' . ($equipe->time->tim_nome ?? '')) }}">
                                            {{ $equipe->eqp_nome_detalhado }} (Time: {{ $equipe->time->tim_nome ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('equipe_ids')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="eqp_cpo_dt_inscricao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data de Inscrição</label>
                                <input type="date" name="eqp_cpo_dt_inscricao" id="eqp_cpo_dt_inscricao" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-2.5 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('eqp_cpo_dt_inscricao', now()->toDateString()) }}">
                                @error('eqp_cpo_dt_inscricao')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 space-x-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <a href="{{ route('eventos.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                                Cancelar
                            </a>
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const disponiveis = document.getElementById('equipesDisponiveis');
    const selecionadas = document.getElementById('equipesSelecionadas');
    const searchInput = document.getElementById('searchDisponiveis');
    
    const btnAdd = document.getElementById('addSelected');
    const btnRemove = document.getElementById('removeSelected');
    const btnAddAll = document.getElementById('addAll');
    const btnRemoveAll = document.getElementById('removeAll');
    
    const countDisponiveis = document.getElementById('countDisponiveis');
    const countSelecionadas = document.getElementById('countSelecionadas');
    const form = document.getElementById('equipesForm');

    // Inicializa contadores
    updateCounters();

    // Filtro de busca
    searchInput.addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        Array.from(disponiveis.options).forEach(option => {
            const text = option.getAttribute('data-search') || option.text.toLowerCase();
            option.style.display = text.includes(term) ? '' : 'none';
        });
    });

    // Ações dos botões
    btnAdd.addEventListener('click', () => moveOptions(disponiveis, selecionadas));
    btnRemove.addEventListener('click', () => moveOptions(selecionadas, disponiveis));
    btnAddAll.addEventListener('click', () => moveAllOptions(disponiveis, selecionadas));
    btnRemoveAll.addEventListener('click', () => moveAllOptions(selecionadas, disponiveis));

    // Duplo clique para mover
    disponiveis.addEventListener('dblclick', () => moveOptions(disponiveis, selecionadas));
    selecionadas.addEventListener('dblclick', () => moveOptions(selecionadas, disponiveis));

    // Submissão do formulário
    form.addEventListener('submit', function() {
        // Seleciona todas as opções na lista da direita para garantir que sejam enviadas
        Array.from(selecionadas.options).forEach(option => option.selected = true);
    });

    function moveOptions(from, to) {
        const selected = Array.from(from.selectedOptions);
        if (selected.length === 0) return;
        
        selected.forEach(option => {
            option.selected = false; // Remove seleção ao mover
            to.appendChild(option);
        });
        
        sortOptions(to);
        updateCounters();
        
        // Se moveu de "Disponíveis", re-aplica o filtro de busca (caso esteja ativo)
        if (from === disponiveis) {
            searchInput.dispatchEvent(new Event('input'));
        }
    }

    function moveAllOptions(from, to) {
        // Move apenas as visíveis (caso haja filtro) ou todas?
        // Geralmente "Mover Todos" move tudo, mas se houver filtro, pode ser confuso.
        // Vamos mover TUDO que está visível no momento.
        
        const options = Array.from(from.options).filter(opt => opt.style.display !== 'none');
        
        options.forEach(option => {
            to.appendChild(option);
        });

        sortOptions(to);
        updateCounters();
        
        if (from === disponiveis) {
            searchInput.dispatchEvent(new Event('input'));
        }
    }

    function sortOptions(select) {
        const options = Array.from(select.options);
        options.sort((a, b) => a.text.localeCompare(b.text));
        select.innerHTML = '';
        options.forEach(opt => select.appendChild(opt));
    }

    function updateCounters() {
        countDisponiveis.textContent = `(${disponiveis.options.length})`;
        countSelecionadas.textContent = `(${selecionadas.options.length})`;
    }
});
</script>