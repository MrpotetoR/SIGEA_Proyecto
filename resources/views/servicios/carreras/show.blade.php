<x-panel title="Detalle de Carrera" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-4xl space-y-6">
        <a href="{{ route('servicios.carreras.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline inline-block">← Volver</a>

        {{-- Info general --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $carrera->nombre_carrera }}</h2>
                    <p class="text-sm text-gray-400 dark:text-gray-400 mt-1">Clave: <span class="font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $carrera->clave_carrera }}</span></p>
                </div>
                <a href="{{ route('servicios.carreras.edit', $carrera) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Editar
                </a>
            </div>

            <div class="grid grid-cols-3 gap-4 pt-4 border-t dark:border-gray-700">
                <div class="text-center">
                    <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">{{ $carrera->alumnos->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Alumnos activos</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">{{ $carrera->materias->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Materias</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">
                        {{ $carrera->director ? $carrera->director->nombre_completo : '—' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Director de carrera</p>
                </div>
            </div>
        </div>

        {{-- Materias por cuatrimestre --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 overflow-hidden border border-transparent dark:border-gray-700">
            <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Plan de Estudios</h3>
                <span class="text-xs text-gray-400 dark:text-gray-400">{{ $carrera->materias->count() }} materias</span>
            </div>
            @if($carrera->materias->isEmpty())
                <p class="px-6 py-8 text-center text-gray-400 dark:text-gray-400 text-sm">No hay materias registradas.</p>
            @else
                @foreach($carrera->materias->groupBy('cuatrimestre') as $cuatrimestre => $materias)
                    <div class="px-6 py-3 bg-indigo-50 dark:bg-indigo-900/20 border-b dark:border-gray-700 text-xs font-semibold text-indigo-700 dark:text-indigo-400 uppercase tracking-wide">
                        Cuatrimestre {{ $cuatrimestre }}
                    </div>
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                        <tbody>
                        @foreach($materias as $m)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-2 text-gray-800 dark:text-gray-200">{{ $m->nombre_materia }}</td>
                                <td class="px-4 py-2 text-gray-400 dark:text-gray-400 text-right">{{ $m->horas_semana }} hrs/sem</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endforeach
            @endif
        </div>

        {{-- Alumnos activos --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 overflow-hidden border border-transparent dark:border-gray-700">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Alumnos Inscritos</h3>
            </div>
            @if($carrera->alumnos->isEmpty())
                <p class="px-6 py-8 text-center text-gray-400 dark:text-gray-400 text-sm">No hay alumnos registrados.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Matrícula</th>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-center">Cuatrimestre</th>
                            <th class="px-4 py-3 text-center">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($carrera->alumnos->take(20) as $alumno)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-2 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $alumno->matricula }}</td>
                                <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-200">{{ $alumno->apellidos }}, {{ $alumno->nombre }}</td>
                                <td class="px-4 py-2 text-center text-gray-600 dark:text-gray-400">{{ $alumno->cuatrimestre_actual }}</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $alumno->estatus === 'activo' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                        {{ ucfirst($alumno->estatus) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($carrera->alumnos->count() > 20)
                    <p class="px-6 py-3 text-xs text-gray-400 dark:text-gray-400 border-t dark:border-gray-700">Mostrando 20 de {{ $carrera->alumnos->count() }} alumnos.</p>
                @endif
            @endif
        </div>
    </div>
</x-panel>
