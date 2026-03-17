<x-panel title="Kárdex Académico" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    {{-- Encabezado con datos del alumno y botón PDF --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">Alumno: <span class="font-semibold text-gray-800">{{ $alumno?->nombre_completo }}</span></p>
            <p class="text-sm text-gray-500">Matrícula: <span class="font-mono font-semibold text-indigo-700">{{ $alumno?->matricula }}</span></p>
            <p class="text-sm text-gray-500">Carrera: <span class="font-medium text-gray-800">{{ $alumno?->carrera?->nombre_carrera }}</span></p>
        </div>
        @if($alumno)
            <a href="{{ route('alumno.kardex.pdf') }}"
               class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                📄 Descargar PDF
            </a>
        @endif
    </div>

    {{-- Promedio general --}}
    @if($alumno)
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg px-6 py-4 mb-6 flex items-center justify-between">
            <span class="text-sm font-medium text-indigo-700">Promedio General Acumulado</span>
            <span class="text-3xl font-bold {{ $promedio >= 7 ? 'text-green-700' : 'text-red-600' }}">
                {{ $promedio }}
            </span>
        </div>
    @endif

    {{-- Historial por ciclo --}}
    @forelse($historial as $cicloNombre => $calificaciones)
        <div class="bg-white rounded-xl shadow mb-6">
            <div class="px-6 py-3 bg-indigo-900 rounded-t-xl">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wide">📅 {{ $cicloNombre }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-indigo-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Parcial 1</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Parcial 2</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Parcial 3</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Promedio</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @php $porMateria = $calificaciones->groupBy('id_materia'); @endphp
                        @foreach($porMateria as $mid => $parciales)
                            @php
                                $materia  = $parciales->first()->materia;
                                $p1       = $parciales->where('parcial', 1)->first()?->calificacion;
                                $p2       = $parciales->where('parcial', 2)->first()?->calificacion;
                                $p3       = $parciales->where('parcial', 3)->first()?->calificacion;
                                $promMat  = round($parciales->avg('calificacion'), 2);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $materia?->nombre_materia ?? '—' }}</td>
                                <td class="px-4 py-3 text-center text-sm {{ ($p1 !== null && $p1 < 7) ? 'text-red-600 font-semibold' : 'text-gray-700' }}">{{ $p1 ?? '—' }}</td>
                                <td class="px-4 py-3 text-center text-sm {{ ($p2 !== null && $p2 < 7) ? 'text-red-600 font-semibold' : 'text-gray-700' }}">{{ $p2 ?? '—' }}</td>
                                <td class="px-4 py-3 text-center text-sm {{ ($p3 !== null && $p3 < 7) ? 'text-red-600 font-semibold' : 'text-gray-700' }}">{{ $p3 ?? '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded text-sm font-bold {{ $promMat >= 7 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $promMat }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
            Sin historial académico registrado.
        </div>
    @endforelse

</x-panel>
