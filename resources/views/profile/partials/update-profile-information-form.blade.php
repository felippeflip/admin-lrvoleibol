<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Seus Dados') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Atualize as informações da sua conta.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="apelido" :value="__('Apelido')" />
            <x-text-input id="apelido" name="apelido" type="text" class="mt-1 block w-full" :value="old('apelido', $user->apelido)" required autofocus autocomplete="apelido" />
            <x-input-error class="mt-2" :messages="$errors->get('apelido')" />
        </div>
        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Seu endereço de e-mail não está verificado.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Clique aqui para reenviar o e-mail de verificação.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('Um novo link de verificação foi enviado para o seu endereço de e-mail.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        <div>
            <x-input-label for="telefone" :value="__('Telefone')" />
            <x-text-input id="telefone" name="telefone" type="text" class="mt-1 block w-full" :value="old('telefone', $user->telefone)" required autofocus autocomplete="telefone" />
            <x-input-error class="mt-2" :messages="$errors->get('telefone')" />
        </div>
        <div>
            <x-input-label for="cpf" :value="__('CPF')" />
            <x-text-input id="cpf" name="cpf" type="text" class="mt-1 block w-full" :value="old('cpf', $user->cpf)" required autofocus autocomplete="cpf" />
            <x-input-error class="mt-2" :messages="$errors->get('cpf')" />
        </div>
        <div>
            <x-input-label for="endereco" :value="__('Endereco')" />
            <x-text-input id="endereco" name="endereco" type="text" class="mt-1 block w-full" :value="old('endereco', $user->endereco)" required autofocus autocomplete="endereco" />
            <x-input-error class="mt-2" :messages="$errors->get('endereco')" />
        </div>
        <div>
            <x-input-label for="bairro" :value="__('Bairro')" />
            <x-text-input id="bairro" name="bairro" type="text" class="mt-1 block w-full" :value="old('bairro', $user->bairro)" required autofocus autocomplete="bairro" />
            <x-input-error class="mt-2" :messages="$errors->get('bairro')" />
        </div>
        <div>
            <x-input-label for="cidade" :value="__('Cidade')" />
            <x-text-input id="cidade" name="cidade" type="text" class="mt-1 block w-full" :value="old('cidade', $user->cidade)" required autofocus autocomplete="cidade" />
            <x-input-error class="mt-2" :messages="$errors->get('cidade')" />
        </div>
        <div>
            <x-input-label for="estado" :value="__('Estado')" />
            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $user->estado)" required autofocus autocomplete="estado" />
            <x-input-error class="mt-2" :messages="$errors->get('estado')" />
        </div>
        <div>
            <x-input-label for="cep" :value="__('Cep')" />
            <x-text-input id="cep" name="cep" type="text" class="mt-1 block w-full" :value="old('cep', $user->cep)" required autofocus autocomplete="cep" />
            <x-input-error class="mt-2" :messages="$errors->get('cep')" />
        </div>
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Salvar') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Salvar.') }}</p>
            @endif
        </div>
    </form>
</section>
