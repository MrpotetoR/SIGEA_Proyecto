{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIGEA') — Sistema de Gestión Educativa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- ─── Navbar ──────────────────────────────── --}}
    @auth
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-blue-900">SIGEA</a>
                    <span class="ml-3 text-sm text-gray-500">
                        @if(auth()->user()->esServiciosEscolares())
                            Servicios Escolares
                        @elseif(auth()->user()->esDirector())
                            Director de Carrera
                        @elseif(auth()->user()->esDocente())
                            Docente
                        @elseif(auth()->user()->esAlumno())
                            Alumno
                        @endif
                    </span>
                </div>

                {{-- Sidebar links --}}
                <div class="hidden sm:flex sm:items-center sm:space-x-4">
                    @yield('nav-links')
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    {{-- ─── Contenido Principal ─────────────────── --}}
    <div class="flex">
        {{-- Sidebar --}}
        @auth
        <aside class="w-64 min-h-screen bg-white border-r border-gray-200 hidden lg:block">
            <nav class="mt-6 px-4 space-y-1">
                @yield('sidebar')
            </nav>
        </aside>
        @endauth

        {{-- Main content --}}
        <main class="flex-1 p-6">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
