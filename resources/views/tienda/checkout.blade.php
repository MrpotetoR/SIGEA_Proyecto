@php
    $user = auth()->user();
    $panelNombre = $user->hasRole('alumno') ? 'Panel Alumno' : 'Panel Docente';
    $navPartial = $user->hasRole('alumno') ? 'partials.alumno-nav' : 'partials.docente-nav';
@endphp

<x-panel title="Confirmar Pedido" :panelNombre="$panelNombre">
    <x-slot name="nav">@include($navPartial)</x-slot>

    <div class="max-w-4xl">
        <a href="{{ route('tienda.carrito') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Volver al carrito</a>

        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-1">Confirmar Pedido</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Revisa los productos y los datos de pago antes de generar tu pedido.</p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Resumen --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Productos ({{ $items->count() }})</h3>
                    <div class="divide-y dark:divide-gray-700">
                        @foreach($items as $item)
                            <div class="flex items-center gap-4 py-3 first:pt-0 last:pb-0">
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($item->producto->imagen_principal)
                                        <img src="{{ Storage::url($item->producto->imagen_principal) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $item->producto->nombre }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if($item->variante->talla) Talla {{ $item->variante->talla }} · @endif
                                        Cantidad: {{ $item->cantidad }}
                                    </p>
                                </div>
                                <p class="text-sm font-bold">${{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t dark:border-gray-700 flex items-center justify-between">
                        <span class="text-sm font-semibold uppercase text-gray-700 dark:text-gray-300">Total</span>
                        <span class="text-2xl font-bold text-[#0606F0] dark:text-blue-400">${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                {{-- Datos bancarios --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Datos para realizar el pago
                    </h3>

                    @if($cuenta)
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-[11px] uppercase text-blue-700 dark:text-blue-400">Banco</p>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $cuenta['banco'] }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase text-blue-700 dark:text-blue-400">Titular</p>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $cuenta['titular'] }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase text-blue-700 dark:text-blue-400">Número de cuenta</p>
                                <p class="font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $cuenta['numero'] }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase text-blue-700 dark:text-blue-400">CLABE (SPEI)</p>
                                <p class="font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $cuenta['clabe'] }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-red-600">La cuenta bancaria no está configurada. Contacta al administrador.</p>
                    @endif

                    @if($instrucciones)
                        <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-800/50">
                            <p class="text-xs text-blue-800 dark:text-blue-300 whitespace-pre-line">{{ $instrucciones }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Entrega + confirmar --}}
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Punto de entrega
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">{{ $ubicacion ?? 'Oficinas de Gestor Escolar' }}</p>
                    <p class="text-[11px] text-gray-500 mt-2"><strong>Horario:</strong> {{ $horario ?? 'Lunes a Viernes' }}</p>
                </div>

                <form method="POST" action="{{ route('tienda.pedido.confirmar') }}">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-xl text-sm font-bold shadow-md">
                        Confirmar y generar pedido
                    </button>
                </form>
                <p class="text-[10px] text-gray-400 text-center">
                    Al generar el pedido tu stock queda reservado. Luego subirás el váucher para validar el pago.
                </p>
            </div>
        </div>
    </div>
</x-panel>
