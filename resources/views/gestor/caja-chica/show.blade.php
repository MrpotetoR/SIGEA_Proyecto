<x-panel title="Vale {{ $vale->folio }}" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    @php
        $tw = $vale->estado_color;
        $estadoClasses = match($tw) {
            'green' => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 border-green-300 dark:border-green-700',
            'blue'  => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border-blue-300 dark:border-blue-700',
            'rose'  => 'bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 border-rose-300 dark:border-rose-700',
            'slate' => 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-600',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600',
        };
        // Permisos calculados
        $puedeAutorizar  = $vale->estatus === 'solicitada';
        $puedeRechazar   = $vale->estatus === 'solicitada';
        $puedeEditar     = $vale->es_editable;
        $puedeCancelar   = !$vale->es_terminal;
        $puedeSubirFactura = $vale->estatus === 'autorizada' && !$vale->tiene_factura;
    @endphp

    <div class="max-w-5xl space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <a href="{{ route('gestor.caja-chica.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline inline-block">← Volver al listado</a>

            @if(!in_array($vale->estatus, ['solicitada']))
                <a href="{{ route('gestor.caja-chica.ticket', $vale) }}" target="_blank"
                   class="inline-flex items-center gap-2 bg-[#1e1b4b] hover:bg-[#312e81] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimir ticket PDF
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- ============= TARJETA PRINCIPAL ============= --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
            <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Folio</p>
                    <p class="text-2xl font-mono font-bold text-gray-900 dark:text-gray-100">{{ $vale->folio }}</p>
                </div>
                <span class="text-sm font-semibold px-3 py-1.5 rounded-lg border {{ $estadoClasses }}">
                    {{ $vale->estado_label }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Solicitante</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $vale->solicitante_nombre }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Monto</p>
                    <p class="font-mono font-bold text-2xl text-gray-900 dark:text-gray-100">
                        ${{ number_format((float) $vale->monto, 2) }}
                    </p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Concepto</p>
                    <p class="text-gray-700 dark:text-gray-300">{{ $vale->concepto }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Solicitado por</p>
                    <p class="text-gray-700 dark:text-gray-300">{{ $vale->solicitante?->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400">{{ $vale->created_at->format('Y-m-d H:i') }}</p>
                </div>
                @if($vale->autorizado_en)
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">
                            {{ $vale->estatus === 'rechazada' ? 'Rechazado por' : 'Autorizado por' }}
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $vale->autorizador?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $vale->autorizado_en->format('Y-m-d H:i') }}</p>
                    </div>
                @endif
                @if($vale->motivo_rechazo)
                    <div class="sm:col-span-2 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700 rounded-lg p-3">
                        <p class="text-xs text-rose-700 dark:text-rose-300 uppercase font-semibold">Motivo de rechazo</p>
                        <p class="text-sm text-rose-800 dark:text-rose-200">{{ $vale->motivo_rechazo }}</p>
                    </div>
                @endif
                @if($vale->tiene_factura)
                    <div class="sm:col-span-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-3">
                        <p class="text-xs text-green-700 dark:text-green-300 uppercase font-semibold">Factura cargada</p>
                        <a href="{{ asset('storage/'.$vale->factura_path) }}" target="_blank"
                           class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline inline-flex items-center gap-1.5 mt-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            Ver factura
                        </a>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Subida por {{ $vale->facturaSubidaPor?->name ?? '—' }} ·
                            {{ $vale->factura_subida_en?->format('Y-m-d H:i') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============= ACCIONES DISPONIBLES ============= --}}
        @if($puedeAutorizar || $puedeRechazar || $puedeCancelar || $puedeSubirFactura || $puedeEditar)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700"
                 x-data="{ accion: null }">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-4">Acciones disponibles</h3>

                <div class="flex flex-wrap gap-2 mb-5">
                    @if($puedeAutorizar)
                        <button type="button" @click="accion = (accion === 'autorizar' ? null : 'autorizar')"
                                class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Autorizar
                        </button>
                    @endif
                    @if($puedeRechazar)
                        <button type="button" @click="accion = (accion === 'rechazar' ? null : 'rechazar')"
                                class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Rechazar
                        </button>
                    @endif
                    @if($puedeSubirFactura)
                        <button type="button" @click="accion = (accion === 'factura' ? null : 'factura')"
                                class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            Subir factura
                        </button>
                    @endif
                    @if($puedeCancelar)
                        <button type="button" @click="accion = (accion === 'cancelar' ? null : 'cancelar')"
                                class="inline-flex items-center gap-1.5 bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Cancelar
                        </button>
                    @endif
                </div>

                {{-- ─── PANEL: AUTORIZAR ─── --}}
                @if($puedeAutorizar)
                    <form x-show="accion === 'autorizar'" x-cloak x-transition method="POST"
                          action="{{ route('gestor.caja-chica.autorizar', $vale) }}" enctype="multipart/form-data"
                          x-data="reauthForm('autorizar_vale', 'Autorizar vale {{ $vale->folio }}', 'Estás por autorizar este vale y descontar ${{ number_format((float) $vale->monto, 2) }} del fondo. Confirma con tu contraseña.')"
                          @submit.prevent="onSubmit($event)"
                          class="border-t dark:border-gray-700 pt-4 space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Motivo *</label>
                                <select name="motivo" x-model="motivo" required
                                        class="w-full mt-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                                    <option value="">— Selecciona —</option>
                                    @foreach(\App\Models\CajaChicaLog::MOTIVOS as $k => $label)
                                        <option value="{{ $k }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Evidencia (opcional)</label>
                                <input type="file" name="evidencia" accept="application/pdf,image/jpeg,image/png"
                                       class="w-full mt-1 text-xs">
                            </div>
                        </div>
                        <div x-show="motivo === 'otro'" x-cloak>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Especifica *</label>
                            <input type="text" name="motivo_personalizado" maxlength="100"
                                   class="w-full mt-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                            Confirmar autorización
                        </button>
                    </form>
                @endif

                {{-- ─── PANEL: RECHAZAR ─── --}}
                @if($puedeRechazar)
                    <form x-show="accion === 'rechazar'" x-cloak x-transition method="POST"
                          action="{{ route('gestor.caja-chica.rechazar', $vale) }}" enctype="multipart/form-data"
                          x-data="reauthForm('rechazar_vale', 'Rechazar vale {{ $vale->folio }}', 'Estás por rechazar este vale. Confirma con tu contraseña.')"
                          @submit.prevent="onSubmit($event)"
                          class="border-t dark:border-gray-700 pt-4 space-y-3">
                        @csrf
                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Motivo del rechazo *</label>
                            <textarea name="motivo_rechazo" required minlength="5" maxlength="500" rows="2"
                                      placeholder="Explica por qué se rechaza este vale..."
                                      class="w-full mt-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Categoría *</label>
                                <select name="motivo" x-model="motivo" required
                                        class="w-full mt-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                                    <option value="">— Selecciona —</option>
                                    @foreach(\App\Models\CajaChicaLog::MOTIVOS as $k => $label)
                                        <option value="{{ $k }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Evidencia (opcional)</label>
                                <input type="file" name="evidencia" accept="application/pdf,image/jpeg,image/png"
                                       class="w-full mt-1 text-xs">
                            </div>
                        </div>
                        <div x-show="motivo === 'otro'" x-cloak>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Especifica *</label>
                            <input type="text" name="motivo_personalizado" maxlength="100"
                                   class="w-full mt-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <button type="submit"
                                class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                            Confirmar rechazo
                        </button>
                    </form>
                @endif

                {{-- ─── PANEL: SUBIR FACTURA ─── --}}
                @if($puedeSubirFactura)
                    <form x-show="accion === 'factura'" x-cloak x-transition method="POST"
                          action="{{ route('gestor.caja-chica.factura', $vale) }}" enctype="multipart/form-data"
                          x-data="reauthForm('subir_factura', 'Subir factura del vale {{ $vale->folio }}', 'La factura es inmutable una vez cargada. Confirma con tu contraseña.')"
                          @submit.prevent="onSubmit($event)"
                          class="border-t dark:border-gray-700 pt-4 space-y-3">
                        @csrf
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-3 text-xs text-amber-700 dark:text-amber-300 inline-flex items-start gap-2 w-full">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>La factura solo se puede subir UNA vez. Asegúrate de elegir el archivo correcto.</span>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Factura digital *</label>
                            <input type="file" name="factura" accept="application/pdf,image/jpeg,image/png" required
                                   class="w-full mt-1 text-xs">
                            <p class="text-[10px] text-gray-500 mt-0.5">PDF, JPG o PNG. Máx 5 MB.</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Categoría *</label>
                            <select name="motivo" x-model="motivo" required
                                    class="w-full mt-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                                @foreach(\App\Models\CajaChicaLog::MOTIVOS as $k => $label)
                                    <option value="{{ $k }}" {{ $k === 'gasto_operativo' ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="motivo === 'otro'" x-cloak>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Especifica *</label>
                            <input type="text" name="motivo_personalizado" maxlength="100"
                                   class="w-full mt-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                            Confirmar carga
                        </button>
                    </form>
                @endif

                {{-- ─── PANEL: CANCELAR ─── --}}
                @if($puedeCancelar)
                    <form x-show="accion === 'cancelar'" x-cloak x-transition method="POST"
                          action="{{ route('gestor.caja-chica.cancelar', $vale) }}" enctype="multipart/form-data"
                          x-data="reauthForm('cancelar_vale', 'Cancelar vale {{ $vale->folio }}', '{{ in_array($vale->estatus, ['autorizada','comprobada']) ? 'El monto será devuelto al fondo. ' : '' }}Confirma con tu contraseña.')"
                          @submit.prevent="onSubmit($event)"
                          class="border-t dark:border-gray-700 pt-4 space-y-3">
                        @csrf
                        @if(in_array($vale->estatus, ['autorizada', 'comprobada']))
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-3 text-xs text-blue-700 dark:text-blue-300 inline-flex items-start gap-2 w-full">
                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Al cancelar este vale, ${{ number_format((float) $vale->monto, 2) }} se devolverán al fondo de Caja Chica.</span>
                            </div>
                        @endif
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Motivo *</label>
                                <select name="motivo" x-model="motivo" required
                                        class="w-full mt-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                                    <option value="">— Selecciona —</option>
                                    @foreach(\App\Models\CajaChicaLog::MOTIVOS as $k => $label)
                                        <option value="{{ $k }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Evidencia (opcional)</label>
                                <input type="file" name="evidencia" accept="application/pdf,image/jpeg,image/png"
                                       class="w-full mt-1 text-xs">
                            </div>
                        </div>
                        <div x-show="motivo === 'otro'" x-cloak>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Especifica *</label>
                            <input type="text" name="motivo_personalizado" maxlength="100"
                                   class="w-full mt-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <button type="submit"
                                class="bg-slate-600 hover:bg-slate-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                            Confirmar cancelación
                        </button>
                    </form>
                @endif
            </div>
        @endif

        {{-- ============= HISTORIAL DEL VALE ============= --}}
        @if($vale->logs->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-4">Historial de este vale</h3>
                <div class="space-y-3">
                    @foreach($vale->logs->sortByDesc('created_at') as $log)
                        <div class="border-l-2 border-[#0606F0] dark:border-blue-400 pl-4 py-1">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    {{ $log->accion_legible }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                                <strong>{{ $log->usuario?->name ?? '—' }}</strong> ·
                                {{ $log->motivo_legible }}
                                @if($log->monto_legible !== '—')
                                    · <span class="font-mono">{{ $log->monto_legible }}</span>
                                @endif
                            </p>
                            @if($log->evidencia_path)
                                <a href="{{ $log->evidencia_url }}" target="_blank"
                                   class="text-xs text-[#0606F0] dark:text-blue-400 hover:underline inline-flex items-center gap-1 mt-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    Ver evidencia
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-panel>

<script>
function reauthForm(action, title, description) {
    return {
        motivo: '',
        reauthOk: false,
        onSubmit(event) {
            if (this.reauthOk) {
                event.target.submit();
                return;
            }
            const self = this;
            window.dispatchEvent(new CustomEvent('reauth:open', {
                detail: {
                    action: action,
                    title: title,
                    description: description,
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
