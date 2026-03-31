<x-panel title="Historial Académico" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
        Registro completo de calificaciones por ciclo escolar.
    </p>

    @forelse($historial as $cicloId => $calificaciones)
        @php $ciclo = $calificaciones->first()->cicloEscolar; @endphp
        <div class="bg-white rounded-xl shadow mb-6 dark:bg-gray-800 dark:shadow-gray-900/20">
            <div class="px-6 py-3 bg-gray-800 rounded-t-xl flex justify-between items-center">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wide">
                    📅 {{ $ciclo?->nombre ?? 'Ciclo ' . $cicloId }}
                </h3>
                @if($ciclo)
                    <span class="text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($ciclo->fecha_inicio)->format('d/m/Y') }}
                        — {{ \Carbon\Carbon::parse($ciclo->fecha_fin)->format('d/m/Y') }}
                    </span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Materia</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Parcial 1</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Parcial 2</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Parcial 3</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Promedio</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach($calificaciones->groupBy('id_materia') as $parciales)
                            @php
                                $materia = $parciales->first()->materia;
                                $p1      = $parciales->where('parcial', 1)->first()?->calificacion;
                                $p2      = $parciales->where('parcial', 2)->first()?->calificacion;
                                $p3      = $parciales->where('parcial', 3)->first()?->calificacion;
                                $prom    = round($parciales->avg('calificacion'), 2);
                                $aprobada = $prom >= 7;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-200">
                                    {{ $materia?->nombre_materia ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm {{ ($p1 !== null && $p1 < 7) ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">{{ $p1 ?? '—' }}</td>
                                <td class="px-4 py-3 text-center text-sm {{ ($p2 !== null && $p2 < 7) ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">{{ $p2 ?? '—' }}</td>
                                <td class="px-4 py-3 text-center text-sm {{ ($p3 !== null && $p3 < 7) ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">{{ $p3 ?? '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-bold {{ $aprobada ? 'text-green-700 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $prom }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $aprobada ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $aprobada ? 'Aprobada' : 'Reprobada' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <td colspan="4" class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Promedio del ciclo:</td>
                            <td class="px-4 py-2 text-center">
                                @php $promCiclo = round($calificaciones->avg('calificacion'), 2); @endphp
                                <span class="text-sm font-bold {{ $promCiclo >= 7 ? 'text-green-700 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $promCiclo }}
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400 dark:bg-gray-800 dark:shadow-gray-900/20 dark:text-gray-600">
            Sin historial académico registrado.
        </div>
    @endforelse

</x-panel>
