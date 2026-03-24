<x-panel title="Grupo {{ $grupo->clave_grupo }}" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <div class="mb-5">
        <a href="{{ route('director.grupos.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver a Grupos
        </a>
    </div>

    <div class="max-w-3xl">
        {{-- Info del grupo --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">{{ $grupo->clave_grupo }}</h3>
                <a href="{{ route('director.grupos.edit', $grupo->id_grupo) }}" class="px-4 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-lg hover:bg-indigo-100 transition-colors">Editar</a>
            </div>
            <div class="grid grid-cols-2 gap-y-4 gap-x-8">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Carrera</p>
                    <p class="text-sm font-medium text-gray-800">{{ $grupo->carrera?->nombre_carrera ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Ciclo Escolar</p>
                    <p class="text-sm font-medium text-gray-800">{{ $grupo->cicloEscolar?->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Cuatrimestre</p>
                    <p class="text-sm font-medium text-gray-800">{{ $grupo->cuatrimestre }}o</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Tutor</p>
                    <p class="text-sm font-medium text-gray-800">{{ $grupo->tutorDocente?->nombre_completo ?? 'Sin tutor' }}</p>
                </div>
            </div>
        </div>

        {{-- Horarios del grupo --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h4 class="text-[15px] font-semibold text-gray-800 mb-4">Horarios</h4>
            @if($grupo->horarios->isEmpty())
                <p class="text-sm text-gray-500">No hay horarios asignados.</p>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 uppercase">Materia</th>
                            <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 uppercase">Docente</th>
                            <th class="text-center px-4 py-2.5 text-xs font-semibold text-gray-500 uppercase">Dia</th>
                            <th class="text-center px-4 py-2.5 text-xs font-semibold text-gray-500 uppercase">Horario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($grupo->horarios()->with('materia', 'docente')->get() as $h)
                            <tr>
                                <td class="px-4 py-2.5 text-gray-700">{{ $h->materia?->nombre_materia ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-gray-600">{{ $h->docente?->nombre_completo ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-center capitalize text-gray-600">{{ $h->dia_semana }}</td>
                                <td class="px-4 py-2.5 text-center text-gray-600">{{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Alumnos inscritos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h4 class="text-[15px] font-semibold text-gray-800 mb-4">Alumnos Inscritos ({{ $grupo->inscripciones()->count() }})</h4>
            @php $inscritos = $grupo->inscripciones()->with('alumno')->get(); @endphp
            @if($inscritos->isEmpty())
                <p class="text-sm text-gray-500">No hay alumnos inscritos.</p>
            @else
                <div class="space-y-2">
                    @foreach($inscritos as $insc)
                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-bold">
                                    {{ strtoupper(substr($insc->alumno?->nombre ?? 'A', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $insc->alumno?->nombre_completo ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">{{ $insc->alumno?->matricula }}</p>
                                </div>
                            </div>
                            <a href="{{ route('director.alumnos.historial', $insc->alumno?->id_alumno) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Ver historial</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-panel>
