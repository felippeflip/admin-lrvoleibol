<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Importar Resultado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Mensagem de Sucesso -->
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md fade-out-message" style="background-color: #58FF33;">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Mensagem de Erro -->
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-md fade-out-message" style="background-color: #FF3633;">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('resultados.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="category" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Categoria</label>
                            <select id="category" name="category" class="form-select mt-1 block w-full">
                                <option value=""></option>
                                @foreach($eventCategorys as $category)
                                    <option value="{{ $category->term->name }}">{{ $category->term->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="pdf" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Selecione o HTML</label>
                            <input id="pdf" name="pdf" type="file" accept=".pdf" class="form-input mt-1 block w-full" required>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                {{ __('Importar HTML') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript para ocultar mensagens após 3 segundos -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                const messages = document.querySelectorAll('.fade-out-message');
                messages.forEach(function (message) {
                    message.classList.add('opacity-0');
                    message.classList.add('transition-opacity');
                    message.classList.add('duration-1000');
                });
            }, 3000); // 3 segundos
        });
    </script>
</x-app-layout>
