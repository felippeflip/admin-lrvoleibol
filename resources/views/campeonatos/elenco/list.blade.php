<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Meus Elencos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6">
                 <p class="text-sm text-gray-600 dark:text-gray-400">
                    Selecione uma participação em campeonato para gerenciar os atletas inscritos.
                </p>
            </div>

            @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Erro!</span> {{ session('error') }}
            </div>
            @endif
            
            <div class="mb-6 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                 <form method="GET" action="{{ route('elenco.list') }}">
                     <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        
                        <!-- Campeonato -->
                        <div>
                            <label for="campeonato_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Campeonato</label>
                            <select name="campeonato_id" id="campeonato_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                <option value="">Todos</option>
                                @foreach($campeonatos as $camp)
                                    <option value="{{ $camp->cpo_id }}" {{ request('campeonato_id') == $camp->cpo_id ? 'selected' : '' }}>{{ $camp->cpo_nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Equipe -->
                        <div>
                            <label for="equipe_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Equipe</label>
                            <select name="equipe_id" id="equipe_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                <option value="">Todas</option>
                                @foreach($equipes as $eqp)
                                    <option value="{{ $eqp->eqp_id }}" {{ request('equipe_id') == $eqp->eqp_id ? 'selected' : '' }}>
                                        {{ $eqp->eqp_nome_detalhado ?? $eqp->time->tim_nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                         <!-- Categoria -->
                         <div>
                            <label for="categoria_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label>
                            <select name="categoria_id" id="categoria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                <option value="">Todas</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->cto_id }}" {{ request('categoria_id') == $cat->cto_id ? 'selected' : '' }}>{{ $cat->cto_nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Botões -->
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">Filtrar</button>
                            <a href="{{ route('elenco.list') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded w-full text-center">Limpar</a>
                        </div>
                    </div>
                 </form>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Campeonato</th>
                                <th scope="col" class="px-6 py-3">Equipe</th>
                                <th scope="col" class="px-6 py-3">Categoria</th>
                                <th scope="col" class="px-6 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($participacoes as $participacao)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $participacao->campeonato->cpo_nome }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $participacao->equipe->eqp_nome_detalhado ?? $participacao->equipe->time->tim_nome }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $participacao->equipe->categoria->cto_nome }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('elenco.index', ['campeonato' => $participacao->cpo_fk_id, 'equipe_campeonato' => $participacao->eqp_cpo_id]) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                        Gerenciar Elenco
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center">
                                    Nenhuma participação em campeonato encontrada. Para gerenciar um elenco, sua equipe precisa estar inscrita em um campeonato.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                     {{ $participacoes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
