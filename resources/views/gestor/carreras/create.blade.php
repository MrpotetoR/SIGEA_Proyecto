<x-panel title="Nueva Carrera" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>
    <div class="max-w-2xl">
        <a href="{{ route('gestor.carreras.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver</a>

        @if(session('error'))
            <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <form method="POST" action="{{ route('gestor.carreras.store') }}"
                  class="space-y-5"
                  x-data="formNuevaCarrera()"
                  @submit.prevent="enviar($event)">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre de la carrera *</label>
                        <input type="text" name="nombre_carrera" value="{{ old('nombre_carrera') }}" required maxlength="120"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('nombre_carrera') border-red-400 @enderror">
                        @error('nombre_carrera')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Clave *</label>
                        <input type="text" name="clave_carrera" value="{{ old('clave_carrera') }}" required maxlength="20"
                               placeholder="Ej: DSM-2026, GE-2026"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('clave_carrera') border-red-400 @enderror">
                        @error('clave_carrera')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            RVOE
                            <span class="text-gray-400 font-normal text-xs">(opcional)</span>
                        </label>
                        <input type="text" name="rvoe" value="{{ old('rvoe') }}" maxlength="50"
                               pattern="[A-Za-z0-9\-/]+"
                               placeholder="Ej: ESLI-2024/05-PE-09"
                               oninput="this.value = this.value.toUpperCase()"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm uppercase tracking-wider focus:ring-2 focus:ring-blue-400 focus:outline-none @error('rvoe') border-red-400 @enderror">
                        <p class="text-[10px] text-gray-400 mt-1">Clave oficial de autorización ante la SEP. Independiente de la clave interna.</p>
                        @error('rvoe')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Área académica *</label>
                        <select name="area_academica" required
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('area_academica') border-red-400 @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach(\App\Models\Carrera::AREAS_ACADEMICAS as $key => $label)
                                <option value="{{ $key }}" @selected(old('area_academica') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('area_academica')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de periodo *</label>
                        <select name="tipo_periodo" id="tipo_periodo" required
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('tipo_periodo') border-red-400 @enderror">
                            <option value="cuatrimestre" @selected(old('tipo_periodo', 'cuatrimestre') === 'cuatrimestre')>Cuatrimestre (10 periodos)</option>
                            <option value="semestre" @selected(old('tipo_periodo') === 'semestre')>Semestre (7 periodos)</option>
                        </select>
                        <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-1">⚠ No se podrá modificar después de crear la carrera.</p>
                        @error('tipo_periodo')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periodos totales</label>
                        <div id="periodos-totales"
                             class="w-full border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2 text-sm text-gray-600 dark:text-gray-300">—</div>
                        <p class="text-[10px] text-gray-400 mt-1">Asignado automáticamente según el tipo.</p>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duración estimada</label>
                        <div id="duracion-estimada"
                             class="w-full border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2 text-sm text-gray-600 dark:text-gray-300">—</div>
                    </div>

                    {{-- ── Asignación: solo visible para gestores con permiso especial (o admin) ── --}}
                    @if($puedeAsignar)
                        <div class="col-span-2 border-t dark:border-gray-700 pt-4 mt-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Asignar a *
                            </label>
                            <select name="gestor_asignado_id" required
                                    class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('gestor_asignado_id') border-red-400 @enderror">
                                <option value="">— Sin asignar (notificar al admin) —</option>
                                @foreach($candidatos as $g)
                                    @php
                                        $esYo  = $miGestorId && $g->id_personal === $miGestorId;
                                        $label = $esYo
                                            ? "Asignarme a mí mismo ({$g->carreras_count}/" . \App\Models\GestorEscolar::MAX_CARRERAS . ")"
                                            : "{$g->apellidos} {$g->nombre} ({$g->carreras_count}/" . \App\Models\GestorEscolar::MAX_CARRERAS . ")";
                                    @endphp
                                    <option value="{{ $g->id_personal }}"
                                            @selected(old('gestor_asignado_id') == $g->id_personal)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1">
                                Solo se muestran gestores con espacio disponible (menos de {{ \App\Models\GestorEscolar::MAX_CARRERAS }} carreras).
                                @if($candidatos->isEmpty())
                                    <span class="text-amber-600 dark:text-amber-400">No hay gestores con espacio; la carrera quedará sin asignar.</span>
                                @endif
                            </p>
                            @error('gestor_asignado_id')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    @else
                        <div class="col-span-2 border-t dark:border-gray-700 pt-4 mt-2">
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg px-3 py-2 text-xs text-blue-700 dark:text-blue-300">
                                <p class="font-semibold mb-0.5">ⓘ Esta carrera quedará pendiente de asignación.</p>
                                <p>Se notificará a los gestores con permisos de asignación para que la administren.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit" :disabled="enviando"
                            class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold disabled:opacity-60 transition-colors">
                        <span x-show="!enviando">Crear carrera</span>
                        <span x-show="enviando" x-cloak>Procesando...</span>
                    </button>
                    <a href="{{ route('gestor.carreras.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>

<script>
function calcularDuracion() {
    const tipo = document.getElementById('tipo_periodo').value;
    const periodos = tipo === 'cuatrimestre' ? 10 : 7;
    document.getElementById('periodos-totales').textContent = periodos + ' periodos';
    const meses = tipo === 'cuatrimestre' ? periodos * 4 : periodos * 6;
    const anios = Math.floor(meses / 12);
    const resto = meses % 12;
    let txt = '';
    if (anios) txt += anios + (anios > 1 ? ' años' : ' año');
    if (anios && resto) txt += ' y ';
    if (resto) txt += resto + (resto > 1 ? ' meses' : ' mes');
    document.getElementById('duracion-estimada').textContent = txt || '—';
}
document.getElementById('tipo_periodo').addEventListener('change', calcularDuracion);
document.addEventListener('DOMContentLoaded', calcularDuracion);

function formNuevaCarrera() {
    return {
        enviando: false,

        async enviar(event) {
            if (this.enviando) return;
            this.enviando = true;
            const form = event.target;

            try {
                // Verificar si el usuario aún tiene grace period activo.
                const tieneGrace = await this.verificarGracePeriod();
                if (tieneGrace) {
                    form.submit();
                    return;
                }

                // No hay grace period: pedir reauth.
                window.dispatchEvent(new CustomEvent('reauth:open', {
                    detail: {
                        action: 'crear_carrera',
                        title:  'Confirmar creación de carrera',
                        description: 'Crear una carrera es una acción administrativa. Confirma con tu contraseña para continuar.',
                        onSuccess: () => { form.submit(); },
                        onCancel:  () => { this.enviando = false; },
                    },
                }));
            } catch (e) {
                this.enviando = false;
            }
        },

        async verificarGracePeriod() {
            try {
                const url = '{{ route('admin.reauth.estado') }}?action=crear_carrera';
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
