<x-panel title="Alumnos" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    {{-- Tabs de modalidad (solo Bachillerato) --}}
    @if($esBachi && $tabs)
        @php
            $tabsConfig = [
                'escolarizado' => [
                    'label'   => 'Escolarizado',
                    'count'   => $tabs['escolarizado'] ?? 0,
                    'icon'    => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                ],
                'no_escolarizado' => [
                    'label'   => 'No Escolarizado',
                    'count'   => $tabs['no_escolarizado'] ?? 0,
                    'icon'    => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                ],
                'todos' => [
                    'label'   => 'Todos',
                    'count'   => $tabs['todos'] ?? 0,
                    'icon'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                ],
            ];
        @endphp
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <nav class="flex gap-1 -mb-px">
                @foreach($tabsConfig as $key => $cfg)
                    @php $activo = $modalidad === $key; @endphp
                    <a href="{{ route('gestor.alumnos.index', array_merge(request()->except(['modalidad', 'page']), ['modalidad' => $key])) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors
                              {{ $activo
                                  ? 'border-amber-500 text-amber-700 dark:text-amber-400'
                                  : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cfg['icon'] }}"/>
                        </svg>
                        {{ $cfg['label'] }}
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold
                                     {{ $activo
                                         ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300'
                                         : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                            {{ $cfg['count'] }}
                        </span>
                    </a>
                @endforeach
            </nav>
        </div>
    @endif

    {{-- Filtros --}}
    <form method="GET"
        class="flex flex-wrap gap-3 mb-6 items-end bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm dark:shadow-gray-900/20 border border-transparent dark:border-gray-700">
        @if($esBachi)
            {{-- Conserva la modalidad activa al filtrar --}}
            <input type="hidden" name="modalidad" value="{{ $modalidad }}">
        @endif
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                placeholder="Nombre, apellido o ID..."
                class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm w-56 focus:ring-2 focus:ring-blue-400 focus:outline-none dark:placeholder-gray-400">
        </div>
        @unless($esBachi)
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Carrera</label>
                <select name="carrera_id"
                    class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <option value="">Todas</option>
                    @foreach($carreras as $c)
                        <option value="{{ $c->id_carrera }}" @selected(request('carrera_id') == $c->id_carrera)>
                            {{ $c->nombre_carrera }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endunless
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Estatus</label>
            <select name="estatus"
                class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Todos</option>
                <option value="activo" @selected(request('estatus') === 'activo')>Activo</option>
                <option value="baja_temporal" @selected(request('estatus') === 'baja_temporal')>Baja temporal</option>
                <option value="baja_definitiva" @selected(request('estatus') === 'baja_definitiva')>Baja definitiva
                </option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Estado de pago</label>
            <select name="pago_estado"
                class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Todos</option>
                <option value="pagado" @selected(request('pago_estado') === 'pagado')>Pagado</option>
                <option value="revision" @selected(request('pago_estado') === 'revision')>En revisión</option>
                <option value="sin_pago" @selected(request('pago_estado') === 'sin_pago')>Sin pago</option>
            </select>
        </div>
        <button type="submit"
            class="bg-[#0606F0] hover:bg-[#04276B] dark:bg-[#0606F0] dark:hover:bg-[#0606F0] text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Filtrar
        </button>
        <a href="{{ route('gestor.alumnos.index', $esBachi ? ['modalidad' => $modalidad] : []) }}"
            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 py-2 px-2">Limpiar</a>

        <a href="{{ route('gestor.alumnos.create') }}"
            class="ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Nuevo alumno
        </a>
    </form>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0"
        style="max-height: calc(100vh - 220px);">
        <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm bg-white dark:bg-gray-800">
                <thead
                    class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left">Identificador Unico</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">
                            @if($esBachi && $modalidad === 'todos')
                                Modalidad
                            @elseif($esBachi)
                                Plan
                            @else
                                Carrera
                            @endif
                        </th>
                        <th class="px-4 py-3 text-center">
                            @if($esBachi && $modalidad === 'no_escolarizado')
                                Cuatrimestre
                            @elseif($esBachi)
                                Semestre
                            @else
                                Cuatrimestre
                            @endif
                        </th>
                        <th class="px-4 py-3 text-center">Estado de pago</th>
                        <th class="px-4 py-3 text-center">Estatus</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($alumnos as $alumno)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-mono text-gray-700 dark:text-gray-300">{{ $alumno->id_alumno_publico }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                                {{ $alumno->nombre_completo }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                @if($alumno->nivel_educativo === 'bachillerato')
                                    @php $plan = $alumno->planBachillerato; @endphp
                                    @if($esBachi && $modalidad === 'todos')
                                        @if($plan?->tipo_periodo === 'cuatrimestre')
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">No Escolarizado</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">Escolarizado</span>
                                        @endif
                                    @else
                                        <span class="font-mono text-[12px]">{{ $plan?->clave_plan ?? '—' }}</span>
                                    @endif
                                @else
                                    {{ $alumno->carrera?->clave_carrera }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">
                                {{ $alumno->cuatrimestre_actual }}°
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $pagoEstado = $alumno->pago_estado_actual;
                                    [$pagoBadge, $pagoTexto] = match ($pagoEstado) {
                                        'pagado' => ['bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300', 'Pagado'],
                                        'revision' => ['bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300', 'En revisión'],
                                        default => ['bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300', 'Sin pago'],
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $pagoBadge }}">
                                    {{ $pagoTexto }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $badge = match ($alumno->estatus) {
                                        'activo' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'baja_temporal' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                        default => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $alumno->estatus)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('gestor.alumnos.show', $alumno) }}"
                                        class="text-[#0606F0] dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 font-medium">Ver</a>
                                    <a href="{{ route('gestor.alumnos.edit', $alumno) }}"
                                        class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 font-medium">Editar</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">No hay alumnos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($alumnos instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="px-4 py-3 border-t dark:border-gray-700 flex-shrink-0">{{ $alumnos->links() }}</div>
        @endif
    </div>
</x-panel>

<script>
    (function () {
        const form = document.querySelector('form[method="GET"]');
        const buscar = form.querySelector('input[name="buscar"]');
        const selects = form.querySelectorAll('select');
        let timer;

        buscar.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(() => form.submit(), 400);
        });

        selects.forEach(s => s.addEventListener('change', () => form.submit()));
    })();
</script>