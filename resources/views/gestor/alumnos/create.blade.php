<x-panel title="Nuevo Alumno" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="max-w-6xl">
        <a href="{{ route('gestor.alumnos.index') }}"
           class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver a la lista</a>

        <form method="POST" action="{{ route('gestor.alumnos.store') }}" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 lg:col-span-2">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-6">Datos del alumno</h2>
                <div class="space-y-5">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre(s) *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="80"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                               title="Solo letras y espacios"
                               oninput="updateCount(this, 'cnt-nombre'); this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('nombre') border-red-400 @enderror">
                        <div class="flex justify-between mt-1">
                            @error('nombre')
                                <p class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</p>
                            @else
                                <span></span>
                            @enderror
                            <span id="cnt-nombre" class="text-xs text-gray-400">0/80</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos') }}" required maxlength="100"
                               pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                               title="Solo letras y espacios"
                               oninput="updateCount(this, 'cnt-apellidos'); this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('apellidos') border-red-400 @enderror">
                        <div class="flex justify-between mt-1">
                            @error('apellidos')
                                <p class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</p>
                            @else
                                <span></span>
                            @enderror
                            <span id="cnt-apellidos" class="text-xs text-gray-400">0/100</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correo electrónico *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required maxlength="255"
                           class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">Se usará como usuario de acceso. Contraseña inicial: <code class="dark:text-gray-300">sigea{{ date('Y') }}</code></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @if($esBachi ?? false)
                        {{-- ─── Bachillerato: campo Plan ─── --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plan de Bachillerato *</label>
                            <select name="id_plan_bachillerato" id="sel-plan" required
                                    class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none @error('id_plan_bachillerato') border-red-400 @enderror">
                                <option value="" data-max="6" data-label="Semestre">Seleccionar...</option>
                                @foreach($planesBachi as $p)
                                    <option value="{{ $p->id_plan_bachillerato }}"
                                            data-max="{{ $p->num_semestres }}"
                                            data-label="{{ $p->label_periodo }}"
                                            data-duracion="{{ $p->duracion_texto }}"
                                            @selected(old('id_plan_bachillerato') == $p->id_plan_bachillerato)>
                                        {{ $p->nombre_plan }} ({{ $p->duracion_texto }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1" id="hint-plan-bachi">&nbsp;</p>
                            @error('id_plan_bachillerato')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 dark:text-gray-500 mb-1" id="lbl-periodo">Semestre actual *</label>
                            <select name="cuatrimestre_actual" id="sel-periodo" required disabled
                                    class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none disabled:bg-gray-100 disabled:dark:bg-gray-800 disabled:cursor-not-allowed">
                                <option value="">— Selecciona primero el plan —</option>
                            </select>
                        </div>
                    @else
                        {{-- ─── Universidad: campo Carrera ─── --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Carrera *</label>
                            <select name="id_carrera" id="sel-carrera" required
                                    class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('id_carrera') border-red-400 @enderror">
                                <option value="" data-max="10" data-label="Cuatrimestre">Seleccionar...</option>
                                @foreach($carreras as $c)
                                    <option value="{{ $c->id_carrera }}"
                                            data-max="{{ $c->max_periodos }}"
                                            data-label="{{ $c->label_periodo }}"
                                            @selected(old('id_carrera') == $c->id_carrera)>
                                        {{ $c->nombre_carrera }} ({{ $c->label_periodo }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_carrera')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" id="lbl-periodo">Cuatrimestre actual *</label>
                            <select name="cuatrimestre_actual" id="sel-periodo" required
                                    class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" @selected(old('cuatrimestre_actual', 1) == $i)>{{ $i }}°</option>
                                @endfor
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1" id="hint-periodo">Selecciona primero la carrera.</p>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tutor</label>
                    <select name="id_tutor"
                            class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="">— Sin tutor asignado —</option>
                        @foreach($tutores as $t)
                            <option value="{{ $t->id_docente }}" @selected(old('id_tutor') == $t->id_docente)>
                                {{ $t->nombre }} {{ $t->apellidos }}
                            </option>
                        @endforeach
                    </select>
                    @if($tutores->isEmpty())
                        <p class="text-xs text-gray-400 mt-1">No hay docentes marcados como tutores.</p>
                    @endif
                </div>
                </div>
            </div>

            {{-- Datos del padre / tutor --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-1">Datos del padre o tutor</h2>
                <p class="text-xs text-gray-400 mb-5">Información de contacto del responsable del alumno.</p>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre(s)</label>
                            <input type="text" name="padre[nombre]" value="{{ old('padre.nombre') }}" maxlength="80"
                                   class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Apellidos</label>
                            <input type="text" name="padre[apellidos]" value="{{ old('padre.apellidos') }}" maxlength="100"
                                   class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Correo electrónico</label>
                        <input type="email" name="padre[email]" value="{{ old('padre.email') }}" maxlength="150"
                               class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Teléfono</label>
                            <input type="tel" name="padre[telefono]" value="{{ old('padre.telefono') }}" maxlength="20"
                                   class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">N° emergencia</label>
                            <input type="tel" name="padre[telefono_emergencia]" value="{{ old('padre.telefono_emergencia') }}" maxlength="20"
                                   class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">INE (PDF)</label>
                        <input type="file" name="padre[ine]" accept="application/pdf"
                               class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-gray-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                    </div>
                </div>
            </div>

            {{-- Pagos por periodo --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-1" id="lbl-pagos">Pagos por periodo</h2>
                <p class="text-xs text-gray-400 mb-2">
                    Los váuchers deben cargarse en orden consecutivo. El siguiente solo se habilita al subir el anterior.
                </p>
                <p id="pagos-warning" class="hidden text-xs text-red-600 dark:text-red-400 font-semibold mb-3 flex items-start gap-1.5">
                    <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z"/>
                    </svg>
                    <span id="pagos-warning-text"></span>
                </p>
                <div class="space-y-2 max-h-80 overflow-y-auto pr-2" id="pagos-list">
                    @for($i = 1; $i <= 10; $i++)
                        <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 transition-colors relative group {{ $i > 1 ? 'opacity-60' : '' }}"
                             data-cuatri="{{ $i }}"
                             @if($i > 1) title="Debes cargar el váucher del cuatrimestre anterior" @endif>
                            <span class="w-12 text-sm font-semibold text-gray-700 dark:text-gray-300 flex-shrink-0">{{ $i }}°</span>
                            <div class="flex-1">
                                <input type="file" name="pagos[{{ $i }}]" accept="application/pdf" data-pago-input="{{ $i }}"
                                       {{ $i > 1 ? 'disabled' : '' }}
                                       class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-gray-700 dark:file:text-blue-300 hover:file:bg-blue-100 disabled:opacity-40 disabled:cursor-not-allowed">
                                <p class="hidden text-[10px] text-red-600 dark:text-red-400 font-semibold mt-1" data-required-label="{{ $i }}">
                                    Obligatorio
                                </p>
                            </div>
                            <span class="text-gray-400 lock-icon flex-shrink-0" data-lock="{{ $i }}">
                                @if($i > 1)<x-icon name="lock" class="w-3.5 h-3.5" />@endif
                            </span>
                            @if($i > 1)
                                <div class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap bg-gray-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity z-10 shadow-lg inline-flex items-center gap-1 tooltip-lock">
                                    <x-icon name="lock" class="w-3 h-3" /> Debes cargar el váucher del cuatrimestre anterior
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Documentación del alumno --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 lg:col-span-2">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-1">Documentación del alumno</h2>
                <p class="text-xs text-gray-400 mb-4">Sube cada documento en formato PDF. Podrás reemplazarlos posteriormente.</p>
                <div class="space-y-3">
                    @foreach(\App\Models\DocumentoAlumno::TIPOS as $tipo => $label)
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:border-gray-200 dark:hover:border-gray-600 transition-colors">
                            <span class="text-sm text-gray-700 dark:text-gray-300 min-w-fit">{{ $label }}</span>
                            <input type="file" name="documentos[{{ $tipo }}]" accept="application/pdf"
                                   class="flex-1 text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-gray-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                        </div>
                    @endforeach
                </div>
            </div>

            </div>

            <div class="flex gap-3 pt-5 mt-5 border-t dark:border-gray-700">
                <button type="submit"
                        class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Registrar alumno
                </button>
                <a href="{{ route('gestor.alumnos.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-panel>

<script>
function updateCount(input, counterId) {
    const counter = document.getElementById(counterId);
    const max = input.maxLength;
    const len = input.value.length;
    counter.textContent = len + '/' + max;
    counter.classList.toggle('text-red-500', len >= max);
    counter.classList.toggle('text-gray-400', len < max);
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[maxlength][oninput]').forEach(el => el.dispatchEvent(new Event('input')));

    // Carga secuencial de váuchers
    const pagoInputs = Array.from(document.querySelectorAll('[data-pago-input]'))
        .sort((a, b) => +a.dataset.pagoInput - +b.dataset.pagoInput);

    const SVG_LOCK = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>';
    const SVG_CHECK = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-3.5 h-3.5 text-green-600" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>';

    function refreshPagos() {
        // periodoTarget = semestre/cuatrimestre AL QUE ENTRA el alumno (1..N).
        // Sólo las filas de 1 a periodoTarget son visibles y obligatorias.
        // Dentro de ese rango, el desbloqueo es consecutivo (uno tras otro).
        const periodoTarget = parseInt(document.getElementById('sel-periodo')?.value) || 0;

        let prevHasFile = true;
        pagoInputs.forEach((input) => {
            const row = input.closest('[data-cuatri]');
            const n = parseInt(row.dataset.cuatri);
            const dentroDelRango = periodoTarget > 0 && n <= periodoTarget;
            const lock = document.querySelector(`[data-lock="${input.dataset.pagoInput}"]`);
            const reqLabel = row.querySelector(`[data-required-label="${n}"]`);

            // Filas fuera del rango: ocultas y deshabilitadas
            if (!dentroDelRango) {
                row.classList.add('hidden');
                input.disabled = true;
                input.required = false;
                input.value = '';
                if (reqLabel) reqLabel.classList.add('hidden');
                return;
            }
            row.classList.remove('hidden');

            // Marca como obligatorio (rojo) — visible siempre dentro del rango
            input.required = true;
            if (reqLabel) reqLabel.classList.remove('hidden');

            // Desbloqueo consecutivo: solo si el anterior ya tiene archivo
            input.disabled = !prevHasFile;
            if (lock) lock.innerHTML = input.disabled ? SVG_LOCK : (input.files.length ? SVG_CHECK : '');

            if (input.disabled) {
                input.value = '';
                row.classList.add('opacity-60');
                row.setAttribute('title', 'Debes cargar el váucher del periodo anterior');
                const tip = row.querySelector('.tooltip-lock');
                if (tip) tip.classList.remove('hidden');
            } else {
                row.classList.remove('opacity-60');
                row.removeAttribute('title');
                const tip = row.querySelector('.tooltip-lock');
                if (tip) tip.classList.add('hidden');
            }
            prevHasFile = prevHasFile && input.files.length > 0;
        });

        // Mensaje de advertencia arriba: solo cuando el alumno entra desde 2° o más
        const warning = document.getElementById('pagos-warning');
        const warningText = document.getElementById('pagos-warning-text');
        if (warning && warningText) {
            if (periodoTarget >= 2) {
                const labelLower = (document.getElementById('lbl-pagos')?.textContent || 'Pagos por periodo')
                    .replace(/Pagos por\s*/i, '').toLowerCase().trim() || 'periodo';
                warningText.textContent =
                    `Como el alumno ingresa en el ${periodoTarget}° ${labelLower}, debes cargar los váuchers ` +
                    `de los ${labelLower}s previos (1° al ${periodoTarget}°) en orden consecutivo.`;
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
    }
    pagoInputs.forEach(i => i.addEventListener('change', refreshPagos));

    // ==== Sincronizar periodos con la carrera/plan seleccionado ====
    // En Universidad existe #sel-carrera; en Bachillerato existe #sel-plan.
    const selOrigen  = document.getElementById('sel-carrera') || document.getElementById('sel-plan');
    const selPeriodo = document.getElementById('sel-periodo');
    const lblPeriodo = document.getElementById('lbl-periodo');
    const hintPeriodo = document.getElementById('hint-periodo');
    const lblPagos = document.getElementById('lbl-pagos');

    function syncPeriodos() {
        if (!selOrigen || !selPeriodo) return;

        const opt = selOrigen.options[selOrigen.selectedIndex];
        const planSeleccionado = !!opt?.value;
        const max = parseInt(opt?.dataset.max || 0);
        const label = opt?.dataset.label || 'Periodo';

        // Habilita/deshabilita el select de período según haya plan elegido
        selPeriodo.disabled = !planSeleccionado;
        selPeriodo.innerHTML = '';

        if (!planSeleccionado) {
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = '— Selecciona primero el plan —';
            selPeriodo.appendChild(placeholder);
            if (lblPeriodo) {
                lblPeriodo.classList.add('text-gray-400', 'dark:text-gray-500');
                lblPeriodo.classList.remove('text-gray-700', 'dark:text-gray-300');
                lblPeriodo.textContent = label + ' actual *';
            }
        } else {
            const current = parseInt(selPeriodo.dataset.last) || 1;
            for (let i = 1; i <= max; i++) {
                const o = document.createElement('option');
                o.value = i; o.textContent = i + '°';
                if (i === Math.min(current, max)) o.selected = true;
                selPeriodo.appendChild(o);
            }
            if (lblPeriodo) {
                lblPeriodo.classList.remove('text-gray-400', 'dark:text-gray-500');
                lblPeriodo.classList.add('text-gray-700', 'dark:text-gray-300');
                lblPeriodo.textContent = label + ' actual *';
            }
        }

        if (hintPeriodo) hintPeriodo.textContent = planSeleccionado
            ? `${label} — ${max} en total`
            : `Selecciona primero ${selOrigen.id === 'sel-plan' ? 'el plan' : 'la carrera'}.`;
        if (lblPagos)    lblPagos.textContent    = 'Pagos por ' + label.toLowerCase();

        // Hint adicional con la duración del plan (solo bachillerato)
        const hintBachi = document.getElementById('hint-plan-bachi');
        if (hintBachi) {
            hintBachi.textContent = opt?.dataset.duracion
                ? `Duración: ${opt.dataset.duracion} (${max} ${label.toLowerCase()}s).`
                : 'Selecciona la modalidad: Escolarizado o No Escolarizado.';
        }

        // Pagos: la logica de visibilidad + obligatoriedad esta en refreshPagos().
        // El periodo seleccionado en sel-periodo es la fuente de verdad.
        if (typeof refreshPagos === 'function') refreshPagos();
    }

    // Cambios en el select de periodo: actualizar pagos y memoria del valor.
    if (selPeriodo) {
        selPeriodo.addEventListener('change', () => {
            if (selPeriodo.value) selPeriodo.dataset.last = selPeriodo.value;
            if (typeof refreshPagos === 'function') refreshPagos();
        });
    }
    if (selOrigen) {
        selOrigen.addEventListener('change', syncPeriodos);
        syncPeriodos();
    }
});
</script>
