<x-panel
    title="Dashboard Director"
    panelNombre="Panel Director">

    <x-slot name="nav">
        <x-sidebar-link href="{{ route('director.dashboard') }}">📊 Dashboard</x-sidebar-link>
        <x-sidebar-link href="{{ route('director.perfil') }}">👤 Mi Perfil</x-sidebar-link>
        <x-sidebar-link href="{{ route('director.docentes') }}">👨‍🏫 Docentes</x-sidebar-link>
        <x-sidebar-link href="{{ route('director.grupos.index') }}">👥 Grupos</x-sidebar-link>
        <x-sidebar-link href="{{ route('director.horarios.index') }}">🗓 Horarios</x-sidebar-link>
        <x-sidebar-link href="{{ route('director.alumnos') }}">🎓 Alumnos</x-sidebar-link>
        <x-sidebar-link href="{{ route('director.indice-aprobacion') }}">📈 Índice Aprobación</x-sidebar-link>
        <x-sidebar-link href="{{ route('director.plan-estudios') }}">📚 Plan de Estudios</x-sidebar-link>
        <x-sidebar-link href="{{ route('director.asistencia') }}">✅ Asistencia</x-sidebar-link>
        <x-sidebar-link href="{{ route('director.evaluacion-docente') }}">⭐ Evaluación Docentes</x-sidebar-link>
        <x-sidebar-link href="{{ route('director.noticias') }}">📰 Noticias</x-sidebar-link>
    </x-slot>

    @if($carrera)
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-indigo-500">Carrera a cargo</p>
            <p class="text-xl font-bold text-indigo-800">{{ $carrera->nombre_carrera }}</p>
            <p class="text-sm text-indigo-600">Clave: {{ $carrera->clave_carrera }}</p>
        </div>
    @endif

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <p class="text-3xl font-bold text-indigo-700">{{ $kpis['total_alumnos'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Alumnos Activos</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <p class="text-3xl font-bold text-indigo-700">{{ $kpis['total_docentes'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Docentes</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $distribucion_semaforo['verde'] }}</p>
            <p class="text-sm text-gray-500 mt-1">🟢 En Verde</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <p class="text-3xl font-bold text-red-600">{{ $distribucion_semaforo['rojo'] }}</p>
            <p class="text-sm text-gray-500 mt-1">🔴 En Rojo</p>
        </div>
    </div>

    {{-- Distribución semáforo --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Distribución Semáforo Académico</h2>
        <div class="flex gap-4">
            @foreach(['verde' => 'bg-green-500', 'amarillo' => 'bg-yellow-400', 'rojo' => 'bg-red-500'] as $nivel => $color)
                @php $total = array_sum($distribucion_semaforo) ?: 1; @endphp
                <div class="flex-1">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="capitalize font-medium">{{ $nivel }}</span>
                        <span>{{ $distribucion_semaforo[$nivel] }}</span>
                    </div>
                    <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                        <div class="{{ $color }} h-full rounded-full transition-all"
                             style="width: {{ round(($distribucion_semaforo[$nivel] / $total) * 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Índice aprobación --}}
    @if(!empty($indice))
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Índice de Aprobación — {{ $ciclo?->nombre }}</h2>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $indice['total'] }}</p>
                    <p class="text-sm text-gray-500">Total calificaciones</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600">{{ $indice['porcentaje_aprobacion'] }}%</p>
                    <p class="text-sm text-gray-500">Aprobación</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-500">{{ 100 - $indice['porcentaje_aprobacion'] }}%</p>
                    <p class="text-sm text-gray-500">Reprobación</p>
                </div>
            </div>
        </div>
    @endif

</x-panel>
