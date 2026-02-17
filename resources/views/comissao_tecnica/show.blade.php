<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes do Membro da Comissão Técnica') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div id="printable-area" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex flex-col md:flex-row gap-8">
                        {{-- Coluna da Esquerda: Foto e Resumo --}}
                        <div class="w-full md:w-1/3 flex flex-col items-center">
                            <div
                                class="w-48 h-64 border-4 border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden mb-4 shadow-lg">
                                <img src="{{ $comissaoTecnica->foto_url }}" alt="Foto de {{ $comissaoTecnica->nome }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 text-center mb-1">
                                {{ $comissaoTecnica->nome }}
                            </h3>
                            <span
                                class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-2">
                                {{ $comissaoTecnica->funcao }}
                            </span>
                            <p class="text-lg text-gray-600 dark:text-gray-300 font-semibold mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ $comissaoTecnica->time->tim_nome ?? 'Sem Time Vinculado' }}
                            </p>
                            <div class="mt-2">
                                <span
                                    class="{{ $comissaoTecnica->status ? 'bg-green-200 text-green-600' : 'bg-red-200 text-red-600' }} py-1 px-3 rounded-full text-xs font-bold uppercase">
                                    {{ $comissaoTecnica->status ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </div>

                        {{-- Coluna da Direita: Detalhes --}}
                        <div class="w-full md:w-2/3">
                            <div class="mb-8">
                                <h4
                                    class="text-lg font-semibold border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 text-blue-600 dark:text-blue-400 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                    Dados Pessoais
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">CPF</p>
                                        <p class="mt-1">{{ $comissaoTecnica->cpf ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">RG</p>
                                        <p class="mt-1">{{ $comissaoTecnica->rg ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Registro LRV</p>
                                        <p class="mt-1">{{ $comissaoTecnica->registro_lrv ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Registro (CREF, CRM, etc)</p>
                                        <p class="mt-1">{{ $comissaoTecnica->documento_registro ?? '-' }}</p>
                                    </div>
                                    @if($comissaoTecnica->comprovante_documento)
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Comprovante</p>
                                            <a href="{{ $comissaoTecnica->comprovante_url }}" target="_blank"
                                                class="text-blue-500 hover:text-blue-700 underline mt-1 block">Visualizar
                                                Documento</a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-8">
                                <h4
                                    class="text-lg font-semibold border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 text-blue-600 dark:text-blue-400 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Contato
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Celular</p>
                                        <p class="mt-1">{{ $comissaoTecnica->celular ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefone</p>
                                        <p class="mt-1">{{ $comissaoTecnica->telefone ?? '-' }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">E-mail</p>
                                        <p class="mt-1">{{ $comissaoTecnica->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <h4
                                    class="text-lg font-semibold border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 text-blue-600 dark:text-blue-400 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Endereço
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                                    <div class="md:col-span-2">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Logradouro</p>
                                        <p class="mt-1">{{ $comissaoTecnica->endereco }}@if($comissaoTecnica->numero),
                                        {{ $comissaoTecnica->numero }}@endif</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Bairro</p>
                                        <p class="mt-1">{{ $comissaoTecnica->bairro ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">CEP</p>
                                        <p class="mt-1">{{ $comissaoTecnica->cep ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cidade / UF</p>
                                        <p class="mt-1">{{ $comissaoTecnica->cidade }} / {{ $comissaoTecnica->estado }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex justify-center gap-3 pt-6 border-t border-gray-200 dark:border-gray-700 no-print">
                                <a href="{{ route('comissao-tecnica.index') }}"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                    Voltar
                                </a>
                                {{-- Botão Imprimir (não funcional como view de impressao, mas talvez util para
                                impressao do navegador) --}}
                                {{--
                                <button onclick="window.print()"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                        </path>
                                    </svg>
                                    Imprimir
                                </button>
                                --}}
                                <a href="{{ route('comissao-tecnica.edit', $comissaoTecnica->id) }}"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Editar
                                </a>
                            </div>

                            <div class="mb-8 mt-6">
                                <h4
                                    class="text-lg font-semibold border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 text-blue-600 dark:text-blue-400 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .85.69 1.529 1.567 1.916C12.44 8.358 13.5 10 13.5 10v1m-7-1v1m0 0v2m0-2h4m0 0v1m0 0v2m0-2h3m0 0v1m0 0v2">
                                        </path>
                                    </svg>
                                    Histórico de Impressão de Cartões
                                </h4>
                                @if($comissaoTecnica->cartoes->where('impresso', true)->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($comissaoTecnica->cartoes->where('impresso', true)->sortByDesc('ano') as $cartao)
                                            <span
                                                class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-sm font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                                Ano {{ $cartao->ano }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm">Nenhum cartão impresso registrado.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>