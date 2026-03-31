<x-panel title="Historial de {{ $alumno->nombre_completo }}" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    {{-- Boton volver --}}
    <div class="mb-5">
        <a href="{{ route('director.alumnos') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Alumnos
        </a>
    </div>

    {{-- Info alumno --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-700 dark:text-blue-400 text-lg font-bold">
                {{ strtoupper(substr($alumno->nombre ?? 'A', 0, 1)) }}{{ strtoupper(substr($alumno->apellidos ?? '', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $alumno->nombre_completo }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Matricula: <span class="font-mono">{{ $alumno->matricula }}</span> | Cuatrimestre: {{ $alumno->cuatrimestre_actual ?? 'N/A' }} | Estatus: <span class="capitalize">{{ $alumno->estatus }}</span></p>
            </div>
        </div>
    </div>

    {{-- Calificaciones por ciclo --}}
    <h3 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-4">Calificaciones por Ciclo</h3>

    @forelse($historial as $cicloId => $calificaciones)
        @php $cicloNombre = $calificaciones->first()->cicloEscolar?->nombre ?? 'Ciclo #' . $cicloId; @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 overflow-hidden mb-4">
            <div class="bg-gray-50 dark:bg-gray-700/50 px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $cicloNombre }}</h4>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 dark:border-gray-700">
                        <th class="text-left px-5 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Materia</th>
                        <th class="text-center px-5 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Parcial</th>
                        <th class="text-center px-5 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Calificacion</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($calificaciones as $cal)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-2.5 text-gray-700 dark:text-gray-300">{{ $cal->materia?->nombre_materia ?? 'N/A' }}</td>
                            <td class="px-5 py-2.5 text-center text-gray-600 dark:text-gray-400">{{ $cal->parcial }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-lg {{ $cal->calificacion >= 7 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                    {{ $cal->calificacion }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-8 text-center">
            <p class="text-gray-500 dark:text-gray-400 text-sm">No hay calificaciones registradas.</p>
        </div>
    @endforelse

    {{-- Asistencias --}}
    <h3 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-4 mt-8">Resumen de Asistencias</h3>
    @if($asistencias->isNotEmpty())
        @php
            $totalAsis = $asistencias->count();
            $presentes = $asistencias->where('estatus', 'presente')->count();
            $ausentes = $asistencias->where('estatus', 'ausente')->count();
            $justificadas = $asistencias->where('estatus', 'justificada')->count();
            $porcAsist = $totalAsis > 0 ? round(($presentes / $totalAsis) * 100, 1) : 0;
        @endphp
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-5 text-center">
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalAsis }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total registros</p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/30 rounded-2xl border border-green-100 dark:border-green-700/50 p-5 text-center">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $presentes }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Presentes</p>
            </div>
            <div class="bg-red-50 dark:bg-red-900/30 rounded-2xl border border-red-100 dark:border-red-700/50 p-5 text-center">
                <p class="text-2xl font-bold text-red-500 dark:text-red-400">{{ $ausentes }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ausentes</p>
            </div>
            <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-2xl border border-yellow-100 dark:border-yellow-700/50 p-5 text-center">
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $justificadas }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Justificadas</p>
            </div>
        </div>
        <div class="mt-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Porcentaje de asistencia</span>
                <span class="text-sm font-bold {{ $porcAsist >= 80 ? 'text-green-600 dark:text-green-400' : ($porcAsist >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-500 dark:text-red-400') }}">{{ $porcAsist }}%</span>
            </div>
            <div class="rainbow-track h-3 rainbow-glow">
                <div class="rainbow-bar" style="width: {{ $porcAsist }}%"></div>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-8 text-center">
            <p class="text-gray-500 dark:text-gray-400 text-sm">No hay registros de asistencia.</p>
        </div>
    @endif
</x-panel>
