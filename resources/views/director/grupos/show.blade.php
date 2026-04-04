<x-panel title="Grupo {{ $grupo->clave_grupo }}" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <div class="mb-5">
        <a href="{{ route('director.grupos.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver a Grupos
        </a>
    </div>

    <div class="max-w-3xl">
        {{-- Info del grupo --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $grupo->clave_grupo }}</h3>
                <a href="{{ route('director.grupos.edit', $grupo->id_grupo) }}" class="px-4 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">Editar</a>
            </div>
            <div class="grid grid-cols-2 gap-y-4 gap-x-8">
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Carrera</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $grupo->carrera?->nombre_carrera ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Ciclo Escolar</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $grupo->cicloEscolar?->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Cuatrimestre</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $grupo->cuatrimestre }}o</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Tutor</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $grupo->tutorDocente?->nombre_completo ?? 'Sin tutor' }}</p>
                </div>
            </div>
        </div>

        {{-- Horarios del grupo --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6 mb-6">
            <h4 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-4">Horarios</h4>
            @if($grupo->horarios->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay horarios asignados.</p>
            @else
                <div class="overflow-y-auto custom-scrollbar" style="max-height: 300px;">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Materia</th>
                            <th class="text-left px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Docente</th>
                            <th class="text-center px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Dia</th>
                            <th class="text-center px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Horario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @foreach($grupo->horarios()->with('materia', 'docente')->get() as $h)
                            <tr>
                                <td class="px-4 py-2.5 text-gray-700 dark:text-gray-300">{{ $h->materia?->nombre_materia ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-gray-600 dark:text-gray-400">{{ $h->docente?->nombre_completo ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-center capitalize text-gray-600 dark:text-gray-400">{{ $h->dia_semana }}</td>
                                <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @endif
        </div>

        {{-- Alumnos inscritos --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
            @php
                $inscritos = $grupo->inscripciones()->with('alumno')->get();
                $inscritosIds = $grupo->inscripciones()->pluck('id_alumno');
                $disponibles = \App\Models\Alumno::where('id_carrera', $grupo->id_carrera)
                    ->activos()
                    ->whereNotIn('id_alumno', $inscritosIds)
                    ->orderBy('apellidos')
                    ->get();
            @endphp
            <h4 class="text-[15px] font-semibold text-gray-800 dark:text-gray-200 mb-4">Alumnos Inscritos ({{ $inscritos->count() }})</h4>

            {{-- Formulario para agregar alumno --}}
            @if($disponibles->isNotEmpty())
                <form action="{{ route('director.grupos.inscribir', $grupo->id_grupo) }}" method="POST" class="flex items-center gap-2 mb-4">
                    @csrf
                    <select name="id_alumno" required class="flex-1 text-sm rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccionar alumno...</option>
                        @foreach($disponibles as $al)
                            <option value="{{ $al->id_alumno }}">{{ $al->nombre_completo }} ({{ $al->matricula }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">Inscribir</button>
                </form>
            @endif

            @if($inscritos->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay alumnos inscritos.</p>
            @else
                <div class="space-y-2">
                    @foreach($inscritos as $insc)
                        <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-700 dark:text-blue-400 text-xs font-bold">
                                    {{ strtoupper(substr($insc->alumno?->nombre ?? 'A', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $insc->alumno?->nombre_completo ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $insc->alumno?->matricula }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('director.alumnos.historial', $insc->alumno?->id_alumno) }}" class="text-xs text-[#0606F0] dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">Ver historial</a>
                                <form action="{{ route('director.grupos.desinscribir', [$grupo->id_grupo, $insc->alumno?->id_alumno]) }}" method="POST" onsubmit="return confirm('¿Remover a este alumno del grupo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Remover del grupo">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-panel>
