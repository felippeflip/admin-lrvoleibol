<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        {{-- Controle de Viewport crítico para evitar que o iOS dede zoom em inputs --}}
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} Mobile</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-gray-900 dark:text-gray-100 overflow-x-hidden selection:bg-orange-500 selection:text-white pb-20">

        {{-- Mobile Global Header / Topbar Navigation --}}
        <header class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 h-16 flex items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="font-bold text-lg text-orange-600 dark:text-orange-500 tracking-tight">
                    {{ config('app.name', 'LRVoleibol') }}
                </a>
            </div>
            
            <div class="flex items-center gap-4">
                {{-- User Profile Dropdown Simples no Mobile --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                        <div class="h-8 w-8 rounded-full bg-orange-100 dark:bg-orange-900 flex items-center justify-center text-orange-700 dark:text-orange-300 font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </button>
                    <!-- Dropdown -->
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 break-words">
                            <p class="text-sm text-gray-900 dark:text-gray-100 font-medium truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Meu Perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">Sair</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Espaçador do Header (evita conteúdo por baixo do navbar sticky) --}}
        <div class="h-16 w-full"></div>

        {{-- Mobile Main Content Area --}}
        <main class="w-full min-h-screen px-2 pb-6 pt-4">
            @yield('content')
        </main>

        <nav x-data="{ openMenu: false }" class="fixed bottom-0 left-0 right-0 z-[100] bg-white/95 dark:bg-gray-800/95 backdrop-blur-md shadow-[0_-10px_30px_rgba(0,0,0,0.08)] border-t border-gray-100 dark:border-gray-700 h-[72px] safe-area-pb">
            {{-- Menu Principal (Ribbon Limpo) --}}
            <div class="flex h-full w-full items-center justify-around px-2">
                
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center font-medium {{ request()->routeIs('dashboard') ? 'text-orange-600 dark:text-orange-500' : 'text-gray-400 dark:text-gray-500' }}">
                    <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 20 20"><path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/></svg>
                    <span class="text-[9px] font-black uppercase tracking-tighter">Início</span>
                </a>

                {{-- Jogos --}}
                @hasanyrole(['Administrador', 'Juiz'])
                <a href="{{ route('jogos.index') }}" class="flex flex-col items-center justify-center font-medium {{ request()->routeIs('jogos.*') ? 'text-orange-600 dark:text-orange-500' : 'text-gray-400 dark:text-gray-500' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 2v2M12 20v2M2 12h2M20 12h2m-13.05-7.05 1.41 1.41m9.24 9.24 1.41 1.41M4.36 17.64l1.41-1.41m9.24-9.24 1.41-1.41M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                    <span class="text-[9px] font-black uppercase tracking-tighter">Jogos</span>
                </a>
                @endhasanyrole

                {{-- Campeonatos --}}
                @hasrole('Administrador')
                <a href="{{ route('eventos.index') }}" class="flex flex-col items-center justify-center font-medium {{ request()->routeIs('eventos.*') ? 'text-orange-600 dark:text-orange-500' : 'text-gray-400 dark:text-gray-500' }}">
                    <svg class="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 20 20"><path d="M19 4h-2V3a1 1 0 0 0-2 0v1H7V3a1 1 0 0 0-2 0v1H3a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm-1 9H2v-2h16v2Zm0-4H2V6h16v3Z"/></svg>
                    <span class="text-[9px] font-black uppercase tracking-tighter">Copas</span>
                </a>
                @endhasrole

                {{-- Agenda --}}
                <a href="{{ route('agendamentos.comissao.index') }}" class="flex flex-col items-center justify-center font-medium {{ request()->routeIs('agendamentos.comissao.*') ? 'text-orange-600 dark:text-orange-500' : 'text-gray-400 dark:text-gray-500' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-[9px] font-black uppercase tracking-tighter">Agenda</span>
                </a>

                {{-- Botão MAIS / ADJUSTES (Gatilho) --}}
                <button @click="openMenu = !openMenu" class="relative group flex flex-col items-center justify-center font-medium transition-all" :class="openMenu ? 'text-orange-600' : 'text-gray-400 dark:text-gray-500'">
                    <div class="absolute -top-1 w-10 h-1 bg-orange-500 rounded-full transition-all opacity-0" :class="openMenu && 'opacity-100'"></div>
                    <svg class="w-7 h-7 mb-0.5 transition-transform duration-300" :class="openMenu && 'rotate-90 scale-110'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <span class="text-[9px] font-black uppercase tracking-tighter">Menu</span>
                </button>
            </div>

            {{-- Overlay Escuro --}}
            <div x-show="openMenu" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="openMenu = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[-1]"></div>

            {{-- Action Sheet (Menu Suspenso) --}}
            <div x-show="openMenu" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 rounded-t-[2.5rem] shadow-[0_-20px_50px_rgba(0,0,0,0.15)] z-[110] border-t border-gray-200 dark:border-gray-700 pb-10 pt-2 overflow-hidden max-h-[85vh] overflow-y-auto">
                {{-- Barra de puxar (Clicável p/ fechar) --}}
                <div @click="openMenu = false" class="w-full py-4 flex items-center justify-center cursor-pointer active:opacity-50 transition-opacity">
                    <div class="w-12 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                </div>
                
                <div class="px-6 grid grid-cols-1 gap-8">
                    {{-- Operacional --}}
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 border-b border-gray-50 pb-2">Competição & Atletas</h4>
                        <div class="grid grid-cols-3 gap-y-6">
                            <a href="{{ route('atletas.index') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z" /></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">Atletas</span>
                            </a>
                            <a href="{{ route('equipes.index') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">Equipes</span>
                            </a>
                            <a href="{{ route('elenco.list') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">Elenco</span>
                            </a>
                            <a href="{{ route('comissao-tecnica.index') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">C.Técnica</span>
                            </a>
                            <a href="{{ route('arbitros.index') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4v7m0 4v1l3 3-3-3-3 3 3-3z"></path><circle cx="12" cy="12" r="9"></circle></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">Árbitros</span>
                            </a>
                        </div>
                    </div>

                    {{-- Cadastros e Apoio --}}
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 border-b border-gray-50 pb-2">Cadastros e Locais</h4>
                        <div class="grid grid-cols-3 gap-y-6">
                            <a href="{{ route('times.index') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-900/20 flex items-center justify-center text-cyan-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 22 22"><path fill-rule="evenodd" d="M10.915 2.345a2 2 0 0 1 2.17 0l7 4.52A2 2 0 0 1 21 8.544V9.5a1.5 1.5 0 0 1-1.5 1.5H19v6h1a1 1 0 1 1 0 2H4a1 1 0 1 1 0-2h1v-6h-.5A1.5 1.5 0 0 1 3 9.5v-.955a2 2 0 0 1 .915-1.68l7-4.52ZM17 17v-6h-2v6h2Zm-6-6h2v6h-2v-6Zm-2 6v-6H7v6h2Z"></path></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">Clubes</span>
                            </a>
                            <a href="{{ route('ginasios.index') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">Ginásios</span>
                            </a>
                            <a href="{{ route('documentos.index') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">Docs</span>
                            </a>
                            @hasanyrole('Administrador|ResponsavelTime|ComissaoTecnica')
                            <a href="{{ route('relatorios.index') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-900/20 flex items-center justify-center text-slate-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9 7V2.221a2 2 0 0 0-.5.365L4.586 6.5a2 2 0 0 0-.365.5H9Zm2 0V2h7a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Zm2-2a1 1 0 1 0 0 2h3a1 1 0 1 0 0-2h-3Zm0 3a1 1 0 1 0 0 2h3a1 1 0 1 0 0-2h-3Zm-4 4a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H10a1 1 0 0 1-1-1Zm0 4a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H10a1 1 0 0 1-1-1Z"/></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">Relatórios</span>
                            </a>
                            @endhasanyrole
                        </div>
                    </div>

                    {{-- Sistema --}}
                    @hasrole('Administrador')
                    <div class="mb-4">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 border-b border-gray-50 pb-2">Ajustes de Sistema</h4>
                        <div class="grid grid-cols-3 gap-y-6">
                            <a href="{{ route('users.index') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center text-violet-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">Usuários</span>
                            </a>
                            <a href="{{ route('categorias.index') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-fuchsia-50 dark:bg-fuchsia-900/20 flex items-center justify-center text-fuchsia-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M7 7h10v10H7z"></path><path d="M7 11h10M7 15h10"></path></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">Categorias</span>
                            </a>
                            <a href="{{ route('torneio-inicio.index') }}" class="flex flex-col items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center text-yellow-600 group-active:scale-90 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M5 3v4M19 3v4M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300 truncate w-full text-center">Torneio Início</span>
                            </a>
                        </div>
                    </div>
                    @endhasrole
                    
                    {{-- Espaçador rodapé menu --}}
                    <div class="h-4"></div>
                </div>
            </div>
        </nav>
        
        <style>
            /* Utilidades para Mobile */
            /* Prevents iOS from adding extra padding at the bottom for home indicator */
            @supports (padding-bottom: env(safe-area-inset-bottom)) {
                .safe-area-pb {
                    padding-bottom: env(safe-area-inset-bottom);
                    height: calc(4rem + env(safe-area-inset-bottom));
                }
            }
            body { -webkit-tap-highlight-color: transparent; }
        </style>
    </body>
</html>
