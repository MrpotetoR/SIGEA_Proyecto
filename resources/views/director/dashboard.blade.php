<x-panel title="Dashboard" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    {{-- Carrera a cargo --}}
    @if($carrera)
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-200 text-sm font-medium">Carrera a cargo</p>
                    <h2 class="text-2xl font-bold mt-1">{{ $carrera->nombre_carrera }}</h2>
                    <p class="text-indigo-200 text-sm mt-1">Clave: {{ $carrera->clave_carrera ?? 'N/A' }}</p>
                </div>
                <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>
    @else
        <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-2xl p-6 mb-8">
            <p class="text-amber-700 dark:text-amber-300 font-medium">No tienes una carrera asignada. Contacta al administrador.</p>
        </div>
    @endif

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $kpis['total_alumnos'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Alumnos activos</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-violet-50 dark:bg-violet-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $kpis['total_docentes'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Docentes</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $distribucion_semaforo['verde'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Semaforo verde</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-50 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-500 dark:text-red-400">{{ $distribucion_semaforo['rojo'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Semaforo rojo</p>
        </div>
    </div>

    {{-- Distribucion semaforo --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6 mb-8">
        <h2 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-5">Distribucion Semaforo Academico</h2>
        <div class="grid grid-cols-3 gap-6">
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

    {{-- Indice de aprobacion --}}
    @if(!empty($indice))
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-5">Indice de Aprobacion — {{ $ciclo?->nombre }}</h2>
            <div class="grid grid-cols-3 gap-6">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-200">{{ $indice['total'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total calificaciones</p>
                </div>
                <div class="text-center p-4 bg-green-50 dark:bg-green-900/30 rounded-xl">
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $indice['porcentaje_aprobacion'] ?? 0 }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aprobacion</p>
                </div>
                <div class="text-center p-4 bg-red-50 dark:bg-red-900/30 rounded-xl">
                    <p class="text-3xl font-bold text-red-500 dark:text-red-400">{{ isset($indice['porcentaje_aprobacion']) ? 100 - $indice['porcentaje_aprobacion'] : 0 }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Reprobacion</p>
                </div>
            </div>
        </div>
    @endif
</x-panel>
