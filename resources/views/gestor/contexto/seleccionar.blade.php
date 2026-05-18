<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Selecciona area — SIGEA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <script>
        (function(){
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .card-area {
            transition: transform .35s cubic-bezier(.22,1,.36,1),
                        box-shadow .35s cubic-bezier(.22,1,.36,1),
                        border-color .25s ease;
        }
        .card-area:hover {
            transform: translateY(-6px) scale(1.015);
            box-shadow: 0 20px 50px -10px rgba(4,39,107,0.25);
        }
        .area-icon {
            transition: transform .4s cubic-bezier(.34,1.56,.64,1);
        }
        .card-area:hover .area-icon { transform: scale(1.1) rotate(-4deg); }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-950 dark:to-black min-h-screen flex items-center justify-center p-6">

    <div class="max-w-4xl w-full">

        {{-- Logo --}}
        <div class="flex justify-center mb-8">
            <img src="{{ asset('images/logo-udea-azul.png') }}" alt="UDEA" class="h-14 dark:hidden">
            <img src="{{ asset('images/logo-udea-blanco.png') }}" alt="UDEA" class="h-14 hidden dark:block">
        </div>

        {{-- Encabezado --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">
                Hola, {{ auth()->user()->name }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm sm:text-base">
                ¿En que area vas a trabajar hoy?
            </p>
        </div>

        {{-- Tarjetas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Universidad --}}
            @if(in_array('universidad', $disponibles, true))
                <form method="POST" action="{{ route('gestor.contexto.cambiar') }}">
                    @csrf
                    <input type="hidden" name="nivel" value="universidad">
                    <button type="submit"
                            class="card-area w-full text-left bg-white dark:bg-gray-800 rounded-2xl border-2 border-transparent dark:border-gray-700 shadow-lg p-7 group hover:border-[#0606F0]">
                        <div class="flex items-start justify-between mb-5">
                            <div class="area-icon w-14 h-14 rounded-xl bg-[#0606F0]/10 dark:bg-[#0606F0]/20 flex items-center justify-center">
                                <svg class="w-7 h-7 text-[#0606F0]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#0606F0] bg-[#0606F0]/10 dark:bg-[#0606F0]/20 px-2.5 py-1 rounded-full">
                                Activa
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-1.5">Universidad</h3>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400 leading-relaxed mb-5">
                            Carreras, ingenierias, maestrias y doctorados. Planes de estudio por cuatrimestre.
                        </p>
                        <div class="flex items-end justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-gray-400 dark:text-gray-500 font-semibold">Alumnos</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['universidad']['alumnos'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] uppercase tracking-wider text-gray-400 dark:text-gray-500 font-semibold">{{ $stats['universidad']['extraLbl'] }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['universidad']['extra'] }}</p>
                            </div>
                            <div class="self-end opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-[#0606F0] text-sm font-semibold">Entrar →</span>
                            </div>
                        </div>
                    </button>
                </form>
            @endif

            {{-- Bachillerato --}}
            @if(in_array('bachillerato', $disponibles, true))
                <form method="POST" action="{{ route('gestor.contexto.cambiar') }}">
                    @csrf
                    <input type="hidden" name="nivel" value="bachillerato">
                    <button type="submit"
                            class="card-area w-full text-left bg-white dark:bg-gray-800 rounded-2xl border-2 border-transparent dark:border-gray-700 shadow-lg p-7 group hover:border-amber-500">
                        <div class="flex items-start justify-between mb-5">
                            <div class="area-icon w-14 h-14 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 flex items-center justify-center">
                                <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-300 bg-amber-500/10 dark:bg-amber-500/20 px-2.5 py-1 rounded-full">
                                Activa
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-1.5">Bachillerato</h3>
                        <p class="text-[13px] text-gray-500 dark:text-gray-400 leading-relaxed mb-5">
                            Grupos por grado, semestres y materias del plan unico. Sin carreras.
                        </p>
                        <div class="flex items-end justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-gray-400 dark:text-gray-500 font-semibold">Alumnos</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['bachillerato']['alumnos'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] uppercase tracking-wider text-gray-400 dark:text-gray-500 font-semibold">{{ $stats['bachillerato']['extraLbl'] }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['bachillerato']['extra'] }}</p>
                            </div>
                            <div class="self-end opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-amber-600 dark:text-amber-400 text-sm font-semibold">Entrar →</span>
                            </div>
                        </div>
                    </button>
                </form>
            @endif

        </div>

        {{-- Footer hint --}}
        <p class="text-center text-[12px] text-gray-400 dark:text-gray-500 mt-8">
            Podras cambiar de area en cualquier momento desde el sidebar.
        </p>

        {{-- Logout discreto --}}
        <div class="text-center mt-3">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-[11px] text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    Cerrar sesion
                </button>
            </form>
        </div>
    </div>
</body>
</html>
