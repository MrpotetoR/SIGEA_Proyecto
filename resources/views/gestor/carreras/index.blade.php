<x-panel title="Carreras" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div x-data="asignacionCarreras()">

    {{-- ── Contenedor "Sin asignar" (solo para usuarios con permiso de asignar) ── --}}
    @if($puedeAsignar && $sinAsignar->isNotEmpty())
        <div id="sin-asignar"
             class="mb-5 bg-amber-50/60 dark:bg-amber-900/10 border-2 border-dashed border-amber-300 dark:border-amber-700/60 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                    Carreras pendientes de asignación
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold bg-amber-600 text-white">
                        {{ $sinAsignar->count() }}
                    </span>
                </h3>
            </div>

            <ul class="space-y-1.5">
                @foreach($sinAsignar as $sa)
                    <li class="flex items-center justify-between gap-3 bg-white dark:bg-gray-800 border border-amber-200 dark:border-amber-700/40 rounded-lg px-3 py-2">
                        <div class="min-w-0 flex items-center gap-3">
                            <span class="font-mono text-xs font-bold text-blue-700 dark:text-blue-400">{{ $sa->clave_carrera }}</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 truncate">{{ $sa->nombre_carrera }}</span>
                            <span class="hidden sm:inline text-[10px] text-gray-400">
                                {{ \App\Models\Carrera::AREAS_ACADEMICAS[$sa->area_academica] ?? '' }}
                            </span>
                        </div>
                        <button type="button"
                                @click="abrirAsignar({{ $sa->id_carrera }}, '{{ addslashes($sa->nombre_carrera) }}', null, null)"
                                class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg whitespace-nowrap transition-colors">
                            Asignar
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center justify-between mb-6 gap-4">
        {{-- Filtro por tipo de periodo --}}
        <form method="GET" class="flex items-center gap-3">
            <label class="text-xs text-gray-500 dark:text-gray-400">Periodo:</label>
            <select name="tipo_periodo" onchange="this.form.submit()"
                class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">Todos</option>
                <option value="cuatrimestre" @selected(request('tipo_periodo') === 'cuatrimestre')>Cuatrimestre</option>
                <option value="semestre" @selected(request('tipo_periodo') === 'semestre')>Semestre</option>
            </select>
        </form>

        <a href="{{ route('gestor.carreras.create') }}"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Nueva carrera
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 flex flex-col min-h-0"
        style="max-height: calc(100vh - 220px);">
        <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm bg-white dark:bg-gray-800">
                <thead
                    class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left">Clave</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">RVOE</th>
                        <th class="px-4 py-3 text-left">Área</th>
                        <th class="px-4 py-3 text-center">Periodo</th>
                        <th class="px-4 py-3 text-center">Duración</th>
                        <th class="px-4 py-3 text-center">Alumnos</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($carreras as $c)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-mono font-bold text-blue-700 dark:text-blue-400">
                                {{ $c->clave_carrera }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $c->nombre_carrera }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">
                                {{ $c->rvoe ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                {{ \App\Models\Carrera::AREAS_ACADEMICAS[$c->area_academica] ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $c->tipo_periodo === 'cuatrimestre'
                                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
                                        : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' }}">
                                    {{ ucfirst($c->tipo_periodo) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-600 dark:text-gray-400">
                                {{ $c->duracion_periodos }} per. <span class="text-gray-400">({{ $c->duracion_estimada }})</span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $c->alumnos_count ?? 0 }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    @if($puedeAsignar)
                                        @php $gestorActual = $c->personal(); @endphp
                                        <button type="button"
                                                @click="abrirAsignar({{ $c->id_carrera }}, '{{ addslashes($c->nombre_carrera) }}',
                                                    {{ $gestorActual?->id_personal ?: 'null' }},
                                                    '{{ $gestorActual?->nombre_completo ?? '' }}')"
                                                class="text-amber-600 dark:text-amber-400 hover:text-amber-900 font-medium">
                                            Reasignar
                                        </button>
                                    @endif
                                    <a href="{{ route('gestor.carreras.edit', $c) }}"
                                        class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 font-medium">Editar</a>
                                    <form method="POST" action="{{ route('gestor.carreras.destroy', $c) }}" class="inline"
                                        data-udea-confirm
                                        data-confirm-title="Eliminar carrera"
                                        data-confirm-message="¿Eliminar la carrera <strong>&quot;{{ $c->nombre_carrera }}&quot;</strong>?"
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
                            <td colspan="8" class="px-4 py-10 text-center text-gray-400">No hay carreras registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ════════ MODAL DE ASIGNACIÓN (teleport a <body> para escapar el layout) ════════ --}}
    @if($puedeAsignar)
        <template x-teleport="body">
        <div x-show="modalAbierto" x-cloak
             x-transition.opacity
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="cerrarModal()"
             @keydown.escape.window="cerrarModal()">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                 @click.stop>
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">
                        <span x-show="!gestorActualId">Asignar carrera</span>
                        <span x-show="gestorActualId" x-cloak>Reasignar carrera</span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        <span x-text="carreraNombre"></span>
                        <template x-if="gestorActualNombre">
                            <span> — actualmente con <strong x-text="gestorActualNombre"></strong></span>
                        </template>
                    </p>
                </div>

                <form :action="formAction" method="POST" class="px-6 py-5 space-y-4"
                      @submit.prevent="enviar($event)">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Asignar a *
                        </label>
                        <select x-model="destinoId" name="gestor_destino_id"
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            <option value="">— Dejar sin asignar —</option>
                            @foreach($candidatos as $g)
                                @php
                                    $esYo = $miGestorId && $g->id_personal === $miGestorId;
                                    $label = $esYo
                                        ? "Asignarme a mí mismo ({$g->carreras_count}/" . \App\Models\GestorEscolar::MAX_CARRERAS . ")"
                                        : "{$g->apellidos} {$g->nombre} ({$g->carreras_count}/" . \App\Models\GestorEscolar::MAX_CARRERAS . ")";
                                @endphp
                                <option value="{{ $g->id_personal }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">
                            Solo gestores con menos de {{ \App\Models\GestorEscolar::MAX_CARRERAS }} carreras.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Motivo de la asignación *
                        </label>
                        <select x-model="motivo" name="motivo" required
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            <option value="">Seleccionar motivo...</option>
                            @foreach(\App\Models\AsignacionCarreraLog::MOTIVOS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="motivo === 'otro'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Especifica el motivo *
                            <span class="text-gray-400 font-normal text-xs">(máx 32 caracteres)</span>
                        </label>
                        <input type="text" x-model="motivoPersonalizado" name="motivo_personalizado"
                               maxlength="32"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <p class="text-[10px] text-gray-400 mt-1"><span x-text="motivoPersonalizado.length"></span>/32</p>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg px-3 py-2 text-[11px] text-blue-700 dark:text-blue-300">
                        ⓘ Esta acción se registrará en el historial de auditoría con tu nombre, fecha, motivo e IP.
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t dark:border-gray-700 -mx-6 px-6">
                        <button type="button" @click="cerrarModal()"
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="enviando || !puedeEnviar"
                                class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <span x-show="!enviando">Confirmar</span>
                            <span x-show="enviando" x-cloak>Procesando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </template>
    @endif

    </div>
</x-panel>

<script>
    (function () {
        const form = document.querySelector('form[method="GET"]');
        if (!form) return;
        const selects = form.querySelectorAll('select');
        selects.forEach(s => s.addEventListener('change', () => form.submit()));
    })();

    function asignacionCarreras() {
        return {
            modalAbierto: false,
            carreraId: null,
            carreraNombre: '',
            gestorActualId: null,
            gestorActualNombre: '',
            destinoId: '',
            motivo: '',
            motivoPersonalizado: '',
            enviando: false,

            get puedeEnviar() {
                if (!this.motivo) return false;
                if (this.motivo === 'otro' && !this.motivoPersonalizado.trim()) return false;
                return true;
            },

            get formAction() {
                if (!this.carreraId) return '';
                return '{{ url('gestor-escolar/carreras') }}/' + this.carreraId + '/asignar';
            },

            abrirAsignar(carreraId, nombre, gestorActualId, gestorActualNombre) {
                this.carreraId          = carreraId;
                this.carreraNombre      = nombre;
                this.gestorActualId     = gestorActualId;
                this.gestorActualNombre = gestorActualNombre || '';
                this.destinoId          = '';
                this.motivo             = '';
                this.motivoPersonalizado = '';
                this.modalAbierto       = true;
                this.enviando           = false;
            },

            cerrarModal() {
                if (this.enviando) return;
                this.modalAbierto = false;
            },

            async enviar(event) {
                if (this.enviando || !this.puedeEnviar) return;
                this.enviando = true;
                const form = event.target;

                try {
                    const tieneGrace = await this.verificarGracePeriod();
                    if (tieneGrace) {
                        form.submit();
                        return;
                    }

                    const self = this;
                    window.dispatchEvent(new CustomEvent('reauth:open', {
                        detail: {
                            action: 'asignar_carrera',
                            title:  'Confirmar asignación de carrera',
                            description: 'Asignar carreras a otros gestores es una acción sensible. Confirma con tu contraseña.',
                            onSuccess: () => { form.submit(); },
                            onCancel:  () => { self.enviando = false; },
                        },
                    }));
                } catch (e) {
                    this.enviando = false;
                }
            },

            async verificarGracePeriod() {
                try {
                    const url = '{{ route('admin.reauth.estado') }}?action=asignar_carrera';
                    const res = await fetch(url, {
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (!res.ok) return false;
                    const data = await res.json();
                    return !!data.tiene_grace_period;
                } catch (e) {
                    return false;
                }
            },
        };
    }
</script>
