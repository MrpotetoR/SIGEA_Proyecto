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
                    <h2 class="text-base font-bold text-green-700 dark:text-green-300 uppercase tracking-wider inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 17l9.2-9.2M17 17V7H7"/>
                        </svg>
                        Ingresos · Caja General
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
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                            Colegiaturas
                        </span>
                        <span class="font-mono font-semibold">${{ number_format($ingresosResumen['colegiaturas'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Productos tienda
                        </span>
                        <span class="font-mono font-semibold">${{ number_format($ingresosResumen['productos'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Trámites
                        </span>
                        <span class="font-mono font-semibold">${{ number_format($ingresosResumen['tramites'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Otros
                        </span>
                        <span class="font-mono font-semibold">${{ number_format($ingresosResumen['otros'], 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- DIFERENCIA (Caja Chica): faltante / sobrante / cuadrada --}}
            @php
                // Determina el tono visual según la diferencia.
                $diffEstado = match (true) {
                    $diferenciaCaja < 0 => 'faltante',
                    $diferenciaCaja > 0 => 'sobrante',
                    default             => 'cuadrada',
                };
                $diffStyles = [
                    'faltante' => [
                        'bg'        => 'bg-gradient-to-br from-rose-50 to-red-50 dark:from-rose-900/20 dark:to-red-900/20',
                        'border'    => 'border-rose-300 dark:border-rose-700',
                        'title'     => 'text-rose-700 dark:text-rose-300',
                        'amount'    => 'text-rose-900 dark:text-rose-200',
                        'sub'       => 'text-rose-700 dark:text-rose-400',
                        'divider'   => 'border-rose-200 dark:border-rose-700',
                        'label'     => 'Faltante · Caja Chica',
                        'helperLbl' => 'text-rose-700 dark:text-rose-400',
                        'sign'      => '−',
                        'helper'    => 'Diferencia por reponer respecto al monto base.',
                        // Flecha hacia abajo (egreso / déficit)
                        'iconPath'  => 'M17 17L7.8 7.8M7 17h10V7',
                    ],
                    'sobrante' => [
                        'bg'        => 'bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20',
                        'border'    => 'border-emerald-300 dark:border-emerald-700',
                        'title'     => 'text-emerald-700 dark:text-emerald-300',
                        'amount'    => 'text-emerald-900 dark:text-emerald-200',
                        'sub'       => 'text-emerald-700 dark:text-emerald-400',
                        'divider'   => 'border-emerald-200 dark:border-emerald-700',
                        'label'     => 'Sobrante · Caja Chica',
                        'helperLbl' => 'text-emerald-700 dark:text-emerald-400',
                        'sign'      => '+',
                        'helper'    => 'Excedente por encima del monto base del fondo.',
                        // Flecha hacia arriba
                        'iconPath'  => 'M7 17l9.2-9.2M17 17V7H7',
                    ],
                    'cuadrada' => [
                        'bg'        => 'bg-gradient-to-br from-sky-50 to-blue-50 dark:from-sky-900/20 dark:to-blue-900/20',
                        'border'    => 'border-sky-300 dark:border-sky-700',
                        'title'     => 'text-sky-700 dark:text-sky-300',
                        'amount'    => 'text-sky-900 dark:text-sky-200',
                        'sub'       => 'text-sky-700 dark:text-sky-400',
                        'divider'   => 'border-sky-200 dark:border-sky-700',
                        'label'     => 'Faltante de Caja Chica',
                        'helperLbl' => 'text-sky-700 dark:text-sky-400',
                        'sign'      => '',
                        'helper'    => 'El saldo actual coincide con el monto base del fondo.',
                        // Check
                        'iconPath'  => 'M5 13l4 4L19 7',
                    ],
                ][$diffEstado];
            @endphp
            <div class="{{ $diffStyles['bg'] }} border-2 {{ $diffStyles['border'] }} rounded-2xl p-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-bold {{ $diffStyles['title'] }} uppercase tracking-wider inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $diffStyles['iconPath'] }}" />
                        </svg>
                        {{ $diffStyles['label'] }}
                    </h2>
                </div>
                <p class="text-4xl font-bold {{ $diffStyles['amount'] }}">
                    {{ $diffStyles['sign'] }} ${{ number_format(abs($diferenciaCaja), 2) }}
                </p>
                <p class="text-sm {{ $diffStyles['sub'] }} mt-1">
                    {{ $diffStyles['helper'] }}
                </p>

                <div class="mt-4 pt-4 border-t {{ $diffStyles['divider'] }}">
                    <p class="text-xs uppercase tracking-wide font-semibold {{ $diffStyles['helperLbl'] }} mb-2">
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
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-2">
                        {{ $egresosResumen['conteo'] }} vales autorizados en el periodo (informativo).
                    </p>
                </div>
            </div>
        </div>

        {{-- Saldo neto del periodo --}}
        @php
            // Formato legible del componente caja chica:
            //   sin diferencia → "± $0.00"
            //   faltante       → "− $X.XX"
            //   sobrante       → "+ $X.XX"
            $cajaSigno = $diferenciaCaja < 0 ? '−' : ($diferenciaCaja > 0 ? '+' : '±');
            $cajaTextoOp = $diferenciaCaja < 0 ? '−' : '+';
        @endphp
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
                    {{ $cajaTextoOp }} Caja Chica ({{ $cajaSigno }}${{ number_format(abs($diferenciaCaja), 2) }})
                    = <strong>${{ number_format($saldoNeto, 2) }}</strong>
                </p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-4 italic max-w-xl mx-auto">
                    Nota: la Caja Chica funciona como un fondo objetivo, no como un flujo de egresos.
                    Lo que aporta al saldo neto del periodo es la diferencia entre su saldo actual y
                    su monto base (un faltante resta; un sobrante suma).
                </p>
            </div>
        </div>
    </div>
</x-panel>
