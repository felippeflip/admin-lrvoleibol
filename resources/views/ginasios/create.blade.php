<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Novo Ginásio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('ginasios.store') }}" method="POST" id="ginasioForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Nome (Full width on mobile, 2/3 on desktop) -->
                            <div class="md:col-span-2">
                                <label for="gin_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome do Ginásio *</label>
                                <input type="text" name="gin_nome" id="gin_nome" value="{{ old('gin_nome') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>

                             <!-- Telefone -->
                            <div>
                                <label for="gin_telefone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label>
                                <input type="text" name="gin_telefone" id="gin_telefone" value="{{ old('gin_telefone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                             <!-- Email -->
                            <div class="md:col-span-2">
                                <label for="gin_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input type="email" name="gin_email" id="gin_email" value="{{ old('gin_email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                             <!-- Time (Opcional) -->
                            <div>
                                <label for="gin_tim_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time Responsável (Opcional)</label>
                                <select name="gin_tim_id" id="gin_tim_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Selecione um Time (ou deixe em branco)</option>
                                    @foreach($times as $time)
                                        <option value="{{ $time->tim_id }}" {{ old('gin_tim_id') == $time->tim_id ? 'selected' : '' }}>{{ $time->tim_nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-3 border-t border-gray-200 dark:border-gray-700 my-4"></div>
                            
                            <!-- CEP (Highligthed) -->
                            <div class="md:col-span-1">
                                <label for="gin_cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP *</label>
                                <input type="text" name="gin_cep" id="gin_cep" value="{{ old('gin_cep') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Informe o CEP para preenchimento automático do endereço.</p>
                            </div>

                            <!-- Endereço -->
                            <div class="md:col-span-2">
                                <label for="gin_endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Endereço *</label>
                                <input type="text" name="gin_endereco" id="gin_endereco" value="{{ old('gin_endereco') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>

                            <!-- Número -->
                            <div>
                                <label for="gin_numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número *</label>
                                <input type="text" name="gin_numero" id="gin_numero" value="{{ old('gin_numero') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>

                            <!-- Complemento -->
                            <div class="md:col-span-2">
                                <label for="gin_complemento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Complemento</label>
                                <input type="text" name="gin_complemento" id="gin_complemento" value="{{ old('gin_complemento') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Bairro -->
                            <div>
                                <label for="gin_bairro" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bairro *</label>
                                <input type="text" name="gin_bairro" id="gin_bairro" value="{{ old('gin_bairro') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>

                            <!-- Cidade -->
                            <div>
                                <label for="gin_cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade *</label>
                                <input type="text" name="gin_cidade" id="gin_cidade" value="{{ old('gin_cidade') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="gin_estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado *</label>
                                <input type="text" name="gin_estado" id="gin_estado" value="{{ old('gin_estado') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('ginasios.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
