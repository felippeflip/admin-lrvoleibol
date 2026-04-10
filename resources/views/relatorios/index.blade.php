<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Relatórios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Selecione um relatório para visualizar</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Relatório de Atletas por Time -->
                        <a href="{{ route('relatorios.atletas-por-time') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 transition">
                            <div class="flex items-center mb-2">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <h5 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Atletas por Time</h5>
                            </div>
                            <p class="font-normal text-gray-700 dark:text-gray-400">Relação completa de atletas agrupados por time. Ideal para protocolo de entrega de carteirinhas.</p>
                        </a>

                        <!-- Relatório de Comissão por Time -->
                        <a href="{{ route('relatorios.comissao-por-time') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 transition">
                            <div class="flex items-center mb-2">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <h5 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Comissão por Time</h5>
                            </div>
                            <p class="font-normal text-gray-700 dark:text-gray-400">Relação de membros da comissão técnica agrupados por time. Ideal para protocolo de credenciais.</p>
                        </a>

                        <!-- Relatório de Tabelas Geradas -->
                        @hasrole('Administrador')
                        <a href="{{ route('relatorios.tabelas-geradas') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 transition">
                            <div class="flex items-center mb-2">
                                <!-- Ícone de Código/Documento -->
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                </svg>
                                <h5 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Tabelas Geradas</h5>
                            </div>
                            <p class="font-normal text-gray-700 dark:text-gray-400">Visualização e acompanhamento dos arquivos HTML estáticos gerados contendo a tabela de classificação.</p>
                        </a>
                        @endhasrole

                        <!-- Espaço para futuros relatórios -->
                        <div class="block p-6 bg-gray-50 border border-gray-200 rounded-lg shadow opacity-50 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700">
                             <div class="flex items-center mb-2">
                                <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h5 class="text-xl font-bold tracking-tight text-gray-400">Em Breve</h5>
                            </div>
                            <p class="font-normal text-gray-400">Novos relatórios estatísticos e gerenciais serão adicionados aqui.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
