<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Definir Grupos - {{ $cmp->cpo_nome }} ({{ \App\Models\Categoria::find($cat->cto_id)->cto_nome }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Existem 16 ou mais equipes nesta categoria. Como as regras exigem, você deve dividir as equipes manualmente nos grupos desejados. Para cada grupo formado, o sistema irá gerar jogos com <strong>Turno A e Turno B</strong> entre os seus integrantes.</p>
                        <p class="text-sm mt-2 text-red-600 font-bold">Instrução: Por favor, para cada equipe defina o nome ou letra do grupo (ex: Grupo A, Grupo B, etc).</p>
                    </div>

                    <form action="{{ route('agendamentos.gerar', ['campeonato' => $cmp->cpo_id, 'categoria' => $cat->cto_id]) }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto mb-6">
                            <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Equipe</th>
                                        <th class="px-3 py-2 text-left">Time Vinculado</th>
                                        <th class="px-3 py-2">Definir Grupo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipes as $pivot)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">
                                            <td class="px-3 py-4 text-left font-bold">{{ $pivot->equipe->eqp_nome_detalhado }}</td>
                                            <td class="px-3 py-4 text-left">{{ $pivot->equipe->time->tim_nome ?? 'N/A' }}</td>
                                            <td class="px-3 py-4">
                                                <select name="grupos[{{ $pivot->eqp_cpo_id }}]" required class="border p-2 rounded w-full md:w-48 text-gray-800">
                                                    <option value="">Selecione...</option>
                                                    <option value="Grupo A">Grupo A</option>
                                                    <option value="Grupo B">Grupo B</option>
                                                    <option value="Grupo C">Grupo C</option>
                                                    <option value="Grupo D">Grupo D</option>
                                                    <option value="Grupo E">Grupo E</option>
                                                    <option value="Grupo F">Grupo F</option>
                                                    <option value="Grupo G">Grupo G</option>
                                                    <option value="Grupo H">Grupo H</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded">
                                CONFIRMAR E GERAR JOGOS
                            </button>
                            <a href="{{ route('agendamentos.admin.index', $cmp->cpo_id) }}" class="text-gray-600 hover:text-gray-900 border border-gray-400 py-2 px-6 rounded">Voltar</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
