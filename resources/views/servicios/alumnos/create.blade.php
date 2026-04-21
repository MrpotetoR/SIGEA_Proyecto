<x-panel title="Nuevo Alumno" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-6xl">
        <a href="{{ route('servicios.alumnos.index') }}"
           class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-6 inline-block">← Volver a la lista</a>

        <form method="POST" action="{{ route('servicios.alumnos.store') }}" enctype="multipart/form-data">
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
                <p class="text-xs text-gray-400 mb-4">Los váuchers deben cargarse en orden consecutivo. El siguiente solo se habilita al subir el anterior.</p>
                <div class="space-y-2 max-h-80 overflow-y-auto pr-2" id="pagos-list">
                    @for($i = 1; $i <= 10; $i++)
                        <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 transition-colors relative group {{ $i > 1 ? 'opacity-60' : '' }}"
                             data-cuatri="{{ $i }}"
                             @if($i > 1) title="Debes cargar el váucher del cuatrimestre anterior" @endif>
                            <span class="w-12 text-sm font-semibold text-gray-700 dark:text-gray-300 flex-shrink-0">{{ $i }}°</span>
                            <input type="file" name="pagos[{{ $i }}]" accept="application/pdf" data-pago-input="{{ $i }}"
                                   {{ $i > 1 ? 'disabled' : '' }}
                                   class="flex-1 text-xs text-gray-500 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-gray-700 dark:file:text-blue-300 hover:file:bg-blue-100 disabled:opacity-40 disabled:cursor-not-allowed">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach(\App\Models\DocumentoAlumno::TIPOS as $tipo => $label)
                        <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:border-gray-200 dark:hover:border-gray-600 transition-colors">
                            <span class="text-sm text-gray-700 dark:text-gray-300 w-56 flex-shrink-0">{{ $label }}</span>
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
                <a href="{{ route('servicios.alumnos.index') }}"
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
        let prevHasFile = true;
        pagoInputs.forEach((input) => {
            const row = input.closest('[data-cuatri]');
            input.disabled = !prevHasFile;
            const lock = document.querySelector(`[data-lock="${input.dataset.pagoInput}"]`);
            if (lock) lock.innerHTML = input.disabled ? SVG_LOCK : (input.files.length ? SVG_CHECK : '');
            if (input.disabled) {
                input.value = '';
                row.classList.add('opacity-60');
                row.setAttribute('title', 'Debes cargar el váucher del cuatrimestre anterior');
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
    }
    pagoInputs.forEach(i => i.addEventListener('change', refreshPagos));

    // ==== Sincronizar periodos con la carrera seleccionada ====
    const selCarrera = document.getElementById('sel-carrera');
    const selPeriodo = document.getElementById('sel-periodo');
    const lblPeriodo = document.getElementById('lbl-periodo');
    const hintPeriodo = document.getElementById('hint-periodo');
    const lblPagos = document.getElementById('lbl-pagos');

    function syncPeriodos() {
        const opt = selCarrera.options[selCarrera.selectedIndex];
        const max = parseInt(opt?.dataset.max || 10);
        const label = opt?.dataset.label || 'Cuatrimestre';
        const current = parseInt(selPeriodo.value) || 1;

        // Rebuild periodo select
        selPeriodo.innerHTML = '';
        for (let i = 1; i <= max; i++) {
            const o = document.createElement('option');
            o.value = i; o.textContent = i + '°';
            if (i === Math.min(current, max)) o.selected = true;
            selPeriodo.appendChild(o);
        }

        lblPeriodo.textContent = label + ' actual *';
        hintPeriodo.textContent = opt?.value ? `${label} — ${max} en total para esta carrera` : 'Selecciona primero la carrera.';
        lblPagos.textContent = 'Pagos por ' + label.toLowerCase();

        // Ocultar filas de pago que excedan el max
        document.querySelectorAll('#pagos-list [data-cuatri]').forEach(row => {
            const n = parseInt(row.dataset.cuatri);
            row.classList.toggle('hidden', n > max);
            if (n > max) {
                const inp = row.querySelector('input[type=file]');
                if (inp) { inp.value = ''; inp.disabled = true; }
            }
        });
        refreshPagos();
    }
    if (selCarrera) {
        selCarrera.addEventListener('change', syncPeriodos);
        syncPeriodos();
    }
});
</script>
