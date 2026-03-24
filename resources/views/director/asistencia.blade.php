<x-panel title="Asistencia" panelNombre="Panel Director">
    <x-slot name="nav">
        @include('partials.director-nav')
    </x-slot>

    <p class="text-sm text-gray-500 mb-5">Consulta de asistencia por grupo — <span class="font-semibold text-gray-700">{{ $carrera?->nombre_carrera ?? 'Sin carrera' }}</span></p>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('director.asistencia') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1.5">Grupo</label>
                <select name="grupo_id" class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                    <option value="">Seleccionar grupo...</option>
                    @foreach($grupos as $g)
                        <option value="{{ $g->id_grupo }}" {{ request('grupo_id') == $g->id_grupo ? 'selected' : '' }}>{{ $g->clave_grupo }} — {{ $g->cicloEscolar?->nombre ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-48">
                <label class="block text-xs text-gray-500 mb-1.5">Fecha (opcional)</label>
                <input type="date" name="fecha" value="{{ request('fecha') }}" class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
            </div>
            <button type="submit" class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors">Consultar</button>
        </div>
    </form>

    {{-- Resultados --}}
    @if($asistencias->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Presentes</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ausentes</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Justificadas</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">% Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($asistencias as $alumno)
                        @php
                            $total = $alumno->asistencias->count();
                            $p = $alumno->asistencias->where('estatus', 'presente')->count();
                            $a = $alumno->asistencias->where('estatus', 'ausente')->count();
                            $j = $alumno->asistencias->where('estatus', 'justificada')->count();
                            $porc = $total > 0 ? round(($p / $total) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $alumno->nombre_completo }}</p>
                                <p class="text-xs text-gray-400">{{ $alumno->matricula }}</p>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-green-600 font-semibold">{{ $p }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-red-500 font-semibold">{{ $a }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-yellow-600 font-semibold">{{ $j }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-lg {{ $porc >= 80 ? 'bg-green-100 text-green-700' : ($porc >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $porc }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif(request('grupo_id'))
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <p class="text-gray-500 text-sm">No se encontraron registros de asistencia para este grupo.</p>
        </div>
    @endif
</x-panel>
