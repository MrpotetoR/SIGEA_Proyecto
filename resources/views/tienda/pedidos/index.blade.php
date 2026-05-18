@php
    $user = auth()->user();
    $panelNombre = $user->hasRole('alumno') ? 'Panel Alumno' : 'Panel Docente';
    $navPartial = $user->hasRole('alumno') ? 'partials.alumno-nav' : 'partials.docente-nav';
@endphp

<x-panel title="Mis Pedidos" :panelNombre="$panelNombre">
    <x-slot name="nav">@include($navPartial)</x-slot>

    <div class="max-w-5xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Mis Pedidos</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Historial de tus compras en la tienda institucional.</p>
            </div>
            <a href="{{ route('tienda.catalogo') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline">← Volver al catálogo</a>
        </div>

        @if($pedidos->isNotEmpty())
            <div class="space-y-3">
                @foreach($pedidos as $pedido)
                    @php
                        $color = $pedido->estado_color;
                        $badge = "bg-{$color}-100 text-{$color}-700 dark:bg-{$color}-900/30 dark:text-{$color}-300";
                    @endphp
                    <a href="{{ route('tienda.pedido.show', $pedido) }}"
                       class="block bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="font-mono font-bold text-gray-900 dark:text-gray-100">{{ $pedido->folio }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $badge }}">
                                        {{ $pedido->estado_label }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    {{ $pedido->created_at->format('d/m/Y H:i') }} ·
                                    {{ $pedido->items->count() }} {{ $pedido->items->count() === 1 ? 'producto' : 'productos' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">${{ number_format($pedido->total, 2) }}</p>
                                <p class="text-[10px] text-gray-400">Ver detalle →</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $pedidos->links() }}</div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-12 text-center">
                <p class="text-sm text-gray-400 mb-3">Aún no has realizado ningún pedido.</p>
                <a href="{{ route('tienda.catalogo') }}" class="text-sm text-[#0606F0] hover:underline font-semibold">Explorar la tienda →</a>
            </div>
        @endif
    </div>
</x-panel>
