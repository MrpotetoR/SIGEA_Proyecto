<x-panel title="Reporte consolidado — Ingresos vs Egresos" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="max-w-6xl space-y-6">

        <div class="flex items-center justify-between flex-wrap gap-3">
            <a href="{{ route('admin.caja-general.index') }}" class="text-sm text-[#0606F0] dark:text-blue-400 hover:underline">← Volver al dashboard</a>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Vista combinada de Caja General (ingresos) y Caja Chica (egresos del fondo).
            </p>
        </div>

        {{-- Filtros de rango --}}
        <form method="GET" class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-4 border border-transparent dark:border-gray-700">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1 block">Rango</label>
                    <select name="rango" onchange="this.form.submit()"
                            class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                        <option value="hoy"          @selected($filtros['rango'] === 'hoy')>Hoy</option>
                        <option value="semana"       @selected($filtros['rango'] === 'semana')>Esta semana</option>
                        <option value="mes"          @selected($filtros['rango'] === 'mes')>Este mes</option>
                        <option value="personalizado" @selected($filtros['rango'] === 'personalizado')>Personalizado</option>
                    </select>
                </div>
                @if($filtros['rango'] === 'personalizado')
                    <div>
                        <label class="text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1 block">Desde</label>
                        <input type="date" name="desde" value="{{ $filtros['desde'] }}"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    </div>
                    <div>
                        <label class="text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1 block">Hasta</label>
                        <input type="date" name="hasta" value="{{ $filtros['hasta'] }}"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    </div>
                @else
                    <div class="col-span-2 text-xs text-gray-500 dark:text-gray-400 self-center">
                        Del <strong>{{ \Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y') }}</strong>
                        al <strong>{{ \Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y') }}</strong>
                    </div>
                @endif
                <button class="bg-[#0606F0] hover:bg-[#04276B] text-white text-xs font-medium px-3 py-1.5 rounded-lg">
                    Aplicar
                </button>
            </div>
        </form>

        {{-- Comparativa lado a lado --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- INGRESOS (Caja General) --}}
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-300 dark:border-green-700 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-bold text-green-700 dark:text-green-300 uppercase tracking-wider">
                        ↗ Ingresos · Caja General
                    </h2>
                </div>
                <p class="text-4xl font-bold text-green-900 dark:text-green-200">
                    + ${{ number_format($ingresosResumen['total'], 2) }}
                </p>
                <p class="text-sm text-green-700 dark:text-green-400 mt-1">
                    {{ $ingresosResumen['conteo'] }} ingresos en el periodo
                </p>

                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>🎓 Colegiaturas</span>
                        <span class="font-mono font-semibold">${{ number_format($ingresosResumen['colegiaturas'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>🛍 Productos tienda</span>
                        <span class="font-mono font-semibold">${{ number_format($ingresosResumen['productos'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>📋 Trámites</span>
                        <span class="font-mono font-semibold">${{ number_format($ingresosResumen['tramites'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>💰 Otros</span>
                        <span class="font-mono font-semibold">${{ number_format($ingresosResumen['otros'], 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- EGRESOS (Caja Chica) --}}
            <div class="bg-gradient-to-br from-rose-50 to-red-50 dark:from-rose-900/20 dark:to-red-900/20 border-2 border-rose-300 dark:border-rose-700 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-bold text-rose-700 dark:text-rose-300 uppercase tracking-wider">
                        ↘ Egresos · Caja Chica
                    </h2>
                </div>
                <p class="text-4xl font-bold text-rose-900 dark:text-rose-200">
                    − ${{ number_format($egresosResumen['total'], 2) }}
                </p>
                <p class="text-sm text-rose-700 dark:text-rose-400 mt-1">
                    {{ $egresosResumen['conteo'] }} vales autorizados en el periodo
                </p>

                <div class="mt-4 pt-4 border-t border-rose-200 dark:border-rose-700">
                    <p class="text-xs uppercase tracking-wide font-semibold text-rose-700 dark:text-rose-400 mb-2">
                        Estado actual del fondo
                    </p>
                    @php
                        $colorTw = $fondo->semaforo_color_tw;
                        $dotCls = match($colorTw) {
                            'green' => 'bg-green-500',
                            'amber' => 'bg-amber-500',
                            'red'   => 'bg-red-500',
                            default => 'bg-gray-400',
                        };
                    @endphp
                    <div class="flex items-center gap-2 text-sm">
                        <span class="w-2 h-2 rounded-full {{ $dotCls }}"></span>
                        <span class="font-mono font-bold text-gray-900 dark:text-gray-100">
                            ${{ number_format((float) $fondo->saldo_actual, 2) }}
                        </span>
                        <span class="text-gray-500 dark:text-gray-400">/ ${{ number_format((float) $fondo->monto_base, 2) }}</span>
                        <span class="ml-auto text-xs text-gray-500 dark:text-gray-400">
                            {{ $fondo->semaforo_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saldo neto del periodo --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg dark:shadow-gray-900/20 p-8 border-2 {{ $saldoNeto >= 0 ? 'border-[#0606F0]' : 'border-rose-500' }}">
            <div class="text-center">
                <p class="text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400 font-semibold mb-2">
                    Saldo Neto del Periodo
                </p>
                <p class="text-5xl font-bold {{ $saldoNeto >= 0 ? 'text-[#0606F0] dark:text-blue-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ $saldoNeto >= 0 ? '+' : '' }}${{ number_format($saldoNeto, 2) }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                    Ingresos (${{ number_format($ingresosResumen['total'], 2) }})
                    − Egresos (${{ number_format($egresosResumen['total'], 2) }})
                    = <strong>${{ number_format($saldoNeto, 2) }}</strong>
                </p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-4 italic max-w-xl mx-auto">
                    Nota: el saldo neto es la diferencia teórica del periodo.
                    El saldo real de la Caja Chica se rige por el fondo objetivo y se mantiene independiente.
                </p>
            </div>
        </div>
    </div>
</x-panel>
