<x-panel title="Historial Asistencia - {{ $grupo->clave_grupo }}" panelNombre="Panel Docente">
<x-slot name="nav">@include('partials.docente-nav')</x-slot>

@php
    // Agrupar fechas por semana ISO para header tipo Excel.
    $fechasPorSemana = $fechas->groupBy(fn($f) => \Carbon\Carbon::parse($f)->isoWeek());
@endphp

<div class="space-y-5" x-data="historialAsistencia()">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-[22px] font-bold text-gray-900 dark:text-gray-100">Historial de Asistencia</h1>
            <p class="text-[13px] text-gray-400 dark:text-gray-500 mt-1">
                {{ $grupo->clave_grupo }} &mdash; {{ $horario?->materia?->nombre_materia ?? 'Materia' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('docente.asistencia.show', $grupo->id_grupo) }}"
               class="text-[12px] font-medium text-sky-700 dark:text-sky-400 bg-sky-50 dark:bg-sky-900/30 px-3 py-1.5 rounded-xl hover:bg-sky-100 dark:hover:bg-sky-900/50 transition-colors">
                Pasar asistencia hoy
            </a>
            <a href="{{ route('docente.asistencia') }}"
               class="text-[12px] font-medium text-gray-500 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">&larr; Volver</a>
        </div>
    </div>

    {{-- Filtro de período --}}
    <form method="GET" class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1 block">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}" class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 text-[13px] outline-none focus:ring-2 focus:ring-sky-300">
            </div>
            <div>
                <label class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1 block">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}" class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 text-[13px] outline-none focus:ring-2 focus:ring-sky-300">
            </div>
            <button type="submit" class="bg-[#0606F0] text-white px-5 py-2 rounded-xl text-[13px] font-medium hover:bg-[#04276B] dark:hover:bg-blue-400 transition-colors">Filtrar</button>
            {{-- Accesos rápidos --}}
            <div class="flex gap-2 ml-auto">
                <a href="?desde={{ today()->startOfWeek()->toDateString() }}&hasta={{ today()->endOfWeek()->toDateString() }}" class="text-[12px] text-sky-700 dark:text-sky-400 hover:underline">Semana</a>
                <a href="?desde={{ today()->startOfMonth()->toDateString() }}&hasta={{ today()->endOfMonth()->toDateString() }}" class="text-[12px] text-sky-700 dark:text-sky-400 hover:underline">Mes</a>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="bg-emerald-50 dark:bg-green-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-green-300 px-4 py-3 rounded-2xl text-[13px]">{{ session('success') }}</div>
    @endif

    @if($fechas->isEmpty())
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-[14px] text-gray-400 dark:text-gray-500">Sin registros de asistencia en este período.</p>
        </div>
    @else
        {{-- Cuadrícula --}}
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 dark:shadow-gray-900/20 rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-auto custom-scrollbar" style="max-height: calc(100vh - 360px);">
                <table class="border-collapse text-[12px] min-w-full">
                    {{-- Header: semana + día --}}
                    <thead>
                        {{-- Fila 1: agrupación por semana --}}
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th rowspan="2" class="sticky left-0 z-30 bg-gray-50 dark:bg-gray-700/50 px-3 py-2 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase border-b border-r dark:border-gray-600" style="min-width:220px">Alumno</th>
                            @foreach($fechasPorSemana as $semana => $dias)
                                <th colspan="{{ count($dias) }}" class="px-2 py-1.5 text-center text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase border-b border-l dark:border-gray-600 bg-sky-50/50 dark:bg-sky-900/20">
                                    Semana {{ $semana }}
                                </th>
                            @endforeach
                            <th colspan="4" rowspan="2" class="sticky right-0 z-30 bg-gray-50 dark:bg-gray-700/50 px-3 py-2 text-center text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase border-b border-l dark:border-gray-600">Totales</th>
                        </tr>
                        {{-- Fila 2: días individuales --}}
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            @foreach($fechas as $f)
                                @php $c = \Carbon\Carbon::parse($f); @endphp
                                <th class="px-1.5 py-1 text-center text-[10px] font-medium text-gray-500 dark:text-gray-400 border-b dark:border-gray-600 min-w-[40px]" title="{{ $c->isoFormat('dddd D MMM YYYY') }}">
                                    <div class="text-gray-400 dark:text-gray-500">{{ strtoupper($c->isoFormat('dd')) }}</div>
                                    <div class="font-bold text-gray-600 dark:text-gray-300">{{ $c->format('d') }}</div>
                                </th>
                            @endforeach
                        </tr>
                        {{-- Fila 3: sub-header totales --}}
                        <tr class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-600">
                            <th class="sticky left-0 z-20 bg-gray-50 dark:bg-gray-700/50 border-r dark:border-gray-600"></th>
                            @foreach($fechas as $f)
                                <th></th>
                            @endforeach
                            <th class="px-1 py-1 text-center text-[9px] font-semibold text-emerald-700 dark:text-emerald-400 uppercase">Asis</th>
                            <th class="px-1 py-1 text-center text-[9px] font-semibold text-sky-700 dark:text-sky-400 uppercase">%</th>
                            <th class="px-1 py-1 text-center text-[9px] font-semibold text-amber-700 dark:text-amber-400 uppercase">Ret</th>
                            <th class="px-1 py-1 text-center text-[9px] font-semibold text-red-700 dark:text-red-400 uppercase">Falt</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($matriz as $idx => $fila)
                            @php
                                $faltas = $fila['faltas'];
                                $alerta = $faltas >= 3;
                                $rowBg = $alerta
                                    ? 'bg-red-50 dark:bg-red-900/20 hover:bg-red-100/60 dark:hover:bg-red-900/30'
                                    : ($idx % 2 === 0 ? 'hover:bg-gray-50 dark:hover:bg-gray-700/40' : 'bg-gray-50/40 dark:bg-gray-700/20 hover:bg-gray-50 dark:hover:bg-gray-700/40');
                                $stickyBg = $alerta
                                    ? 'bg-red-50 dark:bg-red-900/20'
                                    : ($idx % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/60 dark:bg-gray-800/60');
                            @endphp
                            <tr class="{{ $rowBg }} transition-colors">
                                {{-- Alumno sticky --}}
                                <td class="sticky left-0 z-10 {{ $stickyBg }} px-3 py-2 border-r dark:border-gray-700">
                                    <div class="flex items-center gap-2">
                                        @if($alerta)
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0" title="3 o más faltas"></span>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="text-[13px] font-medium text-gray-800 dark:text-gray-200 truncate">{{ $fila['alumno']->nombre_completo }}</div>
                                            <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">{{ $fila['alumno']->matricula }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Celdas por día --}}
                                @foreach($fechas as $f)
                                    @php
                                        $estatus = $fila['dias'][$f] ?? null;
                                        $payload = [
                                            'id_alumno' => $fila['alumno']->id_alumno,
                                            'nombre'    => $fila['alumno']->nombre_completo,
                                            'fecha'     => $f,
                                            'estatus'   => $estatus,
                                        ];
                                        $tituloFecha = \Carbon\Carbon::parse($f)->isoFormat('D MMM YYYY');
                                    @endphp
                                    <td class="px-1.5 py-1.5 text-center border-l dark:border-gray-700/50">
                                        <button type="button"
                                                @click="abrirModal({{ \Illuminate\Support\Js::from($payload) }})"
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-md transition-all hover:scale-110 @if($estatus === 'presente') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 @elseif($estatus === 'ausente') bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 @elseif($estatus === 'retardo') bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 @else bg-gray-100 text-gray-300 dark:bg-gray-700 dark:text-gray-600 @endif"
                                                title="{{ $tituloFecha }} — {{ ucfirst($estatus ?? 'sin registro') }}">
                                            @if($estatus === 'presente')
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="w-4 h-4"><polyline points="20 6 9 17 4 12"/></svg>
                                            @elseif($estatus === 'ausente')
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="w-4 h-4"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            @elseif($estatus === 'retardo')
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-4 h-4"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            @else
                                                <span class="text-[14px] leading-none">·</span>
                                            @endif
                                        </button>
                                    </td>
                                @endforeach

                                {{-- Totales por alumno sticky --}}
                                <td class="sticky right-[120px] z-10 {{ $stickyBg }} px-2 py-2 text-center border-l dark:border-gray-700">
                                    <span class="text-[12px] font-bold text-emerald-700 dark:text-emerald-400">{{ $fila['presentes'] }}</span>
                                </td>
                                <td class="sticky right-[80px] z-10 {{ $stickyBg }} px-2 py-2 text-center">
                                    @php
                                        $pct = $fila['porcentaje'];
                                        $pctClass = $pct >= 80 ? 'text-emerald-700 dark:text-emerald-400' : ($pct >= 60 ? 'text-amber-700 dark:text-amber-400' : 'text-red-700 dark:text-red-400');
                                    @endphp
                                    <span class="text-[12px] font-bold {{ $pctClass }}">{{ $pct }}%</span>
                                </td>
                                <td class="sticky right-[40px] z-10 {{ $stickyBg }} px-2 py-2 text-center">
                                    <span class="text-[12px] font-bold text-amber-700 dark:text-amber-400">{{ $fila['retardos'] }}</span>
                                </td>
                                <td class="sticky right-0 z-10 {{ $stickyBg }} px-2 py-2 text-center border-l dark:border-gray-700">
                                    <span class="text-[12px] font-bold text-red-700 dark:text-red-400">{{ $faltas }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    {{-- Footer: totales por día --}}
                    <tfoot class="border-t-2 border-gray-300 dark:border-gray-600">
                        <tr class="bg-emerald-50/40 dark:bg-emerald-900/10">
                            <th class="sticky left-0 z-20 bg-emerald-50/60 dark:bg-emerald-900/20 px-3 py-1.5 text-left text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 uppercase border-r dark:border-gray-600">Asistencias</th>
                            @foreach($fechas as $f)
                                <td class="px-1 py-1 text-center text-[11px] font-bold text-emerald-700 dark:text-emerald-400">{{ $totalesPorDia[$f]['presente'] }}</td>
                            @endforeach
                            <td colspan="4" class="sticky right-0 z-20 bg-emerald-50/60 dark:bg-emerald-900/20 border-l dark:border-gray-600"></td>
                        </tr>
                        <tr class="bg-sky-50/40 dark:bg-sky-900/10">
                            <th class="sticky left-0 z-20 bg-sky-50/60 dark:bg-sky-900/20 px-3 py-1.5 text-left text-[10px] font-semibold text-sky-700 dark:text-sky-400 uppercase border-r dark:border-gray-600">% Asistencia</th>
                            @foreach($fechas as $f)
                                @php
                                    $t = $totalesPorDia[$f];
                                    $totDia = $t['presente'] + $t['ausente'] + $t['retardo'];
                                    $pctDia = $totDia > 0 ? round(($t['presente'] / $totDia) * 100) : 0;
                                @endphp
                                <td class="px-1 py-1 text-center text-[10px] font-bold text-sky-700 dark:text-sky-400">{{ $pctDia }}%</td>
                            @endforeach
                            <td colspan="4" class="sticky right-0 z-20 bg-sky-50/60 dark:bg-sky-900/20 border-l dark:border-gray-600"></td>
                        </tr>
                        <tr class="bg-amber-50/40 dark:bg-amber-900/10">
                            <th class="sticky left-0 z-20 bg-amber-50/60 dark:bg-amber-900/20 px-3 py-1.5 text-left text-[10px] font-semibold text-amber-700 dark:text-amber-400 uppercase border-r dark:border-gray-600">Retardos</th>
                            @foreach($fechas as $f)
                                <td class="px-1 py-1 text-center text-[11px] font-bold text-amber-700 dark:text-amber-400">{{ $totalesPorDia[$f]['retardo'] }}</td>
                            @endforeach
                            <td colspan="4" class="sticky right-0 z-20 bg-amber-50/60 dark:bg-amber-900/20 border-l dark:border-gray-600"></td>
                        </tr>
                        <tr class="bg-red-50/40 dark:bg-red-900/10">
                            <th class="sticky left-0 z-20 bg-red-50/60 dark:bg-red-900/20 px-3 py-1.5 text-left text-[10px] font-semibold text-red-700 dark:text-red-400 uppercase border-r dark:border-gray-600">Faltas</th>
                            @foreach($fechas as $f)
                                <td class="px-1 py-1 text-center text-[11px] font-bold text-red-700 dark:text-red-400">{{ $totalesPorDia[$f]['ausente'] }}</td>
                            @endforeach
                            <td colspan="4" class="sticky right-0 z-20 bg-red-50/60 dark:bg-red-900/20 border-l dark:border-gray-600"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Leyenda --}}
        <div class="flex flex-wrap gap-4 text-[11px] text-gray-500 dark:text-gray-400 px-2">
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="w-3 h-3"><polyline points="20 6 9 17 4 12"/></svg>
                </span> Presente
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-3 h-3"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </span> Retardo
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="w-3 h-3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </span> Ausente
            </span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700"></span> Alumno con 3+ faltas</span>
            <span class="inline-flex items-center gap-1.5 text-gray-400 dark:text-gray-500">Tip: Click en cualquier celda para editar ese día.</span>
        </div>
    @endif

    {{-- Modal edición --}}
    <template x-teleport="body">
        <div x-show="modal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div class="absolute inset-0 bg-black/50" @click="modal = false"></div>
            <div x-show="modal" x-transition.scale.95 @click.away="modal = false"
                 class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-sm w-full p-6">
                <h3 class="text-[15px] font-semibold text-gray-900 dark:text-gray-100 mb-1">Editar asistencia</h3>
                <p class="text-[12px] text-gray-500 dark:text-gray-400 mb-4">
                    <span x-text="datos.nombre" class="font-medium text-gray-700 dark:text-gray-300"></span><br>
                    <span x-text="fechaFormateada" class="text-gray-400"></span>
                </p>

                <form method="POST" action="{{ route('docente.asistencia.store', $grupo->id_grupo) }}">
                    @csrf
                    <input type="hidden" name="id_grupo" value="{{ $grupo->id_grupo }}">
                    <input type="hidden" name="id_horario" value="{{ $horario->id_horario }}">
                    <input type="hidden" name="fecha" :value="datos.fecha">
                    <input type="hidden" name="asistencia[{{ '' }}]" :name="`asistencia[${datos.id_alumno}]`" x-model="nuevoEstatus">

                    <div class="grid grid-cols-3 gap-2 mb-5">
                        <button type="button" @click="nuevoEstatus = 'presente'"
                                :class="nuevoEstatus === 'presente' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-300 border-gray-200 dark:border-gray-600'"
                                class="py-3 rounded-xl border-2 text-[12px] font-semibold transition-all">
                            Presente
                        </button>
                        <button type="button" @click="nuevoEstatus = 'retardo'"
                                :class="nuevoEstatus === 'retardo' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-300 border-gray-200 dark:border-gray-600'"
                                class="py-3 rounded-xl border-2 text-[12px] font-semibold transition-all">
                            Retardo
                        </button>
                        <button type="button" @click="nuevoEstatus = 'ausente'"
                                :class="nuevoEstatus === 'ausente' ? 'bg-red-500 text-white border-red-500' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-300 border-gray-200 dark:border-gray-600'"
                                class="py-3 rounded-xl border-2 text-[12px] font-semibold transition-all">
                            Ausente
                        </button>
                    </div>

                    <div class="flex gap-2 justify-end">
                        <button type="button" @click="modal = false" class="px-4 py-2 rounded-xl text-[12px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancelar</button>
                        <button type="submit" :disabled="!nuevoEstatus" class="px-4 py-2 rounded-xl text-[12px] font-semibold bg-[#0606F0] text-white hover:bg-[#04276B] disabled:opacity-50 disabled:cursor-not-allowed transition-colors">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>

<script>
function historialAsistencia() {
    return {
        modal: false,
        datos: { id_alumno: null, nombre: '', fecha: '', estatus: null },
        nuevoEstatus: null,
        abrirModal(d) {
            this.datos = d;
            this.nuevoEstatus = d.estatus;
            this.modal = true;
        },
        get fechaFormateada() {
            if (!this.datos.fecha) return '';
            const f = new Date(this.datos.fecha + 'T00:00:00');
            return f.toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }
    }
}
</script>

</x-panel>
