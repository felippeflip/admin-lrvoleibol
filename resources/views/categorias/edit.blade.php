<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Categoria') }}
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
                        <form action="{{ route('categorias.update', $wpTermTaxonomy->term_taxonomy_id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label for="name" class="block text-gray-700">CATEGORIA:</label>
                                <input type="text" name="name" id="name" class="w-full border border-gray-300 p-2 rounded" value="{{ old('name', $wpTerm->name) }}">
                            </div>
                            <div class="mb-4">
                                <label for="slug" class="block text-gray-700">SLUG:</label>
                                <input type="text" name="slug" id="slug" class="w-full border border-gray-300 p-2 rounded" value="{{ old('slug', $wpTerm->slug) }}">
                            </div>
                            <div class="mb-4">
                                <label for="description" class="block text-gray-700">Descrição:</label>
                                <textarea name="description" id="description" class="w-full border border-gray-300 p-2 rounded">{{ old('description', $wpTermTaxonomy->description)}}</textarea>
                            </div>
                           
                            <div class="flex justify-between">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Atualizar</button>
                                
                                <a href="{{ route('categorias.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Voltar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>