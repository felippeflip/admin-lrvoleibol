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
                            <div
                                class="w-48 h-64 border-4 border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden mb-4 shadow-lg">
                                <img src="{{ $atleta->atl_foto_url }}" alt="Foto de {{ $atleta->atl_nome }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 text-center mb-1">
                                {{ $atleta->atl_nome }}</h3>
                            <span
                                class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-2">
                                {{ $atleta->categoria->cto_nome ?? 'Sem Categoria' }}
                            </span>
                            <p class="text-lg text-gray-600 dark:text-gray-300 font-semibold mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ $atleta->time->tim_nome ?? 'Sem Time Vinculado' }}
                            </p>

                            <div class="w-full bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-inner">
                                <div class="grid grid-cols-2 gap-4 text-center">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Idade</p>
                                        <p class="font-semibold text-lg">
                                            {{ $atleta->atl_dt_nasc ? \Carbon\Carbon::parse($atleta->atl_dt_nasc)->age . ' anos' : '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Sexo</p>
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
                                        <p class="mt-1">{{ $atleta->atl_cpf_formatted ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">RG</p>
                                        <p class="mt-1">{{ $atleta->atl_rg_formatted ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Registro LRV</p>
                                        <p class="mt-1">{{ $atleta->atl_resg ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de
                                            Nascimento</p>
                                        <p class="mt-1">{{ $atleta->atl_dt_nasc_formatted ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ano de Inscrição
                                        </p>
                                        <p class="mt-1">{{ $atleta->atl_ano_insc ?? '-' }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Documento de Comprovação</p>
                                        @if($atleta->atl_documento)
                                            <a href="{{ $atleta->atl_documento_url }}" target="_blank" class="mt-1 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">Visualizar Documento</a>
                                        @else
                                            <p class="mt-1 text-sm text-gray-400">Não anexado</p>
                                        @endif
                                    </div>
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
                                        <p class="mt-1">{{ $atleta->atl_endereco }}@if($atleta->atl_numero),
                                        {{ $atleta->atl_numero }}@endif</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Bairro</p>
                                        <p class="mt-1">{{ $atleta->atl_bairro ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">CEP</p>
                                        <p class="mt-1">
                                            {{ $atleta->atl_cep ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $atleta->atl_cep) : '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cidade / UF</p>
                                        <p class="mt-1">{{ $atleta->atl_cidade }} / {{ $atleta->atl_estado }}</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex justify-center gap-3 pt-6 border-t border-gray-200 dark:border-gray-700 no-print">
                                <a href="{{ route('atletas.index') }}"
                                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                    Voltar
                                </a>
                                <a href="{{ route('atletas.print', $atleta->atl_id) }}" target="_blank"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                        </path>
                                    </svg>
                                    Imprimir
                                </a>
                                <a href="{{ route('atletas.edit', $atleta->atl_id) }}"
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

                            <div class="mb-8">
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
                                @if($atleta->cartoes->where('atc_impresso', true)->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($atleta->cartoes->where('atc_impresso', true)->sortByDesc('atc_ano') as $cartao)
                                            <span
                                                class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-sm font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                                Ano {{ $cartao->atc_ano }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm">Nenhum cartão impresso registrado.</p>
                                @endif
                            <div class="mb-4">
                                <h4 class="text-lg font-semibold border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 text-blue-600 dark:text-blue-400 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    Histórico de Transferências
                                </h4>
                                @if($atleta->historicoTransferencias->count() > 0)
                                    <div class="relative max-h-60 overflow-y-auto pr-2">
                                        <div class="absolute w-px h-full bg-gray-200 dark:bg-gray-700 left-2.5 top-0 mt-4 rounded-full"></div>
                                        <ul class="space-y-4 relative">
                                            @foreach($atleta->historicoTransferencias()->orderBy('created_at', 'desc')->get() as $historico)
                                                <li class="relative pl-8">
                                                    <span class="absolute flex items-center justify-center w-5 h-5 bg-indigo-100 rounded-full -left-0 top-1 ring-4 ring-white dark:ring-gray-800 dark:bg-indigo-900">
                                                        <svg class="w-2.5 h-2.5 text-indigo-800 dark:text-indigo-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                                        </svg>
                                                    </span>
                                                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1">
                                                        Transferido para: <span class="text-indigo-600 dark:text-indigo-400">{{ $historico->timeDestino->tim_nome ?? 'N/A' }}</span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        De: {{ $historico->timeOrigem->tim_nome ?? 'Sem time / Desconhecido' }} &bull; Em: {{ \Carbon\Carbon::parse($historico->created_at)->format('d/m/Y \à\s H:i') }}
                                                    </p>
                                                    @if($historico->user)
                                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Por: {{ $historico->user->name }}</p>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">Nenhum histórico de transferências registrado.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>