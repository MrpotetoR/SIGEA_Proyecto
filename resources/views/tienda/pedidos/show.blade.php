@php
    $user = auth()->user();
    $panelNombre = $user->hasRole('alumno') ? 'Panel Alumno' : 'Panel Docente';
    $navPartial = $user->hasRole('alumno') ? 'partials.alumno-nav' : 'partials.docente-nav';
    $color = $pedido->estado_color;
@endphp

<x-panel title="Pedido {{ $pedido->folio }}" :panelNombre="$panelNombre">
    <x-slot name="nav">@include($navPartial)</x-slot>

    <div class="max-w-5xl">
        <a href="{{ route('tienda.pedidos') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline mb-4 inline-block">← Mis pedidos</a>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-[11px] uppercase text-gray-400">Folio</p>
                    <h2 class="text-2xl font-mono font-bold text-gray-900 dark:text-gray-100">{{ $pedido->folio }}</h2>
                    <p class="text-xs text-gray-500 mt-1">Creado el {{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <span class="px-3 py-1.5 rounded-full text-sm font-bold bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-300">
                    {{ $pedido->estado_label }}
                </span>
            </div>

            {{-- Items --}}
            <div class="divide-y dark:divide-gray-700 border-t border-b dark:border-gray-700 py-2">
                @foreach($pedido->items as $item)
                    <div class="flex items-center gap-4 py-3">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->descripcion }}</p>
                            <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $item->codigo_snapshot }}</p>
                        </div>
                        <p class="text-xs text-gray-500">{{ $item->cantidad }} × ${{ number_format($item->precio_unitario, 2) }}</p>
                        <p class="text-sm font-bold w-24 text-right">${{ number_format($item->subtotal, 2) }}</p>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between mt-4">
                <span class="text-sm uppercase font-semibold text-gray-700 dark:text-gray-300">Total</span>
                <span class="text-2xl font-bold text-[#0606F0] dark:text-blue-400">${{ number_format($pedido->total, 2) }}</span>
            </div>

            @if($pedido->estado === 'listo_recoger' || $pedido->estado === 'aprobado' || $pedido->estado === 'entregado')
                <div class="mt-5 pt-5 border-t dark:border-gray-700">
                    <p class="text-[11px] uppercase text-gray-400 mb-2">Recoger en</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $ubicacion }}</p>
                    <p class="text-xs text-gray-500 mt-1"><strong>Horario:</strong> {{ $horario }}</p>
                </div>
            @endif
        </div>

        {{-- Acciones según estado --}}
        @if(in_array($pedido->estado, ['pendiente_pago', 'vaucher_enviado'], true))
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-6 mb-6">
                @if($pedido->estado === 'pendiente_pago' && $pedido->motivo_rechazo)
                    <div class="mb-4 p-3 rounded-lg bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
                        <p class="text-[11px] uppercase text-red-700 dark:text-red-300 font-semibold mb-1">Váucher rechazado</p>
                        <p class="text-sm text-red-800 dark:text-red-200">{{ $pedido->motivo_rechazo }}</p>
                    </div>
                @endif

                <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-300 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ $pedido->estado === 'vaucher_enviado' ? 'Váucher en revisión' : 'Sube tu váucher de pago' }}
                </h3>

                {{-- Datos bancarios --}}
                @if($cuenta && $pedido->estado === 'pendiente_pago')
                    <div class="grid grid-cols-2 gap-3 text-sm mb-4 p-4 rounded-lg bg-white dark:bg-gray-800">
                        <div>
                            <p class="text-[10px] uppercase text-gray-500">Banco</p>
                            <p class="font-semibold">{{ $cuenta['banco'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-gray-500">CLABE</p>
                            <p class="font-mono font-semibold">{{ $cuenta['clabe'] }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] uppercase text-gray-500">Monto a transferir</p>
                            <p class="text-lg font-bold text-[#0606F0]">${{ number_format($pedido->total, 2) }}</p>
                        </div>
                    </div>
                @endif

                @if($pedido->estado === 'vaucher_enviado')
                    <p class="text-sm text-amber-800 dark:text-amber-200">
                        Tu váucher fue recibido y está siendo revisado por el Gestor Escolar.
                        Recibirás una notificación cuando se valide tu pago.
                    </p>
                    @if($pedido->vaucher_path)
                        <a href="{{ Storage::url($pedido->vaucher_path) }}" target="_blank"
                           class="inline-block mt-3 text-xs text-amber-700 dark:text-amber-300 hover:underline font-semibold">
                            Ver váucher enviado →
                        </a>
                    @endif
                @else
                    <form method="POST" action="{{ route('tienda.pedido.vaucher', $pedido) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="file" name="vaucher" accept="application/pdf,image/*" required
                               class="block w-full text-xs text-gray-700 dark:text-gray-300
                                      file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                      file:text-xs file:font-bold file:bg-amber-600 file:text-white
                                      hover:file:bg-amber-700 cursor-pointer">
                        @error('vaucher')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold">
                            Enviar váucher
                        </button>
                    </form>
                @endif

                @if(in_array($pedido->estado, ['pendiente_pago', 'vaucher_enviado'], true))
                    <form method="POST" action="{{ route('tienda.pedido.cancelar', $pedido) }}"
                          data-udea-confirm
                          data-confirm-title="Cancelar pedido"
                          data-confirm-message="¿Cancelar este pedido?"
                          data-confirm-detail="El stock reservado se liberará nuevamente."
                          data-confirm-variant="warning"
                          data-confirm-icon="x-circle"
                          data-confirm-button="Sí, cancelar"
                          data-confirm-cancel="No, mantener"
                          class="mt-4 pt-4 border-t border-amber-200 dark:border-amber-800/50">
                        @csrf
                        <button type="submit" class="text-xs text-red-600 hover:underline">Cancelar pedido</button>
                    </form>
                @endif
            </div>
        @endif

        {{-- Comprobante PDF --}}
        @if(in_array($pedido->estado, ['aprobado', 'listo_recoger', 'entregado'], true))
            <a href="{{ route('tienda.pedido.comprobante', $pedido) }}" target="_blank"
               class="inline-flex items-center gap-2 mb-6 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-700 dark:text-red-300 px-4 py-2.5 rounded-lg text-sm font-semibold">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/>
                </svg>
                Descargar comprobante PDF
            </a>
        @endif

        {{-- Timeline --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Historial del pedido</h3>
            <div class="space-y-3">
                @foreach($pedido->historial as $h)
                    @php
                        $hColor = \App\Models\Pedido::ESTADOS[$h->estado_nuevo]['color'] ?? 'gray';
                        $hLabel = \App\Models\Pedido::ESTADOS[$h->estado_nuevo]['label'] ?? $h->estado_nuevo;
                    @endphp
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-2 h-2 rounded-full bg-{{ $hColor }}-500 mt-1.5"></div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $hLabel }}</p>
                            <p class="text-xs text-gray-500">{{ $h->created_at->format('d/m/Y H:i') }} · {{ $h->usuario?->name ?? 'Sistema' }}</p>
                            @if($h->comentario)
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $h->comentario }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-panel>
