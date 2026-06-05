<x-panel title="Promoción Masiva" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="space-y-6">

        {{-- Encabezado --}}
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Promoción masiva por grupo</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Selecciona un grupo origen y promueve a todos sus alumnos al siguiente periodo en un solo paso.
                </p>
            </div>
            <a href="{{ route('gestor.inscripciones') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver a Inscripciones
            </a>
        </div>

        {{-- PASO 1: Selección de grupo origen --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-1">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#0606F0] text-white text-xs font-bold mr-2">1</span>
                Selecciona el grupo origen
            </h3>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-4 ml-8">El sistema buscará automáticamente el grupo destino correspondiente.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1.5">Filtrar por carrera</label>
                    <select id="filtro-carrera"
                            class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                        <option value="">Todas</option>
                        @foreach($carreras as $c)
                            <option value="{{ $c->id_carrera }}">{{ $c->clave_carrera }} — {{ $c->nombre_carrera }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1.5">Filtrar por ciclo</label>
                    <select id="filtro-ciclo"
                            class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                        <option value="">Todos</option>
                        @foreach($ciclos as $ci)
                            <option value="{{ $ci->id_ciclo }}">{{ $ci->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1.5">Grupo origen *</label>
                    <select id="select-grupo-origen"
                            class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                        <option value="">— Selecciona un grupo —</option>
                        @foreach($grupos as $g)
                            <option value="{{ $g->id_grupo }}"
                                    data-carrera="{{ $g->id_carrera }}"
                                    data-ciclo="{{ $g->id_ciclo }}">
                                {{ $g->clave_grupo }} · {{ $g->cuatrimestre }}° · {{ $g->carrera?->clave_carrera }} · {{ $g->cicloEscolar?->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- PASO 2: Preview de promoción (oculto hasta que se seleccione grupo) --}}
        <div id="preview-container" class="hidden">

            {{-- Resumen origen → destino --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#0606F0] text-white text-xs font-bold mr-2">2</span>
                    Revisa los grupos
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Origen --}}
                    <div class="bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Grupo origen</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-gray-100" id="origen-clave">—</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" id="origen-carrera">—</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span id="origen-cuatri">—</span> · <span id="origen-ciclo">—</span></p>
                    </div>

                    {{-- Destino --}}
                    <div id="destino-card" class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                        <p class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                            Grupo destino sugerido
                        </p>
                        <p class="text-lg font-bold text-blue-900 dark:text-blue-200" id="destino-clave">—</p>
                        <p class="text-xs text-blue-700 dark:text-blue-300" id="destino-cuatri">—</p>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mt-1" id="destino-ciclo">—</p>
                    </div>
                </div>

                {{-- Bloqueador: no hay grupo destino --}}
                <div id="bloqueador-destino" class="hidden mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-red-800 dark:text-red-300" id="bloqueador-titulo">No hay grupo destino disponible</p>
                            <p class="text-xs text-red-700 dark:text-red-400 mt-1" id="bloqueador-mensaje"></p>
                            <p class="text-xs text-red-700 dark:text-red-400 mt-2">
                                💡 Crea primero el grupo destino desde
                                <a href="{{ route('gestor.grupos.index') ?? '#' }}" class="underline font-medium">Operación → Grupos</a>
                                y vuelve a intentarlo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de alumnos con checkboxes --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 mt-6">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#0606F0] text-white text-xs font-bold mr-2">3</span>
                            Selecciona alumnos a promover
                        </h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 ml-8">
                            <span id="resumen-promovibles" class="font-semibold text-emerald-600 dark:text-emerald-400">0</span> de
                            <span id="resumen-total" class="font-semibold">0</span> alumno(s) elegibles.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btn-marcar-todos"
                                class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition-colors">
                            Marcar todos
                        </button>
                        <button type="button" id="btn-desmarcar-todos"
                                class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition-colors">
                            Desmarcar todos
                        </button>
                    </div>
                </div>

                <form method="POST" action="{{ route('gestor.inscripciones.promover') }}" id="form-promover">
                    @csrf
                    <input type="hidden" name="id_grupo_origen" id="hidden-origen">
                    <input type="hidden" name="id_grupo_destino" id="hidden-destino">

                    <div class="overflow-y-auto custom-scrollbar" style="max-height: calc(100vh - 480px); min-height: 300px;">
                        <table class="min-w-full w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 sticky top-0 z-10">
                                <tr>
                                    <th class="w-12 px-4 py-3 text-center">
                                        <input type="checkbox" id="check-todos"
                                               class="rounded border-gray-300 dark:border-gray-600 text-[#0606F0] focus:ring-[#0606F0]/30" />
                                    </th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alumno</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Periodo actual</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-alumnos" class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between flex-wrap gap-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <span id="contador-seleccionados" class="font-semibold text-[#0606F0]">0</span> alumno(s) seleccionado(s) para promover.
                        </p>
                        <button type="submit" id="btn-promover" disabled
                                class="px-6 py-2.5 bg-[#0606F0] hover:bg-[#04276B] text-white text-sm font-semibold rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Promover seleccionados
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Estado inicial --}}
        <div id="estado-inicial" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-gray-900/20 border border-gray-100 dark:border-gray-700 p-10 text-center">
            <svg class="w-14 h-14 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">Selecciona un grupo origen para comenzar.</p>
        </div>

    </div>

    @push('scripts')
    <script>
    (function () {
        const previewUrl = '{{ route("gestor.inscripciones.promover.preview") }}';
        const filtroCarrera = document.getElementById('filtro-carrera');
        const filtroCiclo   = document.getElementById('filtro-ciclo');
        const selectOrigen  = document.getElementById('select-grupo-origen');
        const previewBox    = document.getElementById('preview-container');
        const estadoInicial = document.getElementById('estado-inicial');
        const tbody         = document.getElementById('tbody-alumnos');
        const checkTodos    = document.getElementById('check-todos');
        const btnMarcar     = document.getElementById('btn-marcar-todos');
        const btnDesmarcar  = document.getElementById('btn-desmarcar-todos');
        const btnPromover   = document.getElementById('btn-promover');
        const formPromover  = document.getElementById('form-promover');
        const hiddenOrigen  = document.getElementById('hidden-origen');
        const hiddenDestino = document.getElementById('hidden-destino');

        // Filtrado client-side del dropdown de grupos
        function filtrarGrupos() {
            const carrera = filtroCarrera.value;
            const ciclo   = filtroCiclo.value;
            Array.from(selectOrigen.options).forEach(opt => {
                if (!opt.value) return;
                const okCarrera = !carrera || opt.dataset.carrera === carrera;
                const okCiclo   = !ciclo || opt.dataset.ciclo === ciclo;
                opt.hidden = !(okCarrera && okCiclo);
            });
            selectOrigen.value = '';
            previewBox.classList.add('hidden');
            estadoInicial.classList.remove('hidden');
        }
        filtroCarrera.addEventListener('change', filtrarGrupos);
        filtroCiclo.addEventListener('change', filtrarGrupos);

        // Cargar preview al seleccionar grupo origen
        selectOrigen.addEventListener('change', async function () {
            const idGrupo = this.value;
            if (!idGrupo) {
                previewBox.classList.add('hidden');
                estadoInicial.classList.remove('hidden');
                return;
            }

            estadoInicial.classList.add('hidden');
            previewBox.classList.remove('hidden');
            tbody.innerHTML = '<tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">Cargando alumnos...</td></tr>';

            try {
                const r = await fetch(`${previewUrl}?id_grupo=${idGrupo}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await r.json();
                renderPreview(data);
            } catch (e) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-5 py-10 text-center text-red-500 text-sm">Error al cargar los alumnos.</td></tr>';
            }
        });

        function renderPreview(data) {
            // ─── Origen
            document.getElementById('origen-clave').textContent = data.origen.clave;
            document.getElementById('origen-carrera').textContent = data.origen.carrera || '—';
            document.getElementById('origen-cuatri').textContent = data.origen.cuatrimestre + '° ' + (data.label_periodo || '');
            document.getElementById('origen-ciclo').textContent = data.origen.ciclo || '—';
            hiddenOrigen.value = data.origen.id_grupo;

            // ─── Destino
            const bloqueador = document.getElementById('bloqueador-destino');
            const bloqueadorTitulo = document.getElementById('bloqueador-titulo');
            const bloqueadorMensaje = document.getElementById('bloqueador-mensaje');

            if (data.destino) {
                document.getElementById('destino-clave').textContent = data.destino.clave;
                document.getElementById('destino-cuatri').textContent = data.destino.cuatrimestre + '° ' + (data.label_periodo || '');
                document.getElementById('destino-ciclo').textContent = data.destino.ciclo || '—';
                hiddenDestino.value = data.destino.id_grupo;
                bloqueador.classList.add('hidden');
            } else {
                document.getElementById('destino-clave').textContent = '— No existe —';
                document.getElementById('destino-cuatri').textContent = data.cuatri_destino + '° ' + (data.label_periodo || '');
                document.getElementById('destino-ciclo').textContent = data.ciclo_destino_nombre || 'Sin ciclo siguiente';
                hiddenDestino.value = '';
                bloqueador.classList.remove('hidden');

                if (!data.ciclo_destino_existe) {
                    bloqueadorTitulo.textContent = 'No hay un ciclo escolar siguiente registrado';
                    bloqueadorMensaje.textContent = 'Primero debes crear el ciclo escolar al que se promoverán los alumnos.';
                } else {
                    bloqueadorTitulo.textContent = 'No existe el grupo destino';
                    bloqueadorMensaje.textContent = `Falta crear un grupo del ${data.cuatri_destino}° ${data.label_periodo} para esta carrera en el ciclo ${data.ciclo_destino_nombre}.`;
                }
            }

            // ─── Resumen
            document.getElementById('resumen-total').textContent = data.total;
            document.getElementById('resumen-promovibles').textContent = data.promovibles;

            // ─── Tabla de alumnos
            if (data.alumnos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">No hay alumnos inscritos en este grupo.</td></tr>';
                btnPromover.disabled = true;
                return;
            }

            tbody.innerHTML = data.alumnos.map(a => {
                const badge = badgeEstado(a.estado, a.mensaje);
                const checkboxAttrs = a.bloqueado
                    ? 'disabled'
                    : 'checked';
                const rowClass = a.bloqueado ? 'opacity-60' : 'hover:bg-gray-50/50 dark:hover:bg-gray-700/30';
                return `
                    <tr class="${rowClass} transition-colors">
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" name="alumnos[]" value="${a.id_alumno}"
                                   class="check-alumno rounded border-gray-300 dark:border-gray-600 text-[#0606F0] focus:ring-[#0606F0]/30"
                                   ${checkboxAttrs} ${a.bloqueado ? 'data-bloqueado="1"' : ''} />
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">${escapeHtml(a.nombre)}</td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 font-mono">${escapeHtml(a.id_publico ?? '')}</td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400 text-sm">${a.cuatrimestre_actual}°</td>
                        <td class="px-4 py-3 text-center">${badge}</td>
                    </tr>
                `;
            }).join('');

            actualizarContador();
            // Si no hay grupo destino, deshabilitar todo el form
            if (!data.destino) {
                btnPromover.disabled = true;
                document.querySelectorAll('.check-alumno').forEach(cb => cb.disabled = true);
            }
        }

        function badgeEstado(estado, mensaje) {
            const map = {
                'normal':      { color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', label: 'Promovible' },
                'egresable':   { color: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', label: '🎓 Egresable' },
                'baja':        { color: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', label: '🚫 Baja' },
                'ya_inscrito': { color: 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300', label: 'Ya inscrito' },
            };
            const s = map[estado] || map.normal;
            const titulo = mensaje ? ` title="${escapeHtml(mensaje)}"` : '';
            return `<span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-semibold ${s.color}"${titulo}>${s.label}</span>`;
        }

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        function actualizarContador() {
            const seleccionados = document.querySelectorAll('.check-alumno:checked:not([data-bloqueado])').length;
            document.getElementById('contador-seleccionados').textContent = seleccionados;
            btnPromover.disabled = (seleccionados === 0) || !hiddenDestino.value;
        }

        tbody.addEventListener('change', (e) => {
            if (e.target.classList.contains('check-alumno')) actualizarContador();
        });

        checkTodos.addEventListener('change', function () {
            document.querySelectorAll('.check-alumno:not([data-bloqueado])').forEach(cb => cb.checked = this.checked);
            actualizarContador();
        });

        btnMarcar.addEventListener('click', () => {
            document.querySelectorAll('.check-alumno:not([data-bloqueado])').forEach(cb => cb.checked = true);
            actualizarContador();
        });
        btnDesmarcar.addEventListener('click', () => {
            document.querySelectorAll('.check-alumno').forEach(cb => cb.checked = false);
            actualizarContador();
        });

        formPromover.addEventListener('submit', (e) => {
            const seleccionados = document.querySelectorAll('.check-alumno:checked:not([data-bloqueado])').length;
            if (seleccionados === 0) {
                e.preventDefault();
                alert('Selecciona al menos un alumno para promover.');
                return;
            }
            // Si ya pasamos por el modal y confirmamos, dejamos pasar el submit
            if (formPromover.dataset.udeaConfirmed === '1') {
                formPromover.dataset.udeaConfirmed = '';
                return;
            }
            e.preventDefault();
            udeaConfirm({
                title: 'Promover alumnos',
                message: `¿Promover <strong>${seleccionados}</strong> alumno(s) al grupo destino?`,
                detail: 'Esta acción creará nuevas inscripciones para los alumnos seleccionados.',
                variant: 'primary',
                icon: 'arrow-right',
                confirmText: 'Promover',
                cancelText: 'Cancelar',
            }).then(ok => {
                if (!ok) return;
                formPromover.dataset.udeaConfirmed = '1';
                if (typeof formPromover.requestSubmit === 'function') {
                    formPromover.requestSubmit();
                } else {
                    formPromover.submit();
                }
            });
        });
    })();
    </script>
    @endpush
</x-panel>
