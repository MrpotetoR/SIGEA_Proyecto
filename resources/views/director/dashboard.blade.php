<x-panel title="Dashboard" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

<div class="space-y-5">

    {{-- Saludo + Carrera --}}
    <div>
        <h1 class="text-[26px] font-bold text-gray-900 dark:text-gray-100 leading-tight">
            @php
                $hora = now()->hour;
                $saludo = $hora < 12 ? 'Buenos días' : ($hora < 18 ? 'Buenas tardes' : 'Buenas noches');
            @endphp
            {{ $saludo }}, {{ $docente?->nombre ?? auth()->user()->name }}
        </h1>
        <p class="text-[13px] text-gray-400 mt-1">Panel Director de Carrera — {{ ucfirst(now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}</p>
    </div>

    {{-- Alerta sin carrera --}}
    @unless($carrera)
        <div class="flex items-center gap-3 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-2xl px-5 py-3.5 text-[13px]">
            <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-amber-700 dark:text-amber-300">No tienes una carrera asignada. Contacta al administrador.</span>
        </div>
    @endunless

    {{-- Carrera a cargo --}}
    @if($carrera)
        <div class="bg-gradient-to-r from-[#04276B] to-[#0606F0] rounded-2xl p-5 text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg class="absolute -right-8 -top-8 w-40 h-40 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div class="relative flex items-center justify-between">
                <div>
                    <p class="text-white/60 text-[11px] font-semibold uppercase tracking-wider">Carrera a cargo</p>
                    <h2 class="text-xl font-bold mt-1">{{ $carrera->nombre_carrera }}</h2>
                    <p class="text-white/70 text-[13px] mt-0.5">Clave: {{ $carrera->clave_carrera ?? 'N/A' }}</p>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>
    @endif

    {{-- KPIs --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm dark:shadow-gray-900/20 overflow-hidden">
        <div class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">

            {{-- Alumnos activos --}}
            <div class="p-5 card-hover">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-[#0606F0] dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </span>
                    <span class="text-[13px] font-semibold text-gray-700 dark:text-gray-300">Alumnos Activos</span>
                </div>
                <p class="text-[28px] font-bold text-gray-900 dark:text-gray-100 leading-none">{{ $kpis['total_alumnos'] }}</p>
                <a href="{{ route('director.alumnos') }}" class="text-[11px] text-[#0606F0] dark:text-blue-400 hover:underline mt-1.5 block">Ver listado →</a>
            </div>

            {{-- Docentes --}}
            <div class="p-5 card-hover">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-sky-100 dark:bg-sky-900/40 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    <span class="text-[13px] font-semibold text-gray-700 dark:text-gray-300">Docentes</span>
                </div>
                <p class="text-[28px] font-bold text-gray-900 dark:text-gray-100 leading-none">{{ $kpis['total_docentes'] }}</p>
                <a href="{{ route('director.docentes') }}" class="text-[11px] text-sky-500 dark:text-sky-400 hover:underline mt-1.5 block">Ver listado →</a>
            </div>

            {{-- Semáforo verde --}}
            <div class="p-5 card-hover">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <span class="text-[13px] font-semibold text-gray-700 dark:text-gray-300">Semáforo Verde</span>
                </div>
                <p class="text-[28px] font-bold text-green-600 dark:text-green-400 leading-none">{{ $distribucion_semaforo['verde'] }}</p>
                <span class="text-[11px] text-gray-400 mt-1.5 block">Buen rendimiento</span>
            </div>

            {{-- Semáforo rojo --}}
            <div class="p-5 card-hover">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </span>
                    <span class="text-[13px] font-semibold text-gray-700 dark:text-gray-300">Semáforo Rojo</span>
                </div>
                <p class="text-[28px] font-bold text-red-500 dark:text-red-400 leading-none">{{ $distribucion_semaforo['rojo'] }}</p>
                <span class="text-[11px] text-gray-400 mt-1.5 block">Requieren atención</span>
            </div>

        </div>
    </div>

    {{-- Fila: Ciclo activo + Accesos rápidos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Ciclo escolar activo --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm dark:shadow-gray-900/20 card-hover">
            <div class="flex items-center justify-between px-5 pt-5 pb-3">
                <h2 class="text-[15px] font-bold text-gray-800 dark:text-gray-100">Ciclo Escolar</h2>
            </div>
            <div class="px-5 pb-5">
                @if($ciclo)
                    @php
                        $inicio = \Carbon\Carbon::parse($ciclo->fecha_inicio);
                        $fin    = \Carbon\Carbon::parse($ciclo->fecha_fin);
                        $total  = $inicio->diffInDays($fin);
                        $transcurrido = $inicio->diffInDays(now());
                        $pct = min(100, $total > 0 ? round(($transcurrido / $total) * 100) : 0);
                    @endphp
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[18px] font-bold text-gray-900 dark:text-gray-100 leading-none">{{ $ciclo->nombre }}</p>
                            <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full mt-1 inline-block">Activo</span>
                        </div>
                    </div>
                    <div class="text-[12px] text-gray-400 flex justify-between mb-2">
                        <span>{{ $inicio->format('d/m/Y') }}</span>
                        <span>{{ $fin->format('d/m/Y') }}</span>
                    </div>
                    <div class="rainbow-track h-2 rainbow-glow">
                        <div class="rainbow-bar" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1.5">{{ $pct }}% transcurrido · {{ max(0, now()->diffInDays($fin, false)) }} días restantes</p>
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-[13px] text-gray-400">Sin ciclo activo</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Accesos rápidos --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm dark:shadow-gray-900/20 card-hover">
            <div class="px-5 pt-5 pb-3">
                <h2 class="text-[15px] font-bold text-gray-800 dark:text-gray-100">Accesos Rápidos</h2>
            </div>
            <div class="px-5 pb-5 grid grid-cols-2 gap-2">
                @php
                $accesos = [
                    ['route' => 'director.grupos.index',      'label' => 'Grupos',
                     'color' => 'bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-300',
                     'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['route' => 'director.horarios.index',    'label' => 'Horarios',
                     'color' => 'bg-sky-50 hover:bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:hover:bg-sky-900/50 dark:text-sky-300',
                     'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['route' => 'director.plan-estudios',     'label' => 'Plan de Estudios',
                     'color' => 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-300',
                     'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['route' => 'director.indice-aprobacion', 'label' => 'Índice Aprobación',
                     'color' => 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 dark:text-emerald-300',
                     'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['route' => 'director.evaluacion-docente','label' => 'Eval. Docentes',
                     'color' => 'bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:hover:bg-amber-900/50 dark:text-amber-300',
                     'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                    ['route' => 'director.asistencia',        'label' => 'Asistencia',
                     'color' => 'bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 dark:text-rose-300',
                     'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ];
                @endphp
                @foreach($accesos as $a)
                    <a href="{{ route($a['route']) }}"
                       class="flex items-center gap-2.5 px-3.5 py-3 rounded-xl {{ $a['color'] }} transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $a['icon'] }}"/>
                        </svg>
                        <span class="text-[13px] font-semibold">{{ $a['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Distribución semáforo --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
        <h2 class="text-[15px] font-bold text-gray-800 dark:text-gray-200 mb-5">Distribución Semáforo Académico</h2>
        <div class="grid grid-cols-3 gap-5">
            @foreach(['verde' => ['bg-green-500', 'text-green-700', 'bg-green-50', 'dark:bg-green-900/30', 'dark:text-green-400'], 'amarillo' => ['bg-yellow-400', 'text-yellow-700', 'bg-yellow-50', 'dark:bg-yellow-900/30', 'dark:text-yellow-400'], 'rojo' => ['bg-red-500', 'text-red-700', 'bg-red-50', 'dark:bg-red-900/30', 'dark:text-red-400']] as $nivel => $colors)
                @php $total = array_sum($distribucion_semaforo) ?: 1; $porcentaje = round(($distribucion_semaforo[$nivel] / $total) * 100); @endphp
                <div class="{{ $colors[2] }} {{ $colors[3] }} rounded-xl p-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-semibold {{ $colors[1] }} {{ $colors[4] }} capitalize">{{ $nivel }}</span>
                        <span class="text-lg font-bold {{ $colors[1] }} {{ $colors[4] }}">{{ $distribucion_semaforo[$nivel] }}</span>
                    </div>
                    <div class="rainbow-track-dark h-2">
                        <div class="rainbow-bar" style="width: {{ $porcentaje }}%"></div>
                    </div>
                    <p class="text-xs {{ $colors[1] }} {{ $colors[4] }} mt-1.5 opacity-70">{{ $porcentaje }}% del total</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Índice de aprobación --}}
    @if(!empty($indice))
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-[15px] font-bold text-gray-800 dark:text-gray-200">Índice de Aprobación — {{ $ciclo?->nombre }}</h2>
                <a href="{{ route('director.indice-aprobacion') }}"
                   class="text-[11px] font-medium text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 px-2.5 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Ver detalle →
                </a>
            </div>
            <div class="grid grid-cols-3 gap-5">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-200">{{ $indice['total'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total calificaciones</p>
                </div>
                <div class="text-center p-4 bg-green-50 dark:bg-green-900/30 rounded-xl">
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $indice['porcentaje_aprobacion'] ?? 0 }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aprobación</p>
                </div>
                <div class="text-center p-4 bg-red-50 dark:bg-red-900/30 rounded-xl">
                    <p class="text-3xl font-bold text-red-500 dark:text-red-400">{{ isset($indice['porcentaje_aprobacion']) ? 100 - $indice['porcentaje_aprobacion'] : 0 }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Reprobación</p>
                </div>
            </div>
        </div>
    @endif

</div>
</x-panel>
