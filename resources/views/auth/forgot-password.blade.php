<x-guest-layout>
    <div class="mb-4 text-sm"
        style="font-family: sans-serif; color: var(--texto-atenuado); margin-bottom: 20px; line-height: 1.5;">
        {{ __('¿Olvidaste tu contraseña? No hay problema. Introduce tu dirección de correo electrónico y, si hay conexión a internet, te enviaremos un enlace de restablecimiento de contraseña para que elijas una nueva.') }}
    </div>

    <!-- Mensaje de estado -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-center mt-4">
            <x-primary-button>
                {{ __('Enviar enlace de restablecimiento') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>