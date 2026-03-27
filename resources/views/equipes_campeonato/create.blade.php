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

                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="filterCategoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Filtrar por Categoria (Para visualizar equipes e arrastar)</label>
                                <select id="filterCategoria" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Todas as Categorias</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->cto_id }}">{{ $cat->cto_nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

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
                                <div id="listDisponiveis" class="custom-list flex-1 w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg h-[calc(100vh-28rem)] min-h-[300px] overflow-y-auto bg-white">
                                    @foreach ($equipesDisponiveis as $equipe)
                                        <div class="list-item cursor-grab active:cursor-grabbing p-3 border-b border-gray-100 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" 
                                             draggable="true"
                                             data-value="{{ $equipe->eqp_id }}" 
                                             data-categoria="{{ $equipe->eqp_categoria_id }}"
                                             data-search="{{ strtolower($equipe->eqp_nome_detalhado . ' ' . ($equipe->time->tim_nome ?? '')) }}">
                                            <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $equipe->eqp_nome_detalhado }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Time: {{ $equipe->time->tim_nome ?? 'N/A' }} | Cat: {{ $equipe->categoria->cto_nome ?? 'N/A' }}</div>
                                        </div>
                                    @endforeach
                                </div>
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
                                     <div class="h-[42px] flex items-center justify-end">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Itens finais a salvar</span>
                                     </div>
                                </div>
                                <div id="listSelecionadas" class="custom-list flex-1 w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg h-[calc(100vh-28rem)] min-h-[300px] overflow-y-auto bg-white">
                                    @foreach ($equipesInscritas as $equipe)
                                        <div class="list-item cursor-grab active:cursor-grabbing p-3 border-b border-gray-100 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" 
                                             draggable="true"
                                             data-value="{{ $equipe->eqp_id }}" 
                                             data-categoria="{{ $equipe->eqp_categoria_id }}"
                                             data-search="{{ strtolower($equipe->eqp_nome_detalhado . ' ' . ($equipe->time->tim_nome ?? '')) }}">
                                            <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $equipe->eqp_nome_detalhado }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Time: {{ $equipe->time->tim_nome ?? 'N/A' }} | Cat: {{ $equipe->categoria->cto_nome ?? 'N/A' }}</div>
                                            <input type="hidden" name="equipe_ids[]" value="{{ $equipe->eqp_id }}">
                                        </div>
                                    @endforeach
                                </div>
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
    // Configurações e Elementos
    const listDisponiveis = document.getElementById('listDisponiveis');
    const listSelecionadas = document.getElementById('listSelecionadas');
    const searchInput = document.getElementById('searchDisponiveis');
    const filterCategoria = document.getElementById('filterCategoria');
    
    const btnAdd = document.getElementById('addSelected');
    const btnRemove = document.getElementById('removeSelected');
    const btnAddAll = document.getElementById('addAll');
    const btnRemoveAll = document.getElementById('removeAll');
    
    const countDisponiveis = document.getElementById('countDisponiveis');
    const countSelecionadas = document.getElementById('countSelecionadas');
    
    // Inicialização
    registerEvents(listDisponiveis);
    registerEvents(listSelecionadas);
    updateCounters();

    // Filtros
    function filterItems() {
        const term = searchInput.value.toLowerCase();
        const catId = filterCategoria.value;
        
        [listDisponiveis, listSelecionadas].forEach(list => {
            const items = list.getElementsByClassName('list-item');
            Array.from(items).forEach(item => {
                const text = item.getAttribute('data-search') || '';
                const itemCat = item.getAttribute('data-categoria') || '';
                
                let textMatch = true;
                if (list === listDisponiveis && term !== '') {
                    textMatch = text.includes(term);
                }
                
                let catMatch = true;
                if (catId !== '') {
                    catMatch = (itemCat === catId);
                }
                
                item.style.display = (textMatch && catMatch) ? 'block' : 'none';
            });
        });
        updateCounters();
    }

    searchInput.addEventListener('input', filterItems);
    if (filterCategoria) {
        filterCategoria.addEventListener('change', filterItems);
    }

    // Eventos Click nos Botões
    btnAdd.addEventListener('click', () => { moveSelectedItems(listDisponiveis, listSelecionadas); filterItems(); });
    btnRemove.addEventListener('click', () => { moveSelectedItems(listSelecionadas, listDisponiveis); filterItems(); });
    btnAddAll.addEventListener('click', () => { moveAllItems(listDisponiveis, listSelecionadas); filterItems(); });
    btnRemoveAll.addEventListener('click', () => { moveAllItems(listSelecionadas, listDisponiveis); filterItems(); });

    // Drag and Drop implementation
    let draggedItem = null;

    function handleDragStart(e) {
        draggedItem = this;
        e.dataTransfer.effectAllowed = 'move';
        // e.dataTransfer.setData('text/html', this.outerHTML);
        setTimeout(() => this.classList.add('opacity-50'), 0);
    }

    function handleDragEnd(e) {
        this.classList.remove('opacity-50');
        draggedItem = null;
        cleanItemStyles(this);
    }

    function handleDragOver(e) {
        if (e.preventDefault) e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        this.classList.add('bg-gray-100', 'dark:bg-gray-600');
        return false;
    }
    
    function handleDragLeave(e) {
        this.classList.remove('bg-gray-100', 'dark:bg-gray-600');
    }

    function handleDrop(e) {
        if (e.stopPropagation) e.stopPropagation();
        this.classList.remove('bg-gray-100', 'dark:bg-gray-600');
        
        if (draggedItem && this !== draggedItem.parentNode) {
            cleanItemStyles(draggedItem);
            const isSelectedCol = this.id === 'listSelecionadas';
            manageHiddenInput(draggedItem, isSelectedCol);
            this.appendChild(draggedItem);
            
            sortItems(this);
            filterItems();
        }
        return false;
    }

    function registerEvents(container) {
        container.removeEventListener('click', handleItemClick);
        container.addEventListener('click', handleItemClick);
        
        container.removeEventListener('dragover', handleDragOver);
        container.removeEventListener('dragleave', handleDragLeave);
        container.removeEventListener('drop', handleDrop);
        container.addEventListener('dragover', handleDragOver);
        container.addEventListener('dragleave', handleDragLeave);
        container.addEventListener('drop', handleDrop);
        
        bindDragEventsToItems(container);
    }

    function bindDragEventsToItems(container) {
        const items = container.getElementsByClassName('list-item');
        Array.from(items).forEach(item => {
            item.removeEventListener('dragstart', handleDragStart);
            item.removeEventListener('dragend', handleDragEnd);
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragend', handleDragEnd);
        });
    }

    function handleItemClick(e) {
        const item = e.target.closest('.list-item');
        if (item && this.contains(item)) {
            item.classList.toggle('bg-blue-100');
            item.classList.toggle('dark:bg-blue-900');
            item.classList.toggle('border-blue-300');
            item.classList.toggle('selected-item');
        }
    }

    function moveSelectedItems(from, to) {
        const selected = Array.from(from.getElementsByClassName('selected-item'));
        if (selected.length === 0) return;
        
        selected.forEach(item => {
            cleanItemStyles(item);
            manageHiddenInput(item, to.id === 'listSelecionadas');
            to.appendChild(item);
        });
        
        sortItems(to);
        bindDragEventsToItems(to);
    }

    function moveAllItems(from, to) {
        const items = Array.from(from.getElementsByClassName('list-item')).filter(el => el.style.display !== 'none');
        
        items.forEach(item => {
            cleanItemStyles(item);
            manageHiddenInput(item, to.id === 'listSelecionadas');
            to.appendChild(item);
        });

        sortItems(to);
        bindDragEventsToItems(to);
    }

    function manageHiddenInput(item, isSelectedCol) {
        const existingInput = item.querySelector('input[name="equipe_ids[]"]');
        
        if (isSelectedCol) {
            if (!existingInput) {
                const id = item.getAttribute('data-value');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'equipe_ids[]';
                input.value = id;
                item.appendChild(input);
            }
        } else {
            if (existingInput) {
                existingInput.remove();
            }
        }
    }

    function cleanItemStyles(item) {
        item.classList.remove('bg-blue-100', 'dark:bg-blue-900', 'border-blue-300', 'selected-item');
    }

    function sortItems(container) {
        const items = Array.from(container.getElementsByClassName('list-item'));
        
        items.sort((a, b) => {
            const textA = a.innerText.toLowerCase();
            const textB = b.innerText.toLowerCase();
            return textA.localeCompare(textB);
        });
        
        items.forEach(item => container.appendChild(item));
    }

    function updateCounters() {
        const visDisp = Array.from(listDisponiveis.getElementsByClassName('list-item')).filter(el => el.style.display !== 'none').length;
        const visSel = Array.from(listSelecionadas.getElementsByClassName('list-item')).filter(el => el.style.display !== 'none').length;
        countDisponiveis.textContent = `(${visDisp})`;
        countSelecionadas.textContent = `(${visSel})`;
    }
});
</script>