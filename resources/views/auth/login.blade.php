<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email">
                    <strong>{{ __('Email') }}</strong>
                </x-label>
                <x-input id="email" class="block mt-1 w-full" type="email" name="email"
                         :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password">
                    <strong>{{ __('Password') }}</strong>
                </x-label>
                <x-input id="password" class="block mt-1 w-full" type="password" name="password"
                         required autocomplete="current-password" />
            </div>

            

    <!-- Login button - right-aligned -->
    <div class="col d-flex justify-content-end mb-2 mt-2">
        <x-button>
            {{ __('Log in') }}
        </x-button>
    </div>
</div>



        </form>
    </x-authentication-card>
</x-guest-layout>
