@php
    use App\Http\Controllers\Gestor\ProductosController;

    $tallasDisponibles = ProductosController::TALLAS_DISPONIBLES;
    $aceptaTallas = in_array($producto->categoria, ProductosController::CATEGORIAS_CON_TALLA, true);

    // Tallas ya registradas (para excluir del select de "Agregar variante")
    $tallasUsadas = $producto->variantes->pluck('talla')->filter()->values()->all();
    $tallasLibres = array_values(array_diff($tallasDisponibles, $tallasUsadas));

    $motivosPredefinidos = ProductosController::MOTIVOS_PREDEFINIDOS;
    $stockMaximo = ProductosController::STOCK_MAXIMO;
@endphp

<x-panel title="Detalle de Producto" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="max-w-5xl" x-data="{ editMode: false, mostrarFormVariante: false }">
        <a href="{{ route('gestor.productos.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Volver</a>

        {{-- Banner de modo edición --}}
        <div x-show="editMode" x-cloak
             class="mb-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-300 dark:border-amber-700 rounded-xl px-4 py-3 flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <div class="flex-1 text-xs text-amber-800 dark:text-amber-200">
                <strong>Modo edición activo.</strong> Todos los controles de stock, variantes y galería están desbloqueados.
            </div>
            <a href="{{ route('gestor.productos.edit', $producto) }}" class="text-xs text-amber-700 dark:text-amber-300 hover:underline font-semibold whitespace-nowrap">
                Editar datos básicos →
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- Imagen principal --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden aspect-square">
                    @if($producto->imagen_principal)
                        <img src="{{ Storage::url($producto->imagen_principal) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-50 dark:bg-gray-700">
                            <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-[10px] font-mono text-gray-400 uppercase">{{ $producto->codigo }}</p>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $producto->nombre }}</h2>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $producto->activo ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                            {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mt-4 mb-4 pb-4 border-b dark:border-gray-700">
                        <div>
                            <p class="text-[10px] uppercase text-gray-400">Precio</p>
                            <p class="text-2xl font-bold text-[#0606F0] dark:text-blue-400">${{ number_format($producto->precio, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-gray-400">Stock total</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $producto->variantes->sum('stock') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-gray-400">Categoría</p>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mt-1.5">{{ \App\Models\Producto::CATEGORIAS[$producto->categoria] ?? $producto->categoria }}</p>
                        </div>
                    </div>

                    @if($producto->descripcion)
                        <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $producto->descripcion }}</p>
                    @endif

                    {{-- Botón principal: toggle modo edición --}}
                    <div class="flex gap-2 mt-5">
                        <button type="button"
                                @click="editMode ? document.getElementById('form-cambios-batch').submit() : editMode = true"
                                :class="editMode
                                    ? 'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200'
                                    : 'bg-amber-500 hover:bg-amber-600 text-white'"
                                class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors inline-flex items-center gap-2">
                            <svg x-show="!editMode" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            <svg x-show="editMode" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-show="!editMode">Editar</span>
                            <span x-show="editMode" x-cloak>Salir y guardar</span>
                        </button>

                        @if($producto->activo)
                            <form x-show="editMode" x-cloak method="POST" action="{{ route('gestor.productos.destroy', $producto) }}"
                                  data-udea-confirm
                                  data-confirm-title="Desactivar producto"
                                  data-confirm-message="¿Desactivar el producto <strong>&quot;{{ $producto->nombre ?? 'seleccionado' }}&quot;</strong>?"
                                  data-confirm-detail="Dejará de aparecer en el catálogo público, pero el historial de pedidos se conserva."
                                  data-confirm-variant="warning"
                                  data-confirm-icon="ban"
                                  data-confirm-button="Desactivar"
                                  data-confirm-cancel="Cancelar">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-300 px-4 py-2 rounded-lg text-sm font-semibold inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    Desactivar producto
                                </button>
                            </form>
                        @else
                            <form x-show="editMode" x-cloak method="POST" action="{{ route('gestor.productos.reactivar', $producto) }}">
                                @csrf
                                <button type="submit" class="bg-green-50 hover:bg-green-100 text-green-700 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-300 px-4 py-2 rounded-lg text-sm font-semibold inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Activar producto
                                </button>
                            </form>
                        @endif

                        {{-- Eliminar permanentemente: requiere typed confirmation con código del producto --}}
                        <form id="form-eliminar-perm-{{ $producto->id_producto ?? $producto->id }}" x-show="editMode" x-cloak method="POST" action="{{ route('gestor.productos.eliminar-permanente', $producto) }}"
                              onsubmit="event.preventDefault(); confirmarEliminacionPermanente('{{ $producto->codigo }}', '{{ addslashes($producto->nombre) }}', this); return false;">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold inline-flex items-center gap-2 ml-auto"
                                    title="Eliminar el producto y todo su rastro (variantes, imágenes, movimientos)">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                </svg>
                                Eliminar permanentemente
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────
             VARIANTES Y STOCK
             ───────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Stock por variante</h3>
                <button type="button"
                        @click="mostrarFormVariante = !mostrarFormVariante"
                        x-show="editMode && {{ $aceptaTallas ? (count($tallasLibres) > 0 ? 'true' : 'false') : ($producto->variantes->isEmpty() ? 'true' : 'false') }}"
                        x-cloak
                        class="text-xs font-semibold text-[#0606F0] dark:text-blue-400 hover:underline">
                    <span x-show="!mostrarFormVariante">+ Agregar variante</span>
                    <span x-show="mostrarFormVariante" x-cloak>Cancelar</span>
                </button>
            </div>

            {{-- Aviso si NO hay tallas libres --}}
            @if($aceptaTallas && count($tallasLibres) === 0)
                <div x-show="editMode" x-cloak class="mb-3 text-[11px] text-gray-500 dark:text-gray-400 italic">
                    Todas las tallas disponibles ({{ implode(', ', $tallasDisponibles) }}) ya están registradas. Para modificar el inventario usa los botones <strong>Sumar</strong>/<strong>Restar</strong> sobre cada variante.
                </div>
            @endif

            {{-- Form: agregar variante --}}
            <div x-show="editMode && mostrarFormVariante" x-cloak
                 class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                <form method="POST" action="{{ route('gestor.productos.variantes.agregar', $producto) }}" class="grid grid-cols-12 gap-3 items-end">
                    @csrf
                    @if($aceptaTallas)
                        <div class="col-span-3">
                            <label class="block text-[11px] font-semibold text-gray-700 dark:text-gray-300 mb-1">Talla *</label>
                            <select name="talla" required class="w-full border rounded-lg px-2 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                <option value="">Seleccionar...</option>
                                @foreach($tallasLibres as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1">Solo aparecen tallas no registradas.</p>
                        </div>
                    @else
                        <div class="col-span-3 text-xs text-gray-500 italic pt-6">
                            Producto sin tallas (variante única)
                        </div>
                    @endif
                    <div class="col-span-3">
                        <label class="block text-[11px] font-semibold text-gray-700 dark:text-gray-300 mb-1">Stock inicial *</label>
                        <input type="number" name="stock" min="0" max="9999" value="0" required
                               class="w-full border rounded-lg px-2 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    </div>
                    <div class="col-span-3">
                        <label class="block text-[11px] font-semibold text-gray-700 dark:text-gray-300 mb-1">Stock mínimo</label>
                        <input type="number" name="stock_minimo" min="0" max="9999" value="3"
                               class="w-full border rounded-lg px-2 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    </div>
                    <div class="col-span-3 flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold">Agregar variante</button>
                    </div>
                </form>
            </div>

            {{-- Form batch que envuelve solo la tabla.
                 Se submite al presionar "Salir y guardar" (botón de header). --}}
            <form id="form-cambios-batch" method="POST" action="{{ route('gestor.productos.variantes.guardar-cambios', $producto) }}">
                @csrf
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                        <tr>
                            <th class="text-left py-2">Código</th>
                            <th class="text-left py-2">Talla</th>
                            <th class="text-center py-2">Stock</th>
                            <th class="text-center py-2">Mínimo</th>
                            <th class="text-center py-2">Estado</th>
                            <th class="text-center py-2">Acciones</th>
                        </tr>
                    </thead>

                    {{-- Una <tbody> por variante (scope Alpine local) --}}
                    @foreach($producto->variantes as $v)
                        <tbody x-data="{
                            stockNuevo: {{ (int) $v->stock }},
                            stockOriginal: {{ (int) $v->stock }},
                            minimoNuevo: {{ (int) $v->stock_minimo }},
                            minimoOriginal: {{ (int) $v->stock_minimo }},
                            disponible: {{ $v->disponible ? 'true' : 'false' }},
                            disponibleOriginal: {{ $v->disponible ? 'true' : 'false' }},
                            motivo: '',
                            motivoOpen: false,
                            motivoCustom: false,
                            popoverY: 0,
                            popoverX: 0,
                            get cambioStock() { return this.stockNuevo !== this.stockOriginal; },
                            get cambioMinimo() { return this.minimoNuevo !== this.minimoOriginal; },
                            get cambioEstado() { return this.disponible !== this.disponibleOriginal; },
                            get diff() { return this.stockNuevo - this.stockOriginal; },
                            abrirPopover(e) {
                                if (this.motivoOpen) { this.motivoOpen = false; return; }
                                const rect = e.currentTarget.getBoundingClientRect();
                                const anchoPopover = 256;
                                this.popoverY = rect.bottom + 6;
                                this.popoverX = Math.max(8, Math.min(rect.right - anchoPopover, window.innerWidth - anchoPopover - 8));
                                this.motivoOpen = true;
                            },
                            seleccionarMotivo(m) {
                                this.motivo = m;
                                this.motivoOpen = false;
                                this.motivoCustom = false;
                            }
                        }" class="border-b dark:border-gray-700 last:border-0">
                            <tr>
                                {{-- Inputs ocultos que viajan en el batch --}}
                                <input type="hidden" name="cambios[{{ $v->id_variante }}][stock]" :value="stockNuevo">
                                <input type="hidden" name="cambios[{{ $v->id_variante }}][stock_minimo]" :value="minimoNuevo">
                                <input type="hidden" name="cambios[{{ $v->id_variante }}][disponible]" :value="disponible ? 1 : 0">
                                <input type="hidden" name="cambios[{{ $v->id_variante }}][motivo]" :value="motivo">

                                <td class="py-2 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $v->codigo_variante }}</td>
                                <td class="py-2 text-gray-800 dark:text-gray-200">{{ $v->talla ?: '—' }}</td>

                                {{-- STOCK editable inline en modo edición --}}
                                <td class="py-2 text-center">
                                    <span x-show="!editMode" class="font-bold text-gray-900 dark:text-gray-100">{{ $v->stock }}</span>
                                    <input x-show="editMode" x-cloak type="number"
                                           x-model.number="stockNuevo"
                                           min="0" max="{{ $stockMaximo }}"
                                           :class="cambioStock
                                               ? 'border-amber-400 ring-2 ring-amber-200 dark:ring-amber-800/40 font-bold'
                                               : 'border-gray-300 dark:border-gray-600'"
                                           class="w-20 text-center font-bold rounded-lg px-2 py-1 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:bg-gray-700 dark:text-gray-200">
                                </td>

                                {{-- STOCK MÍNIMO — mismo diseño que Stock, editable directo en modo edición --}}
                                <td class="py-2 text-center">
                                    <span x-show="!editMode" class="text-gray-500">{{ $v->stock_minimo }}</span>
                                    <input x-show="editMode" x-cloak type="number"
                                           x-model.number="minimoNuevo"
                                           min="0" max="9999"
                                           :class="cambioMinimo
                                               ? 'border-amber-400 ring-2 ring-amber-200 dark:ring-amber-800/40 font-bold'
                                               : 'border-gray-300 dark:border-gray-600'"
                                           class="w-20 text-center font-bold rounded-lg px-2 py-1 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none dark:bg-gray-700 dark:text-gray-200">
                                </td>

                                {{-- ESTADO + toggle disponible --}}
                                <td class="py-2 text-center">
                                    @php
                                        $colorMapView = [
                                            'gray'  => ['bg-gray-100','text-gray-700','dark:bg-gray-700','dark:text-gray-300'],
                                            'red'   => ['bg-red-100','text-red-700','dark:bg-red-900/30','dark:text-red-300'],
                                            'amber' => ['bg-amber-100','text-amber-700','dark:bg-amber-900/30','dark:text-amber-300'],
                                            'green' => ['bg-green-100','text-green-700','dark:bg-green-900/30','dark:text-green-300'],
                                        ];
                                    @endphp
                                    {{-- En modo view: badge calculado del modelo --}}
                                    <span x-show="!editMode"
                                          class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ implode(' ', $colorMapView[$v->estado_color] ?? $colorMapView['gray']) }}">
                                        {{ $v->estado_label }}
                                    </span>

                                    {{-- En modo edición: muestra el estado actual + ícono toggle --}}
                                    <span x-show="editMode" x-cloak class="inline-flex items-center gap-1.5">
                                        <span :class="disponible
                                                ? '{{ implode(' ', $colorMapView['green']) }}'
                                                : '{{ implode(' ', $colorMapView['gray']) }}'"
                                              class="px-2 py-0.5 rounded-full text-[10px] font-semibold">
                                            <span x-text="disponible ? 'Disponible' : 'No disponible'"></span>
                                        </span>
                                        <button type="button" @click="disponible = !disponible"
                                                :title="disponible ? 'Cambiar a No disponible' : 'Cambiar a Disponible'"
                                                class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    </span>
                                </td>

                                {{-- ACCIONES: Motivo (opcional, se activa al cambiar stock) + Eliminar --}}
                                <td class="py-2 text-center">
                                    <div class="inline-flex gap-1 items-center">
                                        {{-- Botón Motivo --}}
                                        <button type="button"
                                                @click="abrirPopover($event)"
                                                :disabled="!editMode || !cambioStock"
                                                :class="(editMode && cambioStock)
                                                    ? (motivo ? 'bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-300' : 'bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:hover:bg-amber-900/50 dark:text-amber-300')
                                                    : 'bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500 cursor-not-allowed'"
                                                class="px-2 py-1 rounded text-xs font-semibold inline-flex items-center gap-1 transition-colors"
                                                :title="!editMode ? 'Activa el modo edición' : (!cambioStock ? 'Modifica el stock primero' : 'Motivo del movimiento (opcional)')">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span x-show="!motivo">Motivo</span>
                                            <span x-show="motivo" x-text="motivo.length > 18 ? motivo.slice(0, 18) + '…' : motivo"></span>
                                        </button>

                                        {{-- Popover Motivo — teleported a <body> para no quedar atrapado por overflow --}}
                                        <template x-teleport="body">
                                            <div x-show="motivoOpen" x-cloak x-transition.opacity.duration.150ms
                                                 @click.outside="motivoOpen = false"
                                                 @keydown.escape.window="motivoOpen = false"
                                                 @scroll.window="motivoOpen = false"
                                                 @resize.window="motivoOpen = false"
                                                 :style="`top: ${popoverY}px; left: ${popoverX}px;`"
                                                 class="fixed w-64 bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 z-50 p-2">
                                                <p class="text-[10px] uppercase text-gray-400 px-2 py-1 font-semibold">Selecciona un motivo (opcional)</p>
                                                @foreach($motivosPredefinidos as $m)
                                                    <button type="button" @click="seleccionarMotivo('{{ $m }}')"
                                                            class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                        {{ $m }}
                                                    </button>
                                                @endforeach
                                                <hr class="my-1 border-gray-100 dark:border-gray-700">
                                                <button type="button" @click="motivoCustom = true"
                                                        class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-[#0606F0] dark:text-blue-400 font-semibold">
                                                    + Agregar comentario
                                                </button>
                                                <div x-show="motivoCustom" x-cloak class="mt-2 px-1">
                                                    <input type="text" x-model="motivo" maxlength="200" placeholder="Escribe el motivo..."
                                                           class="w-full border rounded px-2 py-1 text-xs dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                                                           @keydown.enter.prevent="motivoOpen = false">
                                                    <button type="button" @click="motivoOpen = false"
                                                            class="mt-1 w-full bg-[#0606F0] text-white text-xs py-1 rounded">OK</button>
                                                </div>
                                                <button type="button" @click="motivo = ''; motivoOpen = false; motivoCustom = false"
                                                        class="w-full text-left px-2 py-1.5 text-xs rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 mt-1 border-t border-gray-100 dark:border-gray-700 pt-2"
                                                        x-show="motivo">
                                                    × Quitar motivo
                                                </button>
                                            </div>
                                        </template>

                                        {{-- Eliminar variante (form externo) --}}
                                        <button type="submit" form="form-del-{{ $v->id_variante }}"
                                                x-show="editMode" x-cloak
                                                class="px-2 py-1 rounded text-xs font-semibold bg-gray-50 hover:bg-gray-100 text-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300" title="Eliminar variante">🗑</button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Hint: cuando hay cambio de stock pero no motivo --}}
                            <tr x-show="editMode && cambioStock" x-cloak>
                                <td colspan="6" class="px-2 pb-2">
                                    <div class="text-[10px] text-amber-700 dark:text-amber-400 inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>
                                            Cambio detectado:
                                            <strong x-text="diff > 0 ? '+' + diff : diff"></strong>
                                            unidades. Agrega un motivo si quieres (opcional).
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @endforeach
                </table>
            </form>

            {{-- Forms auxiliares fuera del form-cambios-batch para no anidar.
                 (El stock_minimo se guarda dentro del batch, ya no requiere form aparte). --}}
            @foreach($producto->variantes as $v)
                <form id="form-del-{{ $v->id_variante }}" method="POST" action="{{ route('gestor.productos.variantes.eliminar', $v) }}" class="hidden"
                      data-udea-confirm
                      data-confirm-title="Eliminar variante"
                      data-confirm-message="¿Eliminar la variante <strong>&quot;{{ $v->codigo_variante }}&quot;</strong>?"
                      data-confirm-detail="Solo se puede eliminar si no tiene pedidos asociados. Esta acción no se puede deshacer."
                      data-confirm-variant="danger"
                      data-confirm-icon="trash"
                      data-confirm-button="Eliminar"
                      data-confirm-cancel="Cancelar">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        </div>

        {{-- ─────────────────────────────────────────────────
             GALERÍA
             ───────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">Galería ({{ $producto->imagenes->count() }})</h3>

            <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                @foreach($producto->imagenes as $img)
                    <div class="relative group aspect-square">
                        <img src="{{ Storage::url($img->archivo_path) }}" alt="" class="w-full h-full object-cover rounded-lg border dark:border-gray-700">
                        <form x-show="editMode" x-cloak method="POST" action="{{ route('gestor.productos.imagenes.destroy', $img) }}"
                              data-udea-confirm
                              data-confirm-title="Eliminar imagen"
                              data-confirm-message="¿Eliminar esta imagen de la galería?"
                              data-confirm-detail="Esta acción no se puede deshacer."
                              data-confirm-variant="danger"
                              data-confirm-icon="trash"
                              data-confirm-button="Eliminar"
                              data-confirm-cancel="Cancelar"
                              class="absolute top-1 right-1">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-md">×</button>
                        </form>
                    </div>
                @endforeach

                {{-- Dropzone — visible solo en modo edición --}}
                <div x-show="editMode" x-cloak
                     x-data="{ dragOver: false }"
                     @dragover.prevent="dragOver = true"
                     @dragleave.prevent="dragOver = false"
                     @drop.prevent="dragOver = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.uploadForm.submit()"
                     :class="dragOver ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-blue-400'"
                     class="aspect-square rounded-lg border-2 border-dashed flex items-center justify-center cursor-pointer transition-colors"
                     @click="$refs.fileInput.click()">
                    <form method="POST" action="{{ route('gestor.productos.imagenes.agregar', $producto) }}" enctype="multipart/form-data" x-ref="uploadForm">
                        @csrf
                        <input type="file" name="imagenes[]" accept="image/*" multiple class="hidden" x-ref="fileInput"
                               @change="$refs.uploadForm.submit()">
                    </form>
                    <div class="text-center px-2 pointer-events-none">
                        <svg class="w-8 h-8 mx-auto text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-[10px] text-gray-500 leading-tight">
                            Selecciona la imagen o arrástrala para agregarla a la galería de imágenes
                        </p>
                    </div>
                </div>
            </div>

            @if($producto->imagenes->isEmpty())
                <p x-show="!editMode" x-cloak class="text-sm text-gray-400 text-center py-6">Sin imágenes en galería.</p>
            @endif
        </div>

        {{-- ─────────────────────────────────────────────────
             MOVIMIENTOS DE INVENTARIO (bitácora)
             ───────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">Últimos movimientos de inventario</h3>
            @if($movimientos->isNotEmpty())
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                        <tr>
                            <th class="text-left py-2">Fecha</th>
                            <th class="text-left py-2">Tipo</th>
                            <th class="text-left py-2">Variante</th>
                            <th class="text-center py-2">Cantidad</th>
                            <th class="text-center py-2">Stock resultante</th>
                            <th class="text-left py-2">Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $m)
                            @php $tc = \App\Models\MovimientoInventario::TIPOS[$m->tipo] ?? ['label' => $m->tipo, 'color' => 'gray']; @endphp
                            <tr class="border-b dark:border-gray-700 last:border-0">
                                <td class="py-2 text-xs text-gray-500">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-{{ $tc['color'] }}-100 text-{{ $tc['color'] }}-700 dark:bg-{{ $tc['color'] }}-900/30 dark:text-{{ $tc['color'] }}-300">
                                        {{ $tc['label'] }}
                                    </span>
                                </td>
                                <td class="py-2 text-gray-700 dark:text-gray-300 text-xs">{{ $m->variante?->codigo_variante }}</td>
                                <td class="py-2 text-center font-semibold {{ $m->cantidad > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $m->cantidad > 0 ? '+' : '' }}{{ $m->cantidad }}
                                </td>
                                <td class="py-2 text-center text-gray-600 dark:text-gray-400">{{ $m->stock_resultante }}</td>
                                <td class="py-2 text-xs text-gray-500 truncate">{{ $m->motivo }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-400 text-center py-6">Aún no hay movimientos registrados.</p>
            @endif
        </div>
    </div>
</x-panel>

<script>
/**
 * Doble confirmación para eliminación permanente:
 *  1) Confirm de advertencia.
 *  2) Prompt pidiendo escribir el código del producto exactamente.
 * Solo si ambos pasos coinciden se permite el submit.
 */
async function confirmarEliminacionPermanente(codigo, nombre, form) {
    const detalle =
        `Se eliminará permanentemente "${nombre}" junto con: todas sus variantes y stock, todas las imágenes (principal y galería) y todo el historial de movimientos de inventario. Si el producto tiene pedidos asociados, la operación será bloqueada.`;

    const ok = await udeaConfirm({
        title: 'Eliminar producto permanentemente',
        message: '⚠ Esta acción es <strong>IRREVERSIBLE</strong>. ¿Continuar?',
        detail: detalle,
        variant: 'danger',
        icon: 'warning',
        confirmText: 'Continuar',
        cancelText: 'Cancelar',
    });
    if (!ok) return;

    const respuesta = prompt(`Para confirmar, escribe el código del producto: ${codigo}`);
    if (respuesta === null) return;

    if (respuesta.trim().toUpperCase() !== codigo.toUpperCase()) {
        alert(`El código no coincide. Esperado: "${codigo}". Operación cancelada.`);
        return;
    }
    // Permitir el envío real esta vez
    form.onsubmit = null;
    if (typeof form.requestSubmit === 'function') form.requestSubmit();
    else form.submit();
}
</script>
