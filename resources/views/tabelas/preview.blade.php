<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Preview da Classificação') }} - {{ $campeonato->cpo_nome }} / {{ $categoria->cto_nome }}
            </h2>
            <form action="{{ route('classificacao.publicar', [$campeonato->cpo_id, $categoria->cto_id]) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-md transition duration-150">
                    Aprovar e Publicar HTML
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Preview Exato do Iframe</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">O quadro abaixo mostra exatamente como a tabela ficará na página oficial da Liga.</p>
                    
                    <!-- Iframe Container para isolar o CSS e garantir o mesmo visual -->
                    <div class="border-4 border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden relative" style="height: 800px;">
                        <iframe id="preview-iframe" class="w-full h-full border-0" srcdoc="{{ view('tabelas.tabela_publica', compact('campeonato', 'categoria', 'dados'))->render() }}"></iframe>
                    </div>

                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
