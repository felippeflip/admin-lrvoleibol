<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes do Árbitro / Apontador') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col items-center md:flex-row md:items-start gap-8">
                        
                        <!-- Foto -->
                        <div class="flex-shrink-0">
                            @if($arbitro->foto)
                                <img class="h-48 w-48 rounded-full object-cover shadow-lg border-4 border-gray-200 dark:border-gray-700" 
                                     src="{{ $arbitro->foto_url }}" 
                                     alt="{{ $arbitro->name }}">
                            @else
                                <div class="h-48 w-48 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center shadow-lg border-4 border-gray-200 dark:border-gray-700">
                                    <svg class="h-24 w-24 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Informações -->
                        <div class="flex-grow space-y-4 text-center md:text-left w-full">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $arbitro->name }}</h3>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">
                                    {{ $arbitro->roles->pluck('name')->implode(', ') }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Apelido</span>
                                    <span class="text-lg font-medium text-gray-900 dark:text-white">{{ $arbitro->apelido ?? '-' }}</span>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Registro LRV</span>
                                    <span class="text-lg font-medium text-gray-900 dark:text-white">{{ $arbitro->lrv ?? '-' }}</span>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Categoria</span>
                                    <span class="text-lg font-medium text-gray-900 dark:text-white">{{ $arbitro->tipo_arbitro ?? 'N/A' }}</span>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <span class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Contato (Celular)</span>
                                    <span class="text-lg font-medium text-gray-900 dark:text-white">{{ $arbitro->telefone ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-center md:justify-end">
                        <a href="{{ route('arbitros.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Voltar para a Lista
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
