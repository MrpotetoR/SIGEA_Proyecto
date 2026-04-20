<x-panel title="Nuevo Docente" panelNombre="Servicios Escolares">
    <x-slot name="nav">@include('partials.servicios-nav')</x-slot>

    <div class="max-w-5xl">
        <a href="{{ route('servicios.docentes.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Volver</a>

        <form method="POST" action="{{ route('servicios.docentes.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Contenedor 1: Datos del docente --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-6">Datos del docente</h2>

                    <div class="space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre(s) *</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="80"
                                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                                       title="Solo letras y espacios"
                                       oninput="updateCount(this, 'cnt-nombre'); this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('nombre') border-red-400 @enderror">
                                <div class="flex justify-between mt-1">
                                    @error('nombre')
                                        <p class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</p>
                                    @else
                                        <span></span>
                                    @enderror
                                    <span id="cnt-nombre" class="text-xs text-gray-400 dark:text-gray-400">0/80</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellidos *</label>
                                <input type="text" name="apellidos" value="{{ old('apellidos') }}" required maxlength="100"
                                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+"
                                       title="Solo letras y espacios"
                                       oninput="updateCount(this, 'cnt-apellidos'); this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('apellidos') border-red-400 @enderror">
                                <div class="flex justify-between mt-1">
                                    @error('apellidos')
                                        <p class="text-red-500 dark:text-red-400 text-xs">{{ $message }}</p>
                                    @else
                                        <span></span>
                                    @enderror
                                    <span id="cnt-apellidos" class="text-xs text-gray-400 dark:text-gray-400">0/100</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correo electrónico *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required maxlength="255"
                                   class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('email') border-red-400 @enderror">
                            @error('email')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-400 dark:text-gray-400 mt-1">Contraseña inicial: <code class="dark:text-gray-300">docente{{ date('Y') }}</code></p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de cédula profesional</label>
                                <input type="text" name="num_cedula" value="{{ old('num_cedula') }}" maxlength="30"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none @error('num_cedula') border-red-400 @enderror">
                                @error('num_cedula')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RFC</label>
                                <input type="text" name="rfc" value="{{ old('rfc') }}" maxlength="20"
                                       oninput="this.value = this.value.toUpperCase()"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm uppercase focus:ring-2 focus:ring-blue-400 focus:outline-none @error('rfc') border-red-400 @enderror">
                                @error('rfc')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Especialidad *</label>
                            <input type="text" id="especialidad" name="especialidad" value="{{ old('especialidad') }}" maxlength="100"
                                   oninput="updateCount(this, 'cnt-especialidad'); toggleCarreras();"
                                   class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            <div class="flex justify-between mt-1">
                                <p class="text-xs text-gray-400 dark:text-gray-400">Debe llenarse antes de seleccionar las carreras.</p>
                                <span id="cnt-especialidad" class="text-xs text-gray-400 dark:text-gray-400">0/100</span>
                            </div>
                        </div>

                        {{-- Carreras que imparte --}}
                        <div id="bloque-carreras">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Carreras que imparte *</label>
                                <button type="button" id="btn-agregar-carrera" disabled
                                        onclick="agregarCarrera()"
                                        class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg bg-[#0606F0] text-white hover:bg-[#04276B] disabled:bg-gray-300 disabled:dark:bg-gray-600 disabled:cursor-not-allowed transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Agregar
                                </button>
                            </div>
                            <div id="carreras-container" class="space-y-2">
                                <div class="carrera-row flex gap-2">
                                    <select name="carreras[]" disabled
                                            class="flex-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none disabled:bg-gray-100 disabled:dark:bg-gray-800 disabled:cursor-not-allowed disabled:text-gray-400">
                                        <option value="">— Primero escribe la especialidad —</option>
                                        @foreach($carreras as $c)
                                            <option value="{{ $c->id_carrera }}">{{ $c->clave_carrera }} — {{ $c->nombre_carrera }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="eliminarCarrera(this)"
                                            class="px-2.5 py-2 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 text-xs font-medium hidden">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                            @error('carreras.*')<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de contrato *</label>
                            <div class="flex gap-4 mt-2 mb-2">
                                <label class="flex items-center gap-2 text-sm cursor-pointer dark:text-gray-300">
                                    <input type="radio" name="tipo_contrato" value="horas"
                                           @checked(old('tipo_contrato', 'horas') === 'horas')
                                           onchange="document.getElementById('campo-horas').classList.remove('hidden')"
                                           class="text-[#0606F0] focus:ring-blue-500">
                                    Por horas
                                </label>
                                <label class="flex items-center gap-2 text-sm cursor-pointer dark:text-gray-300">
                                    <input type="radio" name="tipo_contrato" value="planta"
                                           @checked(old('tipo_contrato') === 'planta')
                                           onchange="document.getElementById('campo-horas').classList.add('hidden')"
                                           class="text-[#0606F0] focus:ring-blue-500">
                                    Docente de Planta
                                </label>
                            </div>
                            <div id="campo-horas" class="{{ old('tipo_contrato') === 'planta' ? 'hidden' : '' }}">
                                <input type="number" name="horas_contrato" value="{{ old('horas_contrato') }}"
                                       min="1" max="40" placeholder="Ej. 20"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                <p class="text-xs text-gray-400 dark:text-gray-400 mt-1">Entre 1 y 40 horas semanales</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contenedor 2: Documentación del docente --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 flex flex-col" style="max-height: calc(100vh - 180px);">
                    <div class="flex items-start justify-between mb-4 flex-shrink-0">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Documentación del docente</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Todos los archivos son <span class="font-semibold text-red-600 dark:text-red-400">obligatorios</span> — PDF (máx. 5 MB)</p>
                        </div>
                    </div>

                    <div class="overflow-y-auto flex-1 custom-scrollbar pr-1 space-y-3">
                        @foreach(\App\Models\DocumentoDocente::TIPOS as $key => $label)
                            <div class="border dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $label }} <span class="text-red-500">*</span></label>
                                <input type="file" name="documentos[{{ $key }}]" accept="application/pdf" required
                                       class="block w-full text-xs text-gray-600 dark:text-gray-300
                                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                              file:text-xs file:font-medium
                                              file:bg-[#0606F0] file:text-white
                                              hover:file:bg-[#04276B] cursor-pointer
                                              @error('documentos.'.$key) ring-1 ring-red-400 @enderror">
                                @error('documentos.'.$key)
                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-4 border border-transparent dark:border-gray-700 flex gap-3">
                <button type="submit"
                        class="bg-blue-700 hover:bg-blue-800 dark:bg-[#0606F0] dark:hover:bg-blue-400 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Registrar docente
                </button>
                <a href="{{ route('servicios.docentes.index') }}"
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

// Opciones base de carreras (para clonar)
const CARRERAS_OPTIONS = `
    <option value="">Selecciona una carrera…</option>
    @foreach($carreras as $c)
        <option value="{{ $c->id_carrera }}">{{ $c->clave_carrera }} — {{ addslashes($c->nombre_carrera) }}</option>
    @endforeach
`;

function toggleCarreras() {
    const esp = document.getElementById('especialidad').value.trim();
    const habilitado = esp.length > 0;
    const selects = document.querySelectorAll('#carreras-container select[name="carreras[]"]');
    const btnAgregar = document.getElementById('btn-agregar-carrera');

    selects.forEach(sel => {
        sel.disabled = !habilitado;
        if (habilitado && sel.options[0].value === '' && sel.options[0].text.includes('Primero')) {
            sel.innerHTML = CARRERAS_OPTIONS;
        }
    });
    btnAgregar.disabled = !habilitado;
}

function agregarCarrera() {
    const container = document.getElementById('carreras-container');
    const row = document.createElement('div');
    row.className = 'carrera-row flex gap-2';
    row.innerHTML = `
        <select name="carreras[]" class="flex-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
            ${CARRERAS_OPTIONS}
        </select>
        <button type="button" onclick="eliminarCarrera(this)"
                class="px-2.5 py-2 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 text-xs font-medium">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    container.appendChild(row);
    actualizarBotonesEliminar();
}

function eliminarCarrera(btn) {
    const row = btn.closest('.carrera-row');
    const container = document.getElementById('carreras-container');
    if (container.querySelectorAll('.carrera-row').length > 1) {
        row.remove();
        actualizarBotonesEliminar();
    }
}

function actualizarBotonesEliminar() {
    const rows = document.querySelectorAll('#carreras-container .carrera-row');
    rows.forEach((r, i) => {
        const btn = r.querySelector('button');
        if (rows.length > 1) btn.classList.remove('hidden');
        else btn.classList.add('hidden');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[maxlength][oninput]').forEach(el => el.dispatchEvent(new Event('input')));
    toggleCarreras();
    actualizarBotonesEliminar();
});
</script>
