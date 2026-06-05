<x-panel title="Ciclos Escolares" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Ciclos Escolares</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                UDEA opera con 3 cohortes por año (bloques A · B · C, según ingreso de enero, mayo y septiembre).
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button"
                    onclick="document.getElementById('modal-crear-anio').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Crear ciclos del año
            </button>
        </div>
    </div>

    {{-- ════════ Modal: Crear los ciclos del año ════════
         Renderizado vía @push('scripts') para que viva fuera del div .fade-in del panel
         (ese div tiene `transform` aplicado por animación, lo cual crea un containing
         block que rompe el `position: fixed` del modal). --}}
    @push('scripts')
    <div id="modal-crear-anio"
         class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4"
         style="background-color: rgba(0,0,0,0.55);">

        <form method="POST" action="{{ route('gestor.ciclos.crear-anio') }}"
              class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md flex flex-col overflow-hidden"
              style="max-height: 90vh;">
            @csrf

            {{-- Header (fijo arriba) --}}
            <div class="flex-shrink-0 flex items-start justify-between px-6 pt-5 pb-3 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">Crear ciclos del año</h3>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                        Genera todas las cohortes con fechas predefinidas.
                    </p>
                </div>
                <button type="button"
                        onclick="document.getElementById('modal-crear-anio').classList.add('hidden')"
                        class="p-1 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors -mt-1 -mr-2"
                        aria-label="Cerrar">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body (scrollable solo si rebasa 90vh) --}}
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 custom-scrollbar">

                {{-- Selector de tipo --}}
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Tipo de ciclo</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo" value="cuatrimestre" class="peer sr-only" checked>
                            <div class="border-2 border-gray-200 dark:border-gray-600 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 rounded-lg px-3 py-2.5 transition-colors text-center">
                                <p class="text-[13px] font-bold text-gray-800 dark:text-gray-100">Cuatrimestral</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">3 Bloques · 3a 4m</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo" value="semestre" class="peer sr-only">
                            <div class="border-2 border-gray-200 dark:border-gray-600 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 rounded-lg px-3 py-2.5 transition-colors text-center">
                                <p class="text-[13px] font-bold text-gray-800 dark:text-gray-100">Semestral</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">2 Bloques · 3a</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Año --}}
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Año de ingreso</label>
                    <input type="number" name="anio" id="input-anio"
                           value="{{ date('Y') }}" min="2020" max="2100" step="1" required
                           class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>

                {{-- Fechas editables Cuatrimestral --}}
                <div id="preview-cuatri" class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-3 space-y-2 text-[11px]">
                    <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Fechas de inicio de cada bloque</p>
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-semibold text-gray-700 dark:text-gray-200 w-16">Bloque A</span>
                        <input type="date" name="fechas[A]" data-bloque="A" data-default-mes="01" data-default-dia="15"
                               class="fecha-bloque flex-1 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 text-[11px] focus:ring-1 focus:ring-emerald-400 focus:outline-none">
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-semibold text-gray-700 dark:text-gray-200 w-16">Bloque B</span>
                        <input type="date" name="fechas[B]" data-bloque="B" data-default-mes="05" data-default-dia="15"
                               class="fecha-bloque flex-1 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 text-[11px] focus:ring-1 focus:ring-emerald-400 focus:outline-none">
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-semibold text-gray-700 dark:text-gray-200 w-16">Bloque C</span>
                        <input type="date" name="fechas[C]" data-bloque="C" data-default-mes="09" data-default-dia="15"
                               class="fecha-bloque flex-1 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 text-[11px] focus:ring-1 focus:ring-emerald-400 focus:outline-none">
                    </div>
                    <p class="text-[10px] text-amber-600 dark:text-amber-400 pt-1.5 mt-1.5 border-t border-gray-200 dark:border-gray-600">
                        ⓘ Cada ciclo dura 3 años 4 meses (10 cuatrimestres). Cambia el año para autocompletar.
                    </p>
                </div>

                {{-- Fechas editables Semestral --}}
                <div id="preview-semestre" class="hidden bg-gray-50 dark:bg-gray-700/40 rounded-lg p-3 space-y-2 text-[11px]">
                    <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Fechas de inicio de cada bloque</p>
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-semibold text-gray-700 dark:text-gray-200 w-16">Bloque 1</span>
                        <input type="date" name="fechas[1]" data-bloque="1" data-default-mes="01" data-default-dia="15"
                               class="fecha-bloque flex-1 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 text-[11px] focus:ring-1 focus:ring-emerald-400 focus:outline-none">
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-semibold text-gray-700 dark:text-gray-200 w-16">Bloque 2</span>
                        <input type="date" name="fechas[2]" data-bloque="2" data-default-mes="08" data-default-dia="15"
                               class="fecha-bloque flex-1 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 text-[11px] focus:ring-1 focus:ring-emerald-400 focus:outline-none">
                    </div>
                    <p class="text-[10px] text-amber-600 dark:text-amber-400 pt-1.5 mt-1.5 border-t border-gray-200 dark:border-gray-600">
                        ⓘ Cada ciclo dura 3 años exactos (6 semestres). Cambia el año para autocompletar.
                    </p>
                </div>
            </div>

            {{-- Footer (fijo abajo, dentro del form) --}}
            <div class="flex-shrink-0 flex justify-end gap-2 px-6 py-3 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700">
                <button type="button"
                        onclick="document.getElementById('modal-crear-anio').classList.add('hidden')"
                        class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded-lg transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Crear ciclos
                </button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const input = document.getElementById('input-anio');
            const radios = document.querySelectorAll('input[name="tipo"]');
            const previewCuatri = document.getElementById('preview-cuatri');
            const previewSemestre = document.getElementById('preview-semestre');
            if (!input) return;

            // Track de inputs editados manualmente para no sobrescribirlos al cambiar año
            const editadosManual = new Set();

            function fechasDelTipoActivo() {
                const tipo = document.querySelector('input[name="tipo"]:checked')?.value;
                const container = tipo === 'semestre' ? previewSemestre : previewCuatri;
                return Array.from(container.querySelectorAll('.fecha-bloque'));
            }

            function rellenarFechas(forzar = false) {
                const y = parseInt(input.value, 10);
                if (!y || y < 2020 || y > 2100) return;
                fechasDelTipoActivo().forEach(el => {
                    const key = (el.name + '|' + el.dataset.bloque);
                    if (!forzar && editadosManual.has(key)) return;
                    const mes = el.dataset.defaultMes;
                    const dia = el.dataset.defaultDia;
                    el.value = `${y}-${mes}-${dia}`;
                });
            }

            function actualizarTipo() {
                const tipo = document.querySelector('input[name="tipo"]:checked')?.value;
                if (tipo === 'semestre') {
                    previewCuatri.classList.add('hidden');
                    previewSemestre.classList.remove('hidden');
                } else {
                    previewSemestre.classList.add('hidden');
                    previewCuatri.classList.remove('hidden');
                }
                rellenarFechas();
            }

            // Marcar como manual cuando el usuario cambia una fecha
            document.querySelectorAll('.fecha-bloque').forEach(el => {
                el.addEventListener('input', () => {
                    editadosManual.add(el.name + '|' + el.dataset.bloque);
                });
            });

            input.addEventListener('input', () => rellenarFechas());
            radios.forEach(r => r.addEventListener('change', actualizarTipo));

            // Inicialización
            actualizarTipo();
            rellenarFechas(true);
        })();
    </script>
    @endpush

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0"
        style="max-height: calc(100vh - 220px);">
        <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm bg-white dark:bg-gray-800">
                <thead
                    class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-center">Fecha inicio</th>
                        <th class="px-4 py-3 text-center">Fecha fin</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($ciclos as $ciclo)
                        @php $activo = now()->between($ciclo->fecha_inicio, $ciclo->fecha_fin); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-bold text-gray-900 dark:text-gray-100">{{ $ciclo->nombre }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                {{ $ciclo->fecha_inicio->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                {{ $ciclo->fecha_fin->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($activo)
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Activo</span>
                                @else
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $esFuturo = $ciclo->fecha_inicio->isFuture();
                                    $estadoEdicion = $esFuturo
                                        ? null
                                        : ($ciclo->fecha_fin->isPast() ? 'Finalizado: no editable' : 'En curso: no editable');
                                @endphp
                                <div class="flex justify-center gap-2">
                                    @if($esFuturo)
                                        <a href="{{ route('gestor.ciclos.edit', $ciclo) }}"
                                            class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 font-medium">Editar</a>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600 font-medium cursor-not-allowed"
                                              title="{{ $estadoEdicion }}">Editar</span>
                                    @endif
                                    <form method="POST" action="{{ route('gestor.ciclos.destroy', $ciclo) }}"
                                        class="inline"
                                        data-udea-confirm
                                        data-confirm-title="Eliminar ciclo escolar"
                                        data-confirm-message="¿Eliminar el ciclo <strong>&quot;{{ $ciclo->nombre ?? $ciclo->clave ?? 'seleccionado' }}&quot;</strong>?"
                                        data-confirm-detail="Esta acción no se puede deshacer."
                                        data-confirm-variant="danger"
                                        data-confirm-icon="trash"
                                        data-confirm-button="Eliminar"
                                        data-confirm-cancel="Cancelar">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 font-medium">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-400 dark:text-gray-400">No hay ciclos
                                escolares.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-panel>