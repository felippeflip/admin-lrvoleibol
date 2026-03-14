<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Agendamentos Pendentes
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

                    <h3 class="text-lg font-bold mb-4 mt-4">Preencha as informações dos jogos pendientes</h3>

                    <div class="mb-6 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                        <form method="GET" action="{{ route('agendamentos.comissao.index') }}">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Categoria -->
                                <div>
                                    <label for="categoria_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label>
                                    <select name="categoria_id" id="categoria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                        <option value="">Todas</option>
                                        @foreach($categorias as $cat)
                                            <option value="{{ $cat->cto_id }}" {{ request('categoria_id') == $cat->cto_id ? 'selected' : '' }}>
                                                {{ $cat->cto_nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Fase -->
                                <div>
                                    <label for="fase"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fase</label>
                                    <input type="text" name="fase" id="fase" value="{{ request('fase') }}"
                                        placeholder="Fase..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select name="status" id="status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                        <option value="">Todos</option>
                                        <option value="pendente_preenchimento" {{ request('status') == 'pendente_preenchimento' ? 'selected' : '' }}>Livre (Pendente)</option>
                                        <option value="pendente_aprovacao" {{ request('status') == 'pendente_aprovacao' ? 'selected' : '' }}>Bloqueado (Aguardando Aprovação)</option>
                                    </select>
                                </div>

                                <!-- Botões -->
                                <div class="flex items-end space-x-2">
                                    <button type="submit"
                                        class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">Filtrar</button>
                                    <a href="{{ route('agendamentos.comissao.index') }}"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded w-full text-center">Limpar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-2">Competição / Categoria</th>
                                    <th class="px-3 py-2">Prazos</th>
                                    <th class="px-3 py-2">Fase</th>
                                    <th class="px-3 py-2">Confronto</th>
                                    <th class="px-3 py-2">Informe Data, Hora e Local</th>
                                    <th class="px-3 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jogos as $jogo)
                                    @php
                                        // Verificar se já foi sugerido por outra equipe
                                        $isLocked = ($jogo->jgo_status_agendamento == 'pendente_aprovacao');
                                        $sugeridoPorMim = ($jogo->jgo_sugerido_por_equipe_id == $time_id);
                                        $cmp = $jogo->mandante->campeonato;
                                        $dt_fim_classificacao = $cmp ? ($cmp->cpo_dt_fim_classificacao ? \Carbon\Carbon::parse($cmp->cpo_dt_fim_classificacao)->format('d/m/Y') : 'Não Definido') : 'N/A';
                                        $dt_fim_finais = $cmp ? ($cmp->cpo_dt_fim_finais ? \Carbon\Carbon::parse($cmp->cpo_dt_fim_finais)->format('d/m/Y') : 'Não Definido') : 'N/A';
                                    @endphp
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-3 py-2 text-xs">
                                        <b>{{ $cmp->cpo_nome ?? '' }}</b><br>
                                        {{ $jogo->mandante->equipe->categoria->cto_nome }}
                                    </td>
                                    <td class="px-3 py-2 text-xs text-left">
                                        Classificatória: Até {{ $dt_fim_classificacao }}<br>
                                        Finais: Até {{ $dt_fim_finais }}
                                    </td>
                                    <td class="px-3 py-2">{{ $jogo->jgo_fase }}</td>
                                    <td class="px-3 py-2">
                                        {{ $jogo->mandante->equipe->eqp_nome_detalhado }} 
                                        <b>X</b>
                                        {{ $jogo->visitante->equipe->eqp_nome_detalhado }}
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($isLocked)
                                            <div class="text-sm">
                                                {{ \Carbon\Carbon::parse($jogo->jgo_dt_jogo)->format('d/m/Y') }} às {{ $jogo->jgo_hora_jogo }}<br>
                                                {{ $jogo->ginasio->gin_nome ?? 'N/A' }}
                                                <br>
                                                @if($sugeridoPorMim)
                                                    <span class="text-green-600 text-xs">(Aguardando aprovação do Admin)</span>
                                                @else
                                                    <span class="text-yellow-600 text-xs">(Sugerido pelo adversário, aguardando aprovação)</span>
                                                @endif
                                            </div>
                                        @else
                                            <form action="{{ route('agendamentos.comissao.sugerir', $jogo->jgo_id) }}" method="POST" class="flex flex-col gap-2">
                                                @csrf
                                                <input type="date" name="jgo_dt_jogo" required class="border p-1 w-full text-xs">
                                                <input type="time" name="jgo_hora_jogo" required class="border p-1 w-full text-xs">
                                                <select name="jgo_local_jogo_id" required class="border p-1 w-full text-xs">
                                                    <option value="">Ginásio...</option>
                                                    @foreach($ginasios as $gin)
                                                        <option value="{{ $gin->gin_id }}">{{ $gin->gin_nome }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="bg-blue-600 text-white font-bold p-1 rounded text-xs hover:bg-blue-800">ENVIAR</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($isLocked)
                                            <span class="text-yellow-500 font-bold">Bloqueado</span>
                                        @else
                                            <span class="text-blue-500 font-bold">Livre</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @if($jogos->isEmpty())
                                    <tr>
                                        <td colspan="6" class="px-3 py-4 text-center text-gray-500">Nenhum jogo pendente para o seu time.</td>
                                    </tr>
                                @endif
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
</x-app-layout>
