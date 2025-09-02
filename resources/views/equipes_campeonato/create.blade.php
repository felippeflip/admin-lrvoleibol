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
                            <div class="bg-red-500 text-white p-2 my-4 rounded">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Coluna de Equipes Disponíveis -->
                            <div>
                                <label for="equipesDisponiveis" class="block text-gray-700 dark:text-gray-300 mb-2 font-bold">Equipes Disponíveis:</label>
                                <select multiple id="equipesDisponiveis" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded h-80 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach ($equipesDisponiveis as $equipe)
                                        <option value="{{ $equipe->eqp_id }}">
                                            {{ $equipe->eqp_nome_detalhado }} (Time: {{ $equipe->time->tim_nome ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Botões de Ação (Adicionar/Remover) -->
                            <div class="flex items-center justify-center space-x-4 md:space-x-0 md:flex-col md:space-y-4">
                                <button type="button" id="adicionarEquipe" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full md:w-auto">
                                    Adicionar &rarr;
                                </button>
                                <button type="button" id="removerEquipe" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded w-full md:w-auto">
                                    &larr; Remover
                                </button>
                            </div>

                            <!-- Coluna de Equipes Selecionadas (campo de formulário real) -->
                            <div>
                                <label for="equipesSelecionadas" class="block text-gray-700 dark:text-gray-300 mb-2 font-bold">Equipes no Campeonato:</label>
                                <select multiple name="equipe_ids[]" id="equipesSelecionadas" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded h-80 focus:ring-blue-500 focus:border-blue-500">
                                    {{-- As equipes que JÁ estão no campeonato serão pré-preenchidas aqui --}}
                                    @foreach ($equipesInscritas as $equipe)
                                        <option value="{{ $equipe->eqp_id }}" selected>
                                            {{ $equipe->eqp_nome_detalhado }} (Time: {{ $equipe->time->tim_nome ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Todas as equipes nesta lista serão salvas.</p>
                                @error('equipe_ids')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Campos de data de inscrição e classificação (podem ser movidos ou removidos dependendo da sua regra de negócio) --}}
                        <div class="mt-8 mb-4">
                            <label for="eqp_cpo_dt_inscricao" class="block text-gray-700 dark:text-gray-300 mb-2">Data de Inscrição:</label>
                            <input type="date" name="eqp_cpo_dt_inscricao" id="eqp_cpo_dt_inscricao" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('eqp_cpo_dt_inscricao', now()->toDateString()) }}">
                            @error('eqp_cpo_dt_inscricao')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-start mt-6 space-x-4">
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Salvar</button>
                            <a href="{{ route('equipes.campeonato.index', $campeonato->cpo_id) }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const equipesDisponiveis = document.getElementById('equipesDisponiveis');
    const equipesSelecionadas = document.getElementById('equipesSelecionadas');
    const adicionarBotao = document.getElementById('adicionarEquipe');
    const removerBotao = document.getElementById('removerEquipe');
    const form = document.getElementById('equipesForm');

    // Mover itens de "Disponíveis" para "Selecionados"
    adicionarBotao.addEventListener('click', function() {
        moveSelectedOptions(equipesDisponiveis, equipesSelecionadas);
    });

    // Mover itens de "Selecionados" para "Disponíveis"
    removerBotao.addEventListener('click', function() {
        moveSelectedOptions(equipesSelecionadas, equipesDisponiveis);
    });

    // Garantir que todos os itens em "selecionados" estejam realmente selecionados ao submeter
    form.addEventListener('submit', function() {
        for (let i = 0; i < equipesSelecionadas.options.length; i++) {
            equipesSelecionadas.options[i].selected = true;
        }
    });

    function moveSelectedOptions(from, to) {
        let selectedOptions = Array.from(from.selectedOptions);
        selectedOptions.forEach(option => {
            to.appendChild(option);
        });
        sortOptions(to);
    }

    function sortOptions(selectElement) {
        let options = Array.from(selectElement.options);
        options.sort((a, b) => a.text.localeCompare(b.text));
        options.forEach(option => selectElement.add(option));
    }

    // Opcional: pré-selecionar equipes já inscritas no campeonato
    // A view agora já carrega equipes inscritas, mas esta lógica é para outros casos
    // onde o form é dinamicamente gerado ou o estado inicial precisa de ajuste.
    // Como estamos usando o Blade para pré-popular, não é estritamente necessário.
});
</script>