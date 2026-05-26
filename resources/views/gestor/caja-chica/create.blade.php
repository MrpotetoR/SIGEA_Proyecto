<x-panel title="Nueva solicitud de vale" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="max-w-3xl space-y-6">
        <a href="{{ route('gestor.caja-chica.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline inline-block">← Volver</a>

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- Aviso de saldo si es bajo --}}
        @php $colorTw = $fondo->semaforo_color_tw; @endphp
        @if($colorTw !== 'green')
            <div class="rounded-lg p-4 border
                        {{ $colorTw === 'red'
                            ? 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700 text-red-700 dark:text-red-300'
                            : 'bg-amber-50 dark:bg-amber-900/20 border-amber-300 dark:border-amber-700 text-amber-700 dark:text-amber-300' }}">
                <p class="text-sm">
                    <strong>Atención:</strong> el fondo está en
                    <strong>{{ $fondo->semaforo_label }}</strong>
                    (${{ number_format((float) $fondo->saldo_actual, 2) }} disponibles).
                    Puedes crear la solicitud, pero su autorización dependerá del saldo disponible.
                </p>
            </div>
        @endif

        <form method="POST" action="{{ route('gestor.caja-chica.store') }}"
              class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 space-y-5"
              x-data="formNuevoVale()">
            @csrf

            {{-- Solicitante con autocompletado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Solicitante *
                </label>
                <div class="relative" @click.outside="cerrar()">
                    <input type="text" name="solicitante_nombre" required maxlength="150" minlength="2"
                           value="{{ old('solicitante_nombre') }}"
                           x-model="nombre"
                           @input.debounce.250ms="buscar()"
                           @focus="if (sugerencias.length) abierto = true"
                           autocomplete="off"
                           placeholder="Escribe el nombre del solicitante..."
                           class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">

                    {{-- Dropdown sugerencias --}}
                    <div x-show="abierto && sugerencias.length" x-cloak
                         class="absolute z-20 left-0 right-0 mt-1 bg-white dark:bg-gray-700 rounded-lg shadow-lg border dark:border-gray-600 overflow-hidden max-h-60 overflow-y-auto">
                        <template x-for="s in sugerencias" :key="s.id">
                            <button type="button"
                                    @click="seleccionar(s.nombre)"
                                    class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 dark:hover:bg-gray-600 flex items-center justify-between">
                                <span x-text="s.nombre"></span>
                                <span class="text-[10px] text-gray-400" x-text="`${s.veces_usado}× usado`"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                    El sistema recordará el nombre para futuras solicitudes.
                </p>
            </div>

            {{-- Concepto --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Concepto del gasto *
                </label>
                <input type="text" name="concepto" required maxlength="255" minlength="3"
                       value="{{ old('concepto') }}"
                       placeholder="Ej. Gasolina visita industrial — 20 mayo"
                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
            </div>

            {{-- Monto --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Monto solicitado *
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-400">$</span>
                    <input type="number" name="monto" step="0.01" min="0.01" required
                           value="{{ old('monto') }}"
                           x-model.number="monto"
                           placeholder="0.00"
                           class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>

                {{-- Indicador de saldo --}}
                <p class="text-[11px] mt-1.5"
                   :class="{
                       'text-red-600 dark:text-red-400': monto > {{ (float) $fondo->saldo_actual }},
                       'text-amber-600 dark:text-amber-400': monto > 0 && monto <= {{ (float) $fondo->saldo_actual }} && monto > {{ (float) $fondo->saldo_actual * 0.66 }},
                       'text-gray-500 dark:text-gray-400': monto > 0 && monto <= {{ (float) $fondo->saldo_actual * 0.66 }},
                   }">
                    <template x-if="monto > {{ (float) $fondo->saldo_actual }}">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            El monto excede el saldo actual (${{ number_format((float) $fondo->saldo_actual, 2) }}). La solicitud quedará pendiente hasta reponer el fondo.
                        </span>
                    </template>
                    <template x-if="monto > 0 && monto <= {{ (float) $fondo->saldo_actual }}">
                        <span>Saldo disponible: ${{ number_format((float) $fondo->saldo_actual, 2) }} · Quedaría: $<span x-text="({{ (float) $fondo->saldo_actual }} - monto).toFixed(2)"></span></span>
                    </template>
                </p>

                @if($fondo->tope_vale_individual)
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                        Tope máximo por vale: ${{ number_format((float) $fondo->tope_vale_individual, 2) }}
                    </p>
                @endif
            </div>

            {{-- Motivo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo *</label>
                <select name="motivo" x-model="motivo" required
                        class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">— Selecciona —</option>
                    @foreach(\App\Models\CajaChicaLog::MOTIVOS as $k => $label)
                        <option value="{{ $k }}" {{ old('motivo') === $k ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="motivo === 'otro'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Especifica el motivo *</label>
                <input type="text" name="motivo_personalizado" maxlength="100"
                       value="{{ old('motivo_personalizado') }}"
                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            {{-- Botones --}}
            <div class="flex gap-3 pt-3 border-t dark:border-gray-700">
                <button type="submit"
                        class="bg-[#0606F0] hover:bg-[#04276B] text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Crear solicitud
                </button>
                <a href="{{ route('gestor.caja-chica.index') }}"
                   class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-panel>

<script>
function formNuevoVale() {
    return {
        nombre: '{{ old('solicitante_nombre', '') }}',
        monto: {{ old('monto', 0) }},
        motivo: '{{ old('motivo', '') }}',
        sugerencias: [],
        abierto: false,

        async buscar() {
            const q = (this.nombre || '').trim();
            if (q.length < 2) {
                this.sugerencias = [];
                this.abierto = false;
                return;
            }
            try {
                const res = await fetch(`{{ route('gestor.caja-chica.solicitantes.buscar') }}?q=${encodeURIComponent(q)}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) {
                    this.sugerencias = [];
                    return;
                }
                const data = await res.json();
                this.sugerencias = data.sugerencias || [];
                this.abierto = this.sugerencias.length > 0;
            } catch (e) {
                this.sugerencias = [];
            }
        },

        seleccionar(nombre) {
            this.nombre = nombre;
            this.abierto = false;
        },

        cerrar() {
            this.abierto = false;
        },
    };
}
</script>
