<x-panel title="Cobrar trámite" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="max-w-3xl space-y-6">
        <a href="{{ route('admin.caja-general.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline inline-block">← Volver al dashboard</a>

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.caja-general.cobro-tramite.store') }}" enctype="multipart/form-data"
              class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 space-y-5"
              x-data="formCobroTramite()">
            @csrf

            <div>
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Nuevo cobro de trámite</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    El cobro se reflejará automáticamente como ingreso en la Caja General.
                </p>
            </div>

            {{-- Alumno con autocompletado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Alumno *
                </label>
                <input type="hidden" name="alumno_id" x-bind:value="alumnoId" required>
                <div class="relative" @click.outside="cerrar()">
                    <input type="text" required minlength="2"
                           x-model="busqueda"
                           @input.debounce.250ms="buscar()"
                           @focus="if (sugerencias.length) abierto = true"
                           autocomplete="off"
                           placeholder="Buscar por nombre, apellidos o matrícula..."
                           class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <div x-show="abierto && sugerencias.length" x-cloak
                         class="absolute z-20 left-0 right-0 mt-1 bg-white dark:bg-gray-700 rounded-lg shadow-lg border dark:border-gray-600 overflow-hidden max-h-60 overflow-y-auto">
                        <template x-for="s in sugerencias" :key="s.id">
                            <button type="button" @click="seleccionar(s)"
                                    class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 dark:hover:bg-gray-600 flex items-center gap-3">
                                <span class="text-xs font-mono text-gray-400" x-text="s.codigo"></span>
                                <span x-text="s.nombre"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <p class="text-[11px] text-green-700 dark:text-green-400 mt-1 inline-flex items-center gap-1" x-show="alumnoId" x-cloak>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Alumno seleccionado: <strong x-text="busqueda"></strong></span>
                </p>
            </div>

            {{-- Tipo de trámite --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de trámite *</label>
                <select name="tipo_tramite" x-model="tipoTramite" @change="autoTarifa()" required
                        class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">— Selecciona —</option>
                    @foreach(\App\Models\CobroTramite::TIPOS_TRAMITE as $k => $label)
                        <option value="{{ $k }}"
                                data-tarifa="{{ $tarifas[$k] ?? '' }}"
                                @selected(old('tipo_tramite') === $k)>
                            {{ $label }}
                            @if(!empty($tarifas[$k]))
                                — ${{ number_format($tarifas[$k], 2) }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div x-show="tipoTramite === 'otro'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Describe el trámite *</label>
                <input type="text" name="concepto_personalizado" maxlength="255"
                       value="{{ old('concepto_personalizado') }}"
                       placeholder="Ej. Reposición de credencial"
                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            {{-- Monto + método de pago --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monto *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400">$</span>
                        <input type="number" name="monto" step="0.01" min="0.01" required
                               x-model.number="monto"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg pl-7 pr-3 py-2 text-sm">
                    </div>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                        La tarifa default se precarga al elegir el tipo (si está configurada por admin).
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Método de pago *</label>
                    <select name="metodo_pago" required
                            class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                        @foreach(\App\Models\IngresoCajaGeneral::METODOS_PAGO as $k => $label)
                            <option value="{{ $k }}" @selected(old('metodo_pago', 'efectivo') === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Referencia externa <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <input type="text" name="referencia_externa" maxlength="100"
                       value="{{ old('referencia_externa') }}"
                       placeholder="Núm. de recibo, folio bancario, etc."
                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Evidencia <span class="text-gray-400 font-normal">(opcional — PDF/JPG/PNG, máx 5 MB)</span>
                </label>
                <input type="file" name="evidencia" accept="application/pdf,image/jpeg,image/png"
                       class="block w-full text-xs text-gray-600 dark:text-gray-300
                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                              file:text-xs file:font-medium
                              file:bg-[#0606F0] file:text-white hover:file:bg-[#04276B] cursor-pointer">
            </div>

            <div class="flex gap-3 pt-3 border-t dark:border-gray-700">
                <button type="submit"
                        class="bg-[#0606F0] hover:bg-[#04276B] text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Registrar cobro
                </button>
                <a href="{{ route('admin.caja-general.index') }}"
                   class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-panel>

<script>
function formCobroTramite() {
    return {
        busqueda: '',
        alumnoId: '',
        tipoTramite: '{{ old('tipo_tramite', '') }}',
        monto: {{ old('monto', 0) }},
        sugerencias: [],
        abierto: false,

        async buscar() {
            const q = (this.busqueda || '').trim();
            if (q.length < 2) {
                this.sugerencias = [];
                this.abierto = false;
                return;
            }
            try {
                const res = await fetch(`{{ route('admin.caja-general.cobro-tramite.alumnos') }}?q=${encodeURIComponent(q)}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                this.sugerencias = data.sugerencias || [];
                this.abierto = this.sugerencias.length > 0;
            } catch (e) {
                this.sugerencias = [];
            }
        },

        seleccionar(s) {
            this.busqueda = `${s.codigo} — ${s.nombre}`;
            this.alumnoId = s.id;
            this.abierto = false;
        },

        cerrar() {
            this.abierto = false;
        },

        autoTarifa() {
            const select = document.querySelector('select[name=tipo_tramite]');
            const option = select.options[select.selectedIndex];
            const tarifa = option?.dataset?.tarifa;
            if (tarifa && parseFloat(tarifa) > 0) {
                this.monto = parseFloat(tarifa);
            }
        },
    };
}
</script>
