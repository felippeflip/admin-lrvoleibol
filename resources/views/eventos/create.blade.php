<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Adicionar Novo Campeonato') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mx-auto p-4">
                        @if ($errors->any())
                            <div class="bg-red-500 text-white p-2 my-4">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('eventos.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="cpo_nome" class="block text-gray-700">CAMPEONATO:</label>
                                <input type="text" name="cpo_nome" id="cpo_nome" class="w-full border border-gray-300 p-2 rounded" value="{{ old('cpo_nome') }}">
                            </div>
                            <div class="mb-4">
                                <label for="slug" class="block text-gray-700">SLUG:</label>
                                <input type="text" name="slug" id="slug" class="w-full border border-gray-300 p-2 rounded" value="{{ old('slug') }}">
                            </div>
                            <div class="mb-4">
                                <label for="cpo_ano" class="block text-gray-700">ANO:</label>
                                <select name="cpo_ano" id="cpo_ano" class="w-full border border-gray-300 p-2 rounded">
                                    <option value="">Selecione o ano</option>
                                    @for ($year = date('Y') + 1; $year >= 2000; $year--)
                                        <option value="{{ $year }}" {{ old('cpo_ano') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="cpo_dt_inicio" class="block text-gray-700">DATA INICIO:</label>
                                <input type="date" name="cpo_dt_inicio" id="cpo_dt_inicio" class="w-full border border-gray-300 p-2 rounded" value="{{ old('cpo_dt_inicio') }}">
                            </div>

                            <div class="mb-4">
                                <label for="cpo_dt_fim" class="block text-gray-700">DATA FIM:</label>
                                <input type="date" name="cpo_dt_fim" id="cpo_dt_fim" class="w-full border border-gray-300 p-2 rounded" value="{{ old('cpo_dt_fim') }}">
                            </div>
                           
                            <div class="flex justify-between">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Salvar</button>
                                
                                <a href="{{ route('eventos.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Voltar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.getElementById('cpo_nome').addEventListener('input', function() {
        var name = this.value;
        var slug = name.toLowerCase()
            .replace(/ /g, '-') // Substitui espaços por hífens
            .replace(/[^\w-]+/g, ''); // Remove caracteres especiais

        document.getElementById('slug').value = slug;
    });
</script>
