<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Transferir Atleta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-8 border border-gray-200 dark:border-gray-600">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">Informações do Atleta</h3>
                        
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="flex-shrink-0 flex justify-center w-full md:w-auto">
                                <div class="w-32 h-40 border-2 border-gray-300 dark:border-gray-500 rounded-lg overflow-hidden relative shadow-md">
                                    <img src="{{ $atleta->atl_foto_url }}" alt="Foto do atleta" class="w-full h-full object-cover">
                                </div>
                            </div>
                            <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Nome:</p>
                                    <p class="text-lg text-gray-800 dark:text-gray-200 font-medium">{{ $atleta->atl_nome }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Time Atual:</p>
                                    <p class="text-lg text-red-600 dark:text-red-400 font-bold flex items-center">
                                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        {{ $atleta->time->tim_nome ?? 'Sem time vinculado' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('atletas.transferir', $atleta->atl_id) }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label for="novo_time_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Selecione o Novo Time (Destino):</label>
                            <select name="novo_time_id" id="novo_time_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500 text-gray-700 dark:text-gray-300" required>
                                <option value="">-- Selecione o time --</option>
                                @foreach($times as $time)
                                    <option value="{{ $time->tim_id }}">{{ $time->tim_nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('atletas.index') }}" class="inline-flex justify-center items-center py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-300 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 whitespace-nowrap shadow-sm">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex justify-center items-center text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-bold rounded-lg text-sm px-5 py-2.5 text-center dark:bg-indigo-500 dark:hover:bg-indigo-600 dark:focus:ring-indigo-800 transition-colors shadow-sm whitespace-nowrap">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                Efetivar Transferência
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
