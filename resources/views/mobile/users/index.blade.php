@extends('mobile.layouts.app')

@section('content')
<div class="w-full">
    {{-- Header --}}
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-gray-100 tracking-tight leading-none mb-1">Usuários</h2>
            <p class="text-[13px] text-gray-500 font-medium dark:text-gray-400">Controle de acesso e perfis.</p>
        </div>
        <a href="{{ route('users.create') }}" class="w-12 h-12 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-200 dark:shadow-none flex items-center justify-center text-white active:scale-95 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        </a>
    </div>

    {{-- Filtros Rápidos (Estilo Horizontal Scroll) --}}
    <div class="flex gap-2 overflow-x-auto pb-4 mb-2 no-scrollbar -mx-1 px-1">
        <a href="{{ route('users.index') }}" class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold {{ !request('role') ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-500 border border-gray-100 dark:border-gray-700' }}">Todos</a>
        <a href="{{ route('users.index', ['role' => 'Administrador']) }}" class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold {{ request('role') == 'Administrador' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-500 border border-gray-100 dark:border-gray-700' }}">Admins</a>
        <a href="{{ route('users.index', ['role' => 'Juiz']) }}" class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold {{ request('role') == 'Juiz' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-500 border border-gray-100 dark:border-gray-700' }}">Juízes</a>
        <a href="{{ route('users.index', ['role' => 'ResponsavelTime']) }}" class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold {{ request('role') == 'ResponsavelTime' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-500 border border-gray-100 dark:border-gray-700' }}">Resp. Time</a>
    </div>

    {{-- Cards de Usuários --}}
    <div class="space-y-4">
        @forelse($users as $user)
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-sm border border-gray-50 dark:border-gray-700 relative overflow-hidden">
                @if(!$user->active)
                    <div class="absolute top-0 right-0 px-3 py-1 bg-red-500 text-white text-[8px] font-black uppercase rounded-bl-xl tracking-widest">Inativo</div>
                @endif

                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center overflow-hidden border border-indigo-100 dark:border-indigo-800/30">
                        @if($user->foto)
                            <img src="{{ asset('storage/user_fotos/' . $user->foto) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-xl font-black text-indigo-400">{{ substr($user->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900 dark:text-white leading-none mb-1">{{ $user->name }}</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $user->apelido ?? 'Sem apelido' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-3 mb-5 px-1">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-0.5">Perfil / Role</p>
                        <p class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400">
                             @php
                                $roleDisplay = $user->getRoleNames()->first();
                                if($roleDisplay == 'ResponsavelTime') $roleDisplay = 'Resp. Time';
                                elseif($roleDisplay == 'ComissaoTecnica') $roleDisplay = 'Comis. Técnica';
                             @endphp
                             {{ $roleDisplay ?? 'Sem Perfil' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-0.5">Vínculo</p>
                        <p class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate">
                            {{ $user->time ? $user->time->tim_nome : ($user->timeResponsavel ? $user->timeResponsavel->tim_nome : 'Liga Voleibol') }}
                        </p>
                    </div>
                    @if($user->lrv)
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-0.5">Registro LRV</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white">{{ $user->lrv }}</p>
                    </div>
                    @endif
                    @if($user->telefone)
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-0.5">Contato</p>
                        <p class="text-[11px] font-bold text-gray-700 dark:text-gray-300">{{ $user->telefone }}</p>
                    </div>
                    @endif
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('users.edit', $user->id) }}" class="flex-1 bg-gray-50 dark:bg-gray-900 py-3 rounded-xl flex items-center justify-center gap-2 border border-gray-100 dark:border-gray-700 active:scale-95 transition-all">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        <span class="text-[10px] font-black uppercase text-gray-600 dark:text-gray-400">Editar</span>
                    </a>
                    
                    <form action="{{ route('users.toggleStatus', $user->id) }}" method="POST" class="flex-none">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-12 py-3 rounded-xl flex items-center justify-center border border-gray-100 dark:border-gray-700 {{ $user->active ? 'bg-red-50 text-red-500' : 'bg-green-50 text-green-500' }} active:scale-95 transition-all">
                            @if($user->active)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @endif
                        </button>
                    </form>

                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="flex-none" onsubmit="return confirm('Excluir este usuário permanentemente?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-12 py-3 rounded-xl flex items-center justify-center bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 text-gray-400 active:scale-95 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Nenhum usuário encontrado</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6 mb-20 px-2">
        {{ $users->links() }}
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
