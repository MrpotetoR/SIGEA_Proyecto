<x-panel title="Reportes" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    {{-- Filtros --}}
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-5 mb-6 flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Carrera *</label>
            <select name="carrera_id" required class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Seleccionar...</option>
                @foreach($carreras as $c)
                    <option value="{{ $c->id_carrera }}" @selected(request('carrera_id') == $c->id_carrera)>{{ $c->nombre_carrera }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Ciclo escolar *</label>
            <select name="ciclo_id" required class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Seleccionar...</option>
                @foreach($ciclos as $ciclo)
                    <option value="{{ $ciclo->id_ciclo }}" @selected(request('ciclo_id') == $ciclo->id_ciclo)>{{ $ciclo->nombre }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-[#0606F0] hover:bg-[#04276B] dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
            Generar reporte
        </button>
    </form>

    @if($reporte)
        <div class="space-y-6">
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-xl p-4">
                <p class="text-sm text-[#0606F0] dark:text-blue-300">Reporte: <strong>{{ $reporte['carrera']->nombre_carrera }}</strong> — Ciclo <strong>{{ $reporte['ciclo']->nombre }}</strong></p>
            </div>

            {{-- Aprobación --}}
            @php
                $totalCal = $reporte['aprobacion']['total'];
                $pctAprob = $reporte['aprobacion']['porcentaje_aprobacion'];
                $pctRep   = $totalCal > 0 ? round(100 - $pctAprob, 1) : 0;
            @endphp
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6 text-center">
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-200">{{ $totalCal }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total calificaciones</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6 text-center">
                    <p class="text-3xl font-bold {{ $totalCal > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $totalCal > 0 ? $pctAprob.'%' : '—' }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Aprobación {{ $totalCal > 0 ? '('.$reporte['aprobacion']['aprobadas'].')' : '' }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6 text-center">
                    <p class="text-3xl font-bold {{ $totalCal > 0 ? 'text-red-500 dark:text-red-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $totalCal > 0 ? $pctRep.'%' : '—' }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Reprobación {{ $totalCal > 0 ? '('.$reporte['aprobacion']['reprobadas'].')' : '' }}</p>
                </div>
            </div>

            @if($totalCal === 0)
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl p-4 text-sm text-amber-800 dark:text-amber-200">
                    Aún no hay calificaciones capturadas para esta carrera en el ciclo seleccionado. Los porcentajes se mostrarán cuando haya registros.
                </div>
            @endif

            {{-- Semáforo --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-4">Distribución Semáforo Académico</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4">
                        <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $reporte['semaforo']['verde'] }}</p>
                        <p class="text-sm text-green-600 dark:text-green-400 mt-1 inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span> Verde</p>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-lg p-4">
                        <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $reporte['semaforo']['amarillo'] }}</p>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-1 inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-400 inline-block"></span> Amarillo</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/30 rounded-lg p-4">
                        <p class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $reporte['semaforo']['rojo'] }}</p>
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span> Rojo</p>
                    </div>
                </div>
            </div>

            {{-- Evaluación docentes --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0" style="max-height: calc(100vh - 220px);">
                <div class="px-6 py-4 border-b dark:border-gray-700 flex-shrink-0 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-300">Evaluación Docente</h3>
                    @php $totalEval = $reporte['evaluacion_docentes']->sum('total_evaluaciones'); @endphp
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $totalEval }} evaluación(es) en el ciclo</span>
                </div>
                <div class="overflow-y-auto flex-1 custom-scrollbar">
                @if($reporte['evaluacion_docentes']->isNotEmpty())
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm bg-white dark:bg-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left">Docente</th>
                                <th class="px-4 py-3 text-center">Promedio</th>
                                <th class="px-4 py-3 text-center">Evaluaciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($reporte['evaluacion_docentes'] as $ed)
                                @php $conEval = $ed['total_evaluaciones'] > 0; @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $ed['docente']->nombre_completo }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($conEval)
                                            <span class="px-2 py-1 rounded text-sm font-bold {{ $ed['promedio'] >= 8 ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : ($ed['promedio'] >= 6 ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300') }}">
                                                {{ $ed['promedio'] }}/10
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500 italic">Sin evaluaciones</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center {{ $conEval ? 'font-semibold text-gray-700 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500' }}">{{ $ed['total_evaluaciones'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="px-6 py-10 text-center text-sm text-gray-400 dark:text-gray-500">No hay docentes asignados a esta carrera.</p>
                @endif
                </div>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-12 text-center text-gray-400 dark:text-gray-400">
            Selecciona una carrera y ciclo escolar para generar el reporte.
        </div>
    @endif
</x-panel>
