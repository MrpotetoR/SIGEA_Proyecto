@php $color = $pedido->estado_color; @endphp

<x-panel title="Pedido {{ $pedido->folio }}" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="max-w-5xl">
        <a href="{{ route('gestor.pedidos.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Volver a pedidos</a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Info del pedido --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-[11px] uppercase text-gray-400">Folio</p>
                            <h2 class="text-2xl font-mono font-bold text-gray-900 dark:text-gray-100">{{ $pedido->folio }}</h2>
                        </div>
                        <span class="px-3 py-1.5 rounded-full text-sm font-bold bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-300">
                            {{ $pedido->estado_label }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pb-4 border-b dark:border-gray-700">
                        <div>
                            <p class="text-[11px] uppercase text-gray-400">Cliente</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $pedido->usuario->name }}</p>
                            <p class="text-xs text-gray-500">{{ $pedido->usuario->email }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase text-gray-400">Fecha de pedido</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $pedido->created_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $pedido->created_at->format('H:i') }}</p>
                        </div>
                    </div>

                    <h3 class="text-xs uppercase text-gray-500 font-semibold mt-4 mb-2">Productos</h3>
                    <div class="divide-y dark:divide-gray-700">
                        @foreach($pedido->items as $item)
                            <div class="flex items-center gap-4 py-3">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->descripcion }}</p>
                                    <p class="text-xs text-gray-500 font-mono">{{ $item->codigo_snapshot }}</p>
                                </div>
                                <p class="text-xs text-gray-500">{{ $item->cantidad }} × ${{ number_format($item->precio_unitario, 2) }}</p>
                                <p class="text-sm font-bold w-24 text-right">${{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-4 border-t dark:border-gray-700">
                        <span class="text-sm uppercase font-semibold text-gray-700 dark:text-gray-300">Total</span>
                        <span class="text-2xl font-bold text-[#0606F0] dark:text-blue-400">${{ number_format($pedido->total, 2) }}</span>
                    </div>
                </div>

                {{-- Váucher --}}
                @if($pedido->vaucher_path)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">Váucher de pago</h3>
                        <p class="text-xs text-gray-500 mb-3">Subido el {{ $pedido->vaucher_subido_en?->format('d/m/Y H:i') }}</p>
                        @php $ext = strtolower(pathinfo($pedido->vaucher_path, PATHINFO_EXTENSION)); @endphp
                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                            <a href="{{ Storage::url($pedido->vaucher_path) }}" target="_blank">
                                <img src="{{ Storage::url($pedido->vaucher_path) }}" alt="Váucher" class="max-w-md rounded-lg border dark:border-gray-700 hover:opacity-90 transition-opacity cursor-zoom-in">
                            </a>
                        @else
                            <a href="{{ Storage::url($pedido->vaucher_path) }}" target="_blank"
                               class="inline-flex items-center gap-2 bg-red-50 hover:bg-red-100 text-red-700 dark:bg-red-900/20 dark:hover:bg-red-900/40 dark:text-red-300 px-4 py-2.5 rounded-lg text-sm font-semibold">
                                📄 Ver váucher en PDF
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">Acciones disponibles</h3>

                    @if($pedido->estado === 'vaucher_enviado')
                        <form method="POST" action="{{ route('gestor.pedidos.aprobar', $pedido) }}" class="mb-3">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold">
                                ✓ Aprobar pago
                            </button>
                        </form>

                        <details class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3">
                            <summary class="text-xs font-semibold text-red-700 dark:text-red-300 cursor-pointer">✗ Rechazar váucher</summary>
                            <form method="POST" action="{{ route('gestor.pedidos.rechazar', $pedido) }}" class="mt-3 space-y-2">
                                @csrf
                                <textarea name="motivo" required rows="3" minlength="10" maxlength="500" placeholder="Motivo del rechazo (mínimo 10 caracteres)..."
                                          class="w-full border rounded-lg px-3 py-2 text-xs dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"></textarea>
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-xs font-bold">Rechazar y notificar al alumno</button>
                            </form>
                        </details>
                    @elseif($pedido->estado === 'aprobado')
                        <form method="POST" action="{{ route('gestor.pedidos.listo', $pedido) }}"
                              data-udea-confirm
                              data-confirm-title="Marcar como listo"
                              data-confirm-message="¿El producto está preparado para entregarse?"
                              data-confirm-detail="Se enviará un correo automático al alumno avisándole que puede pasar a recogerlo."
                              data-confirm-variant="success"
                              data-confirm-icon="check"
                              data-confirm-button="Sí, está listo"
                              data-confirm-cancel="Aún no">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold">
                                ✉ Marcar como listo para recoger
                            </button>
                        </form>
                        <p class="text-[10px] text-gray-400 mt-2">Esto envía un correo automático al alumno.</p>
                    @elseif($pedido->estado === 'listo_recoger')
                        <form method="POST" action="{{ route('gestor.pedidos.entregar', $pedido) }}"
                              data-udea-confirm
                              data-confirm-title="Confirmar entrega"
                              data-confirm-message="¿Confirmar la entrega del pedido al alumno?"
                              data-confirm-detail="El pedido quedará marcado como entregado y cerrado."
                              data-confirm-variant="success"
                              data-confirm-icon="check"
                              data-confirm-button="Confirmar entrega"
                              data-confirm-cancel="Cancelar">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold">
                                📦 Marcar como entregado
                            </button>
                        </form>
                    @elseif(in_array($pedido->estado, ['pendiente_pago'], true))
                        <p class="text-xs text-gray-500 mb-3">Esperando que el alumno suba el váucher de pago.</p>
                    @endif

                    {{-- Cancelar (disponible en estados no terminales) --}}
                    @if(in_array($pedido->estado, ['pendiente_pago', 'vaucher_enviado', 'aprobado', 'listo_recoger'], true))
                        <details class="mt-4 pt-4 border-t dark:border-gray-700">
                            <summary class="text-xs text-red-600 cursor-pointer hover:underline">Cancelar pedido</summary>
                            <form method="POST" action="{{ route('gestor.pedidos.cancelar', $pedido) }}" class="mt-3 space-y-2"
                                  data-udea-confirm
                                  data-confirm-title="Cancelar pedido"
                                  data-confirm-message="¿Cancelar este pedido?"
                                  data-confirm-detail="El stock reservado se liberará nuevamente al inventario."
                                  data-confirm-variant="warning"
                                  data-confirm-icon="x-circle"
                                  data-confirm-button="Sí, cancelar"
                                  data-confirm-cancel="No, mantener">
                                @csrf
                                <input type="text" name="motivo" placeholder="Motivo (opcional)"
                                       class="w-full border rounded-lg px-3 py-1.5 text-xs dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold">Cancelar pedido</button>
                            </form>
                        </details>
                    @endif
                </div>

                @if($pedido->revisor)
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 text-xs">
                        <p class="text-[10px] uppercase text-gray-500 mb-1">Revisado por</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $pedido->revisor->name }}</p>
                        <p class="text-gray-500">{{ $pedido->revisado_en?->format('d/m/Y H:i') }}</p>
                    </div>
                @endif

                @if($pedido->fecha_listo_recoger)
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-xs">
                        <p class="text-[10px] uppercase text-green-700 mb-1">Listo desde</p>
                        <p class="font-semibold text-green-800 dark:text-green-200">{{ $pedido->fecha_listo_recoger->format('d/m/Y H:i') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Timeline --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6 mt-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Historial</h3>
            <div class="space-y-3">
                @foreach($pedido->historial as $h)
                    @php $hc = \App\Models\Pedido::ESTADOS[$h->estado_nuevo]['color'] ?? 'gray'; $hl = \App\Models\Pedido::ESTADOS[$h->estado_nuevo]['label'] ?? $h->estado_nuevo; @endphp
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-2 h-2 rounded-full bg-{{ $hc }}-500 mt-1.5"></div>
                        <div class="flex-1 text-xs">
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $hl }}</p>
                            <p class="text-gray-500">{{ $h->created_at->format('d/m/Y H:i') }} · {{ $h->usuario?->name ?? 'Sistema' }}</p>
                            @if($h->comentario)<p class="text-gray-600 dark:text-gray-400 mt-1">{{ $h->comentario }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-panel>
