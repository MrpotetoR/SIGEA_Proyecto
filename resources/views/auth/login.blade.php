<x-guest-layout>

    <h2 class="text-xl font-bold text-gray-800 mb-1">Iniciar sesión</h2>
    <p class="text-sm text-gray-500 mb-6">Ingresa tus credenciales institucionales</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username"
                   placeholder="correo@institucion.edu.mx"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-400 @enderror">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="remember"
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                Recordarme
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full bg-indigo-700 hover:bg-indigo-800 text-white font-semibold py-3 rounded-lg transition-colors text-sm shadow-md">
            Iniciar sesión
        </button>
    </form>

</x-guest-layout>
