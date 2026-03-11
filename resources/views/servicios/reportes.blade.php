<x-panel title="Reportes" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    {{-- Filtros --}}
    <form method="GET" class="bg-white rounded-xl shadow p-5 mb-6 flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Carrera *</label>
            <select name="carrera_id" required class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                <option value="">Seleccionar...</option>
                @foreach($carreras as $c)
                    <option value="{{ $c->id_carrera }}" @selected(request('carrera_id') == $c->id_carrera)>{{ $c->nombre_carrera }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Ciclo escolar *</label>
            <select name="ciclo_id" required class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                <option value="">Seleccionar...</option>
                @foreach($ciclos as $ciclo)
                    <option value="{{ $ciclo->id_ciclo }}" @selected(request('ciclo_id') == $ciclo->id_ciclo)>{{ $ciclo->nombre }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
            Generar reporte
        </button>
    </form>

    @if($reporte)
        <div class="space-y-6">
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                <p class="text-sm text-indigo-500">Reporte: <strong>{{ $reporte['carrera']->nombre_carrera }}</strong> — Ciclo <strong>{{ $reporte['ciclo']->nombre }}</strong></p>
            </div>

            {{-- Aprobación --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <p class="text-3xl font-bold text-gray-800">{{ $reporte['aprobacion']['total'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Total calificaciones</p>
                </div>
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $reporte['aprobacion']['porcentaje_aprobacion'] }}%</p>
                    <p class="text-sm text-gray-500 mt-1">Aprobación</p>
                </div>
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <p class="text-3xl font-bold text-red-500">{{ 100 - $reporte['aprobacion']['porcentaje_aprobacion'] }}%</p>
                    <p class="text-sm text-gray-500 mt-1">Reprobación</p>
                </div>
            </div>

            {{-- Semáforo --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-base font-semibold text-gray-700 mb-4">Distribución Semáforo Académico</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-2xl font-bold text-green-700">{{ $reporte['semaforo']['verde'] }}</p>
                        <p class="text-sm text-green-600 mt-1">🟢 Verde</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <p class="text-2xl font-bold text-yellow-700">{{ $reporte['semaforo']['amarillo'] }}</p>
                        <p class="text-sm text-yellow-600 mt-1">🟡 Amarillo</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4">
                        <p class="text-2xl font-bold text-red-700">{{ $reporte['semaforo']['rojo'] }}</p>
                        <p class="text-sm text-red-600 mt-1">🔴 Rojo</p>
                    </div>
                </div>
            </div>

            {{-- Evaluación docentes --}}
            @if($reporte['evaluacion_docentes']->isNotEmpty())
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <div class="px-6 py-4 border-b"><h3 class="font-semibold text-gray-700">Evaluación Docente</h3></div>
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Docente</th>
                                <th class="px-4 py-3 text-center">Promedio</th>
                                <th class="px-4 py-3 text-center">Evaluaciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reporte['evaluacion_docentes'] as $ed)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $ed['docente']->nombre_completo }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 rounded text-sm font-bold {{ $ed['promedio'] >= 8 ? 'bg-green-100 text-green-800' : ($ed['promedio'] >= 6 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $ed['promedio'] }}/10
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-500">{{ $ed['total_evaluaciones'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
            Selecciona una carrera y ciclo escolar para generar el reporte.
        </div>
    @endif
</x-panel>
