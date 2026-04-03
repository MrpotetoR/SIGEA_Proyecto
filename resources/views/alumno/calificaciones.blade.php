<x-panel title="Mis Calificaciones" panelNombre="Panel Alumno">
    <x-slot name="nav">@include('partials.alumno-nav')</x-slot>

    {{-- Filtro por ciclo --}}
    <form method="GET" class="flex gap-3 mb-6 items-center">
        <label class="text-sm font-medium text-gray-600 dark:text-gray-300">Ciclo Escolar:</label>
        <select name="ciclo_id" class="border rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
            @foreach($ciclos as $ciclo)
                <option value="{{ $ciclo->id_ciclo }}" @selected($cicloSeleccionado?->id_ciclo == $ciclo->id_ciclo)>
                    {{ $ciclo->nombre }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-[#0606F0] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#04276B]">
            Filtrar
        </button>
    </form>

    @if($calificaciones->isNotEmpty())
        <div class="bg-white rounded-xl shadow dark:bg-gray-800 dark:shadow-gray-900/20 flex flex-col min-h-0" style="max-height: calc(100vh - 220px);">
            <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-blue-50 dark:bg-gray-700/50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Materia</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Parcial 1</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Parcial 2</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Parcial 3</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Promedio</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100 dark:bg-gray-800 dark:divide-gray-700">
                    @foreach($calificaciones as $materiaId => $parciales)
                        @php
                            $materia = $parciales->first()->materia;
                            $p1 = $parciales->where('parcial', 1)->first()?->calificacion;
                            $p2 = $parciales->where('parcial', 2)->first()?->calificacion;
                            $p3 = $parciales->where('parcial', 3)->first()?->calificacion;
                            $promedio = round($parciales->avg('calificacion'), 2);
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-200">{{ $materia->nombre_materia }}</td>
                            <td class="px-4 py-4 text-center text-sm @if($p1 && $p1 < 7) text-red-600 dark:text-red-400 font-semibold @endif">
                                {{ $p1 ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-center text-sm @if($p2 && $p2 < 7) text-red-600 dark:text-red-400 font-semibold @endif">
                                {{ $p2 ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-center text-sm @if($p3 && $p3 < 7) text-red-600 dark:text-red-400 font-semibold @endif">
                                {{ $p3 ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2 py-1 rounded text-sm font-bold {{ $promedio >= 7 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                    {{ $promedio }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400 dark:bg-gray-800 dark:shadow-gray-900/20 dark:text-gray-600">
            Sin calificaciones registradas para este ciclo.
        </div>
    @endif
</x-panel>
