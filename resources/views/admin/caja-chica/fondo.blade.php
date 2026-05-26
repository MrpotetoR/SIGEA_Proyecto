<x-panel title="Caja Chica — Configuración del Fondo" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="max-w-6xl space-y-6">

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
                <p class="font-semibold mb-1">Hay errores en el formulario:</p>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- ============= TARJETA RESUMEN DEL FONDO ============= --}}
        @php
            $colorTw = $fondo->semaforo_color_tw;
            $bgClasses = [
                'green'  => 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700',
                'amber'  => 'bg-amber-50 dark:bg-amber-900/20 border-amber-300 dark:border-amber-700',
                'red'    => 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700',
                'gray'   => 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700',
            ];
            $dotClasses = [
                'green' => 'bg-green-500', 'amber' => 'bg-amber-500',
                'red'   => 'bg-red-500',   'gray'  => 'bg-gray-400',
            ];
        @endphp
        <div class="rounded-2xl border-2 p-6 {{ $bgClasses[$colorTw] ?? $bgClasses['gray'] }}">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-block w-3 h-3 rounded-full {{ $dotClasses[$colorTw] ?? $dotClasses['gray'] }} animate-pulse"></span>
                        <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Caja Chica — {{ $fondo->semaforo_label }}
                        </h2>
                    </div>
                    <p class="text-4xl font-bold text-gray-900 dark:text-gray-100">
                        ${{ number_format((float) $fondo->saldo_actual, 2) }}
                        <span class="text-lg font-normal text-gray-500">
                            / ${{ number_format((float) $fondo->monto_base, 2) }}
                        </span>
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $fondo->porcentaje_saldo }}% disponible
                        @if($fondo->faltante_reponer > 0)
                            · <span class="text-red-600 dark:text-red-400 font-medium">
                                Falta reponer ${{ number_format($fondo->faltante_reponer, 2) }}
                            </span>
                        @else
                            · <span class="text-green-700 dark:text-green-400 font-medium">Fondo completo</span>
                        @endif
                    </p>
                </div>

                @if($fondo->faltante_reponer > 0)
                    <button type="button" onclick="document.getElementById('seccion-reponer').scrollIntoView({behavior:'smooth'})"
                            class="bg-[#0606F0] hover:bg-[#04276B] text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                        Reponer saldo
                    </button>
                @endif
            </div>

            {{-- Barra de progreso --}}
            @php
                $pct = min(100, max(0, $fondo->porcentaje_saldo));
                $barColor = $colorTw === 'green' ? 'bg-green-500' : ($colorTw === 'amber' ? 'bg-amber-500' : 'bg-red-500');
            @endphp
            <div class="mt-4 w-full bg-white/60 dark:bg-gray-900/40 rounded-full h-3 overflow-hidden">
                <div class="{{ $barColor }} h-3 transition-all" style="width: {{ $pct }}%"></div>
            </div>
        </div>

        {{-- ============= FORMULARIO DE CONFIGURACIÓN ============= --}}
        <form method="POST" action="{{ route('admin.caja-chica.fondo.update') }}" enctype="multipart/form-data"
              class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 space-y-5"
              x-data="formFondo()" @submit.prevent="onSubmit($event)">
            @csrf @method('PUT')

            <div>
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Configuración del fondo</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Solo modifica el monto base, tope individual por vale y los umbrales del semáforo.
                    No afecta el saldo actual.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Monto base mensual *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400">$</span>
                        <input type="number" name="monto_base" step="0.01" min="0" required
                               value="{{ old('monto_base', $fondo->monto_base) }}"
                               x-model.number="montoBase"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">Tope objetivo del fondo (ej. 3000.00).</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tope máximo por vale individual <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400">$</span>
                        <input type="number" name="tope_vale_individual" step="0.01" min="0"
                               value="{{ old('tope_vale_individual', $fondo->tope_vale_individual) }}"
                               placeholder="Sin tope"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">Vacío = sin tope individual por vale.</p>
                </div>
            </div>

            <div class="border-t dark:border-gray-700 pt-5">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Umbrales del semáforo</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            🟢 Verde — saldo MAYOR a:
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400">$</span>
                            <input type="number" name="umbral_verde" step="0.01" min="0" required
                                   value="{{ old('umbral_verde', $fondo->umbral_verde) }}"
                                   x-model.number="umbralVerde"
                                   class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            🔴 Rojo — saldo MENOR o IGUAL a:
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400">$</span>
                            <input type="number" name="umbral_amarillo" step="0.01" min="0" required
                                   value="{{ old('umbral_amarillo', $fondo->umbral_amarillo) }}"
                                   x-model.number="umbralAmarillo"
                                   class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:outline-none">
                        </div>
                    </div>
                </div>

                {{-- Vista previa reactiva del semáforo --}}
                <div class="mt-4 bg-gray-50 dark:bg-gray-900/40 rounded-lg p-4 border dark:border-gray-700">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Vista previa</p>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded p-2">
                            <div class="font-bold">🔴 Rojo</div>
                            <div>≤ $<span x-text="umbralAmarillo.toFixed(2)"></span></div>
                        </div>
                        <div class="bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 rounded p-2">
                            <div class="font-bold">🟡 Amarillo</div>
                            <div>
                                $<span x-text="(umbralAmarillo + 0.01).toFixed(2)"></span>
                                — $<span x-text="umbralVerde.toFixed(2)"></span>
                            </div>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded p-2">
                            <div class="font-bold">🟢 Verde</div>
                            <div>> $<span x-text="umbralVerde.toFixed(2)"></span></div>
                        </div>
                    </div>
                    <p x-show="umbralAmarillo >= umbralVerde" x-cloak class="text-xs text-red-600 dark:text-red-400 mt-2">
                        ⚠ El umbral amarillo debe ser MENOR al verde.
                    </p>
                </div>
            </div>

            <div class="border-t dark:border-gray-700 pt-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo del cambio *</label>
                    <select name="motivo" x-model="motivo" required
                            class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">— Selecciona —</option>
                        @foreach(\App\Models\CajaChicaLog::MOTIVOS as $k => $label)
                            <option value="{{ $k }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="motivo === 'otro'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Especifica el motivo *</label>
                    <input type="text" name="motivo_personalizado" maxlength="100"
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
                                  file:bg-[#0606F0] file:text-white
                                  hover:file:bg-[#04276B] cursor-pointer">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-[#0606F0] hover:bg-[#04276B] text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    Guardar configuración
                </button>
            </div>
        </form>

        {{-- ============= REPOSICIÓN DE SALDO ============= --}}
        <form method="POST" action="{{ route('admin.caja-chica.fondo.repone') }}" enctype="multipart/form-data"
              id="seccion-reponer"
              class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 space-y-5"
              x-data="formRepone()" @submit.prevent="onSubmit($event)">
            @csrf

            <div>
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Reponer saldo</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Reposición parcial o total hasta llegar al monto base.
                    Máximo permitido: <strong>${{ number_format((float) $fondo->faltante_reponer, 2) }}</strong>.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monto a reponer *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400">$</span>
                        <input type="number" name="monto" step="0.01" min="0.01"
                               max="{{ $fondo->faltante_reponer }}"
                               {{ $fondo->faltante_reponer <= 0 ? 'disabled' : 'required' }}
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none disabled:opacity-50">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo *</label>
                    <select name="motivo" x-model="motivo" required
                            {{ $fondo->faltante_reponer <= 0 ? 'disabled' : '' }}
                            class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="reposicion_mensual">Reposición mensual</option>
                        <option value="ajuste_configuracion">Ajuste de configuración</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
            </div>

            <div x-show="motivo === 'otro'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Especifica el motivo *</label>
                <input type="text" name="motivo_personalizado" maxlength="100"
                       class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Evidencia del depósito <span class="text-gray-400 font-normal">(opcional — PDF/JPG/PNG, máx 5 MB)</span>
                </label>
                <input type="file" name="evidencia" accept="application/pdf,image/jpeg,image/png"
                       {{ $fondo->faltante_reponer <= 0 ? 'disabled' : '' }}
                       class="block w-full text-xs text-gray-600 dark:text-gray-300
                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                              file:text-xs file:font-medium
                              file:bg-[#0606F0] file:text-white
                              hover:file:bg-[#04276B] cursor-pointer disabled:opacity-50">
            </div>

            <button type="submit"
                    {{ $fondo->faltante_reponer <= 0 ? 'disabled' : '' }}
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Reponer fondo
            </button>
        </form>

        {{-- ============= ÚLTIMOS MOVIMIENTOS ============= --}}
        @if($ultimosMovimientos->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Últimos movimientos del fondo</h2>
                    <a href="{{ route('admin.caja-chica.historial') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline">
                        Ver historial completo →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                            <tr>
                                <th class="text-left py-2 pr-3">Fecha</th>
                                <th class="text-left py-2 pr-3">Ejecutado por</th>
                                <th class="text-left py-2 pr-3">Acción</th>
                                <th class="text-left py-2 pr-3">Monto</th>
                                <th class="text-left py-2">Motivo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach($ultimosMovimientos as $log)
                                <tr class="text-gray-700 dark:text-gray-300">
                                    <td class="py-2 pr-3 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="py-2 pr-3">{{ $log->usuario?->name ?? '—' }}</td>
                                    <td class="py-2 pr-3">
                                        <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700">
                                            {{ $log->accion_legible }}
                                        </span>
                                    </td>
                                    <td class="py-2 pr-3 font-mono text-xs">{{ $log->monto_legible }}</td>
                                    <td class="py-2 text-xs">{{ $log->motivo_legible }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-panel>

<script>
function formFondo() {
    return {
        montoBase: {{ (float) $fondo->monto_base }},
        umbralVerde: {{ (float) $fondo->umbral_verde }},
        umbralAmarillo: {{ (float) $fondo->umbral_amarillo }},
        motivo: '',
        reauthOk: false,

        onSubmit(event) {
            // Validación cliente: amarillo < verde
            if (this.umbralAmarillo >= this.umbralVerde) {
                alert('El umbral amarillo debe ser MENOR al umbral verde.');
                return;
            }

            // Si ya hay reauth confirmada, enviar directo.
            if (this.reauthOk) {
                event.target.submit();
                return;
            }

            // Solicitar reauth.
            const self = this;
            window.dispatchEvent(new CustomEvent('reauth:open', {
                detail: {
                    action: 'configurar_tope_caja_chica',
                    title:  'Modificar configuración del fondo',
                    description: 'Estás por modificar el monto base, tope individual o umbrales del semáforo. Confirma con tu contraseña.',
                    onSuccess: () => {
                        self.reauthOk = true;
                        event.target.submit();
                    },
                },
            }));
        },
    };
}

function formRepone() {
    return {
        motivo: 'reposicion_mensual',
        reauthOk: false,

        onSubmit(event) {
            if (this.reauthOk) {
                event.target.submit();
                return;
            }
            const self = this;
            window.dispatchEvent(new CustomEvent('reauth:open', {
                detail: {
                    action: 'reponer_fondo',
                    title:  'Reponer saldo de Caja Chica',
                    description: 'Estás por agregar saldo al fondo. Confirma con tu contraseña.',
                    onSuccess: () => {
                        self.reauthOk = true;
                        event.target.submit();
                    },
                },
            }));
        },
    };
}
</script>
