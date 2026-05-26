<x-panel title="Caja General — Dashboard" panelNombre="Panel Admin">
    <x-slot name="nav">@include('partials.admin-nav')</x-slot>

    <div class="space-y-6">

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- ============= TARJETAS RÁPIDAS (siempre hoy/semana/mes) ============= --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-5 border border-transparent dark:border-gray-700">
                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold">Hoy</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                    ${{ number_format($resumen['rapidas']['hoy'], 2) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ now()->translatedFormat('d \d\e F') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-5 border border-transparent dark:border-gray-700">
                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold">Esta semana</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                    ${{ number_format($resumen['rapidas']['semana'], 2) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ now()->startOfWeek()->format('d/m') }} — {{ now()->endOfWeek()->format('d/m') }}
                </p>
            </div>
            <div class="bg-gradient-to-br from-[#0606F0] to-[#04276B] text-white rounded-xl shadow p-5">
                <p class="text-xs uppercase tracking-wider text-white/80 font-semibold">Este mes</p>
                <p class="text-3xl font-bold mt-1">
                    ${{ number_format($resumen['rapidas']['mes'], 2) }}
                </p>
                <p class="text-xs text-white/70 mt-1">{{ now()->translatedFormat('F Y') }}</p>
            </div>
        </div>

        {{-- ============= FILTROS + ACCIONES EXPORT ============= --}}
        <form method="GET" class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-4 border border-transparent dark:border-gray-700">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-3 items-end">
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Rango</label>
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
                        <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Desde</label>
                        <input type="date" name="desde" value="{{ $filtros['desde'] }}"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
                        <input type="date" name="hasta" value="{{ $filtros['hasta'] }}"
                               class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                    </div>
                @else
                    <div class="col-span-2 text-xs text-gray-500 dark:text-gray-400 self-center">
                        Mostrando del <strong>{{ \Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y') }}</strong>
                        al <strong>{{ \Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y') }}</strong>
                    </div>
                @endif
                <div>
                    <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Tipo</label>
                    <select name="tipo"
                            class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                        <option value="">Todos</option>
                        @foreach(\App\Models\IngresoCajaGeneral::TIPOS as $k => $v)
                            <option value="{{ $k }}" @selected($filtros['tipo'] === $k)>{{ $v['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] uppercase text-gray-500 dark:text-gray-400 mb-1">Registrado por</label>
                    <select name="user_id"
                            class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 text-xs">
                        <option value="">Cualquiera</option>
                        @foreach($usuariosRegistradores as $u)
                            <option value="{{ $u->id }}" @selected((int) $filtros['user_id'] === $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-1">
                    <button class="bg-[#0606F0] hover:bg-[#04276B] text-white text-xs font-medium px-3 py-1.5 rounded-lg flex-1">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.caja-general.index') }}"
                       class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium px-3 py-1.5 rounded-lg">
                        Limpiar
                    </a>
                </div>
            </div>
            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t dark:border-gray-700 pt-3">
                <input type="text" name="buscar" value="{{ $filtros['buscar'] }}" placeholder="Buscar por folio o concepto..."
                       class="flex-1 min-w-[200px] border dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-1.5 text-xs">
                <div class="flex gap-2">
                    <a href="{{ route('admin.caja-general.export-pdf', request()->query()) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        PDF
                    </a>
                    <a href="{{ route('admin.caja-general.export-csv', request()->query()) }}"
                       class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        CSV
                    </a>
                    <a href="{{ route('admin.caja-general.cobro-tramite.create') }}"
                       class="inline-flex items-center gap-1.5 bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Cobrar trámite
                    </a>
                </div>
            </div>
        </form>

        {{-- ============= TOTAL DEL RANGO + DESGLOSE ============= --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Total del rango filtrado --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 lg:col-span-1">
                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold">Total del rango</p>
                <p class="text-4xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                    ${{ number_format($resumen['total'], 2) }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $resumen['conteo'] }} {{ $resumen['conteo'] === 1 ? 'ingreso' : 'ingresos' }} registrado{{ $resumen['conteo'] === 1 ? '' : 's' }}
                </p>
                <a href="{{ route('admin.caja-general.consolidado', request()->query()) }}"
                   class="inline-block mt-3 text-xs text-[#0606F0] dark:text-blue-400 hover:underline">
                    Ver reporte consolidado (con egresos Caja Chica) →
                </a>
            </div>

            {{-- Desglose por tipo --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 p-6 border border-transparent dark:border-gray-700 lg:col-span-2">
                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-3">
                    Desglose por concepto
                </p>
                @php
                    $tot = max($resumen['total'], 0.01);
                @endphp
                <div class="space-y-3">
                    @foreach(\App\Models\IngresoCajaGeneral::TIPOS as $k => $v)
                        @php
                            $monto = $resumen['por_tipo'][$k] ?? 0;
                            $pct = $tot > 0 ? round(($monto / $tot) * 100, 1) : 0;
                            $barColor = match($v['tw']) {
                                'blue'   => 'bg-blue-500',
                                'purple' => 'bg-purple-500',
                                'amber'  => 'bg-amber-500',
                                default  => 'bg-gray-400',
                            };
                            $iconColor = match($v['tw']) {
                                'blue'   => 'text-blue-500',
                                'purple' => 'text-purple-500',
                                'amber'  => 'text-amber-500',
                                default  => 'text-gray-400',
                            };
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-700 dark:text-gray-300 inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4 {{ $iconColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $v['icon_path'] }}"/>
                                    </svg>
                                    {{ $v['label'] }}
                                </span>
                                <span class="font-mono font-semibold text-gray-900 dark:text-gray-100">
                                    ${{ number_format($monto, 2) }}
                                    <span class="text-xs text-gray-400 ml-1">({{ $pct }}%)</span>
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div class="{{ $barColor }} h-2 transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ============= TABLA DE INGRESOS ============= --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow dark:shadow-gray-900/20 border border-transparent dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-3 border-b dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300">
                    Últimos ingresos
                </h3>
            </div>

            @if($ingresos->isEmpty())
                <div class="p-12 text-center text-gray-500 dark:text-gray-400 text-sm">
                    No hay ingresos que coincidan con los filtros.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/40 border-b dark:border-gray-700">
                            <tr>
                                <th class="text-left px-4 py-3">Folio</th>
                                <th class="text-left px-4 py-3">Fecha</th>
                                <th class="text-left px-4 py-3">Tipo</th>
                                <th class="text-left px-4 py-3">Concepto</th>
                                <th class="text-left px-4 py-3">Alumno</th>
                                <th class="text-right px-4 py-3">Monto</th>
                                <th class="text-left px-4 py-3">Registrado por</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach($ingresos as $i)
                                @php
                                    $tipoCls = match($i->tipo_color) {
                                        'blue'   => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
                                        'purple' => 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300',
                                        'amber'  => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300',
                                        default  => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                    <td class="px-4 py-3 font-mono text-xs text-[#0606F0] dark:text-blue-400">{{ $i->folio }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                        {{ $i->fecha_cobro?->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded {{ $tipoCls }}">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $i->tipo_icon_path }}"/>
                                            </svg>
                                            {{ $i->tipo_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs max-w-md truncate">
                                        {{ $i->concepto }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">
                                        {{ $i->alumno?->nombre_completo ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold text-gray-900 dark:text-gray-100">
                                        ${{ number_format((float) $i->monto, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                        {{ $i->usuario?->name ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($ingresos->hasPages())
                    <div class="p-4 border-t dark:border-gray-700">{{ $ingresos->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-panel>
