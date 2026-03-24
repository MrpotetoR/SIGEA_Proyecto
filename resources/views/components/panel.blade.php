<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIGEA' }} — SIGEA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="antialiased bg-[#f5f5f0]">

<div class="flex h-screen overflow-hidden">

    {{-- ======== SIDEBAR ======== --}}
    <aside class="w-[220px] bg-white border-r border-gray-200/60 flex flex-col flex-shrink-0">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 h-16 border-b border-gray-100">
            <div class="w-9 h-9 bg-gray-900 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <span class="text-[15px] font-bold text-gray-900 tracking-tight">SIGEA</span>
        </div>

        {{-- Menú label --}}
        <div class="px-5 pt-5 pb-2">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-[0.12em]">{{ $panelNombre ?? 'Menú' }}</p>
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 overflow-y-auto custom-scrollbar px-3 space-y-0.5 pb-4">
            {{ $nav ?? '' }}
        </nav>

        {{-- Separador --}}
        <div class="border-t border-gray-100"></div>

        {{-- Cambiar contraseña --}}
        <div class="px-3 pt-3">
            <a href="{{ route('cambiar-password') }}"
               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[12px] font-medium text-gray-400 hover:text-gray-700 hover:bg-gray-50 transition-colors {{ request()->routeIs('cambiar-password') ? 'bg-gray-50 text-gray-700' : '' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Cambiar contrasena
            </a>
        </div>

        {{-- Usuario --}}
        <div class="p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[13px] font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-gray-400 truncate">
                    @php
                        $user = auth()->user();
                        $rolLabel = match(true) {
                            $user->hasRole('servicios_escolares') => 'Servicios Escolares',
                            $user->hasRole('director_carrera') => 'Director de Carrera',
                            $user->hasRole('docente') => 'Docente',
                            $user->hasRole('alumno') => 'Alumno',
                            default => 'Usuario',
                        };
                    @endphp
                    {{ $rolLabel }}
                </p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Cerrar sesion"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- ======== CONTENIDO PRINCIPAL ======== --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Header --}}
        <header class="bg-[#f5f5f0] flex items-center justify-between px-7 h-14 flex-shrink-0">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-[13px]">
                <span class="text-gray-400">SIGEA</span>
                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-400">{{ $panelNombre ?? 'Panel' }}</span>
                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="font-medium text-gray-700">{{ $title ?? 'Dashboard' }}</span>
            </div>

            {{-- Acciones header --}}
            <div class="flex items-center gap-1">
                {{-- Notificaciones --}}
                <button class="relative p-2.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-white/60">
                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-orange-400 rounded-full pulse-dot"></span>
                </button>
                {{-- Chat --}}
                <button class="p-2.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-white/60">
                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </button>
                {{-- Buscar --}}
                <button class="p-2.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-white/60">
                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </header>

        {{-- Alertas de sesión --}}
        @if (session('success'))
            <div class="mx-7 mb-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-[13px] flex items-center gap-2 fade-in">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mx-7 mb-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-[13px] flex items-center gap-2 fade-in">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Contenido --}}
        <main class="flex-1 overflow-y-auto custom-scrollbar px-7 pb-7 pt-1">
            <div class="fade-in">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
