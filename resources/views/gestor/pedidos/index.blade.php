<x-panel title="Pedidos de la Tienda" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Pedidos</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Valida pagos, prepara productos y marca entregas.</p>
    </div>

    {{-- Tabs por estado --}}
    @php
        $tabs = [
            'vaucher_enviado' => ['label' => 'Por validar', 'color' => 'amber'],
            'aprobado'        => ['label' => 'Por preparar', 'color' => 'blue'],
            'listo_recoger'   => ['label' => 'Listos', 'color' => 'green'],
            'entregado'       => ['label' => 'Entregados', 'color' => 'emerald'],
            'pendiente_pago'  => ['label' => 'Sin pagar', 'color' => 'gray'],
            'cancelado'       => ['label' => 'Cancelados', 'color' => 'red'],
            'todos'           => ['label' => 'Todos', 'color' => 'gray'],
        ];
    @endphp
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <nav class="flex gap-1 -mb-px overflow-x-auto">
            @foreach($tabs as $key => $cfg)
                @php $activo = $estado === $key; $count = $conteos[$key] ?? 0; @endphp
                <a href="{{ route('gestor.pedidos.index', array_merge(request()->except(['estado', 'page']), ['estado' => $key])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap
                          {{ $activo
                              ? "border-{$cfg['color']}-500 text-{$cfg['color']}-700 dark:text-{$cfg['color']}-400"
                              : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                    {{ $cfg['label'] }}
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold
                                 {{ $activo
                                     ? "bg-{$cfg['color']}-100 dark:bg-{$cfg['color']}-900/30 text-{$cfg['color']}-700 dark:text-{$cfg['color']}-300"
                                     : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        {{ $count }}
                    </span>
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex gap-3 mb-4 items-end">
        <input type="hidden" name="estado" value="{{ $estado }}">
        <div class="flex-1 max-w-md">
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   placeholder="Folio o nombre del usuario..."
                   class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>
        <button type="submit" class="bg-[#0606F0] hover:bg-[#04276B] text-white px-4 py-2 rounded-lg text-sm font-medium">Buscar</button>
    </form>

    @if($pedidos->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Folio</th>
                        <th class="px-4 py-3 text-left">Usuario</th>
                        <th class="px-4 py-3 text-left">Productos</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Fecha</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($pedidos as $pedido)
                        @php $color = $pedido->estado_color; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-gray-100">{{ $pedido->folio }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $pedido->usuario->name }}</p>
                                <p class="text-xs text-gray-500">{{ $pedido->usuario->email }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                {{ $pedido->items->count() }} {{ $pedido->items->count() === 1 ? 'item' : 'items' }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold">${{ number_format($pedido->total, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-300">
                                    {{ $pedido->estado_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-500">
                                {{ $pedido->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('gestor.pedidos.show', $pedido) }}" class="text-[#0606F0] dark:text-blue-400 hover:underline text-sm font-semibold">Ver →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $pedidos->links() }}</div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 p-12 text-center">
            <p class="text-sm text-gray-400">No hay pedidos en este estado.</p>
        </div>
    @endif
</x-panel>
