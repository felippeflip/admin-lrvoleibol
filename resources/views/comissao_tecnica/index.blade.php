<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Comissão Técnica') }}
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
                            <form method="GET" action="{{ route('comissao-tecnica.index') }}">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Nome -->
                                    <div class="col-span-1 md:col-span-1">
                                        <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                                        <input type="text" name="nome" id="nome" value="{{ request('nome') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400"
                                            placeholder="Nome">
                                    </div>
                                    
                                    <!-- CPF -->
                                    <div class="col-span-1 md:col-span-1">
                                        <label for="cpf" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CPF</label>
                                        <input type="text" name="cpf" id="cpf" value="{{ request('cpf') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100 placeholder-gray-400"
                                            placeholder="CPF">
                                    </div>

                                    <!-- Função -->
                                    <div class="col-span-1 md:col-span-1">
                                        <label for="funcao" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Função</label>
                                        <select name="funcao" id="funcao" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                            <option value="">Todas</option>
                                            @foreach ($funcoes as $funcao)
                                                <option value="{{ $funcao }}" {{ request('funcao') == $funcao ? 'selected' : '' }}>{{ $funcao }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-span-1 md:col-span-1">
                                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                            <option value="">Todos</option>
                                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativo</option>
                                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativo</option>
                                        </select>
                                    </div>

                                    <!-- Time (Admin Only) -->
                                    @if(auth()->user()->hasRole('Administrador'))
                                    <div class="col-span-1 md:col-span-4">
                                        <label for="time_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time</label>
                                        <select name="time_id" id="time_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-100">
                                            <option value="">Todos os Times</option>
                                            @foreach ($times as $time)
                                                <option value="{{ $time->tim_id }}" {{ request('time_id') == $time->tim_id ? 'selected' : '' }}>{{ $time->tim_nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    <!-- Botões -->
                                    <div class="col-span-1 md:col-span-4 flex justify-end space-x-2">
                                        <button type="submit"
                                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Filtrar</button>
                                        <a href="{{ route('comissao-tecnica.index') }}"
                                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">Limpar</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="flex justify-start mb-4">
                            <a href="{{ route('comissao-tecnica.create') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Novo Membro</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg shadow-md">
                                <thead>
                                    <tr
                                        class="bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-200 uppercase text-sm leading-normal">
                                        <th class="py-3 px-6 text-left">Foto</th>
                                        <th class="py-3 px-6 text-left">Nome</th>
                                        <th class="py-3 px-6 text-left">Função</th>
                                        <th class="py-3 px-6 text-left">Documento</th>
                                        <th class="py-3 px-6 text-left">Time</th>
                                        <th class="py-3 px-6 text-left">Status</th>
                                        <th class="py-3 px-6 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 dark:text-gray-300 text-sm font-light">
                                    @forelse ($comissao as $membro)
                                        <tr
                                            class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="py-3 px-6 text-left">
                                                <img src="{{ $membro->foto_url }}" alt="Foto {{ $membro->nome }}"
                                                    class="h-10 w-10 object-cover rounded-full inline-block border-2 border-gray-300">
                                            </td>
                                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                                {{ $membro->nome }}<br>
                                                <span class="text-xs text-gray-500">{{ $membro->cpf }}</span>
                                            </td>
                                            <td class="py-3 px-6 text-left">
                                                {{ $membro->funcao }}
                                            </td>
                                            <td class="py-3 px-6 text-left">
                                                {{ $membro->documento_registro }}
                                            </td>
                                            <td class="py-3 px-6 text-left">{{ $membro->time->tim_nome ?? 'N/A' }}</td>
                                            <td class="py-3 px-6 text-left">
                                                <span class="{{ $membro->status ? 'bg-green-200 text-green-600' : 'bg-red-200 text-red-600' }} py-1 px-3 rounded-full text-xs">
                                                    {{ $membro->status ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-6 text-center">
                                                <div class="flex item-center justify-center">
                                                    <!-- Toggle Status -->
                                                    <form action="{{ route('comissao-tecnica.toggleStatus', $membro->id) }}" method="POST" class="mr-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="w-4 transform hover:scale-110" title="{{ $membro->status ? 'Desativar' : 'Ativar' }}">
                                                            @if($membro->status)
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-red-500">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                                                </svg>
                                                            @else
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-500">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                </svg>
                                                            @endif
                                                        </button>
                                                    </form>

                                                    <!-- Edit -->
                                                    <a href="{{ route('comissao-tecnica.edit', $membro->id) }}"
                                                        class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110" title="Editar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </a>
                                                    <!-- Delete -->
                                                    <form action="{{ route('comissao-tecnica.destroy', $membro->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Tem certeza que deseja excluir este membro?');"
                                                        class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Excluir">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    @if($membro->comprovante_documento)
                                                     <a href="{{ $membro->comprovante_url }}" target="_blank"
                                                        class="w-4 mr-2 transform hover:text-blue-500 hover:scale-110" title="Ver Documento">
                                                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                        </svg>
                                                    </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-3 px-6 text-center">Nenhum registro encontrado.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $comissao->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
