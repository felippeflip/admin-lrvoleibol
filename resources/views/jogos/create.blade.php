<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Adicionar Novo Jogo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
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
                        <form id="event-form" action="{{ route('jogos.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label for="event_number" class="block text-gray-700">Nº Jogo *:</label>
                                <input type="text" name="event_number" id="event_number" class="border border-gray-300 p-2 rounded" value="{{ old('event_number') }}" required style="width: 150px;">
                            </div>
                            <div class="mb-4">
                                <label for="post_title" class="block text-gray-700">Adversários *:</label>
                                <input type="text" name="post_title" id="post_title" class="w-full border border-gray-300 p-2 rounded" value="{{ old('event_title') }}" required>
                            </div>
                            <div class="mb-4">
                                <label for="event_type" class="block text-gray-700">Campeonato *:</label>
                                <select name="event_type" id="event_type" class="w-full border border-gray-300 p-2 rounded" required>
                                    <option value="">Selecione o Campeonato</option>
                                    @foreach ($eventTypes as $type)
                                        <option value="{{ $type->term_id }}">{{ $type->term->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="event_category" class="block text-gray-700">Categoria *:</label>
                                <select name="event_category" id="event_category" class="w-full border border-gray-300 p-2 rounded" required>
                                    <option value="">Selecione a Categoria</option>
                                    @foreach ($eventCategorys as $category)
                                        <option value="{{ $category->term_id }}">{{ $category->term->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!--
                            <div class="mb-4">
                                <label class="block text-gray-700">Evento on-line *:</label>
                                <div class="flex flex-col items-start">
                                    <div class="flex items-center mb-2">
                                        <input type="radio" name="event_online" id="event_online_yes" value="yes" class="mr-2" {{ old('event_online') == 'yes' ? 'checked' : '' }} required>
                                        <label for="event_online_yes" class="mr-4">SIM</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" name="event_online" id="event_online_no" value="no" class="mr-2" {{ old('event_online') == 'no' ? 'checked' : '' }} required>
                                        <label for="event_online_no">NÃO</label>
                                    </div>
                                </div>
                            </div>
                            -->

                            <div id="offline-fields" class="mb-4">
                                <!--
                                <div class="mb-4">
                                    <label for="event_pincode" class="block text-gray-700">CEP *:</label>
                                    <input type="text" name="event_pincode" id="event_pincode" class="w-full border border-gray-300 p-2 rounded" value="{{ old('event_pincode') }}" required>
                                </div>
                                -->
                                <div class="mb-4">
                                    <label for="event_location" class="block text-gray-700">Local do Jogo *:</label>
                                    <input type="text" name="event_location" id="event_location" class="w-full border border-gray-300 p-2 rounded" value="{{ old('event_location') }}" required>
                                </div>
                                <!--
                                <div class="mb-4">
                                    <label for="event_country" class="block text-gray-700">País do Evento *:</label>
                                    <select name="event_country" id="event_country" class="w-full border border-gray-300 p-2 rounded" required>
                                        <option value="br" {{ old('event_country') == 'br' ? 'selected' : '' }}>Brasil</option>
                                    </select>
                                </div>
                                -->
                            </div>
                            <!--
                            <div class="mb-4">
                                <label for="event_banner" class="block text-gray-700">Banner do evento:</label>
                                <input type="file" name="event_banner" id="event_banner" class="w-full border border-gray-300 p-2 rounded">
                            </div>
                            
                            <div class="mb-4">
                                <label for="post_content" class="block text-gray-700">Descrição *:</label>
                                <textarea name="post_content" id="post_content" class="w-full border border-gray-300 p-2 rounded">{{ old('post_content') }}</textarea>
                            </div>
                            


                            <div class="mb-4">
                                <label for="registration_email_url" class="block text-gray-700">E-mail/URL de registro *:</label>
                                <input type="text" name="registration_email_url" id="registration_email_url" class="w-full border border-gray-300 p-2 rounded" value="{{ old('registration_email_url') }}" required>
                            </div>
                            <div class="mb-4">
                                <label for="video_url" class="block text-gray-700">Video URL (opcional):</label>
                                <input type="text" name="video_url" id="video_url" class="w-full border border-gray-300 p-2 rounded" value="{{ old('video_url') }}">
                            </div>
                            -->
                            <div class="mb-4">
                                <label for="event_start_date" class="block text-gray-700">Data de início *:</label>
                                <input type="date" name="event_start_date" id="event_start_date" class="w-full border border-gray-300 p-2 rounded" value="{{ old('event_start_date') }}" required>
                            </div>
                            <div class="mb-4">
                                <label for="event_start_time" class="block text-gray-700">Horário*:</label>
                                <input type="time" name="event_start_time" id="event_start_time" class="w-full border border-gray-300 p-2 rounded" value="{{ old('event_start_time') }}" required>
                            </div>

                            <!--
                            <div class="mb-4">
                                <label for="event_end_date" class="block text-gray-700">Data de encerramento *:</label>
                                <input type="date" name="event_end_date" id="event_end_date" class="w-full border border-gray-300 p-2 rounded" value="{{ old('event_end_date') }}" required>
                            </div>
                            <div class="mb-4">
                                <label for="event_end_time" class="block text-gray-700">Encerramento *:</label>
                                <input type="time" name="event_end_time" id="event_end_time" class="w-full border border-gray-300 p-2 rounded" value="{{ old('event_end_time') }}" required>
                            </div>
                            <div class="mb-4">
                                <label for="registration_deadline" class="block text-gray-700">Prazo de registro (opcional):</label>
                                <input type="date" name="registration_deadline" id="registration_deadline" class="w-full border border-gray-300 p-2 rounded" value="{{ old('registration_deadline') }}">
                            </div>
                            -->

                            <div class="mb-4">
                                <label for="juiz_principal" class="block text-gray-700 text-sm font-bold mb-2">Juiz 1:</label>
                                <select id="juiz_principal" name="juiz_principal" class="w-full border border-gray-300 p-2 rounded">
                                    <option value=""></option>
                                    @foreach($juizes as $juiz)
                                        <option value="{{ $juiz->id }}">{{ $juiz->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="juiz_linha1" class="block text-gray-700 text-sm font-bold mb-2">Juiz 2:</label>
                                <select id="juiz_linha1" name="juiz_linha1" class="w-full border border-gray-300 p-2 rounded">
                                    <option value=""></option>
                                    @foreach($juizes as $juiz)
                                        <option value="{{ $juiz->id }}">{{ $juiz->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="juiz_linha2" class="block text-gray-700 text-sm font-bold mb-2">Apontador:</label>
                                <select id="juiz_linha2" name="juiz_linha2" class="w-full border border-gray-300 p-2 rounded">
                                    <option value=""></option>
                                    @foreach($juizes as $juiz)
                                        <option value="{{ $juiz->id }}">{{ $juiz->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex justify-between">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Adicionar</button>
                                <a href="{{ route('jogos.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const eventOnlineYes = document.getElementById('event_online_yes');
        const eventOnlineNo = document.getElementById('event_online_no');
        const offlineFields = document.getElementById('offline-fields');

        function toggleOfflineFields() {
            if (eventOnlineNo.checked) {
                offlineFields.style.display = 'block';
                document.getElementById('event_pincode').required = true;
                document.getElementById('event_location').required = true;
                document.getElementById('event_country').required = true;
            } else {
                offlineFields.style.display = 'none';
                document.getElementById('event_pincode').required = false;
                document.getElementById('event_location').required = false;
                document.getElementById('event_country').required = false;
            }
        }

        eventOnlineYes.addEventListener('change', toggleOfflineFields);
        eventOnlineNo.addEventListener('change', toggleOfflineFields);

        window.addEventListener('DOMContentLoaded', (event) => {
            toggleOfflineFields(); // Run on page load
        });
    </script>
</x-app-layout>
