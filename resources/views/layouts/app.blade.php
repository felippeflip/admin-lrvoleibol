<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

            {{-- O SEU NAVBAR EXISTENTE ESTÁ SEMPRE NO TOPO --}}
            @include('layouts.navigation')

            {{-- Container flex para o Sidebar e o Conteúdo Principal --}}
            {{-- Adicionamos pt-16 para que este container comece APÓS o navbar (h-16) --}}
            <div class="flex pt-16 sm:pt-0"> {{-- Importante: 'pt-16' para empurrar para baixo do navbar --}}

                {{-- O SEU SIDEBAR --}}
                {{-- Ele se posicionará fixo no topo, mas visualmente será coberto pelo navbar em mobile
                     e começará abaixo dele em desktop por causa do 'sm:top-16' --}}
                @include('layouts.sidebar')

                {{-- Conteúdo Principal da Página (incluindo o header da página e o slot) --}}
                {{-- A classe 'sm:ml-64' empurra este conteúdo para a direita quando o sidebar está aberto --}}
                <div class="p-4 sm:ml-64 w-full"> {{-- 'w-full' para ocupar o espaço restante --}}

                    {{-- Botão para abrir o sidebar em telas pequenas --}}
                    {{-- IDEALMENTE: Este botão DEVE estar dentro do seu layouts.navigation.blade.php
                         para uma melhor experiência. Se você já o moveu para lá, pode remover este trecho. --}}
                    <button data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar" aria-controls="sidebar-multi-level-sidebar" type="button" class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                        </svg>
                    </button>

                    @isset($header)
                        <header class="bg-white dark:bg-gray-800 shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main>
                        {{ $slot }}
                    </main>
                </div> {{-- Fechamento da div do conteúdo principal --}}
            </div> {{-- Fechamento da div flex principal --}}
        </div>
    </body>
</html>