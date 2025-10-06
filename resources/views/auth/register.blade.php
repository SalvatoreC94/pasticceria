<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <!-- Nome -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Nome e cognome')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Telefono (opzionale) -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Telefono')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Indirizzo spedizione -->
        <div class="mt-6">
            <h3 class="font-semibold">{{ __('Indirizzo di spedizione') }}</h3>

            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="via" :value="__('Via')" />
                    <x-text-input id="via" class="block mt-1 w-full" type="text" name="address[via]"
                        :value="old('address.via')" required />
                    <x-input-error :messages="$errors->get('address.via')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="civico" :value="__('Civico')" />
                    <x-text-input id="civico" class="block mt-1 w-full" type="text" name="address[civico]"
                        :value="old('address.civico')" required />
                    <x-input-error :messages="$errors->get('address.civico')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="cap" :value="__('CAP')" />
                    <x-text-input id="cap" class="block mt-1 w-full" type="text" name="address[cap]"
                        :value="old('address.cap')" required />
                    <x-input-error :messages="$errors->get('address.cap')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="citta" :value="__('Città')" />
                    <x-text-input id="citta" class="block mt-1 w-full" type="text" name="address[citta]"
                        :value="old('address.citta')" required />
                    <x-input-error :messages="$errors->get('address.citta')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="prov" :value="__('Provincia (es. NA)')" />
                    <x-text-input id="prov" class="block mt-1 w-full" type="text" name="address[prov]"
                        :value="old('address.prov')" maxlength="2" required />
                    <x-input-error :messages="$errors->get('address.prov')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Password + Conferma (Breeze già li ha) -->

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
