<x-panel
    title="Mi Dashboard"
    panelNombre="Panel Alumno">

    <x-slot name="nav">
        <x-sidebar-link href="{{ route('alumno.dashboard') }}">📊 Dashboard</x-sidebar-link>
        <x-sidebar-link href="{{ route('alumno.perfil') }}">👤 Mi Perfil</x-sidebar-link>
        <x-sidebar-link href="{{ route('alumno.horario') }}">🗓 Horario</x-sidebar-link>
        <x-sidebar-link href="{{ route('alumno.calificaciones') }}">📝 Calificaciones</x-sidebar-link>
        <x-sidebar-link href="{{ route('alumno.kardex') }}">📋 Kárdex</x-sidebar-link>
        <x-sidebar-link href="{{ route('alumno.historial') }}">📚 Historial Académico</x-sidebar-link>
        <x-sidebar-link href="{{ route('alumno.horas-culturales') }}">🎭 Horas ACUDE</x-sidebar-link>
        <x-sidebar-link href="{{ route('alumno.servicio-social') }}">🤝 Servicio Social</x-sidebar-link>
        <x-sidebar-link href="{{ route('alumno.evaluacion-docente') }}">⭐ Evaluar Docentes</x-sidebar-link>
        <x-sidebar-link href="{{ route('alumno.mis-docentes') }}">👨‍🏫 Mis Docentes</x-sidebar-link>
        <x-sidebar-link href="{{ route('alumno.noticias') }}">📰 Noticias</x-sidebar-link>
    </x-slot>

    {{-- Semáforo académico --}}
    @if($alumno && $semaforo)
        @php
            $colores = ['verde' => 'green', 'amarillo' => 'yellow', 'rojo' => 'red'];
            $color = $colores[$semaforo->nivel] ?? 'gray';
            $bgMap = ['green' => 'bg-green-100 border-green-400 text-green-800', 'yellow' => 'bg-yellow-100 border-yellow-400 text-yellow-800', 'red' => 'bg-red-100 border-red-400 text-red-800'];
            $bg = $bgMap[$color] ?? 'bg-gray-100 border-gray-400 text-gray-800';
        @endphp
        <div class="border rounded-lg p-4 mb-6 {{ $bg }}">
            <div class="flex items-center gap-3">
                <span class="text-3xl">{{ $semaforo->nivel === 'verde' ? '🟢' : ($semaforo->nivel === 'amarillo' ? '🟡' : '🔴') }}</span>
                <div>
                    <p class="font-bold text-lg">Semáforo Académico: {{ ucfirst($semaforo->nivel) }}</p>
                    <p class="text-sm">Promedio: <strong>{{ $semaforo->promedio_calificaciones }}</strong> | Asistencia: <strong>{{ $semaforo->porcentaje_asistencia }}%</strong></p>
                </div>
            </div>
        </div>
    @endif

    {{-- Cards de resumen --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Matrícula</p>
            <p class="text-2xl font-bold text-indigo-700">{{ $alumno?->matricula ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Carrera</p>
            <p class="text-lg font-semibold text-gray-800">{{ $alumno?->carrera?->nombre_carrera ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Cuatrimestre</p>
            <p class="text-2xl font-bold text-indigo-700">{{ $alumno?->cuatrimestre_actual ?? '—' }}°</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Clases de hoy --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">📅 Clases de Hoy</h2>
            @if($proximasClases->isNotEmpty())
                <div class="space-y-3">
                    @foreach($proximasClases as $clase)
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <p class="font-medium text-gray-800">{{ $clase->materia->nombre_materia }}</p>
                                <p class="text-sm text-gray-500">{{ $clase->docente->nombre_completo }}</p>
                            </div>
                            <span class="text-sm text-indigo-600 font-mono">
                                {{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($clase->hora_fin)->format('H:i') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm">Sin clases programadas para hoy.</p>
            @endif
        </div>

        {{-- Noticias recientes --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">📰 Noticias Recientes</h2>
            @if($noticias->isNotEmpty())
                <div class="space-y-3">
                    @foreach($noticias as $noticia)
                        <div class="border-b pb-2">
                            <p class="font-medium text-gray-800">{{ $noticia->titulo }}</p>
                            <p class="text-xs text-gray-400">{{ $noticia->fecha_publicacion->format('d/m/Y') }}</p>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('alumno.noticias') }}" class="text-sm text-indigo-600 hover:underline mt-3 inline-block">Ver todas →</a>
            @else
                <p class="text-gray-400 text-sm">Sin noticias recientes.</p>
            @endif
        </div>
    </div>

</x-panel>
