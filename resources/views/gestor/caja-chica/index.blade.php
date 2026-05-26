<x-panel title="Caja Chica" panelNombre="Panel Gestor Escolar">
    <x-slot name="nav">@include('partials.gestor-nav')</x-slot>

    <div class="space-y-6">

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- ============= TARJETA RESUMEN DEL FONDO ============= --}}
        @php
            $colorTw = $fondo->semaforo_color_tw;
            $bgClasses = [
                'green' => 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700',
                'amber' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-300 dark:border-amber-700',
                'red'   => 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700',
            ];
            $dotClasses = [
                'green' => 'bg-green-500', 'amber' => 'bg-amber-500', 'red' => 'bg-red-500',
            ];
            $pct = min(100, max(0, $fondo->porcentaje_saldo));
            $barColor = $colorTw === 'green' ? 'bg-green-500' : ($colorTw === 'amber' ? 'bg-amber-500' : 'bg-red-500');
        @endphp

        <div class="rounded-2xl border-2 p-6 {{ $bgClasses[$colorTw] ?? $bgClasses['red'] }}">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-block w-3 h-3 rounded-full {{ $dotClasses[$colorTw] ?? $dotClasses['red'] }} animate-pulse"></span>
                        <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            {{ $fondo->semaforo_label }}
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
                        @endif
                    </p>
                </div>
                <a href="{{ route('gestor.caja-chica.create') }}"
                   class="bg-[#0606F0] hover:bg-[#04276B] text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    + Nueva solicitud
                </a>
            </div>
            <div class="mt-4 w-full bg-white/60 dark:bg-gray-900/40 rounded-full h-3 overflow-hidden">
                <div class="{{ $barColor }} h-3 transition-all" style="width: {{ $pct }}%"></div>
            </div>
        </div>

        {{-- ============= FILTROS ============= --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-4 border border-transparent dark:border-gray-700">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[180px]">
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Buscar</label>
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           placeholder="Folio, solicitante, concepto..."
                           class="w-full mt-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Estatus</label>
                    <select name="estatus"
                            class="w-full mt-1 border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">Todos</option>
                        @foreach(\App\Models\ValeCajaChica::ESTADOS as $k => $v)
                            <option value="{{ $k }}" {{ request('estatus') === $k ? 'selected' : '' }}>
                                {{ $v['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200">
                    Filtrar
                </button>
                @if(request('buscar') || request('estatus'))
                    <a href="{{ route('gestor.caja-chica.index') }}"
                       class="text-sm text-gray-500 dark:text-gray-400 hover:underline">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- ============= TABLA DE VALES ============= --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden">
            @if($vales->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">No hay vales que coincidan con los filtros.</p>
                    <a href="{{ route('gestor.caja-chica.create') }}"
                       class="inline-block mt-4 text-[#0606F0] dark:text-blue-400 hover:underline text-sm font-medium">
                        Crear primera solicitud →
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/40 border-b dark:border-gray-700">
                            <tr>
                                <th class="text-left py-3 px-4">Folio</th>
                                <th class="text-left py-3 px-4">Fecha</th>
                                <th class="text-left py-3 px-4">Solicitante</th>
                                <th class="text-left py-3 px-4">Concepto</th>
                                <th class="text-right py-3 px-4">Monto</th>
                                <th class="text-center py-3 px-4">Estatus</th>
                                <th class="text-center py-3 px-4">Factura</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach($vales as $v)
                                @php
                                    $tw = $v->estado_color;
                                    $estadoClass = match($tw) {
                                        'green'  => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300',
                                        'blue'   => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
                                        'rose'   => 'bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300',
                                        'slate'  => 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300',
                                        default  => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                    <td class="py-3 px-4">
                                        <a href="{{ route('gestor.caja-chica.show', $v) }}"
                                           class="font-mono text-xs font-semibold text-[#0606F0] dark:text-blue-400 hover:underline">
                                            {{ $v->folio }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-xs text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                        {{ $v->created_at->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-700 dark:text-gray-300">{{ $v->solicitante_nombre }}</td>
                                    <td class="py-3 px-4 text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ $v->concepto }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-gray-900 dark:text-gray-100">
                                        ${{ number_format((float) $v->monto, 2) }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="text-[11px] font-semibold px-2 py-1 rounded {{ $estadoClass }}">
                                            {{ $v->estado_label }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($v->tiene_factura)
                                            <span class="text-green-600 dark:text-green-400" title="Factura cargada">📎</span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-600">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($vales->hasPages())
                    <div class="p-4 border-t dark:border-gray-700">
                        {{ $vales->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-panel>
