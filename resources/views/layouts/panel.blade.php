<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIGEA' }} - {{ config('app.name', 'SIGEA') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-64 bg-blue-900 text-white flex flex-col flex-shrink-0">

        {{-- Logo --}}
        <div class="flex items-center justify-center h-16 bg-blue-950 px-4">
            <span class="text-xl font-bold tracking-wide">SIGEA</span>
        </div>

        {{-- Rol badge --}}
        <div class="px-4 py-3 bg-blue-800 text-xs text-blue-200 uppercase tracking-widest">
            {{ $panelNombre ?? 'Panel' }}
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-2">
            {{ $nav }}
        </nav>

        {{-- Usuario y logout --}}
        <div class="border-t border-blue-700 p-4">
            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-blue-300 truncate">{{ auth()->user()->email }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                    class="w-full text-left text-xs text-blue-300 hover:text-white transition-colors">
                    Cerrar sesión →
                </button>
            </form>
        </div>
    </aside>

    {{-- Contenido principal --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Header --}}
        <header class="bg-white shadow-sm flex items-center justify-between px-6 h-16 flex-shrink-0">
            <h1 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
            <div class="text-sm text-gray-500">{{ now()->format('d/m/Y') }}</div>
        </header>

        {{-- Alertas de sesión --}}
        @if (session('success'))
            <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- Contenido --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>
</div>

</body>
</html>
