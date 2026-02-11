<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lista de Atletas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mx-auto p-4">
                        @if (session('success'))
                            <div class="bg-green-500 text-white p-2 my-4 rounded flash-message" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="mb-6 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow">
                            <form method="GET" action="{{ route('atletas.index') }}">
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                    <!-- Status -->
                                    <div>
                                        <label for="ativo"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select name="ativo" id="ativo"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                            <option value="1" {{ request('ativo', '1') == '1' ? 'selected' : '' }}>Ativo
                                            </option>
                                            <option value="0" {{ request('ativo') == '0' ? 'selected' : '' }}>Inativo
                                            </option>
                                            <option value="todos" {{ request('ativo') == 'todos' ? 'selected' : '' }}>
                                                Todos</option>
                                        </select>
                                    </div>

                                    <!-- Nome -->
                                    <div class="col-span-1 md:col-span-2">
                                        <label for="nome"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                                        <input type="text" name="nome" id="nome" value="{{ request('nome') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400"
                                            placeholder="Nome do Atleta">
                                    </div>

                                    <!-- CPF -->
                                    <div class="col-span-1 md:col-span-1">
                                        <label for="atl_cpf"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">CPF</label>
                                        <input type="text" name="cpf" id="atl_cpf" value="{{ request('cpf') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400"
                                            placeholder="CPF">
                                    </div>

                                    @role('Administrador')
                                    <!-- Time (Apenas Admin) -->
                                    <div>
                                        <label for="time_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time</label>
                                        <select name="time_id" id="time_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                            <option value="">Todos</option>
                                            @foreach($times as $time)
                                                <option value="{{ $time->tim_id }}" {{ request('time_id') == $time->tim_id ? 'selected' : '' }}>
                                                    {{ $time->tim_nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endrole

                                    <!-- Categoria -->
                                    <div>
                                        <label for="categoria"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label>
                                        <select name="categoria" id="categoria"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                            <option value="">Todas</option>
                                            @foreach($categorias ?? [] as $cat)
                                                <option value="{{ $cat->cto_id }}" {{ request('categoria') == $cat->cto_id ? 'selected' : '' }}>{{ $cat->cto_nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>



                                    <!-- Ano Inscrição -->
                                    <div>
                                        <label for="ano_inscricao"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ano
                                            Inscrição</label>
                                        <input type="number" name="ano_inscricao" id="ano_inscricao"
                                            value="{{ request('ano_inscricao') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400"
                                            placeholder="Ex: 2024">
                                    </div>

                                    <!-- Sexo -->
                                    <div>
                                        <label for="sexo"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sexo</label>
                                        <select name="sexo" id="sexo"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                            <option value="">Todos</option>
                                            <option value="M" {{ request('sexo') == 'M' ? 'selected' : '' }}>Masculino
                                            </option>
                                            <option value="F" {{ request('sexo') == 'F' ? 'selected' : '' }}>Feminino
                                            </option>
                                            <option value="O" {{ request('sexo') == 'O' ? 'selected' : '' }}>Outro
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Botões -->
                                    <div class="flex items-end space-x-2">
                                        <button type="submit"
                                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">Filtrar</button>
                                        <a href="{{ route('atletas.index') }}"
                                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded w-full text-center">Limpar</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="flex justify-start mb-4">
                            <a href="{{ route('atletas.create') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Novo
                                Atleta</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg shadow-md">
                                <thead>
                                    <tr
                                        class="bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-200 uppercase text-sm leading-normal">
                                        <th class="py-3 px-6 text-left">Foto</th>
                                        <th class="py-3 px-6 text-left">CPF</th>
                                        <th class="py-3 px-6 text-left">Nome</th>
                                        <th class="py-3 px-6 text-left">Time</th>
                                        <th class="py-3 px-6 text-left">Idade</th>
                                        <th class="py-3 px-6 text-left">Categoria</th>
                                        <th class="py-3 px-6 text-left">Celular</th>
                                        <th class="py-3 px-6 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 dark:text-gray-300 text-sm font-light">
                                    @foreach ($atletas as $atleta)
                                        <tr
                                            class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="py-3 px-6 text-left">
                                                <img src="{{ $atleta->atl_foto_url }}" alt="Foto {{ $atleta->atl_nome }}"
                                                    class="h-8 w-8 object-cover rounded-full inline-block">
                                            </td>
                                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                                {{ $atleta->atl_cpf_formatted }}
                                            </td>
                                            <td class="py-3 px-6 text-left">
                                                {{ $atleta->atl_nome }}
                                                @if($atleta->cartaoImpresso())
                                                    <span title="Cartão {{ date('Y') }} Impresso"
                                                        class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-green-500 rounded-full">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                        </svg>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-6 text-left">{{ $atleta->time->tim_nome ?? 'N/A' }}</td>
                                            <td class="py-3 px-6 text-left">
                                                {{ $atleta->atl_dt_nasc ? \Carbon\Carbon::parse($atleta->atl_dt_nasc)->age : 'N/A' }}
                                            </td>
                                            <td class="py-3 px-6 text-left">
                                                {{ $atleta->categoria ? $atleta->categoria->cto_nome : 'N/A' }}
                                            </td>
                                            <td class="py-3 px-6 text-left">{{ $atleta->atl_celular_formatted }}</td>
                                            <td class="py-3 px-6 text-center">
                                                <div class="flex item-center justify-center">
                                                    <a href="{{ route('atletas.show', $atleta->atl_id) }}"
                                                        class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('atletas.edit', $atleta->atl_id) }}"
                                                        class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('atletas.inactivate', $atleta->atl_id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Tem certeza que deseja {{ $atleta->atl_ativo ? 'desativar' : 'ativar' }} este atleta?');"
                                                        class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            title="{{ $atleta->atl_ativo ? 'Desativar' : 'Ativar' }} Atleta"
                                                            class="flex items-center justify-center w-full h-full">
                                                            @if($atleta->atl_ativo)
                                                                {{-- Ícone para Desativar (X ou Stop) --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                                </svg>
                                                            @else
                                                                {{-- Ícone para Ativar (Check ou Play) --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            @endif
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('atletas.destroy', $atleta->atl_id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Tem certeza que deseja excluir este atleta?');"
                                                        class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>

                                                    @if(auth()->user()->hasRole('Administrador') && !$atleta->cartaoImpresso())
                                                        <form action="{{ route('atletas.markPrinted', $atleta->atl_id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Confirmar que o cartão foi impresso?');"
                                                            class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110 flex items-center">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" title="Marcar Cartão como Impresso">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $atletas->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>