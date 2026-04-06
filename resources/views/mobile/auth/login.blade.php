<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | LRV Painel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; -webkit-tap-highlight-color: transparent; }
        .bg-mesh {
            background-color: #4f46e5;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
        }
    </style>
</head>
<body class="h-full bg-gray-900 overflow-hidden">
    <div class="h-full flex flex-col bg-mesh relative">
        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500 rounded-full blur-[120px] opacity-20 -mr-32 -mt-32"></div>
        <div class="absolute bottom-1/4 left-0 w-48 h-48 bg-pink-500 rounded-full blur-[100px] opacity-10 -ml-24"></div>

        {{-- Top Content --}}
        <div class="flex-1 flex flex-col justify-center px-8 relative z-10">
            <div class="w-16 h-16 bg-white/10 backdrop-blur-xl rounded-[1.5rem] flex items-center justify-center mb-8 border border-white/10">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4 8-8z"/></svg>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tighter leading-none mb-2">Liga Regional<br>de Voleibol</h1>
            <p class="text-sm font-bold text-indigo-200/60 uppercase tracking-widest">Painel Administrativo v2.0</p>
        </div>

        {{-- Login Card (Bottom Sheet Feel) --}}
        <div class="bg-white dark:bg-gray-950 rounded-t-[3.5rem] p-8 pb-12 shadow-2xl relative z-20">
            <div class="w-12 h-1 bg-gray-100 dark:bg-gray-800 rounded-full mx-auto mb-8"></div>
            
            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight mb-8 text-center">Bem-vindo de volta!</h2>

            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-3 ml-1">Seu E-mail</label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 dark:text-gray-200 focus:ring-4 focus:ring-indigo-500/10 placeholder-gray-300">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 ml-1" />
                </div>

                <div>
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none ml-1">Senha Privada</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[9px] font-black text-indigo-500 uppercase tracking-widest">Esqueceu?</a>
                        @endif
                    </div>
                    <input type="password" name="password" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 dark:text-gray-200 focus:ring-4 focus:ring-indigo-500/10">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 ml-1" />
                </div>

                <div class="flex items-center gap-3 py-2 px-1">
                    <input id="remember_me" type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-2 border-gray-100 dark:border-gray-800 text-indigo-600 focus:ring-0">
                    <label for="remember_me" class="text-[11px] font-extrabold text-gray-400 uppercase tracking-widest">Mantenha-me conectado</label>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-indigo-600 text-white font-black py-5 rounded-2xl shadow-xl shadow-indigo-100 dark:shadow-none active:scale-95 transition-all text-sm uppercase tracking-widest">
                        Acessar Painel
                    </button>
                    <p class="text-center mt-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Acesso restrito a oficiais e árbitros</p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
