<x-panel title="Nuevo Gestores Escolares" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="max-w-5xl">
        <a href="{{ route('admin.personal.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Volver</a>

        @if($errors->any())
            <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
                <p class="font-semibold mb-1">Hay errores en el formulario:</p>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.personal.store') }}" enctype="multipart/form-data"
              class="space-y-6" x-data="formNuevoGestor()"
              data-nombre="{{ old('nombre', '') }}"
              data-apellidos="{{ old('apellidos', '') }}">
            @csrf
            {{-- Flag de permiso especial: solo se envía si reauth fue exitoso. --}}
            <input type="hidden" name="puede_asignar_carreras" :value="permisoEspecialConfirmado ? '1' : '0'">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Datos del personal --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-6">Datos del personal</h2>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre(s) *</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="80"
                                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+" title="Solo letras y espacios"
                                       x-model="nombre"
                                       oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apellidos *</label>
                                <input type="text" name="apellidos" value="{{ old('apellidos') }}" required maxlength="100"
                                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]+" title="Solo letras y espacios"
                                       x-model="apellidos"
                                       oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correo electrónico *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required maxlength="255"
                                   class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            <p class="text-xs text-gray-400 mt-1">Contraseña inicial: <code class="dark:text-gray-300">gestor{{ date('Y') }}</code></p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de cédula profesional</label>
                                <input type="text" name="num_cedula" value="{{ old('num_cedula') }}" maxlength="30"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RFC</label>
                                <input type="text" name="rfc" value="{{ old('rfc') }}" maxlength="20"
                                       oninput="this.value = this.value.toUpperCase()"
                                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm uppercase focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Especialidad *</label>
                            <input type="text" name="especialidad" value="{{ old('especialidad') }}" required maxlength="150"
                                   placeholder="Ej. Administración educativa, Gestión académica..."
                                   class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            <p class="text-xs text-gray-400 mt-1">Las carreras se asignan en el siguiente paso (sección "Asignar carreras").</p>
                        </div>

                        {{-- ── Permiso especial: asignar carreras a otros gestores ── --}}
                        <div class="border-2 border-dashed border-amber-300 dark:border-amber-700/60 bg-amber-50/40 dark:bg-amber-900/10 rounded-lg p-4">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox"
                                       x-model="permisoEspecialCheck"
                                       @change="onTogglePermiso($event)"
                                       :disabled="!nombreCompleto"
                                       class="mt-1 rounded text-amber-600 focus:ring-amber-400 disabled:opacity-40">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        <template x-if="nombreCompleto">
                                            <span>¿Deseas otorgarle permisos a <strong x-text="nombreCompleto"></strong> para que pueda asignar carreras a otros gestores escolares?</span>
                                        </template>
                                        <template x-if="!nombreCompleto">
                                            <span class="text-gray-400">Llena nombre y apellidos para habilitar el permiso especial.</span>
                                        </template>
                                    </p>
                                    <p x-show="permisoEspecialConfirmado" x-cloak class="text-xs text-green-700 dark:text-green-400 mt-1.5 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Permiso confirmado con tu contraseña.
                                    </p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5">
                                        Esta acción es sensible y requiere validar tu contraseña de administrador.
                                    </p>
                                </div>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Asignar carreras <span class="text-gray-400 font-normal">(opcional, máx. {{ \App\Models\GestorEscolar::MAX_CARRERAS }})</span>
                            </label>
                            @if($carrerasDisponibles->isEmpty())
                                <p class="text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg px-3 py-2">
                                    No hay carreras disponibles para asignar. Todas están asignadas a otro personal.
                                </p>
                            @else
                                <div class="border dark:border-gray-600 rounded-lg p-3 max-h-48 overflow-y-auto custom-scrollbar bg-gray-50 dark:bg-gray-700/50 space-y-1.5">
                                    @foreach($carrerasDisponibles as $c)
                                        <label class="flex items-center gap-2 cursor-pointer hover:bg-white dark:hover:bg-gray-700 px-2 py-1 rounded">
                                            <input type="checkbox" name="carreras[]" value="{{ $c->id_carrera }}"
                                                   class="rounded text-[#0606F0] focus:ring-blue-400 carrera-check"
                                                   data-max="{{ \App\Models\GestorEscolar::MAX_CARRERAS }}"
                                                   {{ in_array($c->id_carrera, old('carreras', [])) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700 dark:text-gray-200">{{ $c->nombre_carrera }}</span>
                                            <span class="text-[10px] text-gray-400 ml-auto">{{ $c->clave_carrera }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Documentación --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 flex flex-col" style="max-height: calc(100vh - 180px);">
                    <div class="mb-4 flex-shrink-0">
                        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Documentación del personal</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Todos los archivos son <span class="font-semibold text-red-600 dark:text-red-400">obligatorios</span> — PDF (máx. 5 MB)</p>
                    </div>

                    <div class="overflow-y-auto flex-1 custom-scrollbar pr-1 space-y-3">
                        @foreach(\App\Models\DocumentoPersonalSE::TIPOS as $key => $label)
                            <div class="border dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-700/50">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $label }} <span class="text-red-500">*</span></label>
                                <input type="file" name="documentos[{{ $key }}]" accept="application/pdf" required
                                       class="block w-full text-xs text-gray-600 dark:text-gray-300
                                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                              file:text-xs file:font-medium
                                              file:bg-[#0606F0] file:text-white
                                              hover:file:bg-[#04276B] cursor-pointer">
                                @error('documentos.'.$key)
                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-4 border border-transparent dark:border-gray-700 flex flex-wrap gap-3">
                <button type="submit"
                        class="bg-[#0606F0] hover:bg-[#04276B] text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Registrar personal
                </button>
                <a href="{{ route('admin.personal.index') }}"
                   class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-panel>

<script>
(function() {
    const checks = document.querySelectorAll('.carrera-check');
    if (!checks.length) return;
    const max = parseInt(checks[0].dataset.max, 10);
    function refresh() {
        const sel = Array.from(checks).filter(c => c.checked).length;
        checks.forEach(c => { if (!c.checked) c.disabled = sel >= max; });
    }
    checks.forEach(c => c.addEventListener('change', refresh));
    refresh();
})();

function formNuevoGestor() {
    return {
        nombre: '',
        apellidos: '',
        permisoEspecialCheck: false,
        permisoEspecialConfirmado: false,

        init() {
            const ds = this.$el.dataset;
            this.nombre    = ds.nombre || '';
            this.apellidos = ds.apellidos || '';
        },

        get nombreCompleto() {
            const n = (this.nombre || '').trim();
            const a = (this.apellidos || '').trim();
            return (n || a) ? `${n} ${a}`.trim() : '';
        },

        onTogglePermiso(event) {
            // Si se desmarca, simplemente cancela.
            if (!this.permisoEspecialCheck) {
                this.permisoEspecialConfirmado = false;
                return;
            }
            // Si ya estaba confirmado, no pedir reauth otra vez.
            if (this.permisoEspecialConfirmado) return;

            // Solicitar reauth.
            const self = this;
            window.dispatchEvent(new CustomEvent('reauth:open', {
                detail: {
                    action: 'otorgar_permiso_especial',
                    title:  'Otorgar permiso especial',
                    description: `Estás por darle a ${self.nombreCompleto} la capacidad de asignar carreras a otros gestores. Confirma con tu contraseña.`,
                    onSuccess: () => { self.permisoEspecialConfirmado = true; },
                    onCancel:  () => { self.permisoEspecialCheck = false; self.permisoEspecialConfirmado = false; },
                },
            }));
        },
    };
}
</script>
