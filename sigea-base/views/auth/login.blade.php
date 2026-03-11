{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — SIGEA</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-900">SIGEA</h1>
            <p class="text-gray-500 mt-1">Sistema de Gestión Educativa</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Iniciar Sesión</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Correo electrónico
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               @error('email') border-red-500 @enderror"
                        placeholder="correo@uttecam.edu.mx"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                {{-- Remember --}}
                <div class="flex items-center mb-6">
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 text-sm text-gray-600">
                        Recordar sesión
                    </label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full bg-blue-900 text-white py-2 px-4 rounded-md
                           hover:bg-blue-800 focus:outline-none focus:ring-2
                           focus:ring-blue-500 focus:ring-offset-2 transition"
                >
                    Entrar
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-400 mt-6">
            UTTecam — Universidad Tecnológica de Tecamachalco
        </p>
    </div>

</body>
</html>
