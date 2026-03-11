<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIGEA — {{ config('app.name', 'Sistema de Gestión Educativa') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-indigo-900 via-indigo-800 to-indigo-700 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-4">
        {{-- Logo / título --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
                <svg class="w-10 h-10 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white tracking-wide">SIGEA</h1>
            <p class="text-indigo-200 text-sm mt-1">Sistema de Gestión Educativa</p>
        </div>

        {{-- Tarjeta --}}
        <div class="bg-white rounded-2xl shadow-2xl px-8 py-8">
            {{ $slot }}
        </div>

        <p class="text-center text-indigo-300 text-xs mt-6">
            © {{ date('Y') }} SIGEA — Todos los derechos reservados
        </p>
    </div>

</body>
</html>
