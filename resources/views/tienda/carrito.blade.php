@php
    $user = auth()->user();
    $panelNombre = $user->hasRole('alumno') ? 'Panel Alumno' : 'Panel Docente';
    $navPartial = $user->hasRole('alumno') ? 'partials.alumno-nav' : 'partials.docente-nav';
@endphp

<x-panel title="Tu Carrito" :panelNombre="$panelNombre">
    <x-slot name="nav">@include($navPartial)</x-slot>

    <div class="max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Tu Carrito</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Revisa tus productos antes de generar el pedido.</p>
        </div>

        @if($items->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden">
                <div class="divide-y dark:divide-gray-700">
                    @foreach($items as $item)
                        <div class="flex items-center gap-4 p-4">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0">
                                @if($item->producto->imagen_principal)
                                    <img src="{{ Storage::url($item->producto->imagen_principal) }}" class="w-full h-full object-cover">
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-mono text-gray-400">{{ $item->variante->codigo_variante }}</p>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $item->producto->nombre }}</h3>
                                @if($item->variante->talla)
                                    <p class="text-xs text-gray-500">Talla {{ $item->variante->talla }}</p>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('tienda.carrito.actualizar', $item->id_variante) }}" class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <input type="number" name="cantidad" value="{{ $item->cantidad }}" min="0" max="10"
                                       onchange="this.form.submit()"
                                       class="w-16 border rounded-lg px-2 py-1.5 text-center text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            </form>

                            <div class="text-right min-w-[100px]">
                                <p class="text-[11px] text-gray-500">${{ number_format($item->precio, 2) }} c/u</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">${{ number_format($item->subtotal, 2) }}</p>
                            </div>

                            <form method="POST" action="{{ route('tienda.carrito.actualizar', $item->id_variante) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="cantidad" value="0">
                                <button type="submit" class="text-red-500 hover:text-red-700" title="Quitar">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div class="bg-gray-50 dark:bg-gray-900/30 px-4 py-4 flex items-center justify-between">
                    <form method="POST" action="{{ route('tienda.carrito.vaciar') }}"
                          data-udea-confirm
                          data-confirm-title="Vaciar carrito"
                          data-confirm-message="¿Vaciar todo el carrito?"
                          data-confirm-detail="Se eliminarán todos los productos seleccionados."
                          data-confirm-variant="warning"
                          data-confirm-icon="trash"
                          data-confirm-button="Vaciar"
                          data-confirm-cancel="Cancelar">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-red-600">Vaciar carrito</button>
                    </form>
                    <div class="text-right">
                        <p class="text-xs uppercase text-gray-500">Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($total, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('tienda.catalogo') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-lg text-sm font-medium">
                    Seguir comprando
                </a>
                <a href="{{ route('tienda.checkout') }}" class="bg-[#0606F0] hover:bg-[#04276B] text-white px-8 py-3 rounded-lg text-sm font-bold">
                    Generar pedido →
                </a>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-sm text-gray-400 mb-3">Tu carrito está vacío.</p>
                <a href="{{ route('tienda.catalogo') }}" class="inline-block text-sm text-[#0606F0] hover:underline font-semibold">Ver catálogo →</a>
            </div>
        @endif
    </div>
</x-panel>
