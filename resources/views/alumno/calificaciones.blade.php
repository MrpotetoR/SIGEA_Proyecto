<x-panel title="Mis Calificaciones" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    {{-- Filtro por ciclo --}}
    <form method="GET" class="flex gap-3 mb-6 items-center">
        <label class="text-sm font-medium text-gray-600">Ciclo Escolar:</label>
        <select name="ciclo_id" class="border rounded-lg px-3 py-2 text-sm">
            @foreach($ciclos as $ciclo)
                <option value="{{ $ciclo->id_ciclo }}" @selected($cicloSeleccionado?->id_ciclo == $ciclo->id_ciclo)>
                    {{ $ciclo->nombre }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
            Filtrar
        </button>
    </form>

    @if($calificaciones->isNotEmpty())
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
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
                    @foreach($calificaciones as $materiaId => $parciales)
                        @php
                            $materia = $parciales->first()->materia;
                            $p1 = $parciales->where('parcial', 1)->first()?->calificacion;
                            $p2 = $parciales->where('parcial', 2)->first()?->calificacion;
                            $p3 = $parciales->where('parcial', 3)->first()?->calificacion;
                            $promedio = round($parciales->avg('calificacion'), 2);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $materia->nombre_materia }}</td>
                            <td class="px-4 py-4 text-center text-sm @if($p1 && $p1 < 7) text-red-600 font-semibold @endif">
                                {{ $p1 ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-center text-sm @if($p2 && $p2 < 7) text-red-600 font-semibold @endif">
                                {{ $p2 ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-center text-sm @if($p3 && $p3 < 7) text-red-600 font-semibold @endif">
                                {{ $p3 ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2 py-1 rounded text-sm font-bold {{ $promedio >= 7 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $promedio }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
            Sin calificaciones registradas para este ciclo.
        </div>
    @endif
</x-panel>
