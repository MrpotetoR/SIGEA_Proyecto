<x-panel title="Editar Carrera" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>
    <div class="max-w-2xl">
        <a href="{{ route('servicios.carreras.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver</a>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <form method="POST" action="{{ route('servicios.carreras.update', $carrera) }}" class="space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre *</label>
                        <input type="text" name="nombre_carrera" value="{{ old('nombre_carrera', $carrera->nombre_carrera) }}" required maxlength="120"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Clave</label>
                        <input type="text" value="{{ $carrera->clave_carrera }}" disabled
                               class="w-full border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 rounded-lg px-3 py-2 text-sm">
                        <p class="text-xs text-gray-400 mt-1">La clave no puede modificarse.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Área académica *</label>
                        <select name="area_academica" required
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            <option value="">Seleccionar...</option>
                            @foreach(\App\Models\Carrera::AREAS_ACADEMICAS as $key => $label)
                                <option value="{{ $key }}" @selected(old('area_academica', $carrera->area_academica) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de periodo *</label>
                        <select name="tipo_periodo" id="tipo_periodo" required
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            <option value="cuatrimestre" @selected(old('tipo_periodo', $carrera->tipo_periodo) === 'cuatrimestre')>Cuatrimestre</option>
                            <option value="semestre" @selected(old('tipo_periodo', $carrera->tipo_periodo) === 'semestre')>Semestre</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duración (periodos) *</label>
                        <input type="number" name="duracion_periodos" id="duracion_periodos"
                               value="{{ old('duracion_periodos', $carrera->duracion_periodos) }}" required min="1" max="20"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duración estimada</label>
                        <div id="duracion-estimada"
                             class="w-full border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2 text-sm text-gray-600 dark:text-gray-300">
                            {{ $carrera->duracion_estimada }}
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">Calculado automáticamente.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Director de carrera</label>
                    <select name="id_director"
                            class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="">— Sin director asignado —</option>
                        @foreach($docentes as $d)
                            <option value="{{ $d->id_docente }}" @selected(old('id_director', $carrera->id_director) == $d->id_docente)>
                                {{ $d->apellidos }} {{ $d->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 pt-4 border-t dark:border-gray-700">
                    <button type="submit" class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">Guardar</button>
                    <a href="{{ route('servicios.carreras.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-panel>

<script>
function calcularDuracion() {
    const tipo = document.getElementById('tipo_periodo').value;
    const periodos = parseInt(document.getElementById('duracion_periodos').value) || 0;
    const el = document.getElementById('duracion-estimada');
    if (periodos < 1) { el.textContent = '—'; return; }

    const meses = tipo === 'cuatrimestre' ? periodos * 4 : periodos * 6;
    const anios = Math.floor(meses / 12);
    const resto = meses % 12;
    let txt = '';
    if (anios) txt += anios + (anios > 1 ? ' años' : ' año');
    if (anios && resto) txt += ' y ';
    if (resto) txt += resto + (resto > 1 ? ' meses' : ' mes');
    el.textContent = txt || '—';
}
document.getElementById('tipo_periodo').addEventListener('change', calcularDuracion);
document.getElementById('duracion_periodos').addEventListener('input', calcularDuracion);
document.addEventListener('DOMContentLoaded', calcularDuracion);
</script>
