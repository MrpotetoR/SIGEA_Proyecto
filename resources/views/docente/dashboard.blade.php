<x-panel
    title="Mi Dashboard"
    panelNombre="Panel Docente">

    <x-slot name="nav">
        <x-sidebar-link href="{{ route('docente.dashboard') }}">📊 Dashboard</x-sidebar-link>
        <x-sidebar-link href="{{ route('docente.perfil') }}">👤 Mi Perfil</x-sidebar-link>
        <x-sidebar-link href="{{ route('docente.grupos') }}">👥 Mis Grupos</x-sidebar-link>
        <x-sidebar-link href="{{ route('docente.horario') }}">🗓 Mi Horario</x-sidebar-link>
        <x-sidebar-link href="{{ route('docente.asistencia') }}">✅ Asistencia</x-sidebar-link>
        <x-sidebar-link href="{{ route('docente.calificaciones') }}">📝 Calificaciones</x-sidebar-link>
        <x-sidebar-link href="{{ route('docente.reporte-asistencia') }}">📋 Reporte Asistencia</x-sidebar-link>
        <x-sidebar-link href="{{ route('docente.reporte-rendimiento') }}">📈 Reporte Rendimiento</x-sidebar-link>
        <x-sidebar-link href="{{ route('docente.horas-culturales.index') }}">🎭 Horas ACUDE</x-sidebar-link>
        <x-sidebar-link href="{{ route('docente.servicio-social.index') }}">🤝 Servicio Social</x-sidebar-link>
        <x-sidebar-link href="{{ route('docente.evaluacion-resultados') }}">⭐ Mi Evaluación</x-sidebar-link>
        <x-sidebar-link href="{{ route('docente.noticias') }}">📰 Noticias</x-sidebar-link>
    </x-slot>

    {{-- Cards de resumen --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Grupos Activos</p>
            <p class="text-3xl font-bold text-indigo-700">{{ $grupos->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Ciclo Escolar</p>
            <p class="text-xl font-semibold text-gray-800">{{ $ciclo?->nombre ?? 'Sin ciclo activo' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Especialidad</p>
            <p class="text-lg font-semibold text-gray-800">{{ $docente?->especialidad ?? '—' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Mis grupos --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">👥 Grupos del Ciclo</h2>
            @if($grupos->isNotEmpty())
                <div class="space-y-3">
                    @foreach($grupos as $grupoId => $horariosDel)
                        @php $g = $horariosDel->first()->grupo; @endphp
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <p class="font-medium text-gray-800">{{ $g->clave_grupo }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $horariosDel->map(fn($h) => $h->materia->nombre_materia)->unique()->join(', ') }}
                                </p>
                            </div>
                            <a href="{{ route('docente.asistencia.show', $g->id_grupo) }}"
                               class="text-xs text-indigo-600 hover:underline">Pasar lista</a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm">Sin grupos asignados en el ciclo actual.</p>
            @endif
        </div>

        {{-- Noticias --}}
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
            @else
                <p class="text-gray-400 text-sm">Sin noticias.</p>
            @endif
        </div>
    </div>

</x-panel>
