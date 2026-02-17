<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Documentos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @hasrole('Administrador')
                    <div class="mb-4">
                        <a href="{{ route('documentos.create') }}" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Adicionar Novo Documento
                        </a>
                    </div>
                    @endhasrole

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($documentos as $documento)
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-600">
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $documento->titulo }}</h3>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">{{ $documento->descricao }}</p>
                                    
                                    <div class="flex items-center justify-between mt-4">
                                        <div class="flex items-center">
                                            @if($documento->tipo == 'pdf')
                                                <span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-800">PDF</span>
                                            @else
                                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">Imagem</span>
                                            @endif
                                            
                                            <span class="bg-gray-100 text-gray-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-gray-600 dark:text-gray-300 border border-gray-500">
                                                {{ $documento->permissao }}
                                            </span>
                                            <a href="{{ $documento->url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 text-sm font-medium">Visualizar/Baixar</a>
                                        </div>

                                        @hasrole('Administrador')
                                        <div class="flex space-x-2">
                                            <a href="{{ route('documentos.edit', $documento->id) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                            <form action="{{ route('documentos.destroy', $documento->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este documento?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                        @endhasrole
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-8">
                                Nenhum documento cadastrado.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $documentos->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
