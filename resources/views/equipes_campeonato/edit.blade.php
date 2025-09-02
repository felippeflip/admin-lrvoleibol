<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Inscrição de Equipe no Campeonato') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form action="{{ route('equipes.campeonato.update', ['campeonato' => $campeonato->cpo_id, 'equipe' => $equipe->eqp_id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="campeonato_nome" class="block text-gray-700 dark:text-gray-300 mb-2">Campeonato:</label>
                            <input type="text" id="campeonato_nome" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500 bg-gray-100 dark:bg-gray-600" value="{{ $campeonato->cpo_nome }}" disabled>
                        </div>
                        
                        <div class="mb-4">
                            <label for="equipe_nome" class="block text-gray-700 dark:text-gray-300 mb-2">Equipe:</label>
                            <input type="text" id="equipe_nome" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500 bg-gray-100 dark:bg-gray-600" value="{{ $equipe->eqp_nome_detalhado }}" disabled>
                        </div>

                        <div class="mb-4">
                            <label for="eqp_cpo_dt_inscricao" class="block text-gray-700 dark:text-gray-300 mb-2">Data de Inscrição:</label>
                            <input type="date" name="eqp_cpo_dt_inscricao" id="eqp_cpo_dt_inscricao" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('eqp_cpo_dt_inscricao', $equipe->pivot->eqp_cpo_dt_inscricao) }}">
                            @error('eqp_cpo_dt_inscricao')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="eqp_cpo_classificacaofinal" class="block text-gray-700 dark:text-gray-300 mb-2">Classificação Final:</label>
                            <input type="number" name="eqp_cpo_classificacaofinal" id="eqp_cpo_classificacaofinal" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2 rounded focus:ring-blue-500 focus:border-blue-500" value="{{ old('eqp_cpo_classificacaofinal', $equipe->pivot->eqp_cpo_classificacaofinal) }}">
                            @error('eqp_cpo_classificacaofinal')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-start mt-6 space-x-4">
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Atualizar</button>
                            <a href="{{ route('equipes.campeonato.index', $campeonato->cpo_id) }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
