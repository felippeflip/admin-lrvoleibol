<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes do Atleta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div id="printable-area" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="flex flex-col md:flex-row gap-8">
                        {{-- Coluna da Esquerda: Foto e Resumo --}}
                        <div class="w-full md:w-1/3 flex flex-col items-center">
                            <div class="w-48 h-64 border-4 border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden mb-4 shadow-lg">
                                <img src="{{ $atleta->atl_foto_url }}" alt="Foto de {{ $atleta->atl_nome }}" class="w-full h-full object-cover">
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 text-center mb-1">{{ $atleta->atl_nome }}</h3>
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-4">
                                {{ $atleta->atl_categoria ?? 'Sem Categoria' }}
                            </span>

                            <div class="w-full bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-inner">
                                <div class="grid grid-cols-2 gap-4 text-center">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Idade</p>
                                        <p class="font-semibold text-lg">{{ $atleta->atl_dt_nasc ? \Carbon\Carbon::parse($atleta->atl_dt_nasc)->age . ' anos' : '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sexo</p>
                                        <p class="font-semibold text-lg">
                                            @if($atleta->atl_sexo == 'M') Masculino 
                                            @elseif($atleta->atl_sexo == 'F') Feminino 
                                            @else Outro @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Coluna da Direita: Detalhes --}}
                        <div class="w-full md:w-2/3">
                            <div class="mb-8">
                                <h4 class="text-lg font-semibold border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 text-blue-600 dark:text-blue-400 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Dados Pessoais
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">CPF</p>
                                        <p class="mt-1">{{ $atleta->atl_cpf_formatted ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">RG</p>
                                        <p class="mt-1">{{ $atleta->atl_rg_formatted ?? '-' }} @if($atleta->atl_resg) <span class="text-xs text-gray-400">({{ $atleta->atl_resg }})</span> @endif</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de Nascimento</p>
                                        <p class="mt-1">{{ $atleta->atl_dt_nasc_formatted ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ano de Inscrição</p>
                                        <p class="mt-1">{{ $atleta->atl_ano_insc ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h4 class="text-lg font-semibold border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 text-blue-600 dark:text-blue-400 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    Contato
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Celular</p>
                                        <p class="mt-1">{{ $atleta->atl_celular_formatted ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefone</p>
                                        <p class="mt-1">{{ $atleta->atl_telefone_formatted ?? '-' }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">E-mail</p>
                                        <p class="mt-1">{{ $atleta->atl_email ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h4 class="text-lg font-semibold border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 text-blue-600 dark:text-blue-400 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Endereço
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                                    <div class="md:col-span-2">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Logradouro</p>
                                        <p class="mt-1">{{ $atleta->atl_endereco }}@if($atleta->atl_numero), {{ $atleta->atl_numero }}@endif</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Bairro</p>
                                        <p class="mt-1">{{ $atleta->atl_bairro ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">CEP</p>
                                        <p class="mt-1">{{ $atleta->atl_cep ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $atleta->atl_cep) : '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cidade / UF</p>
                                        <p class="mt-1">{{ $atleta->atl_cidade }} / {{ $atleta->atl_estado }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-center gap-3 pt-6 border-t border-gray-200 dark:border-gray-700 no-print">
                                <a href="{{ route('atletas.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                    Voltar
                                </a>
                                <button onclick="window.print()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Imprimir
                                </button>
                                <a href="{{ route('atletas.edit', $atleta->atl_id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Editar
                                </a>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-area, #printable-area * {
                visibility: visible;
            }
            #printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
