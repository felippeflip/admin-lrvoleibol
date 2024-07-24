<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Adicionar Novo Jogo') }}
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
                        <form id="event-form" action="{{ route('multi.insert.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div id="event-fields">
                                <div class="event-fieldset mb-8 p-4 border border-gray-300 rounded">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <!-- Campos do evento -->
                                        <div class="mb-4">
                                            <label for="post_title" class="block text-gray-700">Título do evento *:</label>
                                            <input type="text" name="post_title[]" class="w-full border border-gray-300 p-2 rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_type" class="block text-gray-700">Tipo de evento *:</label>
                                            <select name="event_type[]" class="w-full border border-gray-300 p-2 rounded" required>
                                                <option value="">Selecione o Tipo de Evento</option>
                                                @foreach ($eventTypes as $type)
                                                    <option value="{{ $type->term_id }}">{{ $type->term->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_category" class="block text-gray-700">Categoria do evento *:</label>
                                            <select name="event_category[]" class="w-full border border-gray-300 p-2 rounded" required>
                                                <option value="">Selecione a Categoria do Evento</option>
                                                @foreach ($eventCategorys as $category)
                                                    <option value="{{ $category->term_id }}">{{ $category->term->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-gray-700">Evento on-line *:</label>
                                            <div class="flex items-center">
                                                <input type="radio" name="event_online[0]" value="yes" class="mr-2" required>
                                                <label for="event_online_yes" class="mr-4">SIM</label>
                                                <input type="radio" name="event_online[0]" value="no" class="mr-2" required>
                                                <label for="event_online_no">NÃO</label>
                                            </div>
                                        </div>
                                        <div class="mb-4 offline-fields">
                                            <label for="event_pincode" class="block text-gray-700">CEP *:</label>
                                            <input type="text" name="event_pincode[]" class="w-full border border-gray-300 p-2 rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_location" class="block text-gray-700">Local do evento *:</label>
                                            <input type="text" name="event_location[]" class="w-full border border-gray-300 p-2 rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_country" class="block text-gray-700">País do Evento *:</label>
                                            <select name="event_country[]" class="w-full border border-gray-300 p-2 rounded" required>
                                                <option value="br">Brasil</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_banner" class="block text-gray-700">Banner do evento *:</label>
                                            <input type="file" name="event_banner[]" class="w-full border border-gray-300 p-2 rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="post_content" class="block text-gray-700">Descrição *:</label>
                                            <textarea name="post_content[]" class="w-full border border-gray-300 p-2 rounded"></textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label for="registration_email_url" class="block text-gray-700">E-mail/URL de registro *:</label>
                                            <input type="text" name="registration_email_url[]" class="w-full border border-gray-300 p-2 rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="video_url" class="block text-gray-700">Video URL (opcional):</label>
                                            <input type="text" name="video_url[]" class="w-full border border-gray-300 p-2 rounded">
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_start_date" class="block text-gray-700">Data de início *:</label>
                                            <input type="date" name="event_start_date[]" class="w-full border border-gray-300 p-2 rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_start_time" class="block text-gray-700">Início *:</label>
                                            <input type="time" name="event_start_time[]" class="w-full border border-gray-300 p-2 rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_end_date" class="block text-gray-700">Data de encerramento *:</label>
                                            <input type="date" name="event_end_date[]" class="w-full border border-gray-300 p-2 rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_end_time" class="block text-gray-700">Encerramento *:</label>
                                            <input type="time" name="event_end_time[]" class="w-full border border-gray-300 p-2 rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="registration_deadline" class="block text-gray-700">Prazo de registro (opcional):</label>
                                            <input type="date" name="registration_deadline[]" class="w-full border border-gray-300 p-2 rounded">
                                        </div>

                                        <div class="mb-4">
                                            <label for="juiz_principal" class="block text-gray-700">Juiz Principal:</label>
                                            <select name="juiz_principal[]" class="w-full border border-gray-300 p-2 rounded">
                                                <option value=""></option>
                                                @foreach($juizes as $juiz)
                                                    <option value="{{ $juiz->id }}">{{ $juiz->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-4">
                                            <label for="juiz_linha1" class="block text-gray-700">Juiz de Linha 1:</label>
                                            <select name="juiz_linha1[]" class="w-full border border-gray-300 p-2 rounded">
                                                <option value=""></option>
                                                @foreach($juizes as $juiz)
                                                    <option value="{{ $juiz->id }}">{{ $juiz->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-4">
                                            <label for="juiz_linha2" class="block text-gray-700">Juiz de Linha 2:</label>
                                            <select name="juiz_linha2[]" class="w-full border border-gray-300 p-2 rounded">
                                                <option value=""></option>
                                                @foreach($juizes as $juiz)
                                                    <option value="{{ $juiz->id }}">{{ $juiz->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end mb-4">
                                <button type="button" id="add-new-event" class="bg-green-500 text-white px-4 py-2 rounded">Adicionar Novo Jogo</button>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('add-new-event').addEventListener('click', function () {
            const eventFields = document.querySelector('.event-fieldset').cloneNode(true);
            eventFields.querySelectorAll('input, select, textarea').forEach(function (element) {
                if (element.tagName.toLowerCase() === 'input' && element.type === 'radio') {
                    element.name = element.name.replace(/\[\d+\]/, '[' + document.querySelectorAll('.event-fieldset').length + ']');
                } else {
                    element.value = '';
                }
            });
            document.getElementById('event-fields').appendChild(eventFields);
        });
    </script>
</x-app-layout>
