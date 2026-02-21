<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Informar Resultado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @php
                        $status = $jogo->jgo_res_status ?? 'nao_informado';
                        $isApproved = $status === 'aprovado';
                        // Fix for Admin Check: Check multiple common role names
                        $isAdmin = auth()->user()->hasRole(['admin', 'Admin', 'Administrador', 'Super Admin']) || auth()->user()->can('manage team');
                    @endphp

                    {{-- Header & Actions --}}
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-3">
                            <span class="text-sm uppercase tracking-wide text-gray-500 font-bold">Status:</span>
                            @if($status == 'aprovado')
                                <span
                                    class="bg-green-100 text-green-800 font-bold px-3 py-1 rounded-full text-sm flex items-center shadow-sm dark:bg-green-900 dark:text-green-300">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    APROVADO
                                </span>
                            @elseif($status == 'pendente')
                                <span
                                    class="bg-yellow-100 text-yellow-800 font-bold px-3 py-1 rounded-full text-sm flex items-center shadow-sm dark:bg-yellow-900 dark:text-yellow-300">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    PENDENTE
                                </span>
                            @else
                                <span
                                    class="bg-gray-100 text-gray-800 font-bold px-3 py-1 rounded-full text-sm shadow-sm dark:bg-gray-700 dark:text-gray-300">NÃO
                                    INFORMADO</span>
                            @endif
                        </div>

                        {{-- Approve Button (Admin Only & Pending) --}}
                        @if($isAdmin && $status == 'pendente')
                            <form action="{{ route('resultados.approve', $jogo->jgo_id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Tem certeza que deseja APROVAR este resultado? Após aprovação, não será possível editar.');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg transform hover:scale-105 transition duration-150 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Aprovar Resultado
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Display Standard Validation Errors --}}
                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm"
                            role="alert">
                            <p class="font-bold">Erros de Validação:</p>
                            <ul class="list-disc ml-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm"
                            role="alert">
                            <p class="font-bold">Atenção</p>
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('resultados.store', $jogo->jgo_id) }}" method="POST">
                        @csrf

                        <fieldset {{ ($isApproved && !$isAdmin) ? 'disabled' : '' }}>

                            {{-- Scoreboard Header (Compact) --}}
                            <div
                                class="bg-gradient-to-r from-blue-900 to-indigo-900 text-white rounded-t-xl p-4 shadow-lg">
                                <div class="flex justify-between items-center text-center">
                                    <div class="w-1/3">
                                        <h2 class="text-xl md:text-2xl font-bold truncate px-2" title="{{ $mandante }}">
                                            {{ $mandante }}</h2>
                                    </div>
                                    <div class="w-1/3 flex flex-col justify-center items-center">
                                        <span class="text-2xl md:text-4xl font-black text-yellow-400 italic">VS</span>
                                    </div>
                                    <div class="w-1/3">
                                        <h2 class="text-xl md:text-2xl font-bold truncate px-2"
                                            title="{{ $visitante }}">{{ $visitante }}</h2>
                                    </div>
                                </div>
                            </div>

                            {{-- Sets Container (Vertical List for Full Names) --}}
                            <div
                                class="bg-gray-50 dark:bg-gray-700 rounded-b-xl shadow-inner border border-t-0 border-gray-200 dark:border-gray-600 p-4">
                                <div class="flex flex-col space-y-3">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div
                                            class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-gray-100 dark:border-gray-600 flex items-center justify-between">

                                            {{-- Set Label --}}
                                            <div
                                                class="w-16 flex-shrink-0 font-bold text-gray-500 dark:text-gray-400 uppercase text-sm border-r border-gray-200 dark:border-gray-600 mr-2 md:mr-4">
                                                Set {{ $i }}
                                            </div>

                                            <div class="flex-grow flex items-center justify-center space-x-2 md:space-x-4">
                                                {{-- Mandante Side --}}
                                                <div class="flex items-center justify-end flex-1 space-x-2 md:space-x-3">
                                                    <span
                                                        class="text-xs md:text-sm text-blue-600 font-bold text-right leading-tight"
                                                        title="{{ $mandante }}">
                                                        {{ $mandante }}
                                                    </span>
                                                    <input type="number" name="sets[{{ $i }}][mandante]"
                                                        value="{{ old('sets.' . $i . '.mandante', isset($sets[$i]) ? $sets[$i]->set_pontos_mandante : '') }}"
                                                        class="w-14 md:w-16 text-center text-lg font-bold border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 p-1 flex-shrink-0"
                                                        placeholder="0" min="0">
                                                </div>

                                                <span class="text-gray-300 text-sm font-light">x</span>

                                                {{-- Visitante Side --}}
                                                <div class="flex items-center justify-start flex-1 space-x-2 md:space-x-3">
                                                    <input type="number" name="sets[{{ $i }}][visitante]"
                                                        value="{{ old('sets.' . $i . '.visitante', isset($sets[$i]) ? $sets[$i]->set_pontos_visitante : '') }}"
                                                        class="w-14 md:w-16 text-center text-lg font-bold border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded focus:ring-red-500 focus:border-red-500 disabled:bg-gray-100 p-1 flex-shrink-0"
                                                        placeholder="0" min="0">
                                                    <span
                                                        class="text-xs md:text-sm text-red-600 font-bold text-left leading-tight"
                                                        title="{{ $visitante }}">
                                                        {{ $visitante }}
                                                    </span>
                                                </div>
                                            </div>

                                        </div>
                                    @endfor
                                </div>
                            </div>

                        </fieldset>

                        <div class="mt-6 flex justify-end space-x-4">
                            <a href="{{ route('jogos.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition">
                                Voltar
                            </a>

                            @if(!$isApproved || $isAdmin)
                                <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow-md transform hover:scale-105 transition duration-150">
                                    {{ $isApproved ? 'Salvar Correção e Reverter para Pendente' : 'Salvar Resultado' }}
                                </button>
                            @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>