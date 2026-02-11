<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestão de Elenco') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">

            <!-- Page header content inside main area -->
            <div class="mb-8">
                <div class="sm:flex sm:justify-between sm:items-center">
                    <div class="mb-4 sm:mb-0">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>Campeonato:</strong> {{ $equipeCampeonato->campeonato->cpo_nome }} <br>
                            <strong>Equipe:</strong>
                            {{ $equipeCampeonato->equipe->eqp_nome_detalhado ?? $equipeCampeonato->equipe->time->tim_nome }}
                        </p>
                    </div>
                    <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                        <a href="{{ route('elenco.list') }}"
                            class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800">
                            Voltar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                    role="alert">
                    <span class="font-medium">Sucesso!</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                    role="alert">
                    <span class="font-medium">Erro!</span> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                    role="alert">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Coluna da Esquerda: Atletas Disponíveis -->
                <div
                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Adicionar Atleta ao Elenco
                    </h2>

                    <form
                        action="{{ route('elenco.store', ['campeonato' => $equipeCampeonato->cpo_fk_id, 'equipe_campeonato' => $equipeCampeonato->eqp_cpo_id]) }}"
                        method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="atleta_id"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Selecione o
                                Atleta</label>
                            <select id="atleta_id" name="atleta_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                                <option value="">Escolha um atleta...</option>
                                @foreach($atletasDisponiveis as $atleta)
                                    @php
                                        $impresso = $atleta->cartaoImpresso(date('Y'));
                                    @endphp
                                    <option value="{{ $atleta->atl_id }}" {{ !$impresso ? 'disabled' : '' }}>
                                        {{ $atleta->atl_nome }} - Cat: {{ $atleta->categoria->cto_nome ?? 'N/A' }}
                                        ({{ $atleta->atl_posicao_preferida ?? 'Sem posição' }})
                                        {{ !$impresso ? '- Cartão não impresso (' . date('Y') . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="numero_camisa"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nº
                                    Camisa</label>
                                <input type="number" id="numero_camisa" name="numero_camisa"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    required>
                            </div>
                            <div>
                                <label for="posicao"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Posição</label>
                                <select id="posicao" name="posicao"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Selecione...</option>
                                    <option value="Levantador">Levantador</option>
                                    <option value="Ponteiro">Ponteiro</option>
                                    <option value="Oposto">Oposto</option>
                                    <option value="Central">Central</option>
                                    <option value="Líbero">Líbero</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Incluir no Campeonato
                        </button>
                    </form>

                    @if($atletasDisponiveis->isEmpty())
                        <div class="mt-4 text-sm text-yellow-600 dark:text-yellow-400">
                            Não há mais atletas disponíveis no seu time para adicionar. <a
                                href="{{ route('atletas.create') }}" class="underline">Cadastre novos atletas</a>.
                        </div>
                    @endif
                </div>

                <!-- Coluna da Direita: Elenco Atual -->
                <div
                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Elenco Inscrito
                        ({{ $elencoAtual->count() }})</h2>

                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-3 py-3">Camisa</th>
                                    <th scope="col" class="px-3 py-3">Nome</th>
                                    <th scope="col" class="px-3 py-3">Categoria</th>
                                    <th scope="col" class="px-3 py-3">Posição</th>
                                    <th scope="col" class="px-3 py-3 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($elencoAtual as $elenco)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row"
                                            class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $elenco->ele_num_camisa }}
                                        </th>
                                        <td class="px-3 py-4">
                                            {{ $elenco->atleta->atl_nome }}
                                        </td>
                                        <td class="px-3 py-4">
                                            {{ $elenco->atleta->categoria->cto_nome ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 py-4">
                                            {{ $elenco->ele_posicao_atuando }}
                                        </td>
                                        <td class="px-3 py-4 text-right">
                                            <form
                                                action="{{ route('elenco.destroy', ['campeonato' => $equipeCampeonato->cpo_fk_id, 'equipe_campeonato' => $equipeCampeonato->eqp_cpo_id, 'elenco_id' => $elenco->ele_id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Tem certeza que deseja remover este atleta do elenco?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="font-medium text-red-600 dark:text-red-500 hover:underline">Remover</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center">Nenhum atleta inscrito neste
                                            campeonato ainda.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>