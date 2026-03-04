<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Confirmar Transferência de Membro da Comissão') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    
                    @if (session('warning'))
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span class="font-bold">Atenção:</span>
                            </div>
                            <p class="mt-2">{{ session('warning') }}</p>
                        </div>
                    @endif

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-8 border border-gray-200 dark:border-gray-600">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">Detalhes do Membro Encontrado</h3>
                        
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="flex-shrink-0 flex justify-center w-full md:w-auto">
                                <div class="w-32 h-40 border-2 border-gray-300 dark:border-gray-500 rounded-lg overflow-hidden relative shadow-md">
                                    <img src="{{ $comissaoTecnica->foto_url }}" alt="Foto do membro" class="w-full h-full object-cover">
                                </div>
                            </div>
                            <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Nome Mapeado:</p>
                                    <p class="text-lg text-gray-800 dark:text-gray-200 font-medium">{{ $comissaoTecnica->nome }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Função Atual:</p>
                                    <p class="text-lg text-gray-800 dark:text-gray-200 font-medium">{{ $comissaoTecnica->funcao }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Registro LRV:</p>
                                    <p class="text-lg text-gray-800 dark:text-gray-200 font-medium">{{ $comissaoTecnica->registro_lrv ?? 'Não informado' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Time Atual:</p>
                                    <p class="text-lg text-red-600 dark:text-red-400 font-bold flex items-center">
                                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        {{ $comissaoTecnica->time->tim_nome ?? 'Sem time vinculado' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-indigo-50 dark:bg-indigo-900/40 rounded-lg p-6 border border-indigo-100 dark:border-indigo-800 flex items-center justify-between flex-col sm:flex-row gap-4">
                        <div class="text-center sm:text-left">
                            <h4 class="text-lg font-bold text-indigo-800 dark:text-indigo-300">Deseja trazer este membro para sua equipe?</h4>
                            <p class="text-indigo-600 dark:text-indigo-400 mt-1">
                                O membro será transferido para a equipe <span class="font-bold text-indigo-900 dark:text-indigo-200 px-1">{{ $novoTime->tim_nome }}</span>.
                            </p>
                            <p class="text-sm text-indigo-500 dark:text-indigo-400 mt-2">Esta ação ficará registrada no histórico de transferências da comissão técnica.</p>
                        </div>
                        
                        <div class="flex-shrink-0 flex items-center gap-3">
                            <a href="{{ route('comissao-tecnica.index') }}" 
                               class="inline-flex justify-center items-center py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-300 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 whitespace-nowrap shadow-sm">
                                Cancelar
                            </a>
                            
                            <form action="{{ route('comissao-tecnica.transferir', $comissaoTecnica->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="novo_time_id" value="{{ $novoTime->tim_id }}">
                                <button type="submit" 
                                        class="inline-flex justify-center items-center text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-bold rounded-lg text-sm px-5 py-2.5 text-center dark:bg-indigo-500 dark:hover:bg-indigo-600 dark:focus:ring-indigo-800 transition-colors shadow-sm whitespace-nowrap">
                                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    Confirmar Transferência
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
