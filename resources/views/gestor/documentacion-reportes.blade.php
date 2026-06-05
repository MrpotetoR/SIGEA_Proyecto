<x-panel title="Documentación y Reportes" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div x-data="{ tab: '{{ $tab }}' }" class="space-y-6">
        {{-- Tabs --}}
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex gap-6" aria-label="Tabs">
                <button type="button" @click="tab = 'reportes'; history.replaceState(null, '', '?tab=reportes')"
                    :class="tab === 'reportes'
                        ? 'border-[#0606F0] text-[#0606F0] dark:text-blue-400 dark:border-blue-400'
                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600'"
                    class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm inline-flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Reportes Académicos
                </button>
                <button type="button" @click="tab = 'documentos'; history.replaceState(null, '', '?tab=documentos')"
                    :class="tab === 'documentos'
                        ? 'border-[#0606F0] text-[#0606F0] dark:text-blue-400 dark:border-blue-400'
                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600'"
                    class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm inline-flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    Documentos Institucionales
                    <span
                        class="ml-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs px-2 py-0.5 rounded-full">{{ $documentos->total() }}</span>
                </button>
            </nav>
        </div>

        {{-- TAB: REPORTES ACADÉMICOS --}}
        <div x-show="tab === 'reportes'" x-cloak>
            <form method="GET"
                class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-5 mb-6 flex flex-wrap gap-4 items-end">
                <input type="hidden" name="tab" value="reportes">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Carrera *</label>
                    <select name="carrera_id" required
                        class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="">Seleccionar...</option>
                        @foreach ($carreras as $c)
                            <option value="{{ $c->id_carrera }}" @selected(request('carrera_id') == $c->id_carrera)>
                                {{ $c->nombre_carrera }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Ciclo escolar *</label>
                    <select name="ciclo_id" required
                        class="border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="">Seleccionar...</option>
                        @foreach ($ciclos as $ciclo)
                            <option value="{{ $ciclo->id_ciclo }}" @selected(request('ciclo_id') == $ciclo->id_ciclo)>
                                {{ $ciclo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="bg-[#0606F0] hover:bg-[#04276B] dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                    Generar reporte
                </button>
            </form>

            @if ($reporte)
                <div class="space-y-6">
                    <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-xl p-4">
                        <p class="text-sm text-[#0606F0] dark:text-blue-300">
                            Reporte: <strong>{{ $reporte['carrera']->nombre_carrera }}</strong> —
                            Ciclo <strong>{{ $reporte['ciclo']->nombre }}</strong>
                        </p>
                    </div>

                    {{-- Aprobación --}}
                    @php
                        $totalCal = $reporte['aprobacion']['total'];
                        $pctAprob = $reporte['aprobacion']['porcentaje_aprobacion'];
                        $pctRep = $totalCal > 0 ? round(100 - $pctAprob, 1) : 0;
                    @endphp
                    <div class="grid grid-cols-3 gap-4">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6 text-center">
                            <p class="text-3xl font-bold text-gray-800 dark:text-gray-200">{{ $totalCal }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total calificaciones</p>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6 text-center">
                            <p
                                class="text-3xl font-bold {{ $totalCal > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                                {{ $totalCal > 0 ? $pctAprob . '%' : '—' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Aprobación {{ $totalCal > 0 ? '(' . $reporte['aprobacion']['aprobadas'] . ')' : '' }}
                            </p>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6 text-center">
                            <p
                                class="text-3xl font-bold {{ $totalCal > 0 ? 'text-red-500 dark:text-red-400' : 'text-gray-400 dark:text-gray-500' }}">
                                {{ $totalCal > 0 ? $pctRep . '%' : '—' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Reprobación
                                {{ $totalCal > 0 ? '(' . $reporte['aprobacion']['reprobadas'] . ')' : '' }}
                            </p>
                        </div>
                    </div>

                    @if ($totalCal === 0)
                        <div
                            class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl p-4 text-sm text-amber-800 dark:text-amber-200">
                            Aún no hay calificaciones capturadas para esta carrera en el ciclo seleccionado.
                            Los porcentajes se mostrarán cuando haya registros.
                        </div>
                    @endif

                    {{-- Semáforo --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                        <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-4">
                            Distribución Semáforo Académico
                        </h3>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4">
                                <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                                    {{ $reporte['semaforo']['verde'] }}
                                </p>
                                <p class="text-sm text-green-600 dark:text-green-400 mt-1 inline-flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span> Verde
                                </p>
                            </div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-lg p-4">
                                <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">
                                    {{ $reporte['semaforo']['amarillo'] }}
                                </p>
                                <p
                                    class="text-sm text-yellow-600 dark:text-yellow-400 mt-1 inline-flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 inline-block"></span> Amarillo
                                </p>
                            </div>
                            <div class="bg-red-50 dark:bg-red-900/30 rounded-lg p-4">
                                <p class="text-2xl font-bold text-red-700 dark:text-red-300">
                                    {{ $reporte['semaforo']['rojo'] }}
                                </p>
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1 inline-flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span> Rojo
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Evaluación docentes --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0"
                        style="max-height: calc(100vh - 320px);">
                        <div
                            class="px-6 py-4 border-b dark:border-gray-700 flex-shrink-0 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300">Evaluación Docente</h3>
                            @php $totalEval = $reporte['evaluacion_docentes']->sum('total_evaluaciones'); @endphp
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $totalEval }} evaluación(es) en
                                el ciclo</span>
                        </div>
                        <div class="overflow-y-auto flex-1 custom-scrollbar">
                            @if ($reporte['evaluacion_docentes']->isNotEmpty())
                                <table
                                    class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm bg-white dark:bg-gray-800">
                                    <thead
                                        class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Docente</th>
                                            <th class="px-4 py-3 text-center">Promedio</th>
                                            <th class="px-4 py-3 text-center">Evaluaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        @foreach ($reporte['evaluacion_docentes'] as $ed)
                                            @php $conEval = $ed['total_evaluaciones'] > 0; @endphp
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $ed['docente']->nombre_completo }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if ($conEval)
                                                        <span
                                                            class="px-2 py-1 rounded text-sm font-bold {{ $ed['promedio'] >= 8 ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : ($ed['promedio'] >= 6 ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300') }}">
                                                            {{ $ed['promedio'] }}/10
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">Sin
                                                            evaluaciones</span>
                                                    @endif
                                                </td>
                                                <td
                                                    class="px-4 py-3 text-center {{ $conEval ? 'font-semibold text-gray-700 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500' }}">
                                                    {{ $ed['total_evaluaciones'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="px-6 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                                    No hay docentes asignados a esta carrera.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-12 text-center text-gray-400 dark:text-gray-400">
                    Selecciona una carrera y ciclo escolar para generar el reporte.
                </div>
            @endif
        </div>

        {{-- TAB: DOCUMENTOS INSTITUCIONALES --}}
        <div x-show="tab === 'documentos'" x-cloak x-data="{ modalCarpeta: false }">
            {{-- Flash messages: el componente panel.blade.php ya los muestra en la cabecera. --}}

            {{-- Breadcrumb + acciones --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <nav class="flex items-center text-sm text-gray-600 dark:text-gray-300 gap-1.5">
                    <a href="{{ route('gestor.documentacion-reportes', ['tab' => 'documentos']) }}"
                        class="hover:text-[#0606F0] dark:hover:text-blue-400 inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <span>Inicio</span>
                    </a>
                    @foreach($breadcrumb as $i => $crumb)
                        <span class="text-gray-400 dark:text-gray-500">/</span>
                        @if($loop->last)
                            <span class="font-semibold text-gray-900 dark:text-gray-100 inline-flex items-center gap-1.5">
                                {{ $crumb->nombre }}
                                @if($crumb->esPrivada())
                                    <span
                                        class="text-[10px] bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 px-1.5 py-0.5 rounded uppercase font-semibold">Privada</span>
                                @endif
                            </span>
                        @else
                            <a href="{{ route('gestor.documentacion-reportes', ['tab' => 'documentos', 'carpeta' => $crumb->id_carpeta]) }}"
                                class="hover:text-[#0606F0] dark:hover:text-blue-400">{{ $crumb->nombre }}</a>
                        @endif
                    @endforeach
                </nav>

                <div class="flex gap-2">
                    <button type="button" @click="modalCarpeta = true"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2zM12 11v6m-3-3h6" />
                        </svg>
                        {{ $carpetaActual ? 'Crear subcarpeta' : 'Crear carpeta' }}
                    </button>
                    <a href="{{ route('gestor.documentos.create', array_filter(['carpeta' => $carpetaActual?->id_carpeta])) }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Subir documento
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0"
                style="max-height: calc(100vh - 320px);">
                <div class="overflow-y-auto flex-1 custom-scrollbar">
                    <table
                        class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm bg-white dark:bg-gray-800">
                        <thead
                            class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left">Nombre</th>
                                <th class="px-4 py-3 text-left">Tipo</th>
                                <th class="px-4 py-3 text-center">Fecha</th>
                                <th class="px-4 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            {{-- CARPETAS primero --}}
                            @foreach ($carpetas as $c)
                                @php
                                    $esPropia = $c->user_id === auth()->id();
                                    $urlCarpeta = route('gestor.documentacion-reportes', ['tab' => 'documentos', 'carpeta' => $c->id_carpeta]);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer select-none"
                                    ondblclick="window.location.href='{{ $urlCarpeta }}'" title="Doble clic para abrir">
                                    <td class="px-4 py-3">
                                        <a href="{{ $urlCarpeta }}"
                                            class="font-medium text-gray-900 dark:text-gray-100 inline-flex items-center gap-2 hover:text-[#0606F0] dark:hover:text-blue-400">
                                            <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M10 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2h-8l-2-2z" />
                                            </svg>
                                            {{ $c->nombre }}
                                            @if($c->esPrivada())
                                                <span
                                                    class="text-[10px] bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 px-1.5 py-0.5 rounded uppercase font-semibold">Privada</span>
                                            @endif
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 italic text-xs">Carpeta</td>
                                    <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 text-xs">
                                        {{ $c->created_at?->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center" ondblclick="event.stopPropagation()">
                                        @if($esPropia)
                                            <div class="flex justify-center gap-2"
                                                x-data="{ renombrar: false, confirmarEliminar: false }">
                                                <button type="button" @click="renombrar = true"
                                                    class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 font-medium">Renombrar</button>
                                                <button type="button" @click="confirmarEliminar = true"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 font-medium">Eliminar</button>

                                                {{-- Modal confirmar eliminar carpeta (teleportado al body) --}}
                                                <template x-teleport="body">
                                                    <div x-show="confirmarEliminar" x-cloak
                                                        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50"
                                                        @keydown.escape.window="confirmarEliminar = false">
                                                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-md mx-4 text-left"
                                                            @click.outside="confirmarEliminar = false">
                                                            <div class="flex items-start gap-3 mb-4">
                                                                <div
                                                                    class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                                                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400"
                                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                        stroke-width="2">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M12 9v2m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z" />
                                                                    </svg>
                                                                </div>
                                                                <div class="flex-1">
                                                                    <h3
                                                                        class="font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                                                        Eliminar carpeta</h3>
                                                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                                                        ¿Estás seguro que quieres eliminar la carpeta
                                                                        <strong
                                                                            class="text-gray-900 dark:text-gray-100">"{{ $c->nombre }}"</strong>?
                                                                    </p>
                                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                                        La carpeta debe estar vacía. Esta acción no se puede
                                                                        deshacer.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <form method="POST"
                                                                action="{{ route('gestor.documentos.carpetas.destroy', $c->id_carpeta) }}">
                                                                @csrf @method('DELETE')
                                                                <div
                                                                    class="flex justify-end gap-2 pt-4 border-t dark:border-gray-700">
                                                                    <button type="button" @click="confirmarEliminar = false"
                                                                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg font-medium">Cancelar</button>
                                                                    <button type="submit"
                                                                        class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">Eliminar</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </template>

                                                {{-- Modal renombrar (teleportado al body para evitar transforms ancestrales)
                                                --}}
                                                <template x-teleport="body">
                                                    <div x-show="renombrar" x-cloak
                                                        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50"
                                                        @keydown.escape.window="renombrar = false">
                                                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-md mx-4"
                                                            @click.outside="renombrar = false">
                                                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                                                Renombrar carpeta</h3>
                                                            <form method="POST"
                                                                action="{{ route('gestor.documentos.carpetas.update', $c->id_carpeta) }}">
                                                                @csrf @method('PUT')
                                                                <input type="text" name="nombre" value="{{ $c->nombre }}"
                                                                    required maxlength="120"
                                                                    class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                                                <div class="flex justify-end gap-2 mt-4">
                                                                    <button type="button" @click="renombrar = false"
                                                                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancelar</button>
                                                                    <button type="submit"
                                                                        class="px-4 py-2 text-sm bg-[#0606F0] hover:bg-[#04276B] text-white rounded-lg font-medium">Guardar</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500 italic">— solo lectura
                                                —</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            {{-- DOCUMENTOS después --}}
                            @forelse ($documentos as $d)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td
                                        class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100 inline-flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ $d->titulo }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $d->tipo }}</td>
                                    <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                        {{ $d->fecha_publicacion?->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('gestor.documentos.edit', $d) }}"
                                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 font-medium">Editar</a>
                                            <form method="POST" action="{{ route('gestor.documentos.destroy', $d) }}"
                                                class="inline"
                                                data-udea-confirm
                                                data-confirm-title="Eliminar documento"
                                                data-confirm-message="¿Eliminar el documento <strong>&quot;{{ $d->titulo ?? $d->nombre_original ?? 'sin nombre' }}&quot;</strong>?"
                                                data-confirm-detail="Esta acción no se puede deshacer."
                                                data-confirm-variant="danger"
                                                data-confirm-icon="trash"
                                                data-confirm-button="Eliminar"
                                                data-confirm-cancel="Cancelar">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 font-medium">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                @if($carpetas->isEmpty())
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-gray-400 dark:text-gray-400">
                                            {{ $carpetaActual ? 'Esta carpeta está vacía.' : 'No hay carpetas ni documentos publicados.' }}
                                        </td>
                                    </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($documentos->hasPages())
                    <div class="px-4 py-3 border-t dark:border-gray-700">
                        {{ $documentos->links() }}
                    </div>
                @endif
            </div>

            {{-- Modal: Crear carpeta (teleportado al body para evitar transforms ancestrales) --}}
            <template x-teleport="body">
                <div x-show="modalCarpeta" x-cloak
                    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50"
                    @keydown.escape.window="modalCarpeta = false">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-md mx-4"
                        @click.outside="modalCarpeta = false">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 inline-flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M10 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2h-8l-2-2z" />
                            </svg>
                            {{ $carpetaActual ? 'Nueva subcarpeta' : 'Nueva carpeta' }}
                        </h3>

                        <form method="POST" action="{{ route('gestor.documentos.carpetas.store') }}">
                            @csrf
                            @if($carpetaActual)
                                <input type="hidden" name="parent_id" value="{{ $carpetaActual->id_carpeta }}">
                            @endif

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre
                                    *</label>
                                <input type="text" name="nombre" required maxlength="120" autofocus
                                    placeholder="Ej. Reglamentos 2026"
                                    class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>

                            @if($carpetaActual)
                                <div
                                    class="mb-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-3 text-xs text-gray-600 dark:text-gray-400">
                                    La subcarpeta hereda la visibilidad de
                                    <strong>{{ $carpetaActual->nombre }}</strong>
                                    ({{ $carpetaActual->esPrivada() ? 'privada' : 'pública' }}).
                                </div>
                            @else
                                <fieldset class="mb-4">
                                    <legend class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Visibilidad</legend>
                                    <div class="space-y-2">
                                        <label
                                            class="flex items-start gap-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <input type="radio" name="visibilidad" value="publica" checked
                                                class="mt-0.5 text-[#0606F0] focus:ring-blue-400">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Pública
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Visible para todo el
                                                    personal de Gestor Escolar.</div>
                                            </div>
                                        </label>
                                        <label
                                            class="flex items-start gap-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <input type="radio" name="visibilidad" value="privada"
                                                class="mt-0.5 text-[#0606F0] focus:ring-blue-400">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Privada
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Solo visible para ti.
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </fieldset>
                            @endif

                            <div class="flex justify-end gap-2 pt-4 border-t dark:border-gray-700">
                                <button type="button" @click="modalCarpeta = false"
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancelar</button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm bg-[#0606F0] hover:bg-[#04276B] text-white rounded-lg font-medium">Crear
                                    carpeta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>
</x-panel>